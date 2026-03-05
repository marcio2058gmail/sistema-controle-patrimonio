<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\Responsibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with(['employee', 'assets'])->latest();

        $user        = $request->user();
        $employee = $user->employee;

        if ($user->isAdmin()) {
            // admin vê tudo
        } elseif ($user->isManager()) {
            // gestor vê os chamados do seu departamento
            if ($employee && $employee->departamento_id) {
                $deptId = $employee->departamento_id;
                $query->whereHas('employee', fn ($q) => $q->where('departamento_id', $deptId));
            } elseif ($employee) {
                $query->where('funcionario_id', $employee->id);
            } else {
                $query->whereNull('id');
            }
        } else {
            // funcionário vê apenas os seus
            if ($employee) {
                $query->where('funcionario_id', $employee->id);
            } else {
                $query->whereNull('id');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets      = $query->paginate(15)->withQueryString();
        $statusLabels = Ticket::statusLabels();

        // Dados para o modal "Abrir Chamado"
        $assets = Asset::disponivel()->orderBy('descricao')->get();
        if ($user->isAdmin()) {
            $employees = Employee::orderBy('nome')->get();
        } elseif ($user->isManager()) {
            $emp       = $user->employee;
            $employees = ($emp && $emp->departamento_id)
                ? Employee::where('departamento_id', $emp->departamento_id)->orderBy('nome')->get()
                : collect();
        } else {
            $employees = collect();
        }

        return view('tickets.index', compact('tickets', 'statusLabels', 'assets', 'employees'));
    }

    public function create(Request $request): View
    {
        $assets = Asset::disponivel()->orderBy('descricao')->get();
        $user        = $request->user();

        if ($user->isAdmin()) {
            $employees = Employee::orderBy('nome')->get();
        } elseif ($user->isManager()) {
            $employee  = $user->employee;
            $employees = ($employee && $employee->departamento_id)
                ? Employee::where('departamento_id', $employee->departamento_id)->orderBy('nome')->get()
                : collect();
        } else {
            $employees = collect();
        }

        return view('tickets.create', compact('assets', 'employees'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $patrimonioIds = $data['patrimonio_ids'] ?? [];
        unset($data['patrimonio_ids']);

        // Funcionário: vincula automaticamente a si mesmo
        if ($request->user()->isEmployee()) {
            $employee = $request->user()->employee;
            if (! $employee) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            $data['funcionario_id'] = $employee->id;
        }
        // Gestor: valida se o funcionário selecionado pertence ao seu departamento
        if ($request->user()->isManager()) {
            $manager = $request->user()->employee;
            if (! $manager) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            if ($manager->departamento_id) {
                $funcSelecionado = Employee::find($data['funcionario_id']);
                if (! $funcSelecionado || $funcSelecionado->departamento_id !== $manager->departamento_id) {
                    return back()->withErrors(['funcionario_id' => 'O funcionário selecionado não pertence ao seu departamento.']);
                }
            }
        }

        $ticket = Ticket::create($data);

        if (! empty($patrimonioIds)) {
            $ticket->assets()->sync($patrimonioIds);
        }

        return redirect()->route('tickets.index')
            ->with('sucesso', 'Chamado aberto com sucesso.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $ticket->load(['employee', 'assets']);

        $user        = $request->user();
        $employee = $user->employee;

        if ($user->isAdmin()) {
            // admin acessa qualquer chamado
        } elseif ($user->isManager()) {
            // gestor acessa chamados do seu departamento
            if (! $employee) abort(403);
            if ($employee->departamento_id) {
                if (! $ticket->employee || $ticket->employee->departamento_id !== $employee->departamento_id) {
                    abort(403);
                }
            } elseif ($ticket->funcionario_id !== $employee->id) {
                abort(403);
            }
        } else {
            // funcionário acessa apenas os seus
            if (! $employee || $ticket->funcionario_id !== $employee->id) abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }

    public function aprovar(Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== Ticket::STATUS_OPEN) {
            return back()->withErrors(['status' => 'Apenas chamados abertos podem ser aprovados.']);
        }

        $ticket->update(['status' => Ticket::STATUS_APPROVED]);

        return back()->with('sucesso', 'Chamado aprovado com sucesso.');
    }

    public function negar(Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== Ticket::STATUS_OPEN) {
            return back()->withErrors(['status' => 'Apenas chamados abertos podem ser negados.']);
        }

        $ticket->update(['status' => Ticket::STATUS_DENIED]);

        return back()->with('sucesso', 'Chamado negado.');
    }

    public function entregar(Ticket $ticket): RedirectResponse
    {
        if ($ticket->status !== Ticket::STATUS_APPROVED) {
            return back()->withErrors(['status' => 'Apenas chamados aprovados podem ser marcados como entregues.']);
        }

        $assets = $ticket->assets()->where('status', Asset::STATUS_AVAILABLE)->get();

        if ($assets->isEmpty()) {
            return back()->withErrors(['status' => 'O chamado não possui patrimônios disponíveis para entrega.']);
        }

        foreach ($assets as $asset) {
            // Cria responsabilidade para cada patrimônio
            Responsibility::create([
                'funcionario_id'         => $ticket->funcionario_id,
                'patrimonio_id'          => $asset->id,
                'data_entrega'           => now()->toDateString(),
                'termo_responsabilidade' => "Termo gerado automaticamente na entrega do chamado #{$ticket->id}. " .
                    "O funcionário {$ticket->employee->nome} recebe o patrimônio " .
                    "{$asset->codigo_patrimonio} — {$asset->descricao}.",
                'assinado'               => false,
            ]);

            $asset->update(['status' => Asset::STATUS_IN_USE]);
        }

        $ticket->update(['status' => Ticket::STATUS_DELIVERED]);

        return back()->with('sucesso', 'Entrega registrada e termos de responsabilidade gerados.');
    }
}
