<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Funcionário: {{ $funcionario->nome }}
            </h2>
            <a href="{{ route('funcionarios.edit', $funcionario) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informações</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Nome</dt><dd class="font-medium text-gray-800 dark:text-gray-200">{{ $funcionario->nome }}</dd></div>
                    <div><dt class="text-gray-500">E-mail</dt><dd class="text-gray-800 dark:text-gray-200">{{ $funcionario->email }}</dd></div>
                    <div><dt class="text-gray-500">Cargo</dt><dd class="text-gray-800 dark:text-gray-200">{{ $funcionario->cargo ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Cadastrado em</dt><dd class="text-gray-800 dark:text-gray-200">{{ $funcionario->created_at->format('d/m/Y') }}</dd></div>
                </dl>
            </div>

            {{-- Chamados --}}
            @if($funcionario->chamados->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Chamados</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($funcionario->chamados as $chamado)
                        <tr>
                            <td class="px-6 py-3 text-gray-500">{{ $chamado->id }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ Str::limit($chamado->descricao, 50) }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $chamado->patrimonio?->codigo_patrimonio ?? '—' }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$chamado->status" type="chamado" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Responsabilidades --}}
            @if($funcionario->responsabilidades->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Responsabilidades</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devolução</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($funcionario->responsabilidades as $r)
                        <tr>
                            <td class="px-6 py-3 font-mono text-gray-800 dark:text-gray-200">{{ $r->patrimonio->codigo_patrimonio }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $r->data_entrega->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $r->data_devolucao?->format('d/m/Y') ?? 'Ativo' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <a href="{{ route('funcionarios.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
        </div>
    </div>
</x-app-layout>
