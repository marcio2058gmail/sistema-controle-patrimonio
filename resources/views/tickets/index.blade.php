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
                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
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
        {{-- ===== FIM MODAL ===== --}}

    </div>
</x-app-layout>
