<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.subscriptions.index') }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Faturas — {{ $company->nome }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Histórico de faturamento</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ openNew: false }" class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <x-alert />

            {{-- Botão gerar fatura --}}
            <div class="flex justify-end">
                <button @click="openNew = !openNew"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Gerar Nova Fatura
                </button>
            </div>

            {{-- Formulário nova fatura --}}
            <div x-show="openNew" x-transition
                 class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Nova Fatura</h4>
                <form method="POST" action="{{ route('admin.subscriptions.invoices.store', $company) }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assinatura</label>
                            <select name="subscription_id" required
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                @if($subscription)
                                <option value="{{ $subscription->id }}">{{ $subscription->plan?->nome }} — R$ {{ number_format($subscription->preco_mensal, 2, ',', '.') }}</option>
                                @else
                                <option value="">Nenhuma assinatura ativa</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Descrição (opcional)</label>
                            <input type="text" name="description" maxlength="255"
                                   placeholder="Ex: Fatura referente ao mês de Março"
                                   class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            Gerar Fatura
                        </button>
                        <button type="button" @click="openNew = false" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Filtro de status --}}
            <form method="GET" class="flex items-center gap-3 flex-wrap">
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Filtrar por status:</label>
                @foreach(['', 'pending', 'paid', 'overdue', 'canceled'] as $s)
                @php
                    $labels = ['' => 'Todos', 'pending' => 'Pendente', 'paid' => 'Paga', 'overdue' => 'Vencida', 'canceled' => 'Cancelada'];
                    $active  = request('status', '') === $s;
                @endphp
                <a href="?status={{ $s }}"
                   class="px-3 py-1 rounded-full text-xs font-medium transition {{ $active ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    {{ $labels[$s] }}
                </a>
                @endforeach
            </form>

            {{-- Tabela de faturas --}}
            @if($invoices->isEmpty())
            <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                </svg>
                <p class="text-sm">Nenhuma fatura encontrada.</p>
            </div>
            @else
            <div x-data="{ payModal: null }"
                 @keydown.escape.window="payModal = null"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrição</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pagamento</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $invColors = [
                                'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'paid'     => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'overdue'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'canceled' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                            ];
                        @endphp
                        @foreach($invoices as $invoice)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5 text-xs text-gray-400">#{{ $invoice->id }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                {{ $invoice->description ?? $invoice->subscription?->plan?->nome ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                                R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-sm {{ $invoice->status === 'overdue' ? 'text-red-500 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $invColors[$invoice->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $invoice->payment_date?->format('d/m/Y') ?? '—' }}
                                @if($invoice->payments->isNotEmpty())
                                <span class="text-xs text-gray-400">({{ $invoice->payments->last()?->method_label }})</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(in_array($invoice->status, ['pending', 'overdue']))
                                    <button @click="payModal = {{ $invoice->id }}"
                                            class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
                                        Marcar Pago
                                    </button>
                                    <form method="POST" action="{{ route('admin.subscriptions.invoices.cancel', $invoice) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                onclick="return confirm('Cancelar esta fatura?')"
                                                class="text-xs text-red-500 hover:underline font-medium">
                                            Cancelar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $invoices->links() }}
                </div>
                @endif

                {{-- Modal Marcar Pago --}}
                @foreach($invoices as $invoice)
                @if(in_array($invoice->status, ['pending', 'overdue']))
                <div x-show="payModal === {{ $invoice->id }}" x-transition
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                    <div @click.outside="payModal = null"
                         class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 w-full max-w-md">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">
                            Registrar Pagamento — Fatura #{{ $invoice->id }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Valor: <strong class="text-gray-800 dark:text-gray-200">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                        </p>
                        <form method="POST" action="{{ route('admin.subscriptions.invoices.markPaid', $invoice) }}">
                            @csrf @method('PATCH')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Método de Pagamento</label>
                                    <select name="method" required
                                            class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        <option value="manual">Manual</option>
                                        <option value="pix">PIX</option>
                                        <option value="boleto">Boleto</option>
                                        <option value="card">Cartão</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">ID da Transação (opcional)</label>
                                    <input type="text" name="transaction_id" maxlength="100"
                                           class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Observações (opcional)</label>
                                    <textarea name="notes" rows="2"
                                              class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button type="submit"
                                            class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                        Confirmar Pagamento
                                    </button>
                                    <button type="button" @click="payModal = null"
                                            class="flex-1 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                @endforeach

            </div>
            @endif

        </div>
    </div>
</x-app-layout>
