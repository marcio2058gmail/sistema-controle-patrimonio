<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('subscriptions.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nova Assinatura</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <form method="POST" action="{{ route('subscriptions.store') }}" class="space-y-5">
                    @csrf

                    {{-- Empresa --}}
                    <div>
                        <label for="empresa_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Empresa <span class="text-red-500">*</span></label>
                        <select name="empresa_id" id="empresa_id" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('empresa_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->nome }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('empresa_id')" class="mt-1" />
                    </div>

                    {{-- Plano --}}
                    <div>
                        <label for="plano_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plano <span class="text-red-500">*</span></label>
                        @if($plans->isEmpty())
                            <p class="text-sm text-yellow-700 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg px-4 py-3">
                                Nenhum plano ativo disponível.
                                <a href="{{ route('plans.create') }}" class="underline">Criar plano →</a>
                            </p>
                        @else
                        <select name="plano_id" id="plano_id" required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Selecione um plano...</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plano_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->nome }} — R$ {{ number_format($plan->preco, 2, ',', '.') }} / mês
                                (até {{ number_format($plan->limite_patrimonios, 0, ',', '.') }} patrimônios)
                            </option>
                            @endforeach
                        </select>
                        @endif
                        <x-input-error :messages="$errors->get('plano_id')" class="mt-1" />
                    </div>

                    {{-- Botões --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('subscriptions.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit" {{ $plans->isEmpty() ? 'disabled' : '' }}
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition shadow-sm">
                            Assinar Plano
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
