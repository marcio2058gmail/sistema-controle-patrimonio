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
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $ticket->funcionario?->nome ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-0.5"><x-status-badge :status="$ticket->status" type="chamado" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Patrimônio(s)</dt>
                        <dd class="mt-1 text-gray-800 dark:text-gray-200">
                            @if($ticket->patrimonios->isEmpty())
                                <span class="text-gray-400">—</span>
                            @else
                                <ul class="space-y-1">
                                    @foreach($ticket->patrimonios as $asset)
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Ações</h3>
                <div class="flex gap-3 flex-wrap">
                    @if($ticket->status === 'aberto')
                        <form action="{{ route('tickets.aprovar', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                                ✔ Aprovar
                            </button>
                        </form>
                        <form action="{{ route('tickets.negar', $ticket) }}" method="POST"
                              onsubmit="return confirm('Confirmar negação do chamado?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg">
                                ✖ Negar
                            </button>
                        </form>
                    @endif

                    @if($ticket->status === 'aprovado')
                        <form action="{{ route('tickets.entregar', $ticket) }}" method="POST"
                              onsubmit="return confirm('Confirmar entrega e criar termo de responsabilidade?')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
                                📦 Registrar Entrega
                            </button>
                        </form>
                    @endif

                    @if(in_array($ticket->status, ['negado', 'entregue']))
                        <p class="text-sm text-gray-400 italic">Chamado encerrado — nenhuma ação disponível.</p>
                    @endif
                </div>
            </div>
            @endif

            <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:underline">← Voltar para lista</a>
        </div>
    </div>
</x-app-layout>
