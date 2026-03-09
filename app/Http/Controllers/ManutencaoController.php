<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManutencaoRequest;
use App\Http\Requests\UpdateManutencaoRequest;
use App\Models\Asset;
use App\Models\Manutencao;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ManutencaoController extends Controller
{
    public function index(): View
    {
        $companyId = (int) session('empresa_ativa_id');

        $manutencoes = Manutencao::query()
            ->with('patrimonio')
            ->when($companyId, fn ($q) => $q->whereHas('patrimonio', fn ($q2) => $q2->where('empresa_id', $companyId)))
            ->orderByDesc('data_abertura')
            ->orderByDesc('created_at')
            ->get();

        $assets = Asset::query()
            ->forCompany($companyId)
            ->orderBy('codigo_patrimonio')
            ->get(['id', 'codigo_patrimonio', 'descricao']);

        return view('manutencoes.index', [
            'manutencoes' => $manutencoes,
            'assets'      => $assets,
            'tipos'       => Manutencao::TIPOS,
            'statusList'  => Manutencao::STATUS,
        ]);
    }

    public function store(StoreManutencaoRequest $request): RedirectResponse
    {
        Manutencao::create($request->validated());

        return back()->with('success', 'Manutenção registrada com sucesso.');
    }

    public function update(UpdateManutencaoRequest $request, Manutencao $manutencao): RedirectResponse
    {
        $manutencao->update($request->validated());

        return back()->with('success', 'Manutenção atualizada com sucesso.');
    }

    public function destroy(Manutencao $manutencao): RedirectResponse
    {
        $manutencao->delete();

        return back()->with('success', 'Manutenção excluída com sucesso.');
    }
}
