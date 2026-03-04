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
        $query = Chamado::with(['funcionario', 'patrimonio'])->latest();

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

        // Vincula automaticamente ao funcionário do usuário logado, se existir
        if ($request->user()->isFuncionario()) {
            $funcionario = $request->user()->funcionario;
            if (! $funcionario) {
                return back()->withErrors(['funcionario_id' => 'Seu usuário não está vinculado a um funcionário.']);
            }
            $data['funcionario_id'] = $funcionario->id;
        }

        Chamado::create($data);

        return redirect()->route('chamados.index')
            ->with('sucesso', 'Chamado aberto com sucesso.');
    }

    public function show(Chamado $chamado): View
    {
        $chamado->load(['funcionario', 'patrimonio']);
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

        if (! $chamado->patrimonio_id) {
            return back()->withErrors(['patrimonio_id' => 'O chamado precisa ter um patrimônio vinculado para entrega.']);
        }

        $patrimonio = $chamado->patrimonio;

        if ($patrimonio->status !== Patrimonio::STATUS_DISPONIVEL) {
            return back()->withErrors(['status' => 'O patrimônio não está disponível para entrega.']);
        }

        // Cria responsabilidade automaticamente
        Responsabilidade::create([
            'funcionario_id'         => $chamado->funcionario_id,
            'patrimonio_id'          => $chamado->patrimonio_id,
            'data_entrega'           => now()->toDateString(),
            'termo_responsabilidade' => "Termo gerado automaticamente na entrega do chamado #{$chamado->id}. " .
                "O funcionário {$chamado->funcionario->nome} recebe o patrimônio " .
                "{$patrimonio->codigo_patrimonio} — {$patrimonio->descricao}.",
            'assinado'               => false,
        ]);

        // Atualiza status do patrimônio e do chamado
        $patrimonio->update(['status' => Patrimonio::STATUS_EM_USO]);
        $chamado->update(['status' => Chamado::STATUS_ENTREGUE]);

        return redirect()->route('chamados.show', $chamado)
            ->with('sucesso', 'Patrimônio entregue e termo de responsabilidade criado.');
    }
}
