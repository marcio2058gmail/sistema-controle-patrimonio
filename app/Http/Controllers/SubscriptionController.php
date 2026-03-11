<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SubscriptionController — gerencia assinaturas de empresas.
 * Acesso restrito a SuperAdmin.
 */
class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
    ) {}

    /**
     * Lista todas as assinaturas (visão SuperAdmin).
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        // Mostra apenas a assinatura mais recente de cada empresa
        $subscriptions = Subscription::with(['company', 'plan'])
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                      ->from('assinaturas')
                      ->groupBy('empresa_id');
            })
            ->latest()
            ->paginate(20);

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Formulário para assinar um plano em nome de uma empresa.
     */
    public function create(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $companies = Company::orderBy('nome')->get();
        $plans     = Plan::ativos()->orderBy('preco')->get();

        return view('subscriptions.create', compact('companies', 'plans'));
    }

    /**
     * Cria ou substitui a assinatura de uma empresa.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $data = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'plano_id'   => ['required', 'exists:planos,id'],
        ]);

        $company = Company::findOrFail($data['empresa_id']);
        $plan    = Plan::findOrFail($data['plano_id']);

        $this->subscriptionService->subscribe($company, $plan);

        return redirect()->route('subscriptions.index')
            ->with('sucesso', "Empresa {$company->nome} assinada no plano {$plan->nome}.");
    }

    /**
     * Cancela a assinatura ativa de uma empresa.
     */
    public function cancel(Request $request, Company $company): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $this->subscriptionService->cancel($company);

        return redirect()->route('subscriptions.index')
            ->with('sucesso', "Assinatura de {$company->nome} cancelada.");
    }
}
