<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\Billing\BillingService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SubscriptionController (Admin) — Gestão completa de assinaturas pelo SuperAdmin.
 */
class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly BillingService      $billingService,
    ) {}

    /**
     * Dashboard de assinaturas com indicadores financeiros e listagem.
     */
    public function index(Request $request): View
    {
        $query = Subscription::with(['company', 'plan'])
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')->from('assinaturas')->groupBy('empresa_id');
            });

        // Filtros
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($planId = $request->input('plan_id')) {
            $query->where('plano_id', $planId);
        }
        if ($search = $request->input('search')) {
            $query->whereHas('company', fn ($q) => $q->where('nome', 'like', "%{$search}%"));
        }
        if ($dueDate = $request->input('due_date')) {
            $query->whereDate('proximo_vencimento', '<=', $dueDate);
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();
        $plans         = Plan::ativos()->orderBy('preco')->get();
        $summary       = $this->billingService->financialSummary();

        // Enriquece com contagem de patrimônios
        $subscriptions->getCollection()->each(function ($sub) {
            if ($sub->company) {
                $sub->assets_count = $sub->company->assets()->count();
            }
        });

        return view('admin.subscriptions.index', compact('subscriptions', 'plans', 'summary'));
    }

    /**
     * Detalhes de uma assinatura / empresa.
     */
    public function show(Subscription $subscription): View
    {
        $subscription->load(['company', 'plan', 'changes.oldPlan', 'changes.newPlan', 'changes.changedByUser', 'invoices.payments']);

        $assetsCount   = $subscription->company->assets()->count();
        $usagePercent  = $this->subscriptionService->usagePercent($subscription->company);
        $plans         = Plan::ativos()->orderBy('preco')->get();
        $recentInvoices = $subscription->invoices()->latest('due_date')->take(5)->get();

        return view('admin.subscriptions.show', compact('subscription', 'assetsCount', 'usagePercent', 'plans', 'recentInvoices'));
    }

    /**
     * Formulário de nova assinatura.
     */
    public function create(): View
    {
        $companies = Company::orderBy('nome')->get();
        $plans     = Plan::ativos()->orderBy('preco')->get();

        return view('admin.subscriptions.create', compact('companies', 'plans'));
    }

    /**
     * Cria nova assinatura para uma empresa.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'empresa_id' => ['required', 'exists:empresas,id'],
            'plano_id'   => ['required', 'exists:planos,id'],
        ]);

        $company = Company::findOrFail($data['empresa_id']);
        $plan    = Plan::findOrFail($data['plano_id']);

        $this->subscriptionService->subscribe($company, $plan, $request->user());

        return redirect()->route('admin.subscriptions.index')
            ->with('sucesso', "Empresa {$company->nome} assinada no plano {$plan->nome}.");
    }

    /**
     * Altera o plano de uma assinatura existente.
     */
    public function changePlan(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'plano_id'     => ['required', 'exists:planos,id'],
            'preco_mensal' => ['nullable', 'numeric', 'min:0'],
            'reason'       => ['nullable', 'string', 'max:500'],
        ]);

        $newPlan = Plan::findOrFail($data['plano_id']);

        $this->subscriptionService->changePlan(
            $subscription,
            $newPlan,
            $request->user(),
            isset($data['preco_mensal']) ? (float) $data['preco_mensal'] : null,
            $data['reason'] ?? null
        );

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('sucesso', 'Plano alterado com sucesso.');
    }

    /**
     * Altera o status de uma assinatura.
     */
    public function changeStatus(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,trial,overdue,suspended,canceled'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptionService->changeStatus(
            $subscription,
            $data['status'],
            $request->user(),
            $data['reason'] ?? null
        );

        $label = Subscription::statusLabels()[$data['status']] ?? $data['status'];

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('sucesso', "Status alterado para: {$label}.");
    }

    /**
     * Cancela a assinatura ativa de uma empresa.
     */
    public function cancel(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptionService->cancel($company, $request->user(), $data['reason'] ?? null);

        return redirect()->route('admin.subscriptions.index')
            ->with('sucesso', "Assinatura de {$company->nome} cancelada.");
    }
}
