<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Patrimônio: {{ $asset->codigo_patrimonio }}
            </h2>
            <a href="{{ route('assets.edit', $asset) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Editar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            {{-- Detalhes do Patrimônio --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informações</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Código</dt><dd class="font-medium text-gray-800 dark:text-gray-200 font-mono">{{ $asset->codigo_patrimonio }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="mt-0.5"><x-status-badge :status="$asset->status" type="patrimonio" /></dd></div>
                    <div><dt class="text-gray-500">Descrição</dt><dd class="font-medium text-gray-800 dark:text-gray-200">{{ $asset->descricao }}</dd></div>
                    <div><dt class="text-gray-500">Modelo</dt><dd class="text-gray-800 dark:text-gray-200">{{ $asset->modelo ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Número de Série</dt><dd class="text-gray-800 dark:text-gray-200">{{ $asset->numero_serie ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Cadastrado em</dt><dd class="text-gray-800 dark:text-gray-200">{{ $asset->created_at->format('d/m/Y') }}</dd></div>
                </dl>
            </div>

            {{-- Responsabilidades --}}
            @if($asset->responsibilities->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Histórico de Responsabilidades</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devolução</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assinado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($asset->responsibilities as $r)
                        <tr>
                            <td class="px-6 py-3">{{ $r->employee->nome }}</td>
                            <td class="px-6 py-3">{{ $r->data_entrega->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">{{ $r->data_devolucao?->format('d/m/Y') ?? 'Ativo' }}</td>
                            <td class="px-6 py-3">{{ $r->assinado ? 'Sim' : 'Não' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="flex justify-start">
                <a href="{{ route('assets.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
            </div>
        </div>
    </div>
</x-app-layout>
