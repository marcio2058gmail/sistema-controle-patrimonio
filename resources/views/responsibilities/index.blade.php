<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Responsabilidades</h2>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('responsibilities.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                + Nova Responsabilidade
            </a>
            @endif
        </div>
    </x-slot>

    <div x-data="{ showDetail: false, detail: null, deleteTarget: null, editTarget: null, openEdit(d) { this.editTarget = d; this.showDetail = false; } }"
         @keydown.escape.window="deleteTarget ? deleteTarget = null : editTarget ? editTarget = null : showDetail = false">

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-alert />

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Funcionário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patrimônio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrega</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devolução</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assinado</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($responsibilities as $r)
                            @php
                            $rd = [
                                'id'           => $r->id,
                                'employee'     => $r->employee->nome,
                                'cargo'        => $r->employee->cargo,
                                'assets'       => $r->assets->map(fn($a) => ['code' => $a->codigo_patrimonio, 'desc' => $a->descricao])->values()->toArray(),
                                'assets_label' => $r->assets->pluck('codigo_patrimonio')->implode(', '),
                                'assets_count' => $r->assets->count(),
                                'entrega'      => $r->data_entrega->format('d/m/Y'),
                                'devolucao'    => $r->data_devolucao?->format('d/m/Y'),
                                'devolucao_raw'=> $r->data_devolucao?->toDateString(),
                                'assinado'     => $r->assinado,
                                'termo'        => $r->termo_responsabilidade,
                                'is_admin'     => auth()->user()->isAdmin(),
                                'url_edit'     => auth()->user()->isAdmin() ? route('responsibilities.edit', $r) : '',
                                'url_update'   => auth()->user()->isAdmin() ? route('responsibilities.update', $r) : '',
                                'url_destroy'  => auth()->user()->isAdmin() ? route('responsibilities.destroy', $r) : '',
                                'url_pdf'      => route('responsibilities.pdf', $r),
                            ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $r->employee->nome }}</td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400">
                                    <span class="font-mono text-xs">{{ $r->assets->pluck('codigo_patrimonio')->implode(', ') }}</span>
                                    @if($r->assets->count() > 1)
                                    <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">{{ $r->assets->count() }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $r->data_entrega->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-gray-500">
                                    @if($r->data_devolucao) {{ $r->data_devolucao->format('d/m/Y') }}
                                    @else <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($r->assinado)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                            <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Assinado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">Pendente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right space-x-3">
                                    <button type="button" @click="detail = {{ Js::from($rd) }}; showDetail = true" class="text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 text-xs font-medium transition-colors">Ver detalhes</button>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('responsibilities.pdf', $r) }}" target="_blank" class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">PDF</a>
                                    <button type="button" @click="openEdit({{ Js::from($rd) }})" class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">Editar</button>
                                    <button type="button" @click="deleteTarget = {{ Js::from(['url'=>route('responsibilities.destroy',$r),'name'=>'#'.$r->id.' - '.$r->employee->nome]) }}" class="text-red-600 hover:text-red-800 text-xs font-medium transition-colors">Excluir</button>
                                    @else
                                    @if($r->assinado)
                                    <a href="{{ route('responsibilities.pdf', $r) }}" target="_blank" class="text-gray-600 hover:text-gray-800 dark:hover:text-gray-300 text-xs font-medium transition-colors">PDF</a>
                                    @endif
                                    @if(!$r->assinado && !auth()->user()->isAdmin() && auth()->user()->employee?->id === $r->funcionario_id)
                                    <a href="{{ route('responsibilities.show', $r) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium font-semibold transition-colors">✍ Assinar</a>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Nenhuma responsabilidade registrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="px-6 py-4">{{ $responsibilities->links() }}</div>
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="detail?.employee"></h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Responsabilidade #' + detail?.id"></p>
                    </div>
                    <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div><dt class="text-gray-500">Cargo</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.cargo || '—'"></dd></div>
                        <div class="col-span-2">
                            <dt class="text-gray-500 mb-2">Equipamentos
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700" x-text="detail?.assets_count"></span>
                            </dt>
                            <dd>
                                <div class="space-y-1">
                                    <template x-for="(a, i) in (detail?.assets ?? [])" :key="i">
                                        <div class="flex items-baseline gap-2 text-sm">
                                            <span class="font-mono font-medium text-gray-800 dark:text-gray-200" x-text="a.code"></span>
                                            <span class="text-gray-500 text-xs" x-text="a.desc"></span>
                                        </div>
                                    </template>
                                </div>
                            </dd>
                        </div>
                        <div><dt class="text-gray-500">Data de Entrega</dt><dd class="mt-0.5 text-gray-800 dark:text-gray-200" x-text="detail?.entrega"></dd></div>
                        <div>
                            <dt class="text-gray-500">Data de Devolução</dt>
                            <dd class="mt-0.5">
                                <template x-if="detail?.devolucao">
                                    <span class="text-gray-800 dark:text-gray-200" x-text="detail?.devolucao"></span>
                                </template>
                                <template x-if="!detail?.devolucao">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                                </template>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Assinado</dt>
                            <dd class="mt-0.5">
                                <template x-if="detail?.assinado"><span class="text-green-600 font-medium">Sim</span></template>
                                <template x-if="!detail?.assinado"><span class="text-gray-400">Não</span></template>
                            </dd>
                        </div>
                    </dl>
                    <div>
                        <dt class="text-sm text-gray-500 mb-1">Termo de Responsabilidade</dt>
                        <dd class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line max-h-40 overflow-y-auto" x-text="detail?.termo"></dd>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 rounded-b-2xl shrink-0">
                    <a :href="detail?.url_pdf" target="_blank" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-400 dark:border-gray-500 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">📄 PDF</a>
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="'Editar #' + (editTarget?.id ?? '')"></h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="editTarget?.employee + ' — ' + (editTarget?.assets_count ?? 0) + ' equipamento(s)'"></p>
                    </div>
                    <button @click="editTarget = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="editTarget !== null">
                    <form :action="editTarget.url_update" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf @method('PATCH')
                        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Devolução</label>
                                <input type="date" name="data_devolucao" :value="editTarget.devolucao_raw ?? ''" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300">
                                <p class="text-xs text-gray-400 mt-1">Deixe em branco se ainda ativo</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Termo de Responsabilidade</label>
                                <textarea name="termo_responsabilidade" rows="5" x-init="$el.value = editTarget.termo ?? ''" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm text-sm focus:ring focus:ring-indigo-300"></textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="assinado" value="0">
                                <input type="checkbox" name="assinado" value="1" id="edit_assinado"
                                    x-init="$el.checked = !!editTarget.assinado"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="edit_assinado" class="text-sm font-medium text-gray-700 dark:text-gray-300">Termo assinado fisicamente</label>
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
                        <p class="font-semibold text-gray-800 dark:text-gray-100">Excluir Responsabilidade</p>
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
