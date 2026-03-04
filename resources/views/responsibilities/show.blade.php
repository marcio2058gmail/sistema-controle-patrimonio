<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Responsabilidade #{{ $responsibility->id }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('responsibilities.pdf', $responsibility) }}" target="_blank"
                   class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    📄 Baixar PDF
                </a>
                <a href="{{ route('responsibilities.edit', $responsibility) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informações</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Funcionário</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $responsibility->funcionario->nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cargo</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->funcionario->cargo ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Patrimônio</dt>
                        <dd class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $responsibility->patrimonio->codigo_patrimonio }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Descrição</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->patrimonio->descricao }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data de Entrega</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->data_entrega->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data de Devolução</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->data_devolucao?->format('d/m/Y') ?? 'Ativo' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Assinado</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->assinado ? 'Sim' : 'Não' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Termo de Responsabilidade</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $responsibility->termo_responsabilidade }}</p>
            </div>

            <a href="{{ route('responsibilities.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
        </div>
    </div>
</x-app-layout>
