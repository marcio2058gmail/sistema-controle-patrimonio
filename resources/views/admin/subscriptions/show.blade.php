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
                    {{ $subscription->company?->nome ?? 'Assinatura' }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Detalhes da Assinatura</p>
            </div>
        </div>
    </x-slot>

    @php
        $statusColors = [
            'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'overdue'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'suspended' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'canceled'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        ];
        $statusClass = $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-500';
        $planLimit   = $subscription->plan?->limite_patrimonios ?? 0;
        $pct         = $planLimit > 0 ? min(100, round(($assetsCount / $planLimit) * 100)) : 0;
        $nearLimit   = $pct >= 80;
    @endphp

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <x-alert />

            {{-- ============================================================
                 CABEÇALHO DA EMPRESA
            ============================================================ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->company?->nome ?? '—' }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->company?->email ?? '' }}</p>
                            @if($subscription->company?->cnpj)
                            <p class="text-xs text-gray-400">CNPJ: {{ $subscription->company->cnpj }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusClass }}">
                            {{ $subscription->status_label }}
                        </span>
                        <a href="{{ route('admin.subscriptions.invoices.index', $subscription->company) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                            </svg>
                            Ver Faturas
                        </a>
                    </div>
                </div>

                {{-- Informações da assinatura --}}
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Plano Atual</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $subscription->plan?->nome ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor Mensal</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">R$ {{ number_format($subscription->preco_mensal, 2, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Início</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $subscription->inicio_em?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Próx. Cobrança</p>
                        <p class="mt-1 text-sm font-semibold {{ $subscription->proximo_vencimento?->isPast() ? 'text-red-500' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $subscription->proximo_vencimento?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Uso do plano --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Patrimônios utilizados: <strong>{{ $assetsCount }}</strong> / {{ $planLimit }}
                        </span>
                        <span class="text-sm font-semibold {{ $nearLimit ? 'text-orange-500' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $usagePercent }}%
                        </span>
                    </div>
                    <div class="h-3 rounded-full bg-gray-200 dark:bg-gray-600">
                        <div class="h-3 rounded-full transition-all {{ $pct >= 95 ? 'bg-red-500' : ($nearLimit ? 'bg-orange-500' : 'bg-indigo-500') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    @if($nearLimit)
                    <p class="mt-1.5 text-xs text-orange-600 dark:text-orange-400 flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        Empresa utilizando {{ $assetsCount }} de {{ $planLimit }} patrimônios. Considere upgrade de plano.
                    </p>
                    @endif
                </div>
            </div>

            {{-- ============================================================
                 AÇÕES: ALTERAR PLANO
            ============================================================ --}}
            <div x-data="{ openPlan: false, openStatus: false }" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Alterar Plano --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Alterar Plano</h4>
                        <button @click="openPlan = !openPlan"
                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            <span x-text="openPlan ? 'Fechar' : 'Alterar'"></span>
                        </button>
                    </div>

                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p>Plano: <strong class="text-gray-800 dark:text-gray-200">{{ $subscription->plan?->nome ?? '—' }}</strong></p>
                        <p>Limite: <strong class="text-gray-800 dark:text-gray-200">{{ $planLimit }} patrimônios</strong></p>
                        <p>Preço: <strong class="text-gray-800 dark:text-gray-200">R$ {{ number_format($subscription->preco_mensal, 2, ',', '.') }}/mês</strong></p>
                    </div>

                    <div x-show="openPlan" x-transition class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <form method="POST" action="{{ route('admin.subscriptions.changePlan', $subscription) }}">
                            @csrf @method('PATCH')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Novo Plano</label>
                                    <select name="plano_id" required
                                            class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ $subscription->plano_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->nome }} — R$ {{ number_format($plan->preco, 2, ',', '.') }} / {{ $plan->limite_patrimonios }} patrimônios
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Valor Mensal Personalizado (opcional)</label>
                                    <input type="number" name="preco_mensal" step="0.01" min="0"
                                           placeholder="Deixe em branco para usar o preço do plano"
                                           class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Motivo da Alteração</label>
                                    <textarea name="reason" rows="2" placeholder="Ex: Upgrade solicitado pelo cliente"
                                              class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    Confirmar Alteração de Plano
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Alterar Status --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Controle de Status</h4>
                        <button @click="openStatus = !openStatus"
                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            <span x-text="openStatus ? 'Fechar' : 'Alterar Status'"></span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        @php
                            $actions = [
                                ['status' => 'active',    'label' => 'Reativar',            'color' => 'green',  'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['status' => 'suspended', 'label' => 'Suspender',           'color' => 'yellow', 'icon' => 'M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['status' => 'canceled',  'label' => 'Cancelar Assinatura', 'color' => 'red',    'icon' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                            $btnColors = [
                                'green'  => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800',
                                'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                'red'    => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                            ];
                        @endphp
                        @foreach($actions as $action)
                        @if($subscription->status !== $action['status'])
                        <form method="POST" action="{{ route('admin.subscriptions.changeStatus', $subscription) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $action['status'] }}">
                            <button type="submit"
                                    onclick="return confirm('Confirmar: {{ $action['label'] }} de {{ addslashes($subscription->company?->nome) }}?')"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium border transition {{ $btnColors[$action['color']] }}">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                                </svg>
                                {{ $action['label'] }}
                            </button>
                        </form>
                        @endif
                        @endforeach
                    </div>

                    {{-- Formulário detalhado com motivo --}}
                    <div x-show="openStatus" x-transition class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <form method="POST" action="{{ route('admin.subscriptions.changeStatus', $subscription) }}">
                            @csrf @method('PATCH')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Novo Status</label>
                                    <select name="status" required
                                            class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        <option value="active">Ativa</option>
                                        <option value="trial">Trial</option>
                                        <option value="overdue">Inadimplente</option>
                                        <option value="suspended">Suspensa</option>
                                        <option value="canceled">Cancelada</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Motivo</label>
                                    <textarea name="reason" rows="2" placeholder="Motivo da alteração de status"
                                              class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                                </div>
                                <button type="submit"
                                        class="w-full py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition">
                                    Aplicar Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 FATURAS RECENTES
            ============================================================ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Faturas Recentes</h4>
                    <a href="{{ route('admin.subscriptions.invoices.index', $subscription->company) }}"
                       class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Ver todas →</a>
                </div>
                @if($recentInvoices->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-gray-400">Nenhuma fatura gerada.</div>
                @else
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pagamento</th>
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
                        @foreach($recentInvoices as $inv)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $inv->due_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">R$ {{ number_format($inv->amount, 2, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $invColors[$inv->status] ?? 'bg-gray-100 text-gray-500' }}">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $inv->payment_date?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- ============================================================
                 HISTÓRICO DE ALTERAÇÕES
            ============================================================ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Histórico de Alterações</h4>
                </div>
                @if($subscription->changes->isEmpty())
                <div class="px-5 py-8 text-center text-sm text-gray-400">Nenhuma alteração registrada.</div>
                @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($subscription->changes->sortByDesc('created_at') as $change)
                    <div class="px-5 py-3.5 flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-indigo-400 mt-1.5 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $change->typeLabel() }}</span>
                                <span class="text-xs text-gray-400">{{ $change->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Por: {{ $change->changedByUser?->name ?? '—' }}
                                @if($change->oldPlan || $change->newPlan)
                                · Plano: {{ $change->oldPlan?->nome ?? '—' }} → {{ $change->newPlan?->nome ?? '—' }}
                                @endif
                                @if($change->old_status && $change->old_status !== $change->new_status)
                                · Status: {{ \App\Models\Subscription::statusLabels()[$change->old_status] ?? $change->old_status }} → {{ \App\Models\Subscription::statusLabels()[$change->new_status] ?? $change->new_status }}
                                @endif
                            </p>
                            @if($change->reason)
                            <p class="text-xs text-gray-400 mt-0.5 italic">"{{ $change->reason }}"</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
