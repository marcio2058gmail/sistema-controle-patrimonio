<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Funcionário</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('employees.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="nome" value="Nome *" />
                        <x-text-input id="nome" name="nome" type="text"
                            class="mt-1 block w-full" :value="old('nome')" required autofocus />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="email" value="E-mail *" />
                        <x-text-input id="email" name="email" type="email"
                            class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="cargo" value="Cargo" />
                        <x-text-input id="cargo" name="cargo" type="text"
                            class="mt-1 block w-full" :value="old('cargo')" />
                        <x-input-error :messages="$errors->get('cargo')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="departamento_id" value="Departamento" />
                        <select id="departamento_id" name="departamento_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Sem departamento</option>
                            @foreach($departments as $dep)
                                <option value="{{ $dep->id }}" {{ old('departamento_id') == $dep->id ? 'selected' : '' }}>
                                    {{ $dep->nome }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('departamento_id')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('employees.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Cadastrar</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
