<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.subscriptions.index') }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nova Assinatura</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <form method="POST" action="{{ route('admin.subscriptions.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa</label>
                            <select name="empresa_id" required
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecione a empresa</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('empresa_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->nome }}
                                </option>
                                @endforeach
                            </select>
                            @error('empresa_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plano</label>
                            <select name="plano_id" required
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecione o plano</option>
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('plano_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->nome }} — R$ {{ number_format($plan->preco, 2, ',', '.') }} / {{ $plan->limite_patrimonios }} patrimônios
                                </option>
                                @endforeach
                            </select>
                            @error('plano_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                                Criar Assinatura
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
