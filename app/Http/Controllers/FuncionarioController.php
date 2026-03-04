<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuncionarioRequest;
use App\Http\Requests\UpdateFuncionarioRequest;
use App\Models\Departamento;
use App\Models\Funcionario;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FuncionarioController extends Controller
{
    public function index(): View
    {
        $funcionarios = Funcionario::with('departamento')->latest()->paginate(15);
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create(): View
    {
        $departamentos = Departamento::orderBy('nome')->get();
        return view('funcionarios.create', compact('departamentos'));
    }

    public function store(StoreFuncionarioRequest $request): RedirectResponse
    {
        Funcionario::create($request->validated());

        return redirect()->route('funcionarios.index')
            ->with('sucesso', 'Funcionário cadastrado com sucesso.');
    }

    public function show(Funcionario $funcionario): View
    {
        $funcionario->load(['departamento', 'responsabilidades.patrimonio', 'chamados.patrimonios']);
        return view('funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario): View
    {
        $departamentos = Departamento::orderBy('nome')->get();
        return view('funcionarios.edit', compact('funcionario', 'departamentos'));
    }

    public function update(UpdateFuncionarioRequest $request, Funcionario $funcionario): RedirectResponse
    {
        $funcionario->update($request->validated());

        return redirect()->route('funcionarios.index')
            ->with('sucesso', 'Funcionário atualizado com sucesso.');
    }

    public function destroy(Funcionario $funcionario): RedirectResponse
    {
        $funcionario->delete();

        return redirect()->route('funcionarios.index')
            ->with('sucesso', 'Funcionário removido com sucesso.');
    }
}
