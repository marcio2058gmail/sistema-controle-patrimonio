<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('companies.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuários — {{ $company->nome }}
            </h2>
        </div>
    </x-slot>

    <div x-data="{ addOpen: false }" @keydown.escape.window="addOpen = false">
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <x-alert />

                {{-- Usuários actuais --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-medium text-gray-700 dark:text-gray-300">Membros</h3>
                        <button type="button" @click="addOpen = true"
                            class="px-3 py-1.5 text-xs bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            + Adicionar Usuário
                        </button>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Papel</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($company->users as $u)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $u->name }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $u->email }}</td>
                                <td class="px-6 py-3">
                                    @php $r = $u->pivot->role; @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full
                                        {{ $r === 'admin' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : ($r === 'manager' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ ucfirst($r) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <form method="POST" action="{{ route('companies.removeUser', $company) }}"
                                          onsubmit="return confirm('Remover {{ $u->name }} desta empresa?')">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $u->id }}">
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Remover</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">
                                    Nenhum usuário associado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- ======================== MODAL: Adicionar Usuário ======================== --}}
        <div x-show="addOpen" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none">
            <div @click.self="addOpen = false"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Adicionar Usuário</h3>
                    <button @click="addOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('companies.addUser', $company) }}" class="px-6 py-4 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="user_id" value="Usuário *" />
                        <select id="user_id" name="user_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 shadow-sm text-sm">
                            <option value="">— selecione —</option>
                            @foreach($allUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="role" value="Papel *" />
                        <select id="role" name="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 shadow-sm text-sm">
                            <option value="employee">Employee</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="addOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            Adicionar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
