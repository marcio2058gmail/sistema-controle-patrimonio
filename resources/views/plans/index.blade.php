<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Planos SaaS</h2>
            <a href="{{ route('plans.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Novo Plano
            </a>
        </div>
    </x-slot>

    <div x-data="{ deleteTarget: null }" class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-alert />

            @if($plans->isEmpty())
            <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">Nenhum plano cadastrado.</p>
                <a href="{{ route('plans.create') }}" class="mt-3 inline-block text-indigo-500 hover:text-indigo-400 text-sm font-medium">Criar primeiro plano →</a>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($plans as $plan)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border {{ $plan->ativo ? 'border-indigo-200 dark:border-indigo-700/50' : 'border-gray-100 dark:border-gray-700' }} flex flex-col overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Header --}}
                    <div class="px-5 pt-5 pb-4 flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl {{ $plan->ativo ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'bg-gray-50 dark:bg-gray-700' }} flex items-center justify-center shrink-0">
                                <svg class="h-5 w-5 {{ $plan->ativo ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $plan->nome }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $plan->subscriptions_count ?? $plan->subscriptions()->count() }} assinatura(s)</p>
                            </div>
                        </div>
                        @if($plan->ativo)
                            <span class="shrink-0 px-2.5 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-semibold">Ativo</span>
                        @else
                            <span class="shrink-0 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-semibold">Inativo</span>
                        @endif
                    </div>

                    {{-- Detalhes --}}
                    <div class="px-5 pb-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Preço mensal</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">R$ {{ number_format($plan->preco, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Limite de patrimônios</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ number_format($plan->limite_patrimonios, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="mt-auto border-t border-gray-100 dark:border-gray-700 px-5 py-3 flex items-center justify-between gap-2">
                        <a href="{{ route('plans.edit', $plan) }}"
                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            Editar
                        </a>
                        <button type="button" @click="deleteTarget = {{ $plan->id }}"
                                class="text-xs text-red-500 hover:underline font-medium">
                            Excluir
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Modal de confirmação de exclusão --}}
            <div x-show="deleteTarget !== null" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                 @keydown.escape.window="deleteTarget = null">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Confirmar exclusão</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Esta ação é irreversível.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="deleteTarget = null"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                            Cancelar
                        </button>
                        <template x-if="deleteTarget">
                            <form :action="'/plans/' + deleteTarget" method="POST" x-ref="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                                    Excluir
                                </button>
                            </form>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
