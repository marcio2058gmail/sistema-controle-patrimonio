<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Selecionar Empresa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <p class="text-gray-600 dark:text-gray-400 mb-8 text-center">
                Escolha a empresa em que você deseja trabalhar nesta sessão.
            </p>

            @if($empresas->isEmpty())
                <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                    <p class="text-lg font-medium">Nenhuma empresa disponível</p>
                    <p class="text-sm mt-1">Contacte o administrador para obter acesso.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($empresas as $empresa)
                        <form method="POST" action="{{ route('companies.switch') }}">
                            @csrf
                            <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                            <button type="submit"
                                class="w-full text-left p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:border-indigo-500 hover:shadow-md transition-all duration-150 group">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">
                                            {{ $empresa->nome }}
                                        </p>
                                        @if($empresa->cnpj)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $empresa->cnpj }}</p>
                                        @endif
                                        @if($empresa->email)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">{{ $empresa->email }}</p>
                                        @endif
                                    </div>
                                    <svg class="h-5 w-5 text-gray-300 group-hover:text-indigo-500 shrink-0 ml-2 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>

                                @auth
                                    @if(!auth()->user()->isSuperAdmin())
                                        @php $roleLabel = $empresa->pivot?->role ?? '—'; @endphp
                                        <span class="mt-3 inline-block text-xs px-2 py-0.5 rounded-full
                                            {{ $roleLabel === 'admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : ($roleLabel === 'manager' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') }}">
                                            {{ ucfirst($roleLabel) }}
                                        </span>
                                    @else
                                        <span class="mt-3 inline-block text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                                            Super Admin
                                        </span>
                                    @endif
                                @endauth
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
