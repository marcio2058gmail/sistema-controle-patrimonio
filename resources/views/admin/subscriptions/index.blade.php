<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Subscriptions Management
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestão de assinaturas e faturamento</p>
            </div>
            <a href="{{ route('admin.subscriptions.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nova Assinatura
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <x-alert />

            {{-- ============================================================
                 DASHBOARD FINANCEIRO
            ============================================================ --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">

                @php
                    $cards = [
                        ['label' => 'Empresas Ativas',      'value' => number_format($summary['active_companies']),    'color' => 'green',  'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Em Trial',             'value' => number_format($summary['trial_companies']),     'color' => 'blue',   'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Inadimplentes',        'value' => number_format($summary['overdue_companies']),   'color' => 'red',    'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z'],
                        ['label' => 'MRR',                  'value' => 'R$ ' . number_format($summary['mrr'], 2, ',', '.'), 'color' => 'indigo', 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 01-.75.75h-.75'],
                        ['label' => 'ARR Estimado',         'value' => 'R$ ' . number_format($summary['arr'], 2, ',', '.'),  'color' => 'purple', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                        ['label' => 'Total Patrimônios',    'value' => number_format($summary['total_assets']),        'color' => 'amber',  'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
                    ];
                    $colorMap = [
                        'green'  => 'bg-green-50  dark:bg-green-900/20  text-green-600  dark:text-green-400  border-green-100  dark:border-green-800',
                        'blue'   => 'bg-blue-50   dark:bg-blue-900/20   text-blue-600   dark:text-blue-400   border-blue-100   dark:border-blue-800',
                        'red'    => 'bg-red-50    dark:bg-red-900/20    text-red-600    dark:text-red-400    border-red-100    dark:border-red-800',
                        'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
                        'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-800',
                        'amber'  => 'bg-amber-50  dark:bg-amber-900/20  text-amber-600  dark:text-amber-400  border-amber-100  dark:border-amber-800',
                    ];
                @endphp

                @foreach($cards as $card)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $colorMap[$card['color']] }} p-4 flex flex-col gap-2 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $card['label'] }}</span>
                        <div class="w-8 h-8 rounded-lg {{ str_replace(['text-', 'dark:text-'], ['bg-', 'dark:bg-'], explode(' ', $card['color'])[0] ?? '') }} flex items-center justify-center opacity-80">
                            <svg class="h-4 w-4 {{ explode(' ', $colorMap[$card['color']])[2] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xl font-bold text-gray-800 dark:text-gray-100 leading-none">{{ $card['value'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ============================================================
                 FILTROS
            ============================================================ --}}
            <form method="GET" action="{{ route('admin.subscriptions.index') }}"
                  class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Empresa</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buscar empresa..."
                               class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Todos</option>
                            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Ativa</option>
                            <option value="trial"     {{ request('status') === 'trial'     ? 'selected' : '' }}>Trial</option>
                            <option value="overdue"   {{ request('status') === 'overdue'   ? 'selected' : '' }}>Inadimplente</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspensa</option>
                            <option value="canceled"  {{ request('status') === 'canceled'  ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Plano</label>
                        <select name="plan_id" class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Todos</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Vencimento até</label>
                        <input type="date" name="due_date" value="{{ request('due_date') }}"
                               class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'status', 'plan_id', 'due_date']))
                    <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                        Limpar
                    </a>
                    @endif
                </div>
            </form>

            {{-- ============================================================
                 TABELA DE ASSINATURAS
            ============================================================ --}}
            @if($subscriptions->isEmpty())
            <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm">Nenhuma assinatura encontrada.</p>
                <a href="{{ route('admin.subscriptions.create') }}" class="mt-3 inline-block text-indigo-500 hover:text-indigo-400 text-sm font-medium">Criar primeira assinatura →</a>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empresa</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plano</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patrimônios</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor Mensal</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Próx. Cobrança</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($subscriptions as $sub)
                            @php
                                $limit    = $sub->plan?->limite_patrimonios ?? 0;
                                $used     = $sub->assets_count ?? 0;
                                $pct      = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
                                $nearLimit = $pct >= 80;
                                $statusColors = [
                                    'active'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'trial'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'overdue'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'suspended' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'canceled'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'past_due'  => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                ];
                                $statusClass = $statusColors[$sub->status] ?? 'bg-gray-100 text-gray-500';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-sm text-gray-800 dark:text-gray-200">{{ $sub->company?->nome ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $sub->company?->email ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $sub->plan?->nome ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm {{ $nearLimit ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $used }} / {{ $limit }}
                                        </span>
                                        @if($nearLimit)
                                        <svg class="h-4 w-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                        </svg>
                                        @endif
                                    </div>
                                    @if($limit > 0)
                                    <div class="mt-1 h-1.5 w-24 rounded-full bg-gray-200 dark:bg-gray-600">
                                        <div class="h-1.5 rounded-full {{ $nearLimit ? 'bg-orange-500' : 'bg-indigo-500' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                    R$ {{ number_format($sub->preco_mensal, 2, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                    @if($sub->proximo_vencimento)
                                        @php $isLate = $sub->proximo_vencimento->isPast() && in_array($sub->status, ['active', 'trial']); @endphp
                                        <span class="{{ $isLate ? 'text-red-500 font-semibold' : '' }}">
                                            {{ $sub->proximo_vencimento->format('d/m/Y') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $sub->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.subscriptions.show', $sub) }}"
                                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                            Detalhes
                                        </a>
                                        @if($sub->company)
                                        <a href="{{ route('admin.subscriptions.invoices.index', $sub->company) }}"
                                           class="text-xs text-gray-500 dark:text-gray-400 hover:underline font-medium">
                                            Faturas
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($subscriptions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $subscriptions->links() }}
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
