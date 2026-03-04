<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::latest();

        // Gestor vê apenas os disponíveis
        if ($request->user()->isManager()) {
            $query->where('status', Asset::STATUS_DISPONIVEL);
        }

        $assets   = $query->paginate(15);
        $apenasDisponiveis = $request->user()->isManager();

        return view('assets.index', compact('assets', 'apenasDisponiveis'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Asset::statusLabels();
        return view('assets.create', compact('statusLabels'));
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        Asset::create($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio cadastrado com sucesso.');
    }

    public function show(Asset $asset): View
    {
        $asset->load(['responsabilidades.funcionario', 'chamados.funcionario']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Request $request, Asset $asset): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Asset::statusLabels();
        return view('assets.edit', compact('asset', 'statusLabels'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $asset->update($request->validated());

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio atualizado com sucesso.');
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $asset->delete();

        return redirect()->route('patrimonios.index')
            ->with('sucesso', 'Patrimônio removido com sucesso.');
    }
}
