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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Top 10 Funcionários com mais Patrimônios</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartTop10"></canvas></div>
        </div>
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Patrimônios por Departamento</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartDepto"></canvas></div>
        </div>
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 md:col-span-2">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Patrimônios por Funcionário (Top 10)</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:180px'"><canvas id="chartPorFunc"></canvas></div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.maintainAspectRatio = false;
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
