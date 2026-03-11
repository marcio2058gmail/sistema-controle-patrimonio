<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('inventories.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate">
                    {{ $inventory->descricao ?: 'Inventário #' . $inventory->id }}
                </h2>
            </div>
            @if($inventory->status === 'em_andamento')
            <form method="POST" action="{{ route('inventories.close', $inventory) }}"
                  onsubmit="return confirm('Finalizar este inventário? Esta ação é irreversível.')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Finalizar
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <x-alert />

            {{-- Resumo --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $total      = $inventory->items->count();
                    $encontrado = $inventory->items->where('status', 'encontrado')->count();
                    $nao        = $inventory->items->where('status', 'nao_encontrado')->count();
                    $avariado   = $inventory->items->where('status', 'avariado')->count();
                    $pendente   = $inventory->items->where('status', 'pendente')->count();
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 text-center">
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $total }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-green-100 dark:border-green-800/40 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $encontrado }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Encontrados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-100 dark:border-red-800/40 p-4 text-center">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $nao }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Não encontrados</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-amber-100 dark:border-amber-800/40 p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendente + $avariado }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pendentes/Avariados</p>
                </div>
            </div>

            {{-- Itens --}}
            @if($inventory->items->isEmpty())
            <div class="text-center py-16 text-gray-400 dark:text-gray-500">
                <p class="text-sm">Nenhum item neste inventário.</p>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patrimônio</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nº Série</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Observação</th>
                            @if($inventory->status === 'em_andamento')
                            <th class="px-5 py-3"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($inventory->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" x-data="{ editing: false }">
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $item->asset->nome ?? '#' . $item->patrimonio_id }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->asset->numero_serie ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $itemColors = [
                                        'encontrado'     => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                        'nao_encontrado' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'avariado'       => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        'pendente'       => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $itemColors[$item->status] ?? '' }}">
                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $item->observacao ?? '—' }}
                            </td>
                            @if($inventory->status === 'em_andamento')
                            <td class="px-5 py-3.5 text-right">
                                <button @click="editing = !editing"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    Atualizar
                                </button>
                                <div x-show="editing" x-cloak x-transition
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                                     @click.self="editing = false">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop>
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4 text-sm">Atualizar: {{ $item->asset->nome ?? '#' . $item->patrimonio_id }}</h3>
                                        <form method="POST" action="{{ route('inventories.items.update', $item) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                                <select name="status" required
                                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach($statusLabels as $val => $label)
                                                    <option value="{{ $val }}" {{ $item->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Observação</label>
                                                <textarea name="observacao" rows="2"
                                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                          placeholder="Opcional...">{{ $item->observacao }}</textarea>
                                            </div>
                                            <div class="flex justify-end gap-3 pt-1">
                                                <button type="button" @click="editing = false"
                                                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 rounded-lg">
                                                    Cancelar
                                                </button>
                                                <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">
                                                    Salvar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Meta do inventário --}}
            <div class="text-xs text-gray-400 dark:text-gray-500 text-right">
                Iniciado em {{ $inventory->iniciado_em->format('d/m/Y \à\s H:i') }}
                @if($inventory->finalizado_em)
                · Finalizado em {{ $inventory->finalizado_em->format('d/m/Y \à\s H:i') }}
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
