<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManutencaoRequest;
use App\Http\Requests\UpdateManutencaoRequest;
use App\Models\Asset;
use App\Models\Manutencao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManutencaoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Manutencao::with('patrimonio')
            ->orderByDesc('data_abertura')
            ->orderByDesc('created_at');

        if ($request->filled('busca')) {
            $term = '%' . $request->string('busca') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('descricao', 'like', $term)
                  ->orWhere('tecnico_fornecedor', 'like', $term)
                  ->orWhere('observacoes', 'like', $term)
                  ->orWhereHas('patrimonio', fn ($p) => $p->where('codigo_patrimonio', 'like', $term)
                      ->orWhere('descricao', 'like', $term));
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_abertura', '>=', $request->date('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_abertura', '<=', $request->date('data_fim'));
        }

        $manutencoes = $query->paginate(15)->withQueryString();

        $assets = Asset::forCompany()->orderBy('codigo_patrimonio')
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
