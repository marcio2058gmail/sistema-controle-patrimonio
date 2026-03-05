<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ auth()->user()->isAdmin() ? 'Patrimônios' : 'Patrimônios Disponíveis' }}
            </h2>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('assets.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Novo Patrimônio
            </a>
            @endif
        </div>
    </x-slot>

    <div x-data="{
            showDetail: false, detail: null,
            deleteTarget: null,
            editTarget: null,
            badgeClass(s) { return { disponivel:'bg-green-100 text-green-800', em_uso:'bg-blue-100 text-blue-800', manutencao:'bg-yellow-100 text-yellow-800' }[s] ?? 'bg-gray-100 text-gray-800'; },
            badgeLabel(s) { return { disponivel:'Disponível', em_uso:'Em Uso', manutencao:'Manutenção' }[s] ?? s; },
            openDetail(d) { this.detail = d; this.showDetail = true; },
            openEdit(d) { this.editTarget = d; this.showDetail = false; }
         }"
         @keydown.escape.window="deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : showDetail = false">

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
                            @php $ad = ['id'=>$asset->id,'code'=>$asset->codigo_patrimonio,'descricao'=>$asset->descricao,'modelo'=>$asset->modelo,'serie'=>$asset->numero_serie,'status'=>$asset->status,'created'=>$asset->created_at->format('d/m/Y'),'url_edit'=>auth()->user()->isAdmin()?route('assets.edit',$asset):'','url_update'=>auth()->user()->isAdmin()?route('assets.update',$asset):'','url_destroy'=>auth()->user()->isAdmin()?route('assets.destroy',$asset):'','is_admin'=>auth()->user()->isAdmin()]; @endphp
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
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[80vh]">
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
                 class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl flex flex-col max-h-[80vh]">
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

    </div>
</x-app-layout>
