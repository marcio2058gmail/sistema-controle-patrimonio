<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Manutenções</h2>
            <button @click="modalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-pink-700 hover:bg-pink-800 text-white text-sm font-medium rounded-lg shadow transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                + Nova Manutenção
            </button>
        </div>
    </x-slot>

    <div x-data="{
            modalOpen: {{ $errors->any() ? 'true' : 'false' }},
            showDetail: false,
            detail: null,
            editTarget: null,
            deleteTarget: null,
            openDetail(m) { this.detail = m; this.showDetail = true; },
            openEdit(m) { this.showDetail = false; this.editTarget = JSON.parse(JSON.stringify(m)); },
            tipoBadge(tipo) {
                const map = { preventiva: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300', corretiva: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300' };
                return map[tipo] ?? 'bg-gray-100 text-gray-600';
            },
            tipoLabel(tipo) { const m = { preventiva: 'Preventiva', corretiva: 'Corretiva' }; return m[tipo] ?? tipo; },
            statusBadge(s) {
                const map = { agendada: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', em_andamento: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300', concluida: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300', cancelada: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' };
                return map[s] ?? 'bg-gray-100 text-gray-600';
            },
            statusLabel(s) { const m = { agendada: 'Agendada', em_andamento: 'Em Andamento', concluida: 'Concluída', cancelada: 'Cancelada' }; return m[s] ?? s; }
         }"
         @keydown.escape.window="modalOpen ? modalOpen = false : deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : showDetail = false">

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 px-4 py-3 text-sm text-green-800 dark:text-green-300">
                    {{ session('success') }}
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Patrimônio</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Tipo</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Abertura</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Conclusão</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Custo</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($manutencoes as $m)
                            @php
                                $md = [
                                    'id'               => $m->id,
                                    'patrimonio_id'    => $m->patrimonio_id,
                                    'patrimonio_label' => $m->patrimonio ? $m->patrimonio->codigo_patrimonio . ' — ' . $m->patrimonio->descricao : '—',
                                    'tipo'             => $m->tipo,
                                    'status'           => $m->status,
                                    'descricao'        => $m->descricao ?? '',
                                    'data_abertura'    => $m->data_abertura?->format('d/m/Y') ?? '',
                                    'data_abertura_raw'=> $m->data_abertura?->format('Y-m-d') ?? '',
                                    'data_conclusao'   => $m->data_conclusao?->format('d/m/Y') ?? '',
                                    'data_conclusao_raw'=> $m->data_conclusao?->format('Y-m-d') ?? '',
                                    'custo'            => $m->custo ? number_format($m->custo, 2, ',', '.') : '',
                                    'custo_raw'        => $m->custo ?? '',
                                    'tecnico_fornecedor' => $m->tecnico_fornecedor ?? '',
                                    'observacoes'      => $m->observacoes ?? '',
                                    'url_update'       => route('manutencoes.update', $m),
                                    'url_delete'       => route('manutencoes.destroy', $m),
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 font-mono text-gray-800 dark:text-gray-200">
                                    {{ $m->patrimonio?->codigo_patrimonio ?? '—' }}
                                    <span class="block text-xs text-gray-400 font-sans truncate max-w-[160px]">{{ $m->patrimonio?->descricao }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $m->tipo === 'preventiva' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300' }}">
                                        {{ \App\Models\Manutencao::TIPOS[$m->tipo] ?? $m->tipo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $sBadge = match($m->status) {
                                            'agendada'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                            'em_andamento' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                            'concluida'    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                            'cancelada'    => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                            default        => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sBadge }}">
                                        {{ \App\Models\Manutencao::STATUS[$m->status] ?? $m->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $m->data_abertura?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $m->data_conclusao?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $m->custo ? 'R$ ' . number_format($m->custo, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="openDetail({{ json_encode($md) }})"
                                                class="text-xs px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            Ver
                                        </button>
                                        <button type="button" @click="openEdit({{ json_encode($md) }})"
                                                class="text-xs px-2.5 py-1 rounded-md border border-indigo-400 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                                            Editar
                                        </button>
                                        <button type="button" @click="deleteTarget = {{ json_encode(['id' => $m->id, 'label' => ($m->patrimonio?->codigo_patrimonio ?? '—') . ' — ' . \App\Models\Manutencao::TIPOS[$m->tipo], 'url' => route('manutencoes.destroy', $m)]) }}"
                                                class="text-xs px-2.5 py-1 rounded-md border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    Nenhuma manutenção registrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <span :class="tipoBadge(detail?.tipo)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="tipoLabel(detail?.tipo)"></span>
                        <span :class="statusBadge(detail?.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" x-text="statusLabel(detail?.status)"></span>
                    </div>
                    <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-4 text-sm">
                        <div class="col-span-2"><dt class="text-gray-500">Patrimônio</dt><dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.patrimonio_label"></dd></div>
                        <div><dt class="text-gray-500">Abertura</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.data_abertura || '—'"></dd></div>
                        <div><dt class="text-gray-500">Conclusão</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.data_conclusao || '—'"></dd></div>
                        <div><dt class="text-gray-500">Custo</dt><dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200" x-text="detail?.custo ? 'R$ ' + detail.custo : '—'"></dd></div>
                        <div><dt class="text-gray-500">Técnico / Fornecedor</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.tecnico_fornecedor || '—'"></dd></div>
                        <div class="col-span-2"><dt class="text-gray-500">Descrição</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="detail?.descricao || '—'"></dd></div>
                        <div class="col-span-2"><dt class="text-gray-500">Observações</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200 whitespace-pre-line" x-text="detail?.observacoes || '—'"></dd></div>
                    </dl>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="openEdit(detail)" class="px-4 py-2 text-sm font-medium rounded-lg border border-indigo-500 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">Editar</button>
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
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Editar Manutenção</h3>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="editTarget !== null">
                    <form :action="editTarget.url_update" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf @method('PATCH')
                        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patrimônio</label>
                                <p class="text-sm text-gray-800 dark:text-gray-200 py-2" x-text="editTarget.patrimonio_label"></p>
                                <input type="hidden" name="patrimonio_id" :value="editTarget.patrimonio_id">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo *</label>
                                    <select name="tipo" x-init="$el.value = editTarget.tipo" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                        @foreach($tipos as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                                    <select name="status" x-init="$el.value = editTarget.status" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                        @foreach($statusList as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Abertura *</label>
                                    <input type="date" name="data_abertura" :value="editTarget.data_abertura_raw" required class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Conclusão</label>
                                    <input type="date" name="data_conclusao" :value="editTarget.data_conclusao_raw" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custo (R$)</label>
                                    <input type="number" step="0.01" min="0" name="custo" :value="editTarget.custo_raw" placeholder="0,00" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Técnico / Fornecedor</label>
                                    <input type="text" name="tecnico_fornecedor" :value="editTarget.tecnico_fornecedor" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição</label>
                                <textarea name="descricao" rows="2" x-text="editTarget.descricao" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                                <textarea name="observacoes" rows="2" x-text="editTarget.observacoes" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300"></textarea>
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
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Excluir Manutenção</p>
                        <p class="text-sm text-gray-500">Confirma a exclusão de <span class="font-medium" x-text="deleteTarget?.label"></span>?</p>
                    </div>
                </div>
                <form :action="deleteTarget?.url" method="POST" class="flex justify-end gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteTarget = null" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">Excluir</button>
                </form>
            </div>
        </div>

        {{-- MODAL NOVA MANUTENÇÃO --}}
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Nova Manutenção</h3>
                    <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5">
                    <form id="form-nova-manutencao" action="{{ route('manutencoes.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="patrimonio_id" value="Patrimônio *" />
                            <select id="patrimonio_id" name="patrimonio_id" required
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 text-sm">
                                <option value="">Selecione...</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('patrimonio_id') == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->codigo_patrimonio }} — {{ $asset->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('patrimonio_id')" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tipo" value="Tipo *" />
                                <select id="tipo" name="tipo" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 text-sm">
                                    @foreach($tipos as $v => $l)
                                        <option value="{{ $v }}" {{ old('tipo') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="status_m" value="Status *" />
                                <select id="status_m" name="status" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 text-sm">
                                    @foreach($statusList as $v => $l)
                                        <option value="{{ $v }}" {{ old('status', 'agendada') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="data_abertura" value="Data Abertura *" />
                                <x-text-input id="data_abertura" name="data_abertura" type="date" class="mt-1 block w-full" :value="old('data_abertura', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('data_abertura')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="data_conclusao" value="Data Conclusão" />
                                <x-text-input id="data_conclusao" name="data_conclusao" type="date" class="mt-1 block w-full" :value="old('data_conclusao')" />
                                <x-input-error :messages="$errors->get('data_conclusao')" class="mt-1" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="custo" value="Custo (R$)" />
                                <x-text-input id="custo" name="custo" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('custo')" placeholder="0,00" />
                                <x-input-error :messages="$errors->get('custo')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="tecnico_fornecedor" value="Técnico / Fornecedor" />
                                <x-text-input id="tecnico_fornecedor" name="tecnico_fornecedor" type="text" class="mt-1 block w-full" :value="old('tecnico_fornecedor')" />
                                <x-input-error :messages="$errors->get('tecnico_fornecedor')" class="mt-1" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="descricao_m" value="Descrição" />
                            <textarea id="descricao_m" name="descricao" rows="2"
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 text-sm">{{ old('descricao') }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="observacoes_m" value="Observações" />
                            <textarea id="observacoes_m" name="observacoes" rows="2"
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300 text-sm">{{ old('observacoes') }}</textarea>
                            <x-input-error :messages="$errors->get('observacoes')" class="mt-1" />
                        </div>
                    </form>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Cancelar</button>
                    <button type="submit" form="form-nova-manutencao" class="px-4 py-2 text-sm font-medium rounded-lg bg-pink-700 hover:bg-pink-800 text-white transition-colors">Registrar</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
