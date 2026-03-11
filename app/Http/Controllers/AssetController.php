<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
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
        $query = Asset::forCompany()->with('currentResponsibility')->latest();

        // Gestor e Funcionário veem apenas os disponíveis
        if ($request->user()->isManager() || $request->user()->isEmployee()) {
            $query->where('status', Asset::STATUS_AVAILABLE);
        }

        // Filtro: busca por código ou descrição
        if ($request->filled('busca')) {
            $term = trim($request->input('busca'));
            $query->where(function ($q) use ($term) {
                $q->where('codigo_patrimonio', 'like', "%{$term}%")
                  ->orWhere('descricao', 'like', "%{$term}%")
                  ->orWhere('modelo', 'like', "%{$term}%")
                  ->orWhere('numero_serie', 'like', "%{$term}%");
            });
        }

        // Filtro: status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro: colaborador (busca pelo nome no termo ativo)
        if ($request->filled('colaborador')) {
            $term = trim($request->input('colaborador'));
            $query->whereHas('currentResponsibility.employee', function ($q) use ($term) {
                $q->where('nome', 'like', "%{$term}%");
            });
        }

        // Filtro: departamento
        if ($request->filled('departamento_id')) {
            $deptId = $request->integer('departamento_id');
            $query->whereHas('currentResponsibility.employee', function ($q) use ($deptId) {
                $q->where('departamento_id', $deptId);
            });
        }

        // Filtro: garantia vencida
        if ($request->filled('garantia')) {
            if ($request->input('garantia') === 'vencida') {
                $query->whereNotNull('garantia_ate')->where('garantia_ate', '<', now());
            } elseif ($request->input('garantia') === 'vigente') {
                $query->whereNotNull('garantia_ate')->where('garantia_ate', '>=', now());
            }
        }

        $assets            = $query->paginate(15)->withQueryString();
        $apenasDisponiveis = ! $request->user()->isAdmin();
        $statusLabels      = Asset::statusLabels();
        $departments       = Department::forCompany()->orderBy('nome')->get();
        $filters           = $request->only(['busca', 'status', 'colaborador', 'departamento_id', 'garantia']);

        return view('assets.index', compact('assets', 'apenasDisponiveis', 'statusLabels', 'departments', 'filters'));
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
