<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Dashboard da Empresa
        </h2>
    </x-slot>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-xl font-bold text-indigo-600 mt-0.5">{{ number_format($kpis['total']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Disponível</p>
            <p class="text-xl font-bold text-green-600 mt-0.5">{{ number_format($kpis['disponivel']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Em Uso</p>
            <p class="text-xl font-bold text-blue-600 mt-0.5">{{ number_format($kpis['em_uso']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Manutenção</p>
            <p class="text-xl font-bold text-yellow-600 mt-0.5">{{ number_format($kpis['manutencao']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Descartado</p>
            <p class="text-xl font-bold text-red-500 mt-0.5">{{ number_format($kpis['descartado']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 xl:col-span-1 col-span-2">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor Total</p>
            <p class="text-base font-bold text-gray-800 dark:text-gray-100 mt-0.5">R$ {{ number_format($kpis['valor_total'], 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Patrimônios por Status</h3>
            <canvas id="chartStatus" height="160"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Patrimônios por Departamento</h3>
            <canvas id="chartDepto" height="160"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Crescimento Mensal (12 meses)</h3>
        <canvas id="chartCrescimento" height="110"></canvas>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';
        const palette = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#f97316','#06b6d4'];

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: @json($porStatus['labels']),
                datasets: [{ data: @json($porStatus['data']), backgroundColor: palette, hoverOffset: 8 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right', labels: { color: labelColor, padding: 12 } } }
            }
        });

        new Chart(document.getElementById('chartDepto'), {
            type: 'bar',
            data: {
                labels: @json($porDepto['labels']),
                datasets: [{ label: 'Patrimônios', data: @json($porDepto['data']), backgroundColor: '#3b82f6', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                    y: { ticks: { color: labelColor }, grid: { color: gridColor } }
                }
            }
        });

        new Chart(document.getElementById('chartCrescimento'), {
            type: 'line',
            data: {
                labels: @json($crescimento['labels']),
                datasets: [{ label: 'Novos', data: @json($crescimento['data']), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.12)', fill: true, tension: 0.3, pointRadius: 3 }]
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
    });
    </script>
    @endpush
</x-app-layout>
