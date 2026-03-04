<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Patrimônio</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('assets.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="codigo_patrimonio" value="Código de Patrimônio *" />
                        <x-text-input id="codigo_patrimonio" name="codigo_patrimonio" type="text"
                            class="mt-1 block w-full" :value="old('codigo_patrimonio')" required />
                        <x-input-error :messages="$errors->get('codigo_patrimonio')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="descricao" value="Descrição *" />
                        <x-text-input id="descricao" name="descricao" type="text"
                            class="mt-1 block w-full" :value="old('descricao')" required />
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="modelo" value="Modelo" />
                            <x-text-input id="modelo" name="modelo" type="text"
                                class="mt-1 block w-full" :value="old('modelo')" />
                            <x-input-error :messages="$errors->get('modelo')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="numero_serie" value="Número de Série" />
                            <x-text-input id="numero_serie" name="numero_serie" type="text"
                                class="mt-1 block w-full" :value="old('numero_serie')" />
                            <x-input-error :messages="$errors->get('numero_serie')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" value="Status *" />
                        <select id="status" name="status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'disponivel') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('assets.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Cadastrar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
