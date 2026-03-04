<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Departamento</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('departments.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="nome" value="Nome *" />
                        <x-text-input id="nome" name="nome" type="text"
                            class="mt-1 block w-full" :value="old('nome')" required autofocus />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="descricao" value="Descrição" />
                        <textarea id="descricao" name="descricao" rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                            placeholder="Descrição do departamento...">{{ old('descricao') }}</textarea>
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('departments.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Cadastrar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
