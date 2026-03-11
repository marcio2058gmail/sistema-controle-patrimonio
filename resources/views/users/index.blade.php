<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Usuários</h2>
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <button type="button" @click="$dispatch('open-novo-usuario')"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Novo Usuário
            </button>
            @endif
        </div>
    </x-slot>

    <div x-data="{
            modalOpen: {{ $errors->any() ? 'true' : 'false' }},
            deleteTarget: null,
            editTarget: null,
            editRole: '{{ old('role', 'employee') }}',
            createRole: '{{ old('role', 'employee') }}',
            openEdit(d) {
                this.editTarget = d;
                this.editRole = d.role;
            }
         }"
         @keydown.escape.window="modalOpen ? modalOpen = false : deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : null"
         @open-novo-usuario.window="modalOpen = true">

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                {{-- Filtros --}}
                <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-3 mb-4">
                    @if(auth()->user()->isSuperAdmin() && $companies->isNotEmpty())
                    <select name="empresa_id"
                        class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg shadow-sm focus:ring-indigo-300">
                        <option value="">Todas as empresas</option>
                        @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ request('empresa_id') == $co->id ? 'selected' : '' }}>{{ $co->nome }}</option>
                        @endforeach
                    </select>
                    @endif
                    <select name="role"
                        class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-lg shadow-sm focus:ring-indigo-300">
                        <option value="">Todos os perfis</option>
                        <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Administrador</option>
                        <option value="manager"  {{ request('role') === 'manager'  ? 'selected' : '' }}>Gestor</option>
                        <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Funcionário</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 text-sm bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">Filtrar</button>
                    @if(request('empresa_id') || request('role'))
                        <a href="{{ route('users.index') }}" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Limpar</a>
                    @endif
                </form>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPF</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perfil</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departamento</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                            @php
                                $ud = [
                                    'id'           => $user->id,
                                    'name'         => $user->name,
                                    'email'        => $user->email,
                                    'cpf'          => $user->cpf ?? '',
                                    'role'         => $user->role,
                                    'cargo'        => $user->employee?->cargo ?? '',
                                    'rg_numero'    => $user->employee?->rg_numero ?? '',
                                    'ctps_numero'  => $user->employee?->ctps_numero ?? '',
                                    'ctps_serie'   => $user->employee?->ctps_serie ?? '',
                                    'dept_id'      => $user->employee?->departamento_id,
                                    'dept'         => $user->employee?->department?->nome ?? '',
                                    'empresa_ids'  => $user->empresas->pluck('id')->values()->toArray(),
                                    'empresa_nome' => $user->empresas->pluck('nome')->implode(', ') ?: '—',
                                    'is_self'      => $user->id === auth()->id(),
                                    'url_update'   => route('users.update', $user),
                                    'url_destroy'  => route('users.destroy', $user),
                                ];
                                $roleLabels = ['admin' => 'Administrador', 'manager' => 'Gestor', 'employee' => 'Funcionário'];
                                $roleColors = [
                                    'admin'    => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
                                    'manager'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                    'employee' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-indigo-400">(você)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs tracking-wide">{{ $user->cpf ?: '—' }}</td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-500' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $user->empresas->pluck('nome')->implode(', ') ?: '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $user->employee?->cargo ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $user->employee?->department?->nome ?? '—' }}</td>
                                <td class="px-6 py-3 text-right space-x-3 whitespace-nowrap">
                                    <button type="button" @click="openEdit({{ Js::from($ud) }})"
                                        class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</button>
                                    @if($user->id !== auth()->id())
                                    <button type="button"
                                        @click="deleteTarget = {{ Js::from(['url' => route('users.destroy', $user), 'name' => $user->name]) }}"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium transition-colors">Excluir</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>

        {{-- ==================== MODAL NOVO USUÁRIO ==================== --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display:none">
            <div class="absolute inset-0" @click="modalOpen = false"></div>
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Novo Usuário</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <form id="form-novo-usuario" action="{{ route('users.store') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="c_name" value="Nome *" />
                            <x-text-input id="c_name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        {{-- E-mail --}}
                        <div>
                            <x-input-label for="c_email" value="E-mail *" />
                            <x-text-input id="c_email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        {{-- Senha --}}
                        <div>
                            <x-input-label for="c_password" value="Senha *" />
                            <x-text-input id="c_password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="c_password_confirmation" value="Confirmar Senha *" />
                            <x-text-input id="c_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                        </div>

                        {{-- Perfil --}}
                        <div>
                            <x-input-label for="c_role" value="Perfil *" />
                            <select id="c_role" name="role" x-model="createRole" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                <option value="employee">Funcionário</option>
                                <option value="manager">Gestor</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-1" />
                        </div>

                        {{-- CPF --}}
                        <div>
                            <x-input-label for="c_cpf" value="CPF" />
                            <x-text-input id="c_cpf" name="cpf" type="text" class="mt-1 block w-full"
                                :value="old('cpf')" placeholder="000.000.000-00"
                                x-mask="999.999.999-99" />
                            <x-input-error :messages="$errors->get('cpf')" class="mt-1" />
                        </div>

                        {{-- RG, CTPS (somente manager/employee) --}}
                        <div x-show="createRole !== 'admin'" x-transition class="grid grid-cols-3 gap-3">
                            <div>
                                <x-input-label for="c_rg" value="RG" />
                                <x-text-input id="c_rg" name="rg_numero" type="text" class="mt-1 block w-full"
                                    :value="old('rg_numero')" placeholder="00.000.000-0" />
                                <x-input-error :messages="$errors->get('rg_numero')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="c_ctps" value="CTPS nº" />
                                <x-text-input id="c_ctps" name="ctps_numero" type="text" class="mt-1 block w-full"
                                    :value="old('ctps_numero')" placeholder="0000000"
                                    x-mask="9999999" />
                                <x-input-error :messages="$errors->get('ctps_numero')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="c_ctps_serie" value="Série" />
                                <x-text-input id="c_ctps_serie" name="ctps_serie" type="text" class="mt-1 block w-full"
                                    :value="old('ctps_serie')" placeholder="000-0"
                                    x-mask="999-9" />
                                <x-input-error :messages="$errors->get('ctps_serie')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Empresa (somente super_admin) --}}
                        @if(auth()->user()->isSuperAdmin())
                        {{-- Multi-select: Administrador pode pertencer a várias empresas --}}
                        <div x-show="createRole === 'admin'" x-transition>
                            <x-input-label for="c_empresa_ids" value="Empresas *" />
                            <select id="c_empresa_ids" name="empresa_ids[]" multiple
                                :disabled="createRole !== 'admin'"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300 h-28">
                                @foreach($companiesForForm as $co)
                                    <option value="{{ $co->id }}" {{ in_array($co->id, (array) old('empresa_ids')) ? 'selected' : '' }}>{{ $co->nome }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Segure Ctrl/⌘ para selecionar mais de uma empresa.</p>
                            <x-input-error :messages="$errors->get('empresa_ids')" class="mt-1" />
                        </div>
                        {{-- Single-select: Gestor / Funcionário pertence a uma única empresa --}}
                        <div x-show="createRole !== 'admin'" x-transition>
                            <x-input-label for="c_empresa_id" value="Empresa *" />
                            <select id="c_empresa_id" name="empresa_ids[]"
                                :disabled="createRole === 'admin'"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                <option value="">— selecione —</option>
                                @foreach($companiesForForm as $co)
                                    <option value="{{ $co->id }}" {{ (old('empresa_ids.0') ?? '') == $co->id ? 'selected' : '' }}>{{ $co->nome }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('empresa_ids')" class="mt-1" />
                        </div>
                        @endif

                        {{-- Cargo e Departamento (somente para manager/employee) --}}
                        <div x-show="createRole !== 'admin'" x-transition class="space-y-4">
                            <div>
                                <x-input-label for="c_cargo" value="Cargo" />
                                <x-text-input id="c_cargo" name="cargo" type="text" class="mt-1 block w-full"
                                    :value="old('cargo')" />
                                <x-input-error :messages="$errors->get('cargo')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="c_departamento_id" value="Departamento" />
                                <select id="c_departamento_id" name="departamento_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    <option value="">Sem departamento</option>
                                    @foreach($departments as $dep)
                                        <option value="{{ $dep->id }}" {{ old('departamento_id') == $dep->id ? 'selected' : '' }}>
                                            {{ $dep->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('departamento_id')" class="mt-1" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" form="form-novo-usuario"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Cadastrar</button>
                </div>
            </div>
        </div>

        {{-- ==================== MODAL EDITAR ==================== --}}
        <div x-show="editTarget !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display:none">
            <div class="absolute inset-0" @click="editTarget = null"></div>
            <div x-show="editTarget !== null"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                        x-text="'Editar — ' + (editTarget?.name ?? '')"></h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="editTarget !== null">
                    <form :action="editTarget.url_update" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf @method('PATCH')
                        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                            {{-- Nome --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                <input type="text" name="name" :value="editTarget.name" required
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>

                            {{-- E-mail --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail *</label>
                                <input type="email" name="email" :value="editTarget.email" required
                                    autocomplete="off"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>

                            {{-- Nova Senha (opcional) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nova Senha <span class="text-gray-400 font-normal">(deixe em branco para manter)</span></label>
                                <input type="password" name="password"
                                    autocomplete="new-password"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation"
                                    autocomplete="new-password"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>

                            {{-- CPF --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CPF</label>
                                <input type="text" name="cpf" :value="editTarget.cpf"
                                    placeholder="000.000.000-00"
                                    x-mask="999.999.999-99"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>

                            {{-- RG, CTPS (somente manager/employee) --}}
                            <div x-show="editRole !== 'admin'" x-transition class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">RG</label>
                                    <input type="text" name="rg_numero" :value="editTarget.rg_numero"
                                        placeholder="00.000.000-0"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CTPS nº</label>
                                    <input type="text" name="ctps_numero" :value="editTarget.ctps_numero"
                                        placeholder="0000000"
                                        x-mask="9999999"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Série</label>
                                    <input type="text" name="ctps_serie" :value="editTarget.ctps_serie"
                                        placeholder="000-0"
                                        x-mask="999-9"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                            </div>

                            {{-- Perfil --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perfil *</label>
                                <select name="role" x-model="editRole"
                                    x-init="editRole = editTarget.role"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    <option value="employee">Funcionário</option>
                                    <option value="manager">Gestor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>

                            {{-- Empresa (somente super_admin) --}}
                            @if(auth()->user()->isSuperAdmin())
                            {{-- Multi-select: Administrador pode pertencer a várias empresas --}}
                            <div x-show="editRole === 'admin'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresas *</label>
                                <select name="empresa_ids[]" multiple
                                    :disabled="editRole !== 'admin'"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300 h-28">
                                    @foreach($companiesForForm as $co)
                                        <option value="{{ $co->id }}"
                                            :selected="editTarget && editTarget.empresa_ids.includes({{ $co->id }})">{{ $co->nome }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Segure Ctrl/⌘ para selecionar mais de uma empresa.</p>
                            </div>
                            {{-- Single-select: Gestor / Funcionário pertence a uma única empresa --}}
                            <div x-show="editRole !== 'admin'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Empresa *</label>
                                <select name="empresa_ids[]"
                                    :disabled="editRole === 'admin'"
                                    x-init="$nextTick(() => { $el.value = editTarget.empresa_ids[0] ?? '' })"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    <option value="">— selecione —</option>
                                    @foreach($companiesForForm as $co)
                                        <option value="{{ $co->id }}">{{ $co->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Cargo e Departamento --}}
                            <div x-show="editRole !== 'admin'" x-transition class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cargo</label>
                                    <input type="text" name="cargo" :value="editTarget.cargo"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                                    <select name="departamento_id"
                                        x-init="$nextTick(() => { $el.value = editTarget.dept_id ?? '' })"
                                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                        <option value="">Sem departamento</option>
                                        @foreach($departments as $dep)
                                            <option value="{{ $dep->id }}">{{ $dep->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                            <button type="button" @click="editTarget = null"
                                class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Salvar</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

        {{-- ==================== MODAL EXCLUIR ==================== --}}
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
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Excluir Usuário</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Confirma a exclusão de <strong x-text="deleteTarget?.name"></strong>?
                            Esta ação não pode ser desfeita.
                        </p>
                    </div>
                </div>
                <form :action="deleteTarget?.url" method="POST" class="flex justify-end gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteTarget = null"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Excluir</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
