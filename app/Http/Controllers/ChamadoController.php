<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChamadoRequest;
use App\Models\Chamado;
use App\Models\Funcionario;
use App\Models\Patrimonio;
use App\Models\Responsabilidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChamadoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Chamado::with(['funcionario', 'patrimonios'])->latest();

        $user        = $request->user();
        $funcionario = $user->funcionario;

        if ($user->isAdmin()) {
            // admin vê tudo
        } elseif ($user->isGestor()) {
            // gestor vê os chamados do seu departamento
            if ($funcionario && $funcionario->departamento_id) {
                $deptId = $funcionario->departamento_id;
                $query->whereHas('funcionario', fn ($q) => $q->where('departamento_id', $deptId));
            } elseif ($funcionario) {
                $query->where('funcionario_id', $funcionario->id);
            } else {
                $query->whereNull('id');
            }
        } else {
            // funcionário vê apenas os seus
            if ($funcionario) {
                $query->where('funcionario_id', $funcionario->id);
            } else {
                $query->whereNull('id');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $chamados     = $query->paginate(15)->withQueryString();
        $statusLabels = Chamado::statusLabels();

        return view('chamados.index', compact('chamados', 'statusLabels'));
    }

    public function create(Request $request): View
    {
        $patrimonios = Patrimonio::disponivel()->orderBy('descricao')->get();
        $user        = $request->user();

        if ($user->isAdmin()) {
            $funcionarios = Funcionario::orderBy('nome')->get();
        } elseif ($user->isGestor()) {
            $funcionario  = $user->funcionario;
            $funcionarios = ($funcionario && $funcionario->departamento_id)
                ? Funcionario::where('departamento_id', $funcionario->departamento_id)->orderBy('nome')->get()
                : collect();
        } else {
            $funcionarios = collect();
        }

        return view('chamados.create', compact('patrimonios', 'funcionarios'));
    }

    public function store(StoreChamadoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $patrimonioIds = $data['patrimonio_ids'] ?? [];
        unset($data['patrimonio_ids']);

        // Funcionário: vincula automaticamente a si mesmo
        if ($request->user()->isFuncionario()) {
            $funcionario = $request->user()->funcionario;
            if (! $funcionario) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            $data['funcionario_id'] = $funcionario->id;
        }
        // Gestor: valida se o funcionário selecionado pertence ao seu departamento
        if ($request->user()->isGestor()) {
            $gestor = $request->user()->funcionario;
            if (! $gestor) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            if ($gestor->departamento_id) {
                $funcSelecionado = Funcionario::find($data['funcionario_id']);
                if (! $funcSelecionado || $funcSelecionado->departamento_id !== $gestor->departamento_id) {
                    return back()->withErrors(['funcionario_id' => 'O funcionário selecionado não pertence ao seu departamento.']);
                }
            }
        }

        $chamado = Chamado::create($data);

        if (! empty($patrimonioIds)) {
            $chamado->patrimonios()->sync($patrimonioIds);
        }

        return redirect()->route('chamados.index')
            ->with('sucesso', 'Chamado aberto com sucesso.');
    }

    public function show(Request $request, Chamado $chamado): View
    {
        $chamado->load(['funcionario', 'patrimonios']);

        $user        = $request->user();
        $funcionario = $user->funcionario;

        if ($user->isAdmin()) {
            // admin acessa qualquer chamado
        } elseif ($user->isGestor()) {
            // gestor acessa chamados do seu departamento
            if (! $funcionario) abort(403);
            if ($funcionario->departamento_id) {
                if (! $chamado->funcionario || $chamado->funcionario->departamento_id !== $funcionario->departamento_id) {
                    abort(403);
                }
            } elseif ($chamado->funcionario_id !== $funcionario->id) {
                abort(403);
            }
        } else {
            // funcionário acessa apenas os seus
            if (! $funcionario || $chamado->funcionario_id !== $funcionario->id) abort(403);
        }

        return view('chamados.show', compact('chamado'));
    }

    public function aprovar(Chamado $chamado): RedirectResponse
    {
        if ($chamado->status !== Chamado::STATUS_ABERTO) {
            return back()->withErrors(['status' => 'Apenas chamados abertos podem ser aprovados.']);
        }

        $chamado->update(['status' => Chamado::STATUS_APROVADO]);

        return back()->with('sucesso', 'Chamado aprovado com sucesso.');
    }

    public function negar(Chamado $chamado): RedirectResponse
    {
        if ($chamado->status !== Chamado::STATUS_ABERTO) {
            return back()->withErrors(['status' => 'Apenas chamados abertos podem ser negados.']);
        }

        $chamado->update(['status' => Chamado::STATUS_NEGADO]);

        return back()->with('sucesso', 'Chamado negado.');
    }

    public function entregar(Chamado $chamado): RedirectResponse
    {
        if ($chamado->status !== Chamado::STATUS_APROVADO) {
            return back()->withErrors(['status' => 'Apenas chamados aprovados podem ser marcados como entregues.']);
        }

        $patrimonios = $chamado->patrimonios()->where('status', Patrimonio::STATUS_DISPONIVEL)->get();

        if ($patrimonios->isEmpty()) {
            return back()->withErrors(['status' => 'O chamado não possui patrimônios disponíveis para entrega.']);
        }

        foreach ($patrimonios as $patrimonio) {
            // Cria responsabilidade para cada patrimônio
            Responsabilidade::create([
                'funcionario_id'         => $chamado->funcionario_id,
                'patrimonio_id'          => $patrimonio->id,
                'data_entrega'           => now()->toDateString(),
                'termo_responsabilidade' => "Termo gerado automaticamente na entrega do chamado #{$chamado->id}. " .
                    "O funcionário {$chamado->funcionario->nome} recebe o patrimônio " .
                    "{$patrimonio->codigo_patrimonio} — {$patrimonio->descricao}.",
                'assinado'               => false,
            ]);

            $patrimonio->update(['status' => Patrimonio::STATUS_EM_USO]);
        }

        $chamado->update(['status' => Chamado::STATUS_ENTREGUE]);

        return back()->with('sucesso', 'Entrega registrada e termos de responsabilidade gerados.');
    }
}
