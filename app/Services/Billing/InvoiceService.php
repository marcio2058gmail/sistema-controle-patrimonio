<?php

namespace App\Services\Billing;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * InvoiceService — cria e gerencia faturas por assinatura.
 */
class InvoiceService
{
    /**
     * Gera uma fatura para a assinatura informada.
     */
    public function generate(Subscription $subscription, ?string $description = null): Invoice
    {
        return Invoice::create([
            'company_id'      => $subscription->empresa_id,
            'subscription_id' => $subscription->id,
            'amount'          => $subscription->preco_mensal,
            'due_date'        => $subscription->proximo_vencimento ?? Carbon::today()->addDays(5),
            'status'          => Invoice::STATUS_PENDING,
            'description'     => $description ?? "Fatura referente ao plano {$subscription->plan?->nome}",
        ]);
    }

    /**
     * Marca uma fatura como paga e registra o pagamento.
     */
    public function markAsPaid(
        Invoice $invoice,
        string $method = 'manual',
        ?string $transactionId = null,
        ?string $notes = null
    ): Invoice {
        return DB::transaction(function () use ($invoice, $method, $transactionId, $notes) {
            $invoice->payments()->create([
                'amount'         => $invoice->amount,
                'method'         => $method,
                'transaction_id' => $transactionId,
                'paid_at'        => Carbon::now(),
                'notes'          => $notes,
            ]);

            $invoice->update([
                'status'       => Invoice::STATUS_PAID,
                'payment_date' => Carbon::today(),
            ]);

            return $invoice->fresh();
        });
    }

    /**
     * Cancela uma fatura pendente.
     */
    public function cancel(Invoice $invoice): Invoice
    {
        $invoice->update(['status' => Invoice::STATUS_CANCELED]);
        return $invoice->fresh();
    }

    /**
     * Marca como vencidas todas as faturas pendentes com due_date no passado.
     *
     * @return int Número de faturas atualizadas
     */
    public function markOverdue(): int
    {
        return Invoice::where('status', Invoice::STATUS_PENDING)
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => Invoice::STATUS_OVERDUE]);
    }

    /**
     * Gera faturas mensais para todas as assinaturas ativas que ainda
     * não têm fatura pending/paid para o próximo vencimento.
     *
     * @return Collection<Invoice>
     */
    public function generateMonthlyInvoices(): Collection
    {
        $generated = collect();

        Subscription::with(['plan', 'company'])
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL])
            ->where('preco_mensal', '>', 0)
            ->chunk(100, function ($subscriptions) use (&$generated) {
                foreach ($subscriptions as $sub) {
                    $alreadyExists = Invoice::where('subscription_id', $sub->id)
                        ->whereIn('status', [Invoice::STATUS_PENDING, Invoice::STATUS_PAID])
                        ->where('due_date', '>=', Carbon::today())
                        ->exists();

                    if (! $alreadyExists) {
                        $generated->push($this->generate($sub));
                    }
                }
            });

        return $generated;
    }
}
