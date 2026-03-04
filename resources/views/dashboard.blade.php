<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @if(auth()->user()->isAdmin())
                Dashboard — Controle Patrimonial
            @elseif(auth()->user()->isManager() && $department)
                Dashboard — {{ $department->nome }}
            @else
                Meu Painel
            @endif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Cards de KPI --}}
            <div class="grid grid-cols-2 {{ auth()->user()->isAdmin() ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4">

                @if(auth()->user()->isAdmin())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patrimônios</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets }}</span>
                    <span class="text-xs text-gray-400">{{ $patrimoniosSemResponsavel }} disponíveis</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Funcionários</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalEmployees }}</span>
                </div>

                @elseif(auth()->user()->isManager())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patrimônios em Uso</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets }}</span>
                    <span class="text-xs text-gray-400">no departamento</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Funcionários</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalEmployees }}</span>
                    <span class="text-xs text-gray-400">no departamento</span>
                </div>

                @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patrimônios Sob Guarda</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets }}</span>
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Chamados Abertos</span>
                    <span class="text-3xl font-bold text-yellow-500">{{ $totalOpenTickets }}</span>
                    @if(! auth()->user()->isAdmin())
                        <span class="text-xs text-gray-400">em aberto</span>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Atribuições Ativas</span>
                    <span class="text-3xl font-bold text-blue-500">{{ $totalResponsibilities }}</span>
                    @if(auth()->user()->isManager())
                        <span class="text-xs text-gray-400">no departamento</span>
                    @endif
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                        {{ auth()->user()->isAdmin() ? 'Patrimônios por Status' : 'Patrimônios em Uso — por Status' }}
                    </h3>
                    <canvas id="chartPatrimonios" height="200"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Chamados — Últimos 6 Meses</h3>
                    <canvas id="chartChamados" height="200"></canvas>
                </div>
            </div>

            {{-- Tabela: Chamados abertos recentes --}}
            @if($latestTickets->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Chamados Abertos Recentes
                        @if(auth()->user()->isManager() && $department)
                            <span class="font-normal text-gray-400">— {{ $department->nome }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('tickets.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
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
                        @foreach($latestTickets as $ticket)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $ticket->id }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $ticket->employee?->nome ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                {{ $ticket->assets->pluck('codigo_patrimonio')->implode(', ') ?: '—' }}
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Tabela: Breakdown por departamento (admin) --}}
            @if(auth()->user()->isAdmin() && $departmentStats->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Visão por Departamento</h3>
                    <a href="{{ route('departments.index') }}" class="text-xs text-blue-600 hover:underline">Gerenciar</a>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Funcionários</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Patrimônios em Uso</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Chamados Abertos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($departmentStats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">
                                <a href="{{ route('departments.show', $stat['department']) }}" class="hover:text-blue-600 hover:underline">
                                    {{ $stat['department']->nome }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $stat['total_funcionarios'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600 dark:text-gray-400">
                                {{ $stat['patrimonios_em_uso'] }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($stat['chamados_abertos'] > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        {{ $stat['chamados_abertos'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
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
                labels: {!! json_encode($assetChartLabels) !!},
                datasets: [{
                    data: {!! json_encode($assetChartData) !!},
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
