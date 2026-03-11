<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\Billing\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * InvoiceController (Admin) — Gestão de faturas pelo SuperAdmin.
 */
class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    /**
     * Lista faturas de uma empresa.
     */
    public function index(Request $request, Company $company): View
    {
        $query = Invoice::where('company_id', $company->id)
            ->with(['subscription.plan', 'payments']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $invoices     = $query->latest('due_date')->paginate(20)->withQueryString();
        $subscription = $company->activeSubscription;

        return view('admin.subscriptions.invoices.index', compact('company', 'invoices', 'subscription'));
    }

    /**
     * Gera nova fatura manualmente para uma assinatura.
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'subscription_id' => ['required', 'exists:assinaturas,id'],
            'description'     => ['nullable', 'string', 'max:255'],
        ]);

        $subscription = Subscription::where('id', $data['subscription_id'])
            ->where('empresa_id', $company->id)
            ->firstOrFail();

        $subscription->load('plan');

        $this->invoiceService->generate($subscription, $data['description'] ?? null);

        return redirect()->route('admin.subscriptions.invoices.index', $company)
            ->with('sucesso', 'Fatura gerada com sucesso.');
    }

    /**
     * Marca uma fatura como paga.
     */
    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'method'         => ['required', 'in:pix,boleto,card,manual'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $this->invoiceService->markAsPaid($invoice, $data['method'], $data['transaction_id'] ?? null, $data['notes'] ?? null);

        $company = Company::findOrFail($invoice->company_id);

        return redirect()->route('admin.subscriptions.invoices.index', $company)
            ->with('sucesso', 'Pagamento registrado com sucesso.');
    }

    /**
     * Cancela uma fatura pendente.
     */
    public function cancel(Invoice $invoice): RedirectResponse
    {
        $company = Company::findOrFail($invoice->company_id);
        $this->invoiceService->cancel($invoice);

        return redirect()->route('admin.subscriptions.invoices.index', $company)
            ->with('sucesso', 'Fatura cancelada.');
    }
}
