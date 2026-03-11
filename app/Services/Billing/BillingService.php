<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;

/**
 * BillingService — processa cobranças, verifica inadimplência e suspende contas.
 *
 * Utilizado principalmente pelo comando Artisan billing:process.
 */
class BillingService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    /**
     * Processo completo de cobrança mensal:
     *  1. Gera faturas para assinaturas ativas sem fatura pendente
     *  2. Marca faturas vencidas como overdue
     *  3. Marca assinaturas com faturas vencidas há X dias como overdue
     *  4. Suspende empresas com faturas vencidas há mais tempo
     *
     * @param  int $suspendAfterDays Dias após vencimento para suspender (default 30)
     * @param  int $overdueAfterDays Dias após vencimento para marcar como overdue (default 5)
     * @return array{invoices_generated: int, invoices_overdue: int, subscriptions_overdue: int, subscriptions_suspended: int}
     */
    public function process(int $overdueAfterDays = 5, int $suspendAfterDays = 30): array
    {
        $result = [
            'invoices_generated'       => 0,
            'invoices_overdue'         => 0,
            'subscriptions_overdue'    => 0,
            'subscriptions_suspended'  => 0,
        ];

        // 1. Gerar faturas mensais
        $generated = $this->invoiceService->generateMonthlyInvoices();
        $result['invoices_generated'] = $generated->count();

        // 2. Marcar faturas pendentes vencidas como overdue
        $result['invoices_overdue'] = $this->invoiceService->markOverdue();

        // 3. Marcar assinaturas ativas com faturas overdue >= X dias como overdue
        $overdueDate = Carbon::today()->subDays($overdueAfterDays);
        $overdueSubscriptionIds = Invoice::where('status', Invoice::STATUS_OVERDUE)
            ->where('due_date', '<=', $overdueDate)
            ->pluck('subscription_id')
            ->unique();

        if ($overdueSubscriptionIds->isNotEmpty()) {
            $result['subscriptions_overdue'] = Subscription::whereIn('id', $overdueSubscriptionIds)
                ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
                ->update(['status' => Subscription::STATUS_OVERDUE]);
        }

        // 4. Suspender assinaturas com faturas vencidas há mais de X dias
        $suspendDate = Carbon::today()->subDays($suspendAfterDays);
        $suspendSubscriptionIds = Invoice::where('status', Invoice::STATUS_OVERDUE)
            ->where('due_date', '<=', $suspendDate)
            ->pluck('subscription_id')
            ->unique();

        if ($suspendSubscriptionIds->isNotEmpty()) {
            $result['subscriptions_suspended'] = Subscription::whereIn('id', $suspendSubscriptionIds)
                ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL, Subscription::STATUS_OVERDUE])
                ->update(['status' => Subscription::STATUS_SUSPENDED]);
        }

        return $result;
    }

    /**
     * Resumo financeiro para o dashboard.
     */
    public function financialSummary(): array
    {
        $activeStatuses = [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL];

        $mrr = Subscription::whereIn('status', $activeStatuses)->sum('preco_mensal');

        return [
            'mrr'                   => (float) $mrr,
            'arr'                   => (float) $mrr * 12,
            'active_companies'      => Subscription::where('status', Subscription::STATUS_ACTIVE)
                ->distinct('empresa_id')->count('empresa_id'),
            'trial_companies'       => Subscription::where('status', Subscription::STATUS_TRIAL)
                ->distinct('empresa_id')->count('empresa_id'),
            'overdue_companies'     => Subscription::whereIn('status', [Subscription::STATUS_OVERDUE, Subscription::STATUS_SUSPENDED])
                ->distinct('empresa_id')->count('empresa_id'),
            'total_assets'          => \App\Models\Asset::count(),
            'pending_invoices_total'=> Invoice::where('status', Invoice::STATUS_PENDING)->sum('amount'),
            'overdue_invoices_total'=> Invoice::where('status', Invoice::STATUS_OVERDUE)->sum('amount'),
        ];
    }
}
