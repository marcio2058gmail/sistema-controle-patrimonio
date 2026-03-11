<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('plans.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Plano</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('plans.store') }}" class="space-y-5">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nome do plano <span class="text-red-500">*</span></label>
                        <x-text-input name="nome" type="text" class="w-full" placeholder="Ex.: Básico, Pro, Enterprise"
                                      value="{{ old('nome') }}" required autofocus />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>

                    {{-- Limite de patrimônios --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Limite de patrimônios <span class="text-red-500">*</span></label>
                        <x-text-input name="limite_patrimonios" type="number" min="1" class="w-full"
                                      placeholder="Ex.: 100" value="{{ old('limite_patrimonios') }}" required />
                        <x-input-error :messages="$errors->get('limite_patrimonios')" class="mt-1" />
                    </div>

                    {{-- Preço --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Preço mensal (R$) <span class="text-red-500">*</span></label>
                        <x-text-input name="preco" type="number" min="0" step="0.01" class="w-full"
                                      placeholder="Ex.: 99.90" value="{{ old('preco', '0.00') }}" required />
                        <x-input-error :messages="$errors->get('preco')" class="mt-1" />
                    </div>

                    {{-- Ativo --}}
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="ativo" value="0">
                        <input type="checkbox" id="ativo" name="ativo" value="1"
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               {{ old('ativo', true) ? 'checked' : '' }}>
                        <label for="ativo" class="text-sm font-medium text-gray-700 dark:text-gray-300">Plano ativo (disponível para assinatura)</label>
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('plans.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-sm">
                            Criar Plano
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
