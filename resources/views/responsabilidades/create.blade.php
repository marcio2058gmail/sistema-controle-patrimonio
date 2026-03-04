<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nova Responsabilidade</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('responsabilidades.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="funcionario_id" value="Funcionário *" />
                        <select id="funcionario_id" name="funcionario_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Selecione o funcionário...</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('funcionario_id') == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nome }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('funcionario_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="patrimonio_id" value="Patrimônio *" />
                        <select id="patrimonio_id" name="patrimonio_id" required
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Selecione o patrimônio disponível...</option>
                            @foreach($patrimonios as $patrimonio)
                                <option value="{{ $patrimonio->id }}" {{ old('patrimonio_id') == $patrimonio->id ? 'selected' : '' }}>
                                    {{ $patrimonio->codigo_patrimonio }} — {{ $patrimonio->descricao }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('patrimonio_id')" class="mt-1" />
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
                        <x-input-label for="termo_responsabilidade" value="Termo de Responsabilidade *" />
                        <textarea id="termo_responsabilidade" name="termo_responsabilidade" rows="6"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                            required minlength="20"
                            placeholder="Declaro que recebi o patrimônio descrito acima e me responsabilizo pelo mesmo...">{{ old('termo_responsabilidade') }}</textarea>
                        <x-input-error :messages="$errors->get('termo_responsabilidade')" class="mt-1" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="assinado" value="0">
                        <input id="assinado" name="assinado" type="checkbox" value="1"
                            {{ old('assinado') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <x-input-label for="assinado" value="Termo assinado fisicamente" class="!mb-0" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('responsabilidades.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Registrar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
