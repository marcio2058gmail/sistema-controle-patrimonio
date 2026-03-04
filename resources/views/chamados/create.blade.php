<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Abrir Chamado</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('chamados.store') }}" method="POST" class="space-y-5">
                    @csrf

                    @if(auth()->user()->isAdminOrGestor())
                    <div>
                        <x-input-label for="funcionario_id" value="Funcionário *" />
                        <select id="funcionario_id" name="funcionario_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Selecione...</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}" {{ old('funcionario_id') == $funcionario->id ? 'selected' : '' }}>
                                    {{ $funcionario->nome }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('funcionario_id')" class="mt-1" />
                    </div>
                    @endif

                    <div>
                        <x-input-label for="patrimonio_id" value="Patrimônio Solicitado (opcional)" />
                        <select id="patrimonio_id" name="patrimonio_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                            <option value="">Nenhum</option>
                            @foreach($patrimonios as $patrimonio)
                                <option value="{{ $patrimonio->id }}" {{ old('patrimonio_id') == $patrimonio->id ? 'selected' : '' }}>
                                    {{ $patrimonio->codigo_patrimonio }} — {{ $patrimonio->descricao }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Apenas patrimônios disponíveis são listados.</p>
                        <x-input-error :messages="$errors->get('patrimonio_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="descricao" value="Descrição *" />
                        <textarea id="descricao" name="descricao" rows="5"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                            required minlength="10" placeholder="Descreva o motivo do chamado...">{{ old('descricao') }}</textarea>
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('chamados.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Abrir Chamado</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
