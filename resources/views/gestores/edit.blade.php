<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Gestor — {{ $gestor->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('gestores.update', $gestor) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    {{-- Nome --}}
                    <div>
                        <x-input-label for="name" value="Nome completo *" />
                        <x-text-input id="name" name="name" type="text"
                            class="mt-1 block w-full" :value="old('name', $gestor->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- E-mail --}}
                    <div>
                        <x-input-label for="email" value="E-mail *" />
                        <x-text-input id="email" name="email" type="email"
                            class="mt-1 block w-full" :value="old('email', $gestor->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Cargo --}}
                    <div>
                        <x-input-label for="cargo" value="Cargo" />
                        <x-text-input id="cargo" name="cargo" type="text"
                            class="mt-1 block w-full"
                            :value="old('cargo', $gestor->funcionario?->cargo ?? 'Gestor')" placeholder="Gestor" />
                        <x-input-error :messages="$errors->get('cargo')" class="mt-1" />
                    </div>

                    {{-- Departamento --}}
                    <div>
                        <x-input-label for="departamento_id" value="Departamento responsável" />
                        <select id="departamento_id" name="departamento_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Sem departamento</option>
                            @foreach($departamentos as $dep)
                                @php
                                    $selected = old('departamento_id', $gestor->funcionario?->departamento_id) == $dep->id;
                                @endphp
                                <option value="{{ $dep->id }}" {{ $selected ? 'selected' : '' }}>
                                    {{ $dep->nome }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('departamento_id')" class="mt-1" />
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700" />

                    <p class="text-xs text-gray-400">Deixe os campos de senha em branco para manter a senha atual.</p>

                    {{-- Senha --}}
                    <div>
                        <x-input-label for="password" value="Nova senha" />
                        <x-text-input id="password" name="password" type="password"
                            class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Confirmar senha --}}
                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar nova senha" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" autocomplete="new-password" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('gestores.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
