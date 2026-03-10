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

    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    {{-- Dashboard com abas analíticas --}}
    <div x-data="{ tab: 'operacional' }">

        {{-- Nav de abas --}}
        <div class="mb-3 flex gap-1 flex-wrap border-b border-gray-200 dark:border-gray-700">
            @php
            $tabBtn = 'px-3 py-1.5 text-xs font-medium rounded-t-lg border-b-2 transition-colors whitespace-nowrap';
            $tabActive = 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800';
            $tabInactive = 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50';
            @endphp

            @if(auth()->user()->isSuperAdmin())
            <button @click="tab='global'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='global' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                🌐 Global
            </button>
            @endif

            <button @click="tab='operacional'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='operacional' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                📋 Operacional
            </button>
            <button @click="tab='empresa'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='empresa' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                🏢 Por Empresa
            </button>
            <button @click="tab='distribuicao'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='distribuicao' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                👥 Distribuição
            </button>
            <button @click="tab='ciclovida'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='ciclovida' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                🔄 Ciclo de Vida
            </button>
            <button @click="tab='manutencao'; $nextTick(() => window.dispatchEvent(new Event('resize')))"
                :class="tab==='manutencao' ? '{{ $tabActive }}' : '{{ $tabInactive }}'"
                class="{{ $tabBtn }}">
                🔧 Manutenções
            </button>
        </div>

        {{-- ──────────── TAB: GLOBAL (super_admin) ──────────── --}}
        @if(auth()->user()->isSuperAdmin())
        <div x-show="tab==='global'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Empresas</p>
                    <p class="text-xl font-bold text-indigo-600">{{ $analytics['global_kpis']['total_empresas'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Usuários</p>
                    <p class="text-xl font-bold text-blue-600">{{ $analytics['global_kpis']['total_usuarios'] }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Patrimônios</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($analytics['global_kpis']['total_patrimonios']) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Valor Total</p>
                    <p class="text-lg font-bold text-yellow-600">R$ {{ number_format($analytics['global_kpis']['valor_total'], 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Patrimônios por Empresa (Top 10)</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="g_porEmpresa"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Crescimento Mensal (12 meses)</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="g_crescimento"></canvas></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Top Empresas por Patrimônio</p>
                <table class="min-w-full text-xs">
                    <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="py-1.5 px-2 text-left text-gray-500">#</th>
                        <th class="py-1.5 px-2 text-left text-gray-500">Empresa</th>
                        <th class="py-1.5 px-2 text-right text-gray-500">Qtd.</th>
                        <th class="py-1.5 px-2 text-right text-gray-500">Valor</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($analytics['global_top'] as $i => $emp)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="py-1.5 px-2 text-gray-400">{{ $i+1 }}</td>
                            <td class="py-1.5 px-2 font-medium text-gray-800 dark:text-gray-200">{{ $emp['nome'] }}</td>
                            <td class="py-1.5 px-2 text-right text-gray-700 dark:text-gray-300">{{ number_format($emp['total']) }}</td>
                            <td class="py-1.5 px-2 text-right text-gray-700 dark:text-gray-300">R$ {{ number_format($emp['valor'], 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ──────────── TAB: OPERACIONAL ──────────── --}}
        <div x-show="tab==='operacional'">
            <div class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Patrimônios</span>
                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets }}</span>
                        <span class="text-xs text-gray-400">{{ $patrimoniosSemResponsavel }} disponíveis</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Funcionários</span>
                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalEmployees }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Chamados Abertos</span>
                        <span class="text-2xl font-bold text-yellow-500">{{ $totalOpenTickets }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Atribuições Ativas</span>
                        <span class="text-2xl font-bold text-blue-500">{{ $totalResponsibilities }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-start">
                    <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Patrimônios por Status</h3>
                            <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                                <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                            </button>
                        </div>
                        <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartPatrimonios"></canvas></div>
                    </div>
                    <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Chamados — Últimos 6 Meses</h3>
                            <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                                <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                            </button>
                        </div>
                        <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartChamados"></canvas></div>
                    </div>
                </div>

                @if($latestTickets->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Chamados Abertos Recentes</h3>
                        <a href="{{ route('tickets.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aberto em</th>
                            <th class="px-4 py-2"></th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($latestTickets as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $ticket->id }}</td>
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $ticket->employee?->nome ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-600 font-mono text-xs">{{ $ticket->assets->pluck('codigo_patrimonio')->implode(', ') ?: '—' }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-right"><a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-xs">Ver</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($departmentStats->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Visão por Departamento</h3>
                        <a href="{{ route('departments.index') }}" class="text-xs text-blue-600 hover:underline">Gerenciar</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Funcionários</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Em Uso</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Chamados</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($departmentStats as $stat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-200"><a href="{{ route('departments.show', $stat['department']) }}" class="hover:text-blue-600 hover:underline">{{ $stat['department']->nome }}</a></td>
                                <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">{{ $stat['total_funcionarios'] }}</td>
                                <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">{{ $stat['patrimonios_em_uso'] }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($stat['chamados_abertos'] > 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">{{ $stat['chamados_abertos'] }}</span>
                                    @else<span class="text-gray-400">—</span>@endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- ──────────── TAB: POR EMPRESA ──────────── --}}
        <div x-show="tab==='empresa'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-3">
                @foreach([['total','Total','text-indigo-600'],['disponivel','Disponível','text-green-600'],['em_uso','Em Uso','text-blue-600'],['manutencao','Manutenção','text-yellow-600'],['descartado','Descartado','text-red-500']] as [$k,$l,$c])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $l }}</p>
                    <p class="text-xl font-bold {{ $c }}">{{ number_format($analytics['empresa'][$k]) }}</p>
                </div>
                @endforeach
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 col-span-2 xl:col-span-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Valor Total</p>
                    <p class="text-base font-bold text-gray-800 dark:text-gray-100">R$ {{ number_format($analytics['empresa']['valor_total'], 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Status</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="e_status"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Departamento</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="e_depto"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Crescimento Mensal</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:180px'"><canvas id="e_crescimento"></canvas></div>
                </div>
            </div>
        </div>

        {{-- ──────────── TAB: DISTRIBUIÇÃO ──────────── --}}
        <div x-show="tab==='distribuicao'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
                @foreach([
                    ['totalAtivos','Ativos Totais','text-indigo-600'],
                    ['emUso','Em Uso','text-blue-600'],
                    ['semAtribuicao','Sem Atribuição','text-yellow-600'],
                    ['totalFuncionarios','Funcionários','text-gray-700 dark:text-gray-200'],
                    ['funcionariosComPatrimonio','Com Patrimônio','text-green-600'],
                    ['funcionariosSemPatrimonio','Sem Patrimônio','text-red-500'],
                ] as [$k,$l,$c])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $l }}</p>
                    <p class="text-xl font-bold {{ $c }}">{{ number_format($analytics['dist_kpis'][$k]) }}</p>
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Top 10 Funcionários</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="d_top10"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Departamento</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="d_depto"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Funcionário (Top 10)</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:180px'"><canvas id="d_func"></canvas></div>
                </div>
            </div>
        </div>

        {{-- ──────────── TAB: CICLO DE VIDA ──────────── --}}
        <div x-show="tab==='ciclovida'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                @foreach([
                    ['idade_media','Idade Média (anos)','text-indigo-600',true],
                    ['com_garantia','Com Garantia','text-green-600',false],
                    ['garantias_vencendo','Garantias (30d)','text-yellow-600',false],
                    ['garantias_vencidas','Garantias Vencidas','text-red-500',false],
                ] as [$k,$l,$c,$dec])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $l }}</p>
                    <p class="text-xl font-bold {{ $c }}">{{ $dec ? number_format($analytics['ciclo_kpis'][$k],1) : number_format($analytics['ciclo_kpis'][$k]) }}</p>
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Faixa de Idade</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="c_idade"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Aquisições por Mês</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="c_aquis"></canvas></div>
                </div>
            </div>
            @if(count($analytics['ciclo_garantias']))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">
                    Garantias vencendo em 90 dias
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 text-xs">{{ count($analytics['ciclo_garantias']) }}</span>
                </p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-1.5 px-2 text-left text-gray-500">Código</th>
                            <th class="py-1.5 px-2 text-left text-gray-500">Descrição</th>
                            <th class="py-1.5 px-2 text-center text-gray-500">Vencimento</th>
                            <th class="py-1.5 px-2 text-center text-gray-500">Dias</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($analytics['ciclo_garantias'] as $g)
                            @php $days = now()->diffInDays(\Carbon\Carbon::parse($g->data_garantia), false); @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="py-1.5 px-2 font-mono text-gray-800 dark:text-gray-200">{{ $g->codigo_patrimonio }}</td>
                                <td class="py-1.5 px-2 text-gray-700 dark:text-gray-300">{{ $g->descricao }}</td>
                                <td class="py-1.5 px-2 text-center text-gray-600">{{ \Carbon\Carbon::parse($g->data_garantia)->format('d/m/Y') }}</td>
                                <td class="py-1.5 px-2 text-center">
                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $days <= 30 ? 'bg-red-100 text-red-700' : ($days <= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $days }}d
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- ──────────── TAB: MANUTENÇÕES ──────────── --}}
        <div x-show="tab==='manutencao'" x-cloak>
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-3">
                @foreach([
                    ['agendada','Agendadas','text-blue-600'],
                    ['em_andamento','Em Andamento','text-yellow-600'],
                    ['concluida','Concluídas','text-green-600'],
                    ['cancelada','Canceladas','text-red-500'],
                    ['tempo_medio','Tempo Médio (d)','text-indigo-600'],
                ] as [$k,$l,$c])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $l }}</p>
                    <p class="text-xl font-bold {{ $c }}">{{ is_float($analytics['man_kpis'][$k]) ? number_format($analytics['man_kpis'][$k],1) : number_format($analytics['man_kpis'][$k]) }}</p>
                </div>
                @endforeach
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3 col-span-2 xl:col-span-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Custo Total</p>
                    <p class="text-base font-bold text-gray-800 dark:text-gray-100">R$ {{ number_format($analytics['man_kpis']['custo_total'], 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Status</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="m_status"></canvas></div>
                </div>
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Por Mês (12 meses)</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="m_mes"></canvas></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 items-start">
                <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">Equipamentos Mais Manutenidos (Top 10)</p>
                        <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                            <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                        </button>
                    </div>
                    <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="m_equip"></canvas></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-3">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">Manutenções Recentes</p>
                    <div class="overflow-y-auto max-h-52">
                        <table class="min-w-full text-xs">
                            <thead class="sticky top-0 bg-white dark:bg-gray-800">
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-1.5 px-2 text-left text-gray-500">Patrimônio</th>
                                    <th class="py-1.5 px-2 text-left text-gray-500">Tipo</th>
                                    <th class="py-1.5 px-2 text-left text-gray-500">Status</th>
                                    <th class="py-1.5 px-2 text-right text-gray-500">Custo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($analytics['man_recentes'] as $m)
                                @php
                                    $sc = ['agendada'=>'bg-blue-100 text-blue-700','em_andamento'=>'bg-yellow-100 text-yellow-700','concluida'=>'bg-green-100 text-green-700','cancelada'=>'bg-red-100 text-red-700'][$m->status ?? ''] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-1.5 px-2 font-mono text-gray-800 dark:text-gray-200">{{ $m->codigo_patrimonio ?? '-' }}</td>
                                    <td class="py-1.5 px-2 capitalize text-gray-600">{{ $m->tipo }}</td>
                                    <td class="py-1.5 px-2"><span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $sc }}">{{ str_replace('_',' ',$m->status) }}</span></td>
                                    <td class="py-1.5 px-2 text-right text-gray-700">{{ $m->custo ? 'R$ '.number_format($m->custo,2,',','.') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end x-data --}}

    @else
    {{-- Painel simplificado para gestor/funcionário --}}
    <div class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                    @if(auth()->user()->isManager()) Patrimônios em Uso @else Patrimônios Sob Guarda @endif
                </span>
                <span class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalAssets }}</span>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Chamados Abertos</span>
                <span class="text-2xl font-bold text-yellow-500">{{ $totalOpenTickets }}</span>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col gap-1">
                @if(auth()->user()->isManager())
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sem Patrimônio</span>
                    <span class="text-2xl font-bold {{ $totalResponsibilities > 0 ? 'text-red-500' : 'text-green-500' }}">{{ $totalResponsibilities }}</span>
                @else
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Atribuições Ativas</span>
                    <span class="text-2xl font-bold text-blue-500">{{ $totalResponsibilities }}</span>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-start">
            <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                        @if(auth()->user()->isManager()) Cobertura — {{ $department?->nome }} @else Patrimônios em Uso @endif
                    </h3>
                    <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                        <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                    </button>
                </div>
                <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartPatrimonios"></canvas></div>
            </div>
            <div x-data="{ exp: false }" :class="exp && 'md:col-span-2'" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Chamados — Últimos 6 Meses</h3>
                    <button @click="exp=!exp; $nextTick(()=>window.dispatchEvent(new Event('resize')))" :title="exp?'Reduzir':'Expandir'" class="p-0.5 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg x-show="!exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
                        <svg x-show="exp" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6-1l5 5m0 0v-4m0 4h-4M9 15l-5 5m0 0v-4m0 4h4m6 1l5-5m0 0v4m0-4h-4"/></svg>
                    </button>
                </div>
                <div class="relative transition-all duration-300" :style="exp ? 'height:400px' : 'height:220px'"><canvas id="chartChamados"></canvas></div>
            </div>
        </div>
        @if(auth()->user()->isManager() && $employeeStats->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Funcionários do Departamento</h3>
                <a href="{{ route('employees.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Patrimônios</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Chamados</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($employeeStats as $stat)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-200"><a href="{{ route('employees.show', $stat['employee']) }}" class="hover:text-blue-600 hover:underline">{{ $stat['employee']->nome }}</a></td>
                        <td class="px-4 py-2 text-center">{{ $stat['patrimonios_em_uso'] ?: '—' }}</td>
                        <td class="px-4 py-2 text-center">{{ $stat['chamados_abertos'] ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        @if($latestTickets->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xs font-semibold text-gray-700 dark:text-gray-300">Chamados Abertos Recentes</h3>
                <a href="{{ route('tickets.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aberto em</th>
                    <th class="px-4 py-2"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($latestTickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $ticket->id }}</td>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $ticket->employee?->nome ?? '—' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right"><a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-xs">Ver</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endif

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gc = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const lc = isDark ? '#9ca3af' : '#6b7280';
        const pal = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#f97316','#06b6d4','#ec4899','#10b981'];

        // Fixar altura dos gráficos (o canvas fica dentro de div com height definido)
        Chart.defaults.maintainAspectRatio = false;

        // ── Operacional ──────────────────────────────
        if (document.getElementById('chartPatrimonios')) {
            new Chart(document.getElementById('chartPatrimonios'), {
                type: 'doughnut',
                data: {
                    labels: @json($assetChartLabels),
                    datasets: [{ data: @json($assetChartData), backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#ef4444'], borderWidth: 2 }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: lc } } } }
            });
            new Chart(document.getElementById('chartChamados'), {
                type: 'bar',
                data: {
                    labels: @json($mesesLabels),
                    datasets: [{ label: 'Chamados', data: @json($mesesData), backgroundColor: '#6366f1', borderRadius: 4 }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc, stepSize: 1 }, grid: { color: gc } } } }
            });
        }

        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())

        // ── Super Admin Global ──────────────────────
        @if(auth()->user()->isSuperAdmin())
        new Chart(document.getElementById('g_porEmpresa'), {
            type: 'bar',
            data: { labels: @json($analytics['global_empresas']['labels']), datasets: [{ data: @json($analytics['global_empresas']['data']), backgroundColor: '#6366f1', borderRadius: 3 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { ticks: { color: lc }, grid: { color: gc } } } }
        });
        new Chart(document.getElementById('g_crescimento'), {
            type: 'line',
            data: { labels: @json($analytics['global_crescimento']['labels']), datasets: [{ data: @json($analytics['global_crescimento']['data']), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.12)', fill: true, tension: 0.3, pointRadius: 2 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc } } } }
        });
        @endif

        // ── Por Empresa ─────────────────────────────
        new Chart(document.getElementById('e_status'), {
            type: 'doughnut',
            data: { labels: @json($analytics['empresa_status']['labels']), datasets: [{ data: @json($analytics['empresa_status']['data']), backgroundColor: pal, hoverOffset: 6 }] },
            options: { responsive: true, plugins: { legend: { position: 'right', labels: { color: lc, padding: 10 } } } }
        });
        new Chart(document.getElementById('e_depto'), {
            type: 'bar',
            data: { labels: @json($analytics['empresa_depto']['labels']), datasets: [{ data: @json($analytics['empresa_depto']['data']), backgroundColor: '#3b82f6', borderRadius: 3 }] },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { ticks: { color: lc }, grid: { color: gc } } } }
        });
        new Chart(document.getElementById('e_crescimento'), {
            type: 'line',
            data: { labels: @json($analytics['empresa_crescimento']['labels']), datasets: [{ data: @json($analytics['empresa_crescimento']['data']), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.12)', fill: true, tension: 0.3, pointRadius: 2 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc } } } }
        });

        // ── Distribuição ────────────────────────────
        new Chart(document.getElementById('d_top10'), {
            type: 'bar',
            data: { labels: @json(collect($analytics['dist_top10'])->pluck('nome')), datasets: [{ data: @json(collect($analytics['dist_top10'])->pluck('total')), backgroundColor: '#6366f1', borderRadius: 3 }] },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { ticks: { color: lc }, grid: { color: gc } } } }
        });
        new Chart(document.getElementById('d_depto'), {
            type: 'doughnut',
            data: { labels: @json($analytics['dist_depto']['labels']), datasets: [{ data: @json($analytics['dist_depto']['data']), backgroundColor: pal, hoverOffset: 6 }] },
            options: { responsive: true, plugins: { legend: { position: 'right', labels: { color: lc, padding: 10 } } } }
        });
        new Chart(document.getElementById('d_func'), {
            type: 'bar',
            data: { labels: @json($analytics['dist_func']['labels']), datasets: [{ data: @json($analytics['dist_func']['data']), backgroundColor: '#3b82f6', borderRadius: 3 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc } } } }
        });

        // ── Ciclo de Vida ───────────────────────────
        new Chart(document.getElementById('c_idade'), {
            type: 'bar',
            data: { labels: @json($analytics['ciclo_idade']['labels']), datasets: [{ data: @json($analytics['ciclo_idade']['data']), backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#f97316','#ef4444'], borderRadius: 3 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc } } } }
        });
        new Chart(document.getElementById('c_aquis'), {
            type: 'bar',
            data: {
                labels: @json($analytics['ciclo_aquis']['labels']),
                datasets: [
                    { label: 'Qtd.', data: @json($analytics['ciclo_aquis']['qtd']), backgroundColor: '#6366f1', borderRadius: 3, yAxisID: 'y' },
                    { label: 'Valor', data: @json($analytics['ciclo_aquis']['valor']), type: 'line', borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', fill: false, tension: 0.3, yAxisID: 'y2', pointRadius: 2 }
                ]
            },
            options: { responsive: true, plugins: { legend: { labels: { color: lc } } }, scales: {
                x: { ticks: { color: lc }, grid: { color: gc } },
                y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc }, position: 'left' },
                y2: { beginAtZero: true, ticks: { color: lc }, grid: { display: false }, position: 'right' }
            }}
        });

        // ── Manutenções ─────────────────────────────
        new Chart(document.getElementById('m_status'), {
            type: 'doughnut',
            data: { labels: @json($analytics['man_status']['labels']), datasets: [{ data: @json($analytics['man_status']['data']), backgroundColor: ['#3b82f6','#f59e0b','#22c55e','#ef4444'], hoverOffset: 6 }] },
            options: { responsive: true, plugins: { legend: { position: 'right', labels: { color: lc, padding: 10 } } } }
        });
        new Chart(document.getElementById('m_mes'), {
            type: 'bar',
            data: { labels: @json($analytics['man_mes']['labels']), datasets: [{ data: @json($analytics['man_mes']['data']), backgroundColor: '#8b5cf6', borderRadius: 3 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { beginAtZero: true, ticks: { color: lc }, grid: { color: gc } } } }
        });
        new Chart(document.getElementById('m_equip'), {
            type: 'bar',
            data: { labels: @json(collect($analytics['man_equip'])->pluck('descricao')), datasets: [{ data: @json(collect($analytics['man_equip'])->pluck('total')), backgroundColor: '#f97316', borderRadius: 3 }] },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: lc }, grid: { color: gc } }, y: { ticks: { color: lc }, grid: { color: gc } } } }
        });

        @endif
    });
    </script>
    @endpush
</x-app-layout>
