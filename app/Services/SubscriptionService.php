<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionChange;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * SubscriptionService — gerencia assinaturas de planos SaaS para empresas.
 */
class SubscriptionService
{
    /**
     * Cria ou substitui a assinatura ativa de uma empresa.
     */
    public function subscribe(Company $company, Plan $plan, ?User $changedBy = null): Subscription
    {
        return DB::transaction(function () use ($company, $plan, $changedBy) {
            Subscription::where('empresa_id', $company->id)
                ->whereIn('status', ['active', 'trial'])
                ->update(['status' => Subscription::STATUS_CANCELED]);

            return Subscription::create([
                'empresa_id'         => $company->id,
                'plano_id'           => $plan->id,
                'preco_mensal'       => $plan->preco,
                'inicio_em'          => Carbon::today(),
                'proximo_vencimento' => Carbon::today()->addMonth(),
                'status'             => Subscription::STATUS_ACTIVE,
                'changed_by'         => $changedBy?->id,
            ]);
        });
    }

    /**
     * Altera o plano de uma assinatura ativa manualmente, registrando histórico.
     */
    public function changePlan(
        Subscription $subscription,
        Plan $newPlan,
        User $changedBy,
        ?float $customPrice = null,
        ?string $reason = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $newPlan, $changedBy, $customPrice, $reason) {
            $oldPlanId = $subscription->plano_id;
            $oldPrice  = $subscription->preco_mensal;

            SubscriptionChange::create([
                'subscription_id' => $subscription->id,
                'old_plan_id'     => $oldPlanId,
                'new_plan_id'     => $newPlan->id,
                'old_status'      => $subscription->status,
                'new_status'      => $subscription->status,
                'old_price'       => $oldPrice,
                'new_price'       => $customPrice ?? $newPlan->preco,
                'changed_by'      => $changedBy->id,
                'reason'          => $reason,
                'type'            => SubscriptionChange::TYPE_PLAN_CHANGE,
            ]);

            $subscription->update([
                'plano_id'    => $newPlan->id,
                'preco_mensal' => $customPrice ?? $newPlan->preco,
                'changed_by'  => $changedBy->id,
            ]);

            return $subscription->fresh(['plan', 'company']);
        });
    }

    /**
     * Altera o status de uma assinatura manualmente, registrando histórico.
     */
    public function changeStatus(
        Subscription $subscription,
        string $newStatus,
        User $changedBy,
        ?string $reason = null
    ): Subscription {
        return DB::transaction(function () use ($subscription, $newStatus, $changedBy, $reason) {
            SubscriptionChange::create([
                'subscription_id' => $subscription->id,
                'old_plan_id'     => $subscription->plano_id,
                'new_plan_id'     => $subscription->plano_id,
                'old_status'      => $subscription->status,
                'new_status'      => $newStatus,
                'old_price'       => $subscription->preco_mensal,
                'new_price'       => $subscription->preco_mensal,
                'changed_by'      => $changedBy->id,
                'reason'          => $reason,
                'type'            => SubscriptionChange::TYPE_STATUS_CHANGE,
            ]);

            $subscription->update([
                'status'     => $newStatus,
                'changed_by' => $changedBy->id,
            ]);

            return $subscription->fresh();
        });
    }

    /**
     * Suspende a assinatura da empresa.
     */
    public function suspend(Company $company, User $changedBy, ?string $reason = null): void
    {
        $subscription = $this->activeSubscription($company);
        if ($subscription) {
            $this->changeStatus($subscription, Subscription::STATUS_SUSPENDED, $changedBy, $reason);
        }
    }

    /**
     * Reativa a assinatura suspensa/inadimplente da empresa.
     */
    public function reactivate(Company $company, User $changedBy, ?string $reason = null): void
    {
        $subscription = Subscription::where('empresa_id', $company->id)
            ->whereIn('status', [Subscription::STATUS_SUSPENDED, Subscription::STATUS_OVERDUE, Subscription::STATUS_PAST_DUE])
            ->latest('inicio_em')
            ->first();

        if ($subscription) {
            $this->changeStatus($subscription, Subscription::STATUS_ACTIVE, $changedBy, $reason);
        }
    }

    /**
     * Cancela a assinatura ativa da empresa.
     */
    public function cancel(Company $company, ?User $changedBy = null, ?string $reason = null): void
    {
        $subscription = $this->activeSubscription($company);
        if (! $subscription) {
            Subscription::where('empresa_id', $company->id)
                ->whereIn('status', ['active', 'trial'])
                ->update(['status' => Subscription::STATUS_CANCELED]);
            return;
        }
        $this->changeStatus($subscription, Subscription::STATUS_CANCELED, $changedBy ?? new User(), $reason);
    }

    /**
     * Verifica se a empresa atingiu o limite de patrimônios do plano.
     */
    public function hasReachedAssetLimit(Company $company): bool
    {
        $subscription = $company->activeSubscription;
        if (! $subscription) return false;
        return $company->assets()->count() >= $subscription->plan->limite_patrimonios;
    }

    /**
     * Retorna o percentual de uso do plano.
     */
    public function usagePercent(Company $company): float
    {
        $subscription = $company->activeSubscription;
        if (! $subscription || ! $subscription->plan->limite_patrimonios) return 0.0;
        $used = $company->assets()->count();
        return round(($used / $subscription->plan->limite_patrimonios) * 100, 1);
    }

    /**
     * Obtém a assinatura ativa de uma empresa.
     */
    public function activeSubscription(Company $company): ?Subscription
    {
        return Subscription::where('empresa_id', $company->id)
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->with('plan')
            ->latest('inicio_em')
            ->first();
    }
}
