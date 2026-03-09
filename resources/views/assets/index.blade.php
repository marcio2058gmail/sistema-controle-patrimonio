<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ auth()->user()->isAdmin() ? 'Patrimônios' : 'Patrimônios Disponíveis' }}
            </h2>
            @if(auth()->user()->isAdmin())
            <button type="button" @click="$dispatch('open-novo-patrimonio')"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Novo Patrimônio
            </button>
            @endif
        </div>
    </x-slot>

    <div x-data="{
            modalOpen: {{ $errors->any() ? 'true' : 'false' }},
            showDetail: false, detail: null,
            deleteTarget: null,
            editTarget: null,
            badgeClass(s) { return { disponivel:'bg-green-100 text-green-800', em_uso:'bg-blue-100 text-blue-800', manutencao:'bg-yellow-100 text-yellow-800' }[s] ?? 'bg-gray-100 text-gray-800'; },
            badgeLabel(s) { return { disponivel:'Disponível', em_uso:'Em Uso', manutencao:'Manutenção' }[s] ?? s; },
            openDetail(d) { this.detail = d; this.showDetail = true; },
            openEdit(d) { this.editTarget = d; this.showDetail = false; }
         }"
         @keydown.escape.window="modalOpen ? modalOpen = false : deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : showDetail = false"
         @open-novo-patrimonio.window="modalOpen = true">

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                @if($apenasDisponiveis)
                <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-sm text-blue-700 dark:text-blue-300">
                    Exibindo apenas patrimônios <strong>disponíveis</strong> para solicitação via chamado.
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº Série</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($assets as $asset)
                            @php $ad = [
                                'id'           => $asset->id,
                                'code'         => $asset->codigo_patrimonio,
                                'descricao'    => $asset->descricao,
                                'modelo'       => $asset->modelo,
                                'serie'        => $asset->numero_serie,
                                'status'       => $asset->status,
                                'created'      => $asset->created_at->format('d/m/Y'),
                                'valor_aquisicao'    => $asset->valor_aquisicao ? number_format($asset->valor_aquisicao, 2, ',', '.') : null,
                                'data_aquisicao'     => $asset->data_aquisicao?->format('d/m/Y'),
                                'data_aquisicao_raw' => $asset->data_aquisicao?->format('Y-m-d'),
                                'fornecedor'         => $asset->fornecedor,
                                'numero_nota_fiscal' => $asset->numero_nota_fiscal,
                                'garantia_ate'       => $asset->garantia_ate?->format('d/m/Y'),
                                'garantia_ate_raw'   => $asset->garantia_ate?->format('Y-m-d'),
                                'garantia_vencida'   => $asset->garantia_ate?->isPast(),
                                'valor_atual'        => $asset->valor_atual ? number_format($asset->valor_atual, 2, ',', '.') : null,
                                'valor_aquisicao_raw'=> $asset->valor_aquisicao,
                                'valor_atual_raw'    => $asset->valor_atual,
                                'url_edit'     => auth()->user()->isAdmin() ? route('assets.edit', $asset) : '',
                                'url_update'   => auth()->user()->isAdmin() ? route('assets.update', $asset) : '',
                                'url_destroy'  => auth()->user()->isAdmin() ? route('assets.destroy', $asset) : '',
                                'is_admin'     => auth()->user()->isAdmin(),
                            ]; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $asset->codigo_patrimonio }}</td>
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $asset->descricao }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $asset->modelo ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $asset->numero_serie ?? '—' }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$asset->status" type="patrimonio" /></td>
                                <td class="px-6 py-3 text-right space-x-3">
                                    <button type="button" @click="openDetail({{ Js::from($ad) }})" class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-medium transition-colors">Ver detalhes</button>
                                    @if(auth()->user()->isAdmin())
                                    <button type="button" @click="openEdit({{ Js::from($ad) }})" class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</button>
                                    <button type="button" @click="deleteTarget = {{ Js::from(['url'=>route('assets.destroy',$asset),'name'=>$asset->codigo_patrimonio]) }}" class="text-red-600 hover:text-red-800 text-xs font-medium transition-colors">Excluir</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Nenhum patrimônio cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">{{ $assets->links() }}</div>
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
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 font-mono" x-text="detail?.code"></h3>
                        <span :class="badgeClass(detail?.status)" x-text="badgeLabel(detail?.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                    </div>
                    <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-gray-500">Descrição</dt><dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.descricao"></dd></div>
                        <div><dt class="text-gray-500">Modelo</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.modelo || '—'"></dd></div>
                        <div><dt class="text-gray-500">Nº de Série</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.serie || '—'"></dd></div>
                        <div><dt class="text-gray-500">Cadastrado em</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.created"></dd></div>
                    </dl>

                    {{-- Dados financeiros (apenas admin) --}}
                    @if(auth()->user()->isAdmin())
                    <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Aquisição &amp; Valor</p>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div><dt class="text-gray-500">Valor de Aquisição</dt><dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.valor_aquisicao ? 'R$ ' + detail.valor_aquisicao : '—'"></dd></div>
                            <div><dt class="text-gray-500">Data de Aquisição</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.data_aquisicao || '—'"></dd></div>
                            <div><dt class="text-gray-500">Fornecedor</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.fornecedor || '—'"></dd></div>
                            <div><dt class="text-gray-500">Nota Fiscal</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.numero_nota_fiscal || '—'"></dd></div>
                            <div>
                                <dt class="text-gray-500">Garantia Até</dt>
                                <dd class="mt-0.5" :class="detail?.garantia_vencida ? 'text-red-500 font-medium' : 'text-gray-800 dark:text-gray-200'">
                                    <span x-text="detail?.garantia_ate ? detail.garantia_ate + (detail.garantia_vencida ? ' (vencida)' : '') : '—'"></span>
                                </dd>
                            </div>
                            <div><dt class="text-gray-500">Valor Atual</dt><dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.valor_atual ? 'R$ ' + detail.valor_atual : '—'"></dd></div>
                        </dl>
                    </div>
                    @endif
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <template x-if="detail?.is_admin">
                        <button type="button" @click="openEdit(detail)" class="px-4 py-2 text-sm font-medium rounded-lg border border-indigo-500 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">Editar</button>
                    </template>
                    <button type="button" @click="showDetail = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Fechar</button>
                </div>
            </div>
        </div>

        {{-- MODAL EDITAR --}}
        <div x-show="editTarget !== null"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display:none">
            <div class="absolute inset-0" @click="editTarget = null"></div>
            <div x-show="editTarget !== null"
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="'Editar — ' + (editTarget?.code ?? '')"></h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="editTarget !== null">
                    <form :action="editTarget.url_update" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf @method('PATCH')
                        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Código *</label>
                                <input type="text" name="codigo_patrimonio" :value="editTarget.code" required class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição *</label>
                                <input type="text" name="descricao" :value="editTarget.descricao" required class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modelo</label>
                                    <input type="text" name="modelo" :value="editTarget.modelo" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nº Série</label>
                                    <input type="text" name="numero_serie" :value="editTarget.serie" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                                <select name="status" x-init="$el.value = editTarget.status ?? ''" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    @foreach($statusLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Aquisição & Valor --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Aquisição &amp; Valor</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor de Aquisição (R$)</label>
                                        <input type="number" step="0.01" min="0" name="valor_aquisicao" :value="editTarget.valor_aquisicao_raw" placeholder="0,00" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Aquisição</label>
                                        <input type="date" name="data_aquisicao" :value="editTarget.data_aquisicao_raw" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fornecedor</label>
                                        <input type="text" name="fornecedor" :value="editTarget.fornecedor" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nota Fiscal</label>
                                        <input type="text" name="numero_nota_fiscal" :value="editTarget.numero_nota_fiscal" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Garantia Até</label>
                                        <input type="date" name="garantia_ate" :value="editTarget.garantia_ate_raw" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Atual (R$)</label>
                                        <input type="number" step="0.01" min="0" name="valor_atual" :value="editTarget.valor_atual_raw" placeholder="0,00" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                            <button type="button" @click="editTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Salvar</button>
                        </div>
                    </form>
                </template>
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
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Excluir Patrimônio</p>
                        <p class="text-sm text-gray-500">Confirma a exclusão de <span class="font-mono font-medium" x-text="deleteTarget?.name"></span>?</p>
                    </div>
                </div>
                <form :action="deleteTarget?.url" method="POST" class="flex justify-end gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Excluir</button>
                </form>
            </div>
        </div>

        {{-- ===== MODAL NOVO PATRIMÔNIO ===== --}}
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
             style="display:none">
            <div class="absolute inset-0" @click="modalOpen = false"></div>
            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Novo Patrimônio</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <form id="form-novo-patrimonio" action="{{ route('assets.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <x-input-label for="codigo_patrimonio" value="Código de Patrimônio *" />
                            <x-text-input id="codigo_patrimonio" name="codigo_patrimonio" type="text" class="mt-1 block w-full" :value="old('codigo_patrimonio')" required />
                            <x-input-error :messages="$errors->get('codigo_patrimonio')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="descricao" value="Descrição *" />
                            <x-text-input id="descricao" name="descricao" type="text" class="mt-1 block w-full" :value="old('descricao')" required />
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="modelo" value="Modelo" />
                                <x-text-input id="modelo" name="modelo" type="text" class="mt-1 block w-full" :value="old('modelo')" />
                                <x-input-error :messages="$errors->get('modelo')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="numero_serie" value="Número de Série" />
                                <x-text-input id="numero_serie" name="numero_serie" type="text" class="mt-1 block w-full" :value="old('numero_serie')" />
                                <x-input-error :messages="$errors->get('numero_serie')" class="mt-1" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="status" value="Status *" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300">
                                @foreach($statusLabels as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'disponivel') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>

                        {{-- Aquisição & Valor --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Aquisição &amp; Valor</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="valor_aquisicao" value="Valor de Aquisição (R$)" />
                                    <x-text-input id="valor_aquisicao" name="valor_aquisicao" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('valor_aquisicao')" placeholder="0,00" />
                                    <x-input-error :messages="$errors->get('valor_aquisicao')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="data_aquisicao" value="Data de Aquisição" />
                                    <x-text-input id="data_aquisicao" name="data_aquisicao" type="date" class="mt-1 block w-full" :value="old('data_aquisicao')" />
                                    <x-input-error :messages="$errors->get('data_aquisicao')" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div>
                                    <x-input-label for="fornecedor" value="Fornecedor" />
                                    <x-text-input id="fornecedor" name="fornecedor" type="text" class="mt-1 block w-full" :value="old('fornecedor')" />
                                    <x-input-error :messages="$errors->get('fornecedor')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="numero_nota_fiscal" value="Número da Nota Fiscal" />
                                    <x-text-input id="numero_nota_fiscal" name="numero_nota_fiscal" type="text" class="mt-1 block w-full" :value="old('numero_nota_fiscal')" />
                                    <x-input-error :messages="$errors->get('numero_nota_fiscal')" class="mt-1" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-3">
                                <div>
                                    <x-input-label for="garantia_ate" value="Garantia Até" />
                                    <x-text-input id="garantia_ate" name="garantia_ate" type="date" class="mt-1 block w-full" :value="old('garantia_ate')" />
                                    <x-input-error :messages="$errors->get('garantia_ate')" class="mt-1" />
                                </div>
                                <div>
                                    <x-input-label for="valor_atual" value="Valor Atual (R$)" />
                                    <x-text-input id="valor_atual" name="valor_atual" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('valor_atual')" placeholder="0,00" />
                                    <x-input-error :messages="$errors->get('valor_atual')" class="mt-1" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" form="form-novo-patrimonio" class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">Cadastrar</button>
                </div>
            </div>
        </div>
        {{-- ===== FIM MODAL NOVO PATRIMÔNIO ===== --}}

    </div>
</x-app-layout>
