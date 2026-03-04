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

        // Funcionário só vê seus próprios chamados
        if ($request->user()->isFuncionario()) {
            $funcionario = $request->user()->funcionario;
            if ($funcionario) {
                $query->where('funcionario_id', $funcionario->id);
            } else {
                $query->whereNull('id'); // nenhum resultado
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $chamados     = $query->paginate(15)->withQueryString();
        $statusLabels = Chamado::statusLabels();

        return view('chamados.index', compact('chamados', 'statusLabels'));
    }

    public function create(): View
    {
        $patrimonios = Patrimonio::disponivel()->orderBy('descricao')->get();
        $funcionarios = Funcionario::orderBy('nome')->get();

        return view('chamados.create', compact('patrimonios', 'funcionarios'));
    }

    public function store(StoreChamadoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $patrimonioIds = $data['patrimonio_ids'] ?? [];
        unset($data['patrimonio_ids']);

        // Vincula automaticamente ao funcionário do usuário logado, se existir
        if ($request->user()->isFuncionario()) {
            $funcionario = $request->user()->funcionario;
            if (! $funcionario) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            $data['funcionario_id'] = $funcionario->id;
        }

        $chamado = Chamado::create($data);

        if (! empty($patrimonioIds)) {
            $chamado->patrimonios()->sync($patrimonioIds);
        }

        return redirect()->route('chamados.index')
            ->with('sucesso', 'Chamado aberto com sucesso.');
    }

    public function show(Chamado $chamado): View
    {
        $chamado->load(['funcionario', 'patrimonios']);
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
