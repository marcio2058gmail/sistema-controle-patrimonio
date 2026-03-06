<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Empresas</h2>
            <button type="button" @click="$dispatch('open-nova-empresa')"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nova Empresa
            </button>
        </div>
    </x-slot>

    <div x-data="{
            modalOpen: {{ $errors->any() ? 'true' : 'false' }},
            deleteTarget: null,
            editTarget: null,
            openEdit(d) { this.editTarget = d; }
         }"
         @keydown.escape.window="modalOpen ? modalOpen = false : deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : null"
         @open-nova-empresa.window="modalOpen = true">

        <div class="py-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                @if($companies->isEmpty())
                <div class="text-center py-20 text-gray-400 dark:text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                    <p class="text-sm">Nenhuma empresa cadastrada.</p>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($companies as $company)
                    @php
                        $ec = [
                            'nome'        => $company->nome,
                            'cnpj'        => $company->cnpj ?? '',
                            'telefone'    => $company->telefone ?? '',
                            'email'       => $company->email ?? '',
                            'ativa'       => $company->ativa,
                            'url_update'  => route('companies.update', $company),
                            'url_destroy' => route('companies.destroy', $company),
                            'url_users'   => route('companies.users', $company),
                        ];
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col overflow-hidden hover:shadow-md transition-shadow">

                        {{-- Header do card --}}
                        <div class="px-5 pt-5 pb-4 flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                                    <svg class="h-5 w-5 text-pink-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $company->nome }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $company->cnpj ?? 'CNPJ não informado' }}</p>
                                </div>
                            </div>
                            @if($company->ativa)
                                <span class="shrink-0 px-2.5 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-semibold">Ativa</span>
                            @else
                                <span class="shrink-0 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-semibold">Inativa</span>
                            @endif
                        </div>

                        {{-- Informações de contato --}}
                        <div class="px-5 pb-4 space-y-1.5">
                            @if($company->email)
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                                <span class="truncate">{{ $company->email }}</span>
                            </div>
                            @endif
                            @if($company->telefone)
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                </svg>
                                <span>{{ $company->telefone }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-100 dark:border-gray-700 flex gap-4">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                                <span><strong class="text-gray-700 dark:text-gray-300">{{ $company->employees_count }}</strong> funcionários</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                                </svg>
                                <span><strong class="text-gray-700 dark:text-gray-300">{{ $company->assets_count }}</strong> patrimônios</span>
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="px-5 py-3 flex items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('companies.users', $company) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                                Usuários
                            </a>
                            <button type="button" @click="openEdit({{ Js::from($ec) }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                </svg>
                                Editar
                            </button>
                            <button type="button"
                                @click="deleteTarget = {{ Js::from(['url' => route('companies.destroy', $company), 'name' => $company->nome]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                Remover
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6">{{ $companies->links() }}</div>
                @endif
            </div>
        </div>

        {{-- ======================== MODAL: Nova Empresa ======================== --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" style="display:none">
            <div class="absolute inset-0" @click="modalOpen = false"></div>
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Nova Empresa</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('companies.store') }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="nome" value="Nome *" />
                        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                            value="{{ old('nome') }}" required autofocus />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="cnpj" value="CNPJ" />
                            <x-text-input id="cnpj" name="cnpj" type="text" class="mt-1 block w-full"
                                value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00"
                                x-mask="99.999.999/9999-99" />
                            <x-input-error :messages="$errors->get('cnpj')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="telefone" value="Telefone" />
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
                                value="{{ old('telefone') }}" placeholder="(00) 00000-0000"
                                x-mask:dynamic="$input.replace(/\D/g,'').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            value="{{ old('email') }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="ativa" name="ativa" value="1" checked
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700">
                        <x-input-label for="ativa" value="Empresa ativa" class="mb-0" />
                    </div>
                    <div class="flex justify-end gap-3 pt-1">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======================== MODAL: Editar Empresa ======================== --}}
        <div x-show="editTarget"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" style="display:none">
            <div class="absolute inset-0" @click="editTarget = null"></div>
            <div x-show="editTarget"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200" x-text="'Editar — ' + (editTarget?.nome ?? '')"></h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <template x-if="editTarget">
                    <form :action="editTarget.url_update" method="POST" class="px-6 py-5 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label value="Nome *" />
                            <x-text-input name="nome" type="text" class="mt-1 block w-full"
                                x-model="editTarget.nome" required />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label value="CNPJ" />
                                <x-text-input name="cnpj" type="text" class="mt-1 block w-full"
                                    x-model="editTarget.cnpj" placeholder="00.000.000/0000-00"
                                    x-mask="99.999.999/9999-99" />
                            </div>
                            <div>
                                <x-input-label value="Telefone" />
                                <x-text-input name="telefone" type="text" class="mt-1 block w-full"
                                    x-model="editTarget.telefone" placeholder="(00) 00000-0000"
                                    x-mask:dynamic="$input.replace(/\D/g,'').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'" />
                            </div>
                        </div>
                        <div>
                            <x-input-label value="E-mail" />
                            <x-text-input name="email" type="email" class="mt-1 block w-full"
                                x-model="editTarget.email" />
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="ativa" value="1"
                                   :checked="editTarget.ativa"
                                   @change="editTarget.ativa = $event.target.checked"
                                   class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700">
                            <x-input-label value="Empresa ativa" class="mb-0" />
                        </div>
                        <div class="flex justify-end gap-3 pt-1">
                            <button type="button" @click="editTarget = null"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                                Salvar
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

        {{-- ======================== MODAL: Confirmação de Remoção ======================== --}}
        <div x-show="deleteTarget"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" style="display:none">
            <div class="absolute inset-0" @click="deleteTarget = null"></div>
            <div x-show="deleteTarget"
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Remover Empresa</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Tem certeza que deseja remover <strong x-text="deleteTarget?.name"></strong>?
                        </p>
                    </div>
                </div>
                <form :action="deleteTarget?.url" method="POST" class="flex justify-end gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteTarget = null"
                        class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        Remover
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

