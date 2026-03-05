<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Chamado #{{ $ticket->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Funcionário</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $ticket->employee?->nome ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-0.5"><x-status-badge :status="$ticket->status" type="chamado" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Patrimônio(s)</dt>
                        <dd class="mt-1 text-gray-800 dark:text-gray-200">
                            @if($ticket->assets->isEmpty())
                                <span class="text-gray-400">—</span>
                            @else
                                <ul class="space-y-1">
                                    @foreach($ticket->assets as $asset)
                                    <li class="flex items-center gap-2 text-sm">
                                        <span class="font-mono font-medium">{{ $asset->codigo_patrimonio }}</span>
                                        <span class="text-gray-500">— {{ $asset->descricao }}</span>
                                        <x-status-badge :status="$asset->status" type="patrimonio" />
                                    </li>
                                    @endforeach
                                </ul>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Aberto em</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>

                <div>
                    <dt class="text-sm text-gray-500 mb-1">Descrição</dt>
                    <dd class="p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $ticket->descricao }}</dd>
                </div>
            </div>

            {{-- Ações apenas para Admin --}}
            @if(auth()->user()->isAdmin())
            <div x-data="{ modal: null }"
                 class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Ações</h3>
                <div class="flex gap-3 flex-wrap">
                    @if($ticket->status === 'aberto')
                        <button type="button" @click="modal = 'aprovar'"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                            ✔ Aprovar
                        </button>
                        <button type="button" @click="modal = 'negar'"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors">
                            ✖ Negar
                        </button>
                    @endif

                    @if($ticket->status === 'aprovado')
                        <button type="button" @click="modal = 'entregar'"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition-colors">
                            📦 Registrar Entrega
                        </button>
                    @endif

                    @if(in_array($ticket->status, ['negado', 'entregue']))
                        <p class="text-sm text-gray-400 italic">Chamado encerrado — nenhuma ação disponível.</p>
                    @endif
                </div>

                {{-- Formulários ocultos --}}
                <form id="form-aprovar" action="{{ route('tickets.aprovar', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                </form>
                <form id="form-negar" action="{{ route('tickets.negar', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                </form>
                <form id="form-entregar" action="{{ route('tickets.entregar', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                </form>

                {{-- Overlay dos modais --}}
                <template x-if="modal !== null">
                    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                         @keydown.escape.window="modal = null">

                        <div class="absolute inset-0" @click="modal = null"></div>

                        {{-- Modal Aprovar --}}
                        <div x-show="modal === 'aprovar'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Aprovar Chamado</h3>
                                    <p class="text-sm text-gray-500">Confirma a aprovação do chamado #{{ $ticket->id }}?</p>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="modal = null"
                                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" form="form-aprovar"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                                    Aprovar
                                </button>
                            </div>
                        </div>

                        {{-- Modal Negar --}}
                        <div x-show="modal === 'negar'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Negar Chamado</h3>
                                    <p class="text-sm text-gray-500">Confirma a negação do chamado #{{ $ticket->id }}? Esta ação é irreversível.</p>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="modal = null"
                                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" form="form-negar"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors">
                                    Negar
                                </button>
                            </div>
                        </div>

                        {{-- Modal Registrar Entrega --}}
                        <div x-show="modal === 'entregar'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Registrar Entrega</h3>
                                    <p class="text-sm text-gray-500">Confirma a entrega dos patrimônios e criação dos termos de responsabilidade?</p>
                                </div>
                            </div>
                            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 px-4 py-3 text-sm text-amber-700 dark:text-amber-400">
                                Os patrimônios serão marcados como <strong>em uso</strong> e os termos serão gerados automaticamente.
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="modal = null"
                                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" form="form-entregar"
                                    class="px-4 py-2 text-sm font-medium rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors">
                                    Confirmar Entrega
                                </button>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
            @endif

            <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
        </div>
    </div>
</x-app-layout>
