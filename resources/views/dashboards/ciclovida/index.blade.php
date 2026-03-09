<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Dashboard de Ciclo de Vida
        </h2>
    </x-slot>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Idade Média (anos)</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ number_format($kpis['idade_media'], 1) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Com Garantia</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($kpis['com_garantia']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Garantias (30d)</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ number_format($kpis['garantias_vencendo']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Garantias Vencidas</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ number_format($kpis['garantias_vencidas']) }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Distribuição por Faixa de Idade</h3>
            <canvas id="chartIdade" height="260"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Aquisições por Mês (24 meses)</h3>
            <canvas id="chartAquisicoes" height="260"></canvas>
        </div>
    </div>

    {{-- Tabela garantias próximas --}}
    @if(count($garantiasProximas))
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
            Garantias vencendo em 90 dias
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                {{ count($garantiasProximas) }}
            </span>
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-3 text-left font-medium text-gray-600 dark:text-gray-400">Código</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-600 dark:text-gray-400">Descrição</th>
                        <th class="py-2 px-3 text-center font-medium text-gray-600 dark:text-gray-400">Vencimento</th>
                        <th class="py-2 px-3 text-center font-medium text-gray-600 dark:text-gray-400">Dias Restantes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($garantiasProximas as $g)
                    @php
                        $days = now()->diffInDays(\Carbon\Carbon::parse($g->data_garantia), false);
                        $badgeClass = $days <= 30 ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
                                    : ($days <= 60 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200'
                                    : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200');
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="py-2 px-3 text-gray-800 dark:text-gray-200 font-mono text-xs">{{ $g->codigo_patrimonio }}</td>
                        <td class="py-2 px-3 text-gray-700 dark:text-gray-300">{{ $g->descricao }}</td>
                        <td class="py-2 px-3 text-center text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($g->data_garantia)->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $days }} dias
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        new Chart(document.getElementById('chartIdade'), {
            type: 'bar',
            data: {
                labels: @json($porIdade['labels']),
                datasets: [{
                    label: 'Patrimônios',
                    data: @json($porIdade['data']),
                    backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#f97316','#ef4444'],
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                    y: { ticks: { color: labelColor }, grid: { color: gridColor }, beginAtZero: true }
                }
            }
        });

        new Chart(document.getElementById('chartAquisicoes'), {
            type: 'bar',
            data: {
                labels: @json($aquisicoes['labels']),
                datasets: [
                    { label: 'Qtd.', data: @json($aquisicoes['qtd']), backgroundColor: '#6366f1', borderRadius: 4, yAxisID: 'y' },
                    { label: 'Valor (R$)', data: @json($aquisicoes['valor']), type: 'line', borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', fill: false, tension: 0.3, yAxisID: 'y2', pointRadius: 2 }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: labelColor } } },
                scales: {
                    x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                    y: { ticks: { color: labelColor }, grid: { color: gridColor }, beginAtZero: true, position: 'left' },
                    y2: { ticks: { color: labelColor }, grid: { display: false }, beginAtZero: true, position: 'right' }
                }
            }
        });
    });
    </script>
    @endpush
</x-app-layout>
