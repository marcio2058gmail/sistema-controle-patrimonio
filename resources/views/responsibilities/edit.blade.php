<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Responsabilidade #{{ $responsibility->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-4">
                <p class="text-sm text-gray-500">
                    Funcionário: <strong class="text-gray-800 dark:text-gray-200">{{ $responsibility->funcionario->nome }}</strong>
                    &nbsp;|&nbsp;
                    Patrimônio: <strong class="text-gray-800 dark:text-gray-200 font-mono">{{ $responsibility->patrimonio->codigo_patrimonio }}</strong>
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('responsibilities.update', $responsibility) }}" method="POST" class="space-y-5">
                    @csrf @method('PATCH')

                    <div>
                        <x-input-label for="data_devolucao" value="Data de Devolução (deixe vazio se ainda ativo)" />
                        <x-text-input id="data_devolucao" name="data_devolucao" type="date"
                            class="mt-1 block w-full"
                            :value="old('data_devolucao', $responsibility->data_devolucao?->toDateString())" />
                        <x-input-error :messages="$errors->get('data_devolucao')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="termo_responsabilidade" value="Termo de Responsabilidade" />
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

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('responsibilities.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Salvar Alterações</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
