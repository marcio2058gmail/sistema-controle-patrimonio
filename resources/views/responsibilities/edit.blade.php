<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Termo #{{ $responsibility->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <form action="{{ route('responsibilities.update', $responsibility) }}" method="POST" class="space-y-6">
                @csrf @method('PATCH')

                {{-- Info do funcionário --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-sm text-gray-500">
                        Funcionário: <strong class="text-gray-800 dark:text-gray-200">{{ $responsibility->employee->nome }}</strong>
                        @if($responsibility->employee->cargo)
                        &nbsp;|&nbsp; Cargo: <strong class="text-gray-800 dark:text-gray-200">{{ $responsibility->employee->cargo }}</strong>
                        @endif
                    </p>
                </div>

                {{-- Equipamentos vinculados --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6" x-data="{ search: '' }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            Equipamentos do Termo
                            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">{{ $responsibility->assets->count() }}</span>
                        </h3>
                        @if($assets->isNotEmpty())
                        <input type="text" x-model="search" placeholder="Filtrar disponíveis..."
                            class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 w-48">
                        @endif
                    </div>

                    {{-- Atuais --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                        @foreach($responsibility->assets as $asset)
                        <div class="flex items-center gap-3 px-4 py-3">
                            <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>
                            <span class="font-mono text-sm text-gray-700 dark:text-gray-300 w-24 shrink-0">{{ $asset->codigo_patrimonio }}</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 flex-1">{{ $asset->descricao }}</span>
                            @if($asset->modelo)<span class="text-xs text-gray-400">{{ $asset->modelo }}</span>@endif
                        </div>
                        @endforeach
                    </div>

                    @if($assets->isNotEmpty())
                    <p class="text-xs text-gray-500 mb-2">Adicionar mais equipamentos disponíveis:</p>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 max-h-56 overflow-y-auto">
                        @foreach($assets as $asset)
                        <label
                            x-show="search === '' || '{{ strtolower($asset->codigo_patrimonio . ' ' . $asset->descricao . ' ' . ($asset->modelo ?? '')) }}'.includes(search.toLowerCase())"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer transition-colors">
                            <input type="checkbox" name="patrimonio_ids[]" value="{{ $asset->id }}"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 shrink-0">
                            <span class="font-mono text-sm text-gray-700 dark:text-gray-300 w-24 shrink-0">{{ $asset->codigo_patrimonio }}</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 flex-1">{{ $asset->descricao }}</span>
                            @if($asset->modelo)<span class="text-xs text-gray-400">{{ $asset->modelo }}</span>@endif
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Campos editáveis --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Dados do Termo</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="data_entrega_info" value="Data de Entrega" />
                            <p id="data_entrega_info" class="mt-1 text-sm text-gray-700 dark:text-gray-300 py-2">
                                {{ $responsibility->data_entrega->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <x-input-label for="data_devolucao" value="Data de Devolução (deixe vazio se ainda ativo)" />
                            <x-text-input id="data_devolucao" name="data_devolucao" type="date"
                                class="mt-1 block w-full"
                                :value="old('data_devolucao', $responsibility->data_devolucao?->toDateString())" />
                            <x-input-error :messages="$errors->get('data_devolucao')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="termo_responsabilidade" value="Texto do Termo" />
                        <textarea id="termo_responsabilidade" name="termo_responsabilidade" rows="6"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">{{ old('termo_responsabilidade', $responsibility->termo_responsabilidade) }}</textarea>
                        <x-input-error :messages="$errors->get('termo_responsabilidade')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="assinado" value="0">
                        <input id="assinado" name="assinado" type="checkbox" value="1"
                            {{ old('assinado', $responsibility->assinado) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <x-input-label for="assinado" value="Termo assinado fisicamente" class="!mb-0" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('responsibilities.index') }}"
                       class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button>Salvar Alterações</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
