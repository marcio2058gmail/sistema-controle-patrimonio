<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ auth()->user()->isAdmin() ? 'Patrimônios' : 'Patrimônios Disponíveis' }}
            </h2>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('patrimonios.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Novo Patrimônio
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            @if($apenasDisponiveis)
            <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-sm text-blue-700 dark:text-blue-300">
                Exibindo apenas patrimônios <strong>disponíveis</strong> para solicitação via chamado.
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº Série</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($patrimonios as $patrimonio)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $patrimonio->codigo_patrimonio }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $patrimonio->descricao }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $patrimonio->modelo ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $patrimonio->numero_serie ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <x-status-badge :status="$patrimonio->status" type="patrimonio" />
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('patrimonios.show', $patrimonio) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('patrimonios.edit', $patrimonio) }}" class="text-indigo-600 hover:underline text-xs">Editar</a>
                                <form action="{{ route('patrimonios.destroy', $patrimonio) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Confirmar exclusão?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Excluir</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">Nenhum patrimônio cadastrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $patrimonios->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
