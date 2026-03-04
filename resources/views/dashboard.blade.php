<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard — Controle Patrimonial
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Cards de KPI --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patrimônios</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPatrimonios }}</span>
                    <span class="text-xs text-gray-400">{{ $patrimoniosSemResponsavel }} disponíveis</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Funcionários</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalFuncionarios }}</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Chamados Abertos</span>
                    <span class="text-3xl font-bold text-yellow-500">{{ $totalChamadosAbertos }}</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Atribuições Ativas</span>
                    <span class="text-3xl font-bold text-blue-500">{{ $totalResponsabilidades }}</span>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Patrimônios por Status</h3>
                    <canvas id="chartPatrimonios" height="200"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Chamados — Últimos 6 Meses</h3>
                    <canvas id="chartChamados" height="200"></canvas>
                </div>
            </div>

            {{-- Tabela: Chamados abertos recentes --}}
            @if($ultimosChamados->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Chamados Abertos Recentes</h3>
                    <a href="{{ route('chamados.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aberto em</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($ultimosChamados as $chamado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $chamado->id }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $chamado->funcionario?->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                {{ $chamado->patrimonios->pluck('codigo_patrimonio')->implode(', ') ?: '—' }}
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $chamado->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('chamados.show', $chamado) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Gráfico de pizza — Patrimônios por Status
        new Chart(document.getElementById('chartPatrimonios'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($patrimonioChartLabels) !!},
                datasets: [{
                    data: {!! json_encode($patrimonioChartData) !!},
                    backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b'],
                    borderWidth: 2,
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // Gráfico de barras — Chamados por mês
        new Chart(document.getElementById('chartChamados'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($mesesLabels) !!},
                datasets: [{
                    label: 'Chamados',
                    data: {!! json_encode($mesesData) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>
    @endpush
</x-app-layout>
