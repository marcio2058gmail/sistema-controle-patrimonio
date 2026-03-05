<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Termo de Responsabilidade</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <form action="{{ route('responsibilities.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Dados do Termo --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Dados do Termo</h3>

                    <div>
                        <x-input-label for="funcionario_id" value="Funcionário *" />
                        <select id="funcionario_id" name="funcionario_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Selecione o funcionário...</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('funcionario_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->nome }}@if($employee->cargo) — {{ $employee->cargo }}@endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('funcionario_id')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="data_entrega" value="Data de Entrega *" />
                            <x-text-input id="data_entrega" name="data_entrega" type="date"
                                class="mt-1 block w-full" :value="old('data_entrega', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('data_entrega')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="data_devolucao" value="Data de Devolução (opcional)" />
                            <x-text-input id="data_devolucao" name="data_devolucao" type="date"
                                class="mt-1 block w-full" :value="old('data_devolucao')" />
                            <x-input-error :messages="$errors->get('data_devolucao')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="termo_responsabilidade" value="Texto do Termo *" />
                        <textarea id="termo_responsabilidade" name="termo_responsabilidade" rows="5"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                            required minlength="20"
                            placeholder="Declaro que recebi os equipamentos listados abaixo e me responsabilizo pela sua guarda e conservação...">{{ old('termo_responsabilidade') }}</textarea>
                        <x-input-error :messages="$errors->get('termo_responsabilidade')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="assinado" value="0">
                        <input id="assinado" name="assinado" type="checkbox" value="1"
                            {{ old('assinado') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <x-input-label for="assinado" value="Termo assinado fisicamente" class="!mb-0" />
                    </div>
                </div>

                {{-- Seleção de Equipamentos --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6" x-data="{ search: '' }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            Equipamentos *
                            <span class="ml-2 text-xs font-normal text-gray-400 normal-case">Selecione um ou mais</span>
                        </h3>
                        <input type="text" x-model="search" placeholder="Filtrar..."
                            class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 w-52">
                    </div>

                    <x-input-error :messages="$errors->get('patrimonio_ids')" class="mb-3" />
                    <x-input-error :messages="$errors->get('patrimonio_ids.*')" class="mb-3" />

                    @if($assets->isEmpty())
                        <p class="text-sm text-gray-400 italic">Nenhum patrimônio disponível no momento.</p>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 max-h-72 overflow-y-auto">
                            @foreach($assets as $asset)
                            <label
                                x-show="search === '' || '{{ strtolower($asset->codigo_patrimonio . ' ' . $asset->descricao . ' ' . ($asset->modelo ?? '')) }}'.includes(search.toLowerCase())"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer transition-colors">
                                <input type="checkbox" name="patrimonio_ids[]" value="{{ $asset->id }}"
                                    {{ in_array($asset->id, (array) old('patrimonio_ids', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 shrink-0">
                                <span class="font-mono text-sm text-gray-700 dark:text-gray-300 w-24 shrink-0">{{ $asset->codigo_patrimonio }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200 flex-1">{{ $asset->descricao }}</span>
                                @if($asset->modelo)
                                <span class="text-xs text-gray-400">{{ $asset->modelo }}</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('responsibilities.index') }}"
                       class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <x-primary-button>Registrar Termo</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
