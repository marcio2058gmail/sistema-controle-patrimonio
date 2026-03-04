<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuncionarioRequest;
use App\Http\Requests\UpdateFuncionarioRequest;
use App\Models\Funcionario;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FuncionarioController extends Controller
{
    public function index(): View
    {
        $funcionarios = Funcionario::latest()->paginate(15);
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create(): View
    {
        return view('funcionarios.create');
    }

    public function store(StoreFuncionarioRequest $request): RedirectResponse
    {
        Funcionario::create($request->validated());

        return redirect()->route('funcionarios.index')
            ->with('sucesso', 'Funcionário cadastrado com sucesso.');
    }

    public function show(Funcionario $funcionario): View
    {
        $funcionario->load(['responsabilidades.patrimonio', 'chamados.patrimonio']);
        return view('funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario): View
    {
        return view('funcionarios.edit', compact('funcionario'));
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
