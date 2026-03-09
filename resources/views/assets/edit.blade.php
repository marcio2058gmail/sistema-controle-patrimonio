<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Patrimônio — {{ $asset->codigo_patrimonio }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('assets.update', $asset) }}" method="POST" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <x-input-label for="codigo_patrimonio" value="Código de Patrimônio *" />
                        <x-text-input id="codigo_patrimonio" name="codigo_patrimonio" type="text"
                            class="mt-1 block w-full" :value="old('codigo_patrimonio', $asset->codigo_patrimonio)" required />
                        <x-input-error :messages="$errors->get('codigo_patrimonio')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="descricao" value="Descrição *" />
                        <x-text-input id="descricao" name="descricao" type="text"
                            class="mt-1 block w-full" :value="old('descricao', $asset->descricao)" required />
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="modelo" value="Modelo" />
                            <x-text-input id="modelo" name="modelo" type="text"
                                class="mt-1 block w-full" :value="old('modelo', $asset->modelo)" />
                        </div>
                        <div>
                            <x-input-label for="numero_serie" value="Número de Série" />
                            <x-text-input id="numero_serie" name="numero_serie" type="text"
                                class="mt-1 block w-full" :value="old('numero_serie', $asset->numero_serie)" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" value="Status *" />
                        <select id="status" name="status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $asset->status) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div>
                        <x-input-label for="empresa_id" value="Empresa *" />
                        <select id="empresa_id" name="empresa_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">— selecione —</option>
                            @foreach($companies as $co)
                                <option value="{{ $co->id }}" {{ old('empresa_id', $asset->empresa_id) == $co->id ? 'selected' : '' }}>{{ $co->nome }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('empresa_id')" class="mt-1" />
                    </div>
                    @endif

                    {{-- Dados de Aquisição --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Aquisição &amp; Valor</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="valor_aquisicao" value="Valor de Aquisição (R$)" />
                                <x-text-input id="valor_aquisicao" name="valor_aquisicao" type="number" step="0.01" min="0"
                                    class="mt-1 block w-full" :value="old('valor_aquisicao', $asset->valor_aquisicao)" placeholder="0,00" />
                                <x-input-error :messages="$errors->get('valor_aquisicao')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="data_aquisicao" value="Data de Aquisição" />
                                <x-text-input id="data_aquisicao" name="data_aquisicao" type="date"
                                    class="mt-1 block w-full" :value="old('data_aquisicao', $asset->data_aquisicao?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('data_aquisicao')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="fornecedor" value="Fornecedor" />
                                <x-text-input id="fornecedor" name="fornecedor" type="text"
                                    class="mt-1 block w-full" :value="old('fornecedor', $asset->fornecedor)" />
                                <x-input-error :messages="$errors->get('fornecedor')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="numero_nota_fiscal" value="Número da Nota Fiscal" />
                                <x-text-input id="numero_nota_fiscal" name="numero_nota_fiscal" type="text"
                                    class="mt-1 block w-full" :value="old('numero_nota_fiscal', $asset->numero_nota_fiscal)" />
                                <x-input-error :messages="$errors->get('numero_nota_fiscal')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="garantia_ate" value="Garantia Até" />
                                <x-text-input id="garantia_ate" name="garantia_ate" type="date"
                                    class="mt-1 block w-full" :value="old('garantia_ate', $asset->garantia_ate?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('garantia_ate')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="valor_atual" value="Valor Atual / Depreciado (R$)" />
                                <x-text-input id="valor_atual" name="valor_atual" type="number" step="0.01" min="0"
                                    class="mt-1 block w-full" :value="old('valor_atual', $asset->valor_atual)" placeholder="0,00" />
                                <x-input-error :messages="$errors->get('valor_atual')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('assets.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Salvar Alterações</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
