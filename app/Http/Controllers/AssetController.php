<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Company;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}
    public function index(Request $request): View
    {
        $query = Asset::forCompany()->latest();

        // Gestor e Funcionário veem apenas os disponíveis
        if ($request->user()->isManager() || $request->user()->isEmployee()) {
            $query->where('status', Asset::STATUS_AVAILABLE);
        }

        $assets   = $query->paginate(15);
        $apenasDisponiveis = ! $request->user()->isAdmin();
        $statusLabels = Asset::statusLabels();

        return view('assets.index', compact('assets', 'apenasDisponiveis', 'statusLabels'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Asset::statusLabels();
        $companies = $request->user()->isSuperAdmin() ? Company::orderBy('nome')->get() : collect();
        return view('assets.create', compact('statusLabels', 'companies'));
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $empresaId = $request->user()->isSuperAdmin() && $request->filled('empresa_id')
            ? $request->integer('empresa_id')
            : (int) session('empresa_ativa_id');

        // Verifica limite do plano antes de cadastrar
        $company = Company::find($empresaId);
        if ($company && $this->subscriptionService->hasReachedAssetLimit($company)) {
            $sub   = $this->subscriptionService->activeSubscription($company);
            $limit = $sub?->plan->limite_patrimonios ?? 0;
            return redirect()->back()
                ->withInput()
                ->with('erro', "Limite de {$limit} patrimônios do plano \"{$sub?->plan->nome}\" atingido. Faça upgrade para continuar.");
        }

        Asset::create(array_merge($request->validated(), [
            'empresa_id' => $empresaId,
        ]));

        return redirect()->route('assets.index')
            ->with('sucesso', 'Patrimônio cadastrado com sucesso.');
    }

    public function show(Asset $asset): View
    {
        $asset->load(['responsibilities.employee', 'tickets.employee']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Request $request, Asset $asset): View
    {
        abort_unless($request->user()->isAdmin(), 403);
        $statusLabels = Asset::statusLabels();
        $companies = $request->user()->isSuperAdmin() ? Company::orderBy('nome')->get() : collect();
        return view('assets.edit', compact('asset', 'statusLabels', 'companies'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validated();

        if ($request->user()->isSuperAdmin() && $request->filled('empresa_id')) {
            $data['empresa_id'] = $request->integer('empresa_id');
        }

        $asset->update($data);

        return redirect()->route('assets.index')
            ->with('sucesso', 'Patrimônio atualizado com sucesso.');
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('sucesso', 'Patrimônio removido com sucesso.');
    }
}
