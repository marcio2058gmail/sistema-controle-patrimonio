<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Departamento: {{ $departamento->nome }}
            </h2>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('departamentos.edit', $departamento) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Editar
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            {{-- KPIs --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Funcionários</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $totalFuncionarios }}</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col gap-1">
                    <span class="text-xs text-gray-400 uppercase tracking-wide">Patrimônios em Uso</span>
                    <span class="text-3xl font-bold text-indigo-600">{{ $totalPatrimoniosEmUso }}</span>
                </div>
            </div>

            {{-- Info do departamento --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Informações</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex gap-2">
                        <dt class="text-gray-500 w-24 shrink-0">Nome</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $departamento->nome }}</dd>
                    </div>
                    @if($departamento->descricao)
                    <div class="flex gap-2">
                        <dt class="text-gray-500 w-24 shrink-0">Descrição</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ $departamento->descricao }}</dd>
                    </div>
                    @endif
                    <div class="flex gap-2">
                        <dt class="text-gray-500 w-24 shrink-0">Criado em</dt>
                        <dd class="text-gray-700 dark:text-gray-300">{{ $departamento->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Uso por funcionário --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Patrimônios em Uso no Departamento</h3>
                    <span class="text-xs text-gray-400">Responsabilidades ativas</span>
                </div>

                @if($funcionariosComPatrimonios->isEmpty())
                    <p class="text-center text-gray-400 text-sm py-10">Nenhum patrimônio em uso neste departamento.</p>
                @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desde</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($funcionariosComPatrimonios as $funcionario)
                            @foreach($funcionario->responsabilidades as $resp)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    <a href="{{ route('funcionarios.show', $funcionario) }}" class="hover:underline text-indigo-600">
                                        {{ $funcionario->nome }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $funcionario->cargo ?? '—' }}</td>
                                <td class="px-6 py-3 font-mono text-gray-700 dark:text-gray-300">
                                    <a href="{{ route('patrimonios.show', $resp->patrimonio) }}" class="hover:underline text-blue-600">
                                        {{ $resp->patrimonio->codigo_patrimonio }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $resp->patrimonio->descricao }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $resp->data_entrega->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Lista de todos os funcionários --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Todos os Funcionários</h3>
                </div>

                @if($departamento->funcionarios->isEmpty())
                    <p class="text-center text-gray-400 text-sm py-8">Nenhum funcionário neste departamento.</p>
                @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônios ativos</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($departamento->funcionarios as $funcionario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $funcionario->nome }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $funcionario->email }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $funcionario->cargo ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $funcionario->responsabilidades->count() > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $funcionario->responsabilidades->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('funcionarios.show', $funcionario) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            <a href="{{ route('departamentos.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
        </div>
    </div>
</x-app-layout>
