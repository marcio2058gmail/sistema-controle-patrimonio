<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Assinaturas</h2>
            <a href="{{ route('subscriptions.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nova Assinatura
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-alert />

            @if($subscriptions->isEmpty())
            <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <p class="text-sm">Nenhuma assinatura cadastrada.</p>
                <a href="{{ route('subscriptions.create') }}" class="mt-3 inline-block text-indigo-500 hover:text-indigo-400 text-sm font-medium">Criar primeira assinatura →</a>
            </div>
            @else
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empresa</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Plano</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Início</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vencimento</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($subscriptions as $sub)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $sub->company->nome ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                {{ $sub->plan->nome ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                {{ $sub->inicio_em?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600 dark:text-gray-400">
                                {{ $sub->proximo_vencimento?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if(in_array($sub->status, ['active', 'trial']))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $sub->status_label }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">{{ $sub->status_label }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if(in_array($sub->status, ['active', 'trial']))
                                <form method="POST" action="{{ route('subscriptions.cancel', $sub->company) }}"
                                      onsubmit="return confirm('Cancelar assinatura de {{ addslashes($sub->company->nome) }}?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:underline font-medium">
                                        Cancelar
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($subscriptions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $subscriptions->links() }}
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
