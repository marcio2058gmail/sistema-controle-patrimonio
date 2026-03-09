<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Dashboard de Distribuição
        </h2>
    </x-slot>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ativos Totais</p>
            <p class="text-xl font-bold text-indigo-600 mt-0.5">{{ number_format($kpis['totalAtivos']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Em Uso</p>
            <p class="text-xl font-bold text-blue-600 mt-0.5">{{ number_format($kpis['emUso']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sem Atribuição</p>
            <p class="text-xl font-bold text-yellow-600 mt-0.5">{{ number_format($kpis['semAtribuicao']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Funcionários Total</p>
            <p class="text-xl font-bold text-gray-700 dark:text-gray-200 mt-0.5">{{ number_format($kpis['totalFuncionarios']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Com Patrimônio</p>
            <p class="text-xl font-bold text-green-600 mt-0.5">{{ number_format($kpis['funcionariosComPatrimonio']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sem Patrimônio</p>
            <p class="text-xl font-bold text-red-500 mt-0.5">{{ number_format($kpis['funcionariosSemPatrimonio']) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Top 10 Funcionários com mais Patrimônios</h3>
            <canvas id="chartTop10" height="160"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Patrimônios por Departamento</h3>
            <canvas id="chartDepto" height="160"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Patrimônios por Funcionário (Top 10)</h3>
        <canvas id="chartPorFunc" height="110"></canvas>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';
        const palette = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#f97316','#06b6d4','#ec4899','#10b981'];

        const top10Labels = @json(collect($top10)->pluck('nome'));
        const top10Data   = @json(collect($top10)->pluck('total'));

        new Chart(document.getElementById('chartTop10'), {
            type: 'bar',
            data: {
                labels: top10Labels,
                datasets: [{ label: 'Patrimônios', data: top10Data, backgroundColor: '#6366f1', borderRadius: 4 }]
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

        new Chart(document.getElementById('chartDepto'), {
            type: 'doughnut',
            data: {
                labels: @json($porDepartamento['labels']),
                datasets: [{ data: @json($porDepartamento['data']), backgroundColor: palette, hoverOffset: 8 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right', labels: { color: labelColor, padding: 12 } } }
            }
        });

        new Chart(document.getElementById('chartPorFunc'), {
            type: 'bar',
            data: {
                labels: @json($porFuncionario['labels']),
                datasets: [{ label: 'Patrimônios', data: @json($porFuncionario['data']), backgroundColor: '#3b82f6', borderRadius: 4 }]
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
