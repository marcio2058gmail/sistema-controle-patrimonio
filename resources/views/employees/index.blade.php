<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Funcionários</h2>
            <a href="{{ route('employees.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Novo Funcionário
            </a>
        </div>
    </x-slot>

    <div x-data="{ showDetail: false, detail: null, deleteTarget: null }"
         @keydown.escape.window="deleteTarget ? deleteTarget = null : showDetail = false">

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
                            @php $ed = ['id'=>$employee->id,'nome'=>$employee->nome,'email'=>$employee->email,'cargo'=>$employee->cargo,'dept'=>$employee->department?->nome,'created'=>$employee->created_at->format('d/m/Y'),'url_edit'=>route('employees.edit',$employee),'url_destroy'=>route('employees.destroy',$employee)]; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $employee->nome }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $employee->email }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $employee->cargo ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $employee->department?->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-right space-x-3">
                                    <button type="button" @click="detail = {{ Js::from($ed) }}; showDetail = true" class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-medium transition-colors">Ver detalhes</button>
                                    <a href="{{ route('employees.edit', $employee) }}" class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</a>
                                    <button type="button" @click="deleteTarget = {{ Js::from(['url'=>route('employees.destroy',$employee),'name'=>$employee->nome]) }}" class="text-red-600 hover:text-red-800 text-xs font-medium transition-colors">Excluir</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">Nenhum funcionário cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">{{ $employees->links() }}</div>
                </div>
            </div>
        </div>

        {{-- MODAL VER DETALHES --}}
        <div x-show="showDetail"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display:none">
            <div class="absolute inset-0" @click="showDetail = false"></div>
            <div x-show="showDetail"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="detail?.nome"></h3>
                    <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-gray-500">E-mail</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.email"></dd></div>
                        <div><dt class="text-gray-500">Cargo</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.cargo || '—'"></dd></div>
                        <div><dt class="text-gray-500">Departamento</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.dept || '—'"></dd></div>
                        <div><dt class="text-gray-500">Cadastrado em</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.created"></dd></div>
                    </dl>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <a :href="detail?.url_edit" class="px-4 py-2 text-sm font-medium rounded-lg border border-indigo-500 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">Editar</a>
                    <button type="button" @click="showDetail = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Fechar</button>
                </div>
            </div>
        </div>

        {{-- MODAL EXCLUIR --}}
        <div x-show="deleteTarget !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display:none">
            <div class="absolute inset-0" @click="deleteTarget = null"></div>
            <div x-show="deleteTarget !== null"
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Excluir Funcionário</p>
                        <p class="text-sm text-gray-500">Confirma a exclusão de <strong x-text="deleteTarget?.name"></strong>?</p>
                    </div>
                </div>
                <form :action="deleteTarget?.url" method="POST" class="flex justify-end gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Excluir</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
