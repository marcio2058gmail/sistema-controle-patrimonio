<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Inventários</h2>
            <button type="button" @click="$dispatch('open-novo-inventario')"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Novo Inventário
            </button>
        </div>
    </x-slot>

    <div x-data="{ modalOpen: false }" @open-novo-inventario.window="modalOpen = true" @keydown.escape.window="modalOpen = false" class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-alert />

            @if($inventories->isEmpty())
            <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <p class="text-sm">Nenhum inventário encontrado.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($inventories as $inventory)
                <a href="{{ route('inventories.show', $inventory) }}"
                   class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 px-5 py-4 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-700 transition group">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">
                                {{ $inventory->descricao ?: 'Inventário #' . $inventory->id }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Iniciado em {{ $inventory->iniciado_em->format('d/m/Y H:i') }}
                                · {{ $inventory->items->count() }} itens
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @php
                            $statusColors = [
                                'em_andamento' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                'concluido'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'cancelado'    => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $statusLabels = ['em_andamento' => 'Em Andamento', 'concluido' => 'Concluído', 'cancelado' => 'Cancelado'];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$inventory->status] ?? '' }}">
                            {{ $statusLabels[$inventory->status] ?? $inventory->status }}
                        </span>
                        <svg class="h-4 w-4 text-gray-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                @endforeach
            </div>

            @if($inventories->hasPages())
            <div class="mt-5">
                {{ $inventories->links() }}
            </div>
            @endif
            @endif

        </div>

        {{-- Modal: novo inventário --}}
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
             @click.self="modalOpen = false">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop>
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Novo Inventário</h3>
                <form method="POST" action="{{ route('inventories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descrição (opcional)</label>
                        <x-text-input name="descricao" type="text" class="w-full"
                                      placeholder="Ex.: Inventário Q1 2026" />
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                            Abrir Inventário
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
