<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Dashboard Global
            </h2>
            <span class="text-sm text-purple-600 dark:text-purple-400 font-medium">Super Admin</span>
        </div>
    </x-slot>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Empresas</p>
            <p class="text-xl font-bold text-indigo-600 mt-0.5">{{ $kpis['total_empresas'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Usuários</p>
            <p class="text-xl font-bold text-blue-600 mt-0.5">{{ $kpis['total_usuarios'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Patrimônios</p>
            <p class="text-xl font-bold text-green-600 mt-0.5">{{ number_format($kpis['total_patrimonios']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor Total</p>
            <p class="text-lg font-bold text-yellow-600 mt-0.5">R$ {{ number_format($kpis['valor_total'], 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Gráficos linha 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Patrimônios por Empresa (Top 10)</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartPorEmpresa"></canvas></div>
        </div>
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Crescimento Mensal (12 meses)</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartCrescimento"></canvas></div>
        </div>
    </div>

    {{-- Tabela top empresas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Top Empresas por Patrimônio</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-2 px-3 text-left font-medium text-gray-600 dark:text-gray-400">#</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-600 dark:text-gray-400">Empresa</th>
                        <th class="py-2 px-3 text-right font-medium text-gray-600 dark:text-gray-400">Qtd.</th>
                        <th class="py-2 px-3 text-right font-medium text-gray-600 dark:text-gray-400">Valor Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($topEmpresas as $i => $emp)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="py-2 px-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="py-2 px-3 text-gray-800 dark:text-gray-200 font-medium">{{ $emp['nome'] }}</td>
                        <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">{{ number_format($emp['total']) }}</td>
                        <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">R$ {{ number_format($emp['valor'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.maintainAspectRatio = false;
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        // Patrimônios por empresa
        new Chart(document.getElementById('chartPorEmpresa'), {
            type: 'bar',
            data: {
                labels: @json($porEmpresa['labels']),
                datasets: [{
                    label: 'Patrimônios',
                    data: @json($porEmpresa['data']),
                    backgroundColor: '#6366f1',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                    y: { ticks: { color: labelColor }, grid: { color: gridColor } }
                }
            }
        });

        // Crescimento mensal
        new Chart(document.getElementById('chartCrescimento'), {
            type: 'line',
            data: {
                labels: @json($crescimento['labels']),
                datasets: [{
                    label: 'Novos',
                    data: @json($crescimento['data']),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.15)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
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
    });
    </script>
    @endpush
</x-app-layout>
