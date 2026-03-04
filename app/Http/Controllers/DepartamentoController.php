<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartamentoRequest;
use App\Http\Requests\UpdateDepartamentoRequest;
use App\Models\Departamento;
use App\Models\Responsabilidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartamentoController extends Controller
{
    public function index(): View
    {
        $departamentos = Departamento::withCount('funcionarios')
            ->orderBy('nome')
            ->paginate(15);

        return view('departamentos.index', compact('departamentos'));
    }

    public function create(): View
    {
        return view('departamentos.create');
    }

    public function store(StoreDepartamentoRequest $request): RedirectResponse
    {
        Departamento::create($request->validated());

        return redirect()->route('departamentos.index')
            ->with('sucesso', 'Departamento criado com sucesso.');
    }

    public function show(Departamento $departamento): View
    {
        $departamento->load([
            'funcionarios.responsabilidades' => fn ($q) => $q->whereNull('data_devolucao')->with('patrimonio'),
        ]);

        // Estatísticas do departamento
        $totalFuncionarios    = $departamento->funcionarios->count();
        $totalPatrimoniosEmUso = $departamento->funcionarios->sum(
            fn ($f) => $f->responsabilidades->count()
        );

        // Patrimônios por funcionário para a tabela de uso
        $funcionariosComPatrimonios = $departamento->funcionarios->filter(
            fn ($f) => $f->responsabilidades->isNotEmpty()
        );

        return view('departamentos.show', compact(
            'departamento',
            'totalFuncionarios',
            'totalPatrimoniosEmUso',
            'funcionariosComPatrimonios'
        ));
    }

    public function edit(Departamento $departamento): View
    {
        return view('departamentos.edit', compact('departamento'));
    }

    public function update(UpdateDepartamentoRequest $request, Departamento $departamento): RedirectResponse
    {
        $departamento->update($request->validated());

        return redirect()->route('departamentos.index')
            ->with('sucesso', 'Departamento atualizado com sucesso.');
    }

    public function destroy(Departamento $departamento): RedirectResponse
    {
        // Desvincula funcionários antes de excluir (nullOnDelete já cuida no DB, mas garantimos)
        $departamento->funcionarios()->update(['departamento_id' => null]);
        $departamento->delete();

        return redirect()->route('departamentos.index')
            ->with('sucesso', 'Departamento excluído. Funcionários desvinculados.');
    }
}
