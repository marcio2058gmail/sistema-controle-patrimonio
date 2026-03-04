<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatrimonioRequest;
use App\Http\Requests\UpdatePatrimonioRequest;
use App\Models\Patrimonio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatrimonioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Patrimonio::latest();

        // Gestor vê apenas os disponíveis
        if ($request->user()->isGestor()) {
            $query->where('status', Patrimonio::STATUS_DISPONIVEL);
        }

        $patrimonios   = $query->paginate(15);
        $apenasDisponiveis = $request->user()->isGestor();

        return view('patrimonios.index', compact('patrimonios', 'apenasDisponiveis'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Patrimonio::statusLabels();
        return view('patrimonios.create', compact('statusLabels'));
    }

    public function store(StorePatrimonioRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        Patrimonio::create($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio cadastrado com sucesso.');
    }

    public function show(Patrimonio $patrimonio): View
    {
        $patrimonio->load(['responsabilidades.funcionario', 'chamados.funcionario']);
        return view('patrimonios.show', compact('patrimonio'));
    }

    public function edit(Request $request, Patrimonio $patrimonio): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Patrimonio::statusLabels();
        return view('patrimonios.edit', compact('patrimonio', 'statusLabels'));
    }

    public function update(UpdatePatrimonioRequest $request, Patrimonio $patrimonio): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $patrimonio->update($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio atualizado com sucesso.');
    }

    public function destroy(Request $request, Patrimonio $patrimonio): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $patrimonio->delete();

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio removido com sucesso.');
    }
}
