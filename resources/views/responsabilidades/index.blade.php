<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Responsabilidades</h2>
            <a href="{{ route('responsabilidades.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Nova Responsabilidade
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devolução</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assinado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($responsabilidades as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $r->funcionario->nome }}</td>
                            <td class="px-6 py-3 font-mono text-gray-600 dark:text-gray-400">{{ $r->patrimonio->codigo_patrimonio }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $r->data_entrega->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-500">
                                @if($r->data_devolucao)
                                    {{ $r->data_devolucao->format('d/m/Y') }}
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if($r->assinado)
                                    <span class="text-green-600 font-medium">Sim</span>
                                @else
                                    <span class="text-gray-400">Não</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('responsabilidades.show', $r) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                                <a href="{{ route('responsabilidades.pdf', $r) }}" class="text-gray-600 hover:underline text-xs" target="_blank">PDF</a>
                                <a href="{{ route('responsabilidades.edit', $r) }}" class="text-indigo-600 hover:underline text-xs">Editar</a>
                                <form action="{{ route('responsabilidades.destroy', $r) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Confirmar exclusão?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">Nenhuma responsabilidade registrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $responsabilidades->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
