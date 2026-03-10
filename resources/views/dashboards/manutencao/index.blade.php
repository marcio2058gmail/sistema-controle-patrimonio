<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Dashboard de Manutenções
        </h2>
    </x-slot>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-3">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Agendadas</p>
            <p class="text-xl font-bold text-blue-600 mt-0.5">{{ number_format($kpis['agendada']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Em Andamento</p>
            <p class="text-xl font-bold text-yellow-600 mt-0.5">{{ number_format($kpis['em_andamento']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Concluídas</p>
            <p class="text-xl font-bold text-green-600 mt-0.5">{{ number_format($kpis['concluida']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Canceladas</p>
            <p class="text-xl font-bold text-red-500 mt-0.5">{{ number_format($kpis['cancelada']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tempo Médio (dias)</p>
            <p class="text-xl font-bold text-indigo-600 mt-0.5">{{ number_format($kpis['tempo_medio'], 1) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 xl:col-span-1 col-span-2">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Custo Total</p>
            <p class="text-base font-bold text-gray-800 dark:text-gray-100 mt-0.5">R$ {{ number_format($kpis['custo_total'], 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Manutenções por Status</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartStatus"></canvas></div>
        </div>
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Manutenções por Mês (12 meses)</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartPorMes"></canvas></div>
        </div>
    </div>

    {{-- Top equipamentos + recentes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
        <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Equipamentos com mais Manutenções</h3>
                <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                    <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                </button>
            </div>
            <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartEquip"></canvas></div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Manutenções Recentes</h3>
            <div class="overflow-y-auto max-h-64">
                <table class="min-w-full text-xs">
                    <thead class="sticky top-0 bg-white dark:bg-gray-800">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 px-2 text-left font-medium text-gray-600 dark:text-gray-400">Patrimônio</th>
                            <th class="py-2 px-2 text-left font-medium text-gray-600 dark:text-gray-400">Tipo</th>
                            <th class="py-2 px-2 text-left font-medium text-gray-600 dark:text-gray-400">Status</th>
                            <th class="py-2 px-2 text-right font-medium text-gray-600 dark:text-gray-400">Custo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recentes as $m)
                        @php
                            $statusColors = [
                                'agendada'     => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200',
                                'em_andamento' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
                                'concluida'    => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
                                'cancelada'    => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200',
                            ];
                            $sc = $statusColors[$m->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="py-1.5 px-2 text-gray-800 dark:text-gray-200 font-mono">{{ $m->codigo_patrimonio ?? '-' }}</td>
                            <td class="py-1.5 px-2 text-gray-600 dark:text-gray-400 capitalize">{{ $m->tipo }}</td>
                            <td class="py-1.5 px-2">
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium {{ $sc }}">
                                    {{ str_replace('_', ' ', $m->status) }}
                                </span>
                            </td>
                            <td class="py-1.5 px-2 text-right text-gray-700 dark:text-gray-300">
                                {{ $m->custo ? 'R$ ' . number_format($m->custo, 2, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.maintainAspectRatio = false;
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: @json($porStatus['labels']),
                datasets: [{ data: @json($porStatus['data']), backgroundColor: ['#3b82f6','#f59e0b','#22c55e','#ef4444'], hoverOffset: 8 }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'right', labels: { color: labelColor, padding: 12 } } }
            }
        });

        new Chart(document.getElementById('chartPorMes'), {
            type: 'bar',
            data: {
                labels: @json($porMes['labels']),
                datasets: [{ label: 'Manutenções', data: @json($porMes['data']), backgroundColor: '#8b5cf6', borderRadius: 4 }]
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

        const equipLabels = @json(collect($porEquip)->pluck('descricao'));
        const equipData   = @json(collect($porEquip)->pluck('total'));

        new Chart(document.getElementById('chartEquip'), {
            type: 'bar',
            data: {
                labels: equipLabels,
                datasets: [{ label: 'Manutenções', data: equipData, backgroundColor: '#f97316', borderRadius: 4 }]
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
    });
    </script>
    @endpush
</x-app-layout>
