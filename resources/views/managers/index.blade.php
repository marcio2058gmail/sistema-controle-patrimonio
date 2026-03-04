<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestores</h2>
            <a href="{{ route('managers.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Novo Gestor
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        @forelse($managers as $manager)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200 font-medium">
                                {{ $manager->name }}
                            </td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                {{ $manager->email }}
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">
                                {{ $manager->employee?->cargo ?? '—' }}
                            </td>
                            <td class="px-6 py-3">
                                @if($manager->employee?->department)
                                    <a href="{{ route('departments.show', $manager->employee->department) }}"
                                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 hover:underline">
                                        {{ $manager->employee->department->nome }}
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Sem departamento</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('managers.edit', $manager) }}"
                                   class="text-indigo-600 hover:underline text-xs font-medium">Editar</a>

                                @if(auth()->user()->isAdmin())
                                <form action="{{ route('managers.destroy', $manager) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Remover gestor {{ addslashes($manager->name) }}? O registro de funcionário vinculado também será excluído.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs font-medium">
                                        Remover
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                Nenhum gestor cadastrado. <a href="{{ route('managers.create') }}" class="text-indigo-600 hover:underline">Criar o primeiro</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($managers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $managers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
