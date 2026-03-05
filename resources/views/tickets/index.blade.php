<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chamados</h2>
            <button type="button" x-data @click="$dispatch('open-ticket-modal')"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Abrir Chamado
            </button>
        </div>
    </x-slot>

    {{-- Wrapper Alpine --}}
    <div x-data="{
            modalOpen: {{ $errors->any() ? 'true' : 'false' }},
            showDetail: false,
            detail: null,
            confirm: null,
            badgeClass(status, type) {
                const map = {
                    chamado:    { aberto: 'bg-yellow-100 text-yellow-800', aprovado: 'bg-blue-100 text-blue-800', negado: 'bg-red-100 text-red-800', entregue: 'bg-green-100 text-green-800' },
                    patrimonio: { disponivel: 'bg-green-100 text-green-800', em_uso: 'bg-blue-100 text-blue-800', manutencao: 'bg-yellow-100 text-yellow-800' }
                };
                return (map[type]?.[status]) ?? 'bg-gray-100 text-gray-800';
            },
            badgeLabel(status, type) {
                const map = {
                    chamado:    { aberto: 'Aberto', aprovado: 'Aprovado', negado: 'Negado', entregue: 'Entregue' },
                    patrimonio: { disponivel: 'Disponível', em_uso: 'Em Uso', manutencao: 'Manutenção' }
                };
                return (map[type]?.[status]) ?? status;
            },
            openDetail(data) { this.detail = data; this.confirm = null; this.showDetail = true; },
            initSearch() {
                const input   = document.getElementById('busca-patrimonio');
                const items   = document.querySelectorAll('.patrimonio-item');
                const counter = document.getElementById('contador-patrimonio');
                function updateCounter() {
                    const n = document.querySelectorAll('.patrimonio-item input:checked').length;
                    counter.textContent = n > 0 ? n + ' patrimônio(s) selecionado(s)' : '';
                }
                input?.addEventListener('input', function () {
                    const term = this.value.toLowerCase();
                    items.forEach(i => { i.style.display = i.dataset.busca.includes(term) ? '' : 'none'; });
                });
                items.forEach(i => i.querySelector('input')?.addEventListener('change', updateCounter));
                updateCounter();
            }
         }"
         @open-ticket-modal.window="modalOpen = true"
         x-init="if (modalOpen) $nextTick(() => initSearch()); $watch('modalOpen', v => { if (v) $nextTick(() => initSearch()) })">

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
                <x-alert />

                {{-- Filtro de status --}}
                <div class="flex gap-2 items-center flex-wrap">
                    <span class="text-sm text-gray-500 font-medium">Filtrar:</span>
                    @foreach($statusLabels as $value => $label)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $value]) }}"
                           class="px-3 py-1 text-xs rounded-full border {{ request('status') === $value ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                    @if(request('status'))
                        <a href="{{ route('tickets.index') }}" class="text-xs text-gray-400 hover:underline">Limpar</a>
                    @endif
                </div>

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
                            @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 text-gray-500">{{ $ticket->id }}</td>
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $ticket->employee?->nome ?? '—' }}</td>
                                <td class="px-6 py-3 font-mono text-gray-500 text-xs">
                                    @if($ticket->assets->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        {{ $ticket->assets->pluck('codigo_patrimonio')->implode(', ') }}
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ Str::limit($ticket->descricao, 60) }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$ticket->status" type="chamado" /></td>
                                <td class="px-6 py-3 text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @php
                                    $td = [
                                        'id'          => $ticket->id,
                                        'employee'    => $ticket->employee?->nome ?? '—',
                                        'status'      => $ticket->status,
                                        'descricao'   => $ticket->descricao,
                                        'created_at'  => $ticket->created_at->format('d/m/Y H:i'),
                                        'assets'      => $ticket->assets->map(fn($a) => [
                                            'code'   => $a->codigo_patrimonio,
                                            'desc'   => $a->descricao,
                                            'status' => $a->status,
                                        ])->values()->all(),
                                        'is_admin'    => auth()->user()->isAdmin(),
                                        'can_aprovar' => $ticket->status === 'aberto'  && auth()->user()->isAdmin(),
                                        'can_negar'   => $ticket->status === 'aberto'  && auth()->user()->isAdmin(),
                                        'can_entregar'=> $ticket->status === 'aprovado' && auth()->user()->isAdmin(),
                                        'url_aprovar' => route('tickets.aprovar', $ticket),
                                        'url_negar'   => route('tickets.negar',   $ticket),
                                        'url_entregar'=> route('tickets.entregar', $ticket),
                                    ];
                                    @endphp
                                    <button type="button"
                                            @click="openDetail({{ Js::from($td) }})"
                                            class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-medium transition-colors">
                                        Ver detalhes
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400">Nenhum chamado encontrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">{{ $tickets->links() }}</div>
                </div>
            </div>
        </div>

        {{-- ===== MODAL ABRIR CHAMADO ===== --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             @keydown.escape.window="modalOpen = false"
             style="display:none">

            <div class="absolute inset-0" @click="modalOpen = false"></div>

            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Abrir Chamado</h3>
                    <button @click="modalOpen = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corpo com scroll --}}
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <form id="form-novo-chamado"
                          action="{{ route('tickets.store') }}"
                          method="POST"
                          class="space-y-5">
                        @csrf

                        @if(auth()->user()->isAdminOrManager())
                        <div>
                            <x-input-label for="funcionario_id"
                                value="{{ auth()->user()->isManager() ? 'Funcionário do Departamento *' : 'Funcionário *' }}" />
                            @if(auth()->user()->isManager())
                                <p class="mt-0.5 mb-1 text-xs text-gray-400">Selecione o membro da sua equipe ou você mesmo.</p>
                            @endif
                            <select id="funcionario_id" name="funcionario_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                                <option value="">Selecione...</option>
                                @foreach($employees as $func)
                                    <option value="{{ $func->id }}" {{ old('funcionario_id') == $func->id ? 'selected' : '' }}>
                                        {{ $func->nome }}
                                        @if($func->cargo) — {{ $func->cargo }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('funcionario_id')" class="mt-1" />
                        </div>
                        @endif

                        <div>
                            <x-input-label value="Patrimônios Solicitados (opcional)" />
                            <p class="mt-0.5 mb-2 text-xs text-gray-400">Apenas patrimônios disponíveis são listados. Selecione um ou mais.</p>
                            <input type="text" id="busca-patrimonio" placeholder="Buscar por código ou descrição..."
                                class="mb-2 block w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300" />
                            <div class="border border-gray-300 dark:border-gray-600 rounded-md overflow-y-auto max-h-44 divide-y divide-gray-100 dark:divide-gray-700"
                                 id="lista-patrimonios">
                                @forelse($assets as $asset)
                                <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer patrimonio-item"
                                       data-busca="{{ strtolower($asset->codigo_patrimonio . ' ' . $asset->descricao) }}">
                                    <input type="checkbox" name="patrimonio_ids[]"
                                        value="{{ $asset->id }}"
                                        {{ in_array($asset->id, old('patrimonio_ids', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-sm text-gray-800 dark:text-gray-200">
                                        <span class="font-mono font-medium">{{ $asset->codigo_patrimonio }}</span>
                                        <span class="text-gray-500"> — {{ $asset->descricao }}</span>
                                        @if($asset->modelo)
                                            <span class="text-xs text-gray-400 ml-1">({{ $asset->modelo }})</span>
                                        @endif
                                    </span>
                                </label>
                                @empty
                                <p class="px-4 py-4 text-sm text-gray-400 text-center">Nenhum patrimônio disponível no momento.</p>
                                @endforelse
                            </div>
                            <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-400" id="contador-patrimonio"></p>
                            <x-input-error :messages="$errors->get('patrimonio_ids')" class="mt-1" />
                            <x-input-error :messages="$errors->get('patrimonio_ids.*')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="descricao" value="Descrição *" />
                            <textarea id="descricao" name="descricao" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                                required minlength="10" placeholder="Descreva o motivo do chamado...">{{ old('descricao') }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="modalOpen = false"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" form="form-novo-chamado"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                        Abrir Chamado
                    </button>
                </div>
            </div>
        </div>
        {{-- ===== FIM MODAL ABRIR CHAMADO ===== --}}

        {{-- ===== MODAL VER CHAMADO ===== --}}
        <div x-show="showDetail"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             @keydown.escape.window="confirm ? confirm = null : showDetail = false"
             style="display:none">

            <div class="absolute inset-0" @click="confirm ? confirm = null : showDetail = false"></div>

            {{-- Painel principal --}}
            <div x-show="showDetail"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Chamado <span x-text="'#' + detail?.id"></span>
                        </h3>
                        <span x-show="detail"
                              :class="badgeClass(detail?.status, 'chamado')"
                              x-text="badgeLabel(detail?.status, 'chamado')"
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        </span>
                    </div>
                    <button @click="showDetail = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Corpo --}}
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                    {{-- Info grid --}}
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Funcionário</dt>
                            <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.employee"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Aberto em</dt>
                            <dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.created_at"></dd>
                        </div>
                    </dl>

                    {{-- Patrimônios --}}
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400 mb-2">Patrimônio(s)</dt>
                        <template x-if="detail?.assets?.length">
                            <ul class="space-y-1.5">
                                <template x-for="a in detail.assets" :key="a.code">
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="font-mono font-medium text-gray-800 dark:text-gray-200" x-text="a.code"></span>
                                        <span class="text-gray-400">—</span>
                                        <span class="text-gray-600 dark:text-gray-300" x-text="a.desc"></span>
                                        <span :class="badgeClass(a.status, 'patrimonio')"
                                              x-text="badgeLabel(a.status, 'patrimonio')"
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-auto shrink-0">
                                        </span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="!detail?.assets?.length">
                            <span class="text-sm text-gray-400">—</span>
                        </template>
                    </div>

                    {{-- Descrição --}}
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400 mb-1">Descrição</dt>
                        <dd class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="detail?.descricao"></dd>
                    </div>

                    {{-- Ações (admin) --}}
                    <template x-if="detail?.is_admin && (detail?.can_aprovar || detail?.can_negar || detail?.can_entregar)">
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ações</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-if="detail?.can_aprovar">
                                    <button type="button" @click="confirm = 'aprovar'"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                                        ✔ Aprovar
                                    </button>
                                </template>
                                <template x-if="detail?.can_negar">
                                    <button type="button" @click="confirm = 'negar'"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors">
                                        ✖ Negar
                                    </button>
                                </template>
                                <template x-if="detail?.can_entregar">
                                    <button type="button" @click="confirm = 'entregar'"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                                        📦 Registrar Entrega
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Formulários ocultos das ações --}}
                    <form id="form-det-aprovar"  :action="detail?.url_aprovar"  method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"></form>
                    <form id="form-det-negar"    :action="detail?.url_negar"    method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"></form>
                    <form id="form-det-entregar" :action="detail?.url_entregar" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"></form>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="showDetail = false"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Fechar
                    </button>
                </div>
            </div>

            {{-- Sub-modal de confirmação --}}
            <template x-if="confirm !== null">
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/40" @click="confirm = null"></div>

                    {{-- Aprovar --}}
                    <div x-show="confirm === 'aprovar'"
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100">Aprovar Chamado</p>
                                <p class="text-sm text-gray-500">Confirma a aprovação do chamado <span x-text="'#' + detail?.id"></span>?</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="confirm = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                            <button type="submit" form="form-det-aprovar" class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">Aprovar</button>
                        </div>
                    </div>

                    {{-- Negar --}}
                    <div x-show="confirm === 'negar'"
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center shrink-0">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100">Negar Chamado</p>
                                <p class="text-sm text-gray-500">Confirma a negação do chamado <span x-text="'#' + detail?.id"></span>? Esta ação é irreversível.</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="confirm = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                            <button type="submit" form="form-det-negar" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Negar</button>
                        </div>
                    </div>

                    {{-- Entregar --}}
                    <div x-show="confirm === 'entregar'"
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center shrink-0">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100">Registrar Entrega</p>
                                <p class="text-sm text-gray-500">Confirma a entrega dos patrimônios do chamado <span x-text="'#' + detail?.id"></span>?</p>
                            </div>
                        </div>
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 px-4 py-3 text-sm text-amber-700 dark:text-amber-400">
                            Os patrimônios serão marcados como <strong>em uso</strong> e os termos gerados automaticamente.
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="confirm = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                            <button type="submit" form="form-det-entregar" class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors">Confirmar Entrega</button>
                        </div>
                    </div>

                </div>
            </template>
        </div>
        {{-- ===== FIM MODAL VER CHAMADO ===== --}}

    </div>
</x-app-layout>
