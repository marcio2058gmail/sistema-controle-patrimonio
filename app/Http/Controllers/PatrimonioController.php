<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatrimonioRequest;
use App\Http\Requests\UpdatePatrimonioRequest;
use App\Models\Patrimonio;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PatrimonioController extends Controller
{
    public function index(): View
    {
        $patrimonios = Patrimonio::latest()->paginate(15);

        return view('patrimonios.index', compact('patrimonios'));
    }

    public function create(): View
    {
        $statusLabels = Patrimonio::statusLabels();
        return view('patrimonios.create', compact('statusLabels'));
    }

    public function store(StorePatrimonioRequest $request): RedirectResponse
    {
        Patrimonio::create($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio cadastrado com sucesso.');
    }

    public function show(Patrimonio $patrimonio): View
    {
        $patrimonio->load(['responsabilidades.funcionario', 'chamados.funcionario']);
        return view('patrimonios.show', compact('patrimonio'));
    }

    public function edit(Patrimonio $patrimonio): View
    {
        $statusLabels = Patrimonio::statusLabels();
        return view('patrimonios.edit', compact('patrimonio', 'statusLabels'));
    }

    public function update(UpdatePatrimonioRequest $request, Patrimonio $patrimonio): RedirectResponse
    {
        $patrimonio->update($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio atualizado com sucesso.');
    }

    public function destroy(Patrimonio $patrimonio): RedirectResponse
    {
        $patrimonio->delete();

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio removido com sucesso.');
    }
}
