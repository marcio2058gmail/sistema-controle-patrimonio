<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Funcionários</h2>
            <a href="{{ route('employees.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + Novo Funcionário
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $employee->nome }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->email }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->cargo ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">
                                @if($employee->departamento)
                                    <a href="{{ route('departments.show', $employee->departamento) }}" class="text-indigo-600 hover:underline text-xs">
                                        {{ $employee->departamento->nome }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                                <a href="{{ route('employees.edit', $employee) }}" class="text-indigo-600 hover:underline text-xs">Editar</a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Confirmar exclusão?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">Nenhum funcionário cadastrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4">{{ $employees->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
