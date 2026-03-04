<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chamados</h2>
            <a href="{{ route('chamados.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Abrir Chamado
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <x-alert />

            {{-- Filtro de status --}}
            <form method="GET" class="flex gap-2 items-center flex-wrap">
                <span class="text-sm text-gray-500 font-medium">Filtrar:</span>
                @foreach($statusLabels as $value => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $value]) }}"
                       class="px-3 py-1 text-xs rounded-full border {{ request('status') === $value ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
                @if(request('status'))
                    <a href="{{ route('chamados.index') }}" class="text-xs text-gray-400 hover:underline">Limpar</a>
                @endif
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($chamados as $chamado)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 text-gray-500">{{ $chamado->id }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $chamado->funcionario?->nome ?? '—' }}</td>
                            <td class="px-6 py-3 font-mono text-gray-500">{{ $chamado->patrimonio?->codigo_patrimonio ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ Str::limit($chamado->descricao, 60) }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$chamado->status" type="chamado" /></td>
                            <td class="px-6 py-3 text-gray-500">{{ $chamado->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('chamados.show', $chamado) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">Nenhum chamado encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $chamados->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
