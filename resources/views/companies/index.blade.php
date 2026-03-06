<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Empresas</h2>
            <button type="button" @click="$dispatch('open-nova-empresa')"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                + Nova Empresa
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

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CNPJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionários</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônios</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($companies as $company)
                            @php
                                $ec = [
                                    'nome'     => $company->nome,
                                    'cnpj'     => $company->cnpj ?? '',
                                    'telefone' => $company->telefone ?? '',
                                    'email'    => $company->email ?? '',
                                    'ativa'    => $company->ativa,
                                    'url_update'  => route('companies.update', $company),
                                    'url_destroy' => route('companies.destroy', $company),
                                    'url_users'   => route('companies.users', $company),
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200 font-medium">{{ $company->nome }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $company->cnpj ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $company->email ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $company->employees_count }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $company->assets_count }}</td>
                                <td class="px-6 py-3">
                                    @if($company->ativa)
                                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-medium">Ativa</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-medium">Inativa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('companies.users', $company) }}"
                                       class="text-indigo-500 hover:text-indigo-700 text-xs font-medium transition-colors">Usuários</a>
                                    <button type="button" @click="openEdit({{ Js::from($ec) }})"
                                        class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</button>
                                    <button type="button"
                                        @click="deleteTarget = {{ Js::from(['url' => route('companies.destroy', $company), 'name' => $company->nome]) }}"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Remover</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                    Nenhuma empresa cadastrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $companies->links() }}</div>
            </div>
        </div>

        {{-- ======================== MODAL: Nova Empresa ======================== --}}
        <div x-show="modalOpen" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none">
            <div @click.self="modalOpen = false"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Nova Empresa</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('companies.store') }}" class="px-6 py-4 space-y-4">
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
                                value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00" />
                            <x-input-error :messages="$errors->get('cnpj')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="telefone" value="Telefone" />
                            <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
                                value="{{ old('telefone') }}" />
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
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg transition">
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
        <div x-show="editTarget" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none">
            <div x-show="editTarget" @click.self="editTarget = null"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">Editar Empresa</h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <template x-if="editTarget">
                    <form :action="editTarget.url_update" method="POST" class="px-6 py-4 space-y-4">
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
                                    x-model="editTarget.cnpj" placeholder="00.000.000/0000-00" />
                            </div>
                            <div>
                                <x-input-label value="Telefone" />
                                <x-text-input name="telefone" type="text" class="mt-1 block w-full"
                                    x-model="editTarget.telefone" />
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
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="editTarget = null"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg transition">
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
        <div x-show="deleteTarget" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="display:none">
            <div x-show="deleteTarget" @click.self="deleteTarget = null"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm p-6">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Remover Empresa</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Tem certeza que deseja remover <strong x-text="deleteTarget?.name"></strong>?
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="deleteTarget = null"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:text-gray-800 dark:hover:text-gray-200 transition">
                        Cancelar
                    </button>
                    <form :action="deleteTarget?.url" method="POST" x-show="deleteTarget">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
