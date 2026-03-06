<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administradores</h2>
            <button type="button" @click="$dispatch('open-novo-admin')"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Novo Admin
            </button>
        </div>
    </x-slot>

    <div x-data="{ modalOpen: {{ $errors->any() ? 'true' : 'false' }}, deleteTarget: null, editTarget: null, openEdit(d) { this.editTarget = d; } }"
         @keydown.escape.window="modalOpen ? modalOpen = false : deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : null"
         @open-novo-admin.window="modalOpen = true">

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cadastrado em</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($admins as $admin)
                            @php $ad = ['name'=>$admin->name,'email'=>$admin->email,'url_update'=>route('admins.update',$admin),'url_destroy'=>route('admins.destroy',$admin),'is_self'=>($admin->id===auth()->id())]; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $admin->name }}
                                    @if($admin->id === auth()->id())
                                        <span class="ml-1 text-xs text-indigo-500">(você)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $admin->email }}</td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">
                                    {{ $admin->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3 text-right space-x-3">
                                    <button type="button" @click="openEdit({{ Js::from($ad) }})"
                                        class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</button>
                                    @if($admin->id !== auth()->id())
                                    <button type="button"
                                        @click="deleteTarget = {{ Js::from(['url'=>route('admins.destroy',$admin),'name'=>$admin->name]) }}"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Remover</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-400 text-sm">Nenhum administrador encontrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($admins->hasPages())
                <div class="mt-4">{{ $admins->links() }}</div>
                @endif

            </div>
        </div>

        {{-- ===== MODAL EDITAR ADMIN ===== --}}
        <div x-show="editTarget !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             style="display:none">
            <div class="absolute inset-0" @click="editTarget = null"></div>
            <div x-show="editTarget !== null"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[80vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Editar Administrador</h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <template x-if="editTarget">
                        <form id="form-edit-admin" :action="editTarget.url_update" method="POST" class="space-y-4">
                            @csrf @method('PATCH')
                            <div>
                                <x-input-label for="edit_name" value="Nome *" />
                                <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" x-model="editTarget.name" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit_email" value="E-mail *" />
                                <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" x-model="editTarget.email" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                            <hr class="border-gray-200 dark:border-gray-700" />
                            <p class="text-xs text-gray-400">Deixe em branco para manter a senha atual.</p>
                            <div>
                                <x-input-label for="edit_password" value="Nova Senha" />
                                <x-text-input id="edit_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit_password_confirmation" value="Confirmar Nova Senha" />
                                <x-text-input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            </div>
                        </form>
                    </template>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="editTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" form="form-edit-admin" class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Salvar</button>
                </div>
            </div>
        </div>
        {{-- ===== FIM MODAL EDITAR ADMIN ===== --}}

        {{-- ===== MODAL EXCLUIR ADMIN ===== --}}
        <div x-show="deleteTarget !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             style="display:none">
            <div class="absolute inset-0" @click="deleteTarget = null"></div>
            <div x-show="deleteTarget !== null"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirmar remoção</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Tem certeza que deseja remover o administrador <strong x-text="deleteTarget?.name"></strong>? Esta ação não pode ser desfeita.
                </p>
                <template x-if="deleteTarget">
                    <form :action="deleteTarget.url" method="POST" class="flex justify-end gap-3">
                        @csrf @method('DELETE')
                        <button type="button" @click="deleteTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Remover</button>
                    </form>
                </template>
            </div>
        </div>
        {{-- ===== FIM MODAL EXCLUIR ADMIN ===== --}}

        {{-- ===== MODAL NOVO ADMIN ===== --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             style="display:none">
            <div class="absolute inset-0" @click="modalOpen = false"></div>
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[80vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Novo Administrador</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <form id="form-novo-admin" action="{{ route('admins.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="name" value="Nome *" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="email" value="E-mail *" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700" />
                        <div>
                            <x-input-label for="password" value="Senha *" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar Senha *" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" form="form-novo-admin" class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Cadastrar</button>
                </div>
            </div>
        </div>
        {{-- ===== FIM MODAL NOVO ADMIN ===== --}}

    </div>
</x-app-layout>
