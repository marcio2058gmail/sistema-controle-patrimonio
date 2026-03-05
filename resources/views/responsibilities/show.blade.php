<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Termo #{{ $responsibility->id }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('responsibilities.pdf', $responsibility) }}" target="_blank"
                   class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    📄 Baixar PDF
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('responsibilities.edit', $responsibility) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    Editar
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert />

            {{-- Informações gerais --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Informações</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Funcionário</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $responsibility->employee->nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cargo</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->employee->cargo ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data de Entrega</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->data_entrega->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data de Devolução</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $responsibility->data_devolucao?->format('d/m/Y') ?? 'Em aberto' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Assinado</dt>
                        <dd class="text-gray-800 dark:text-gray-200">
                            @if($responsibility->assinado)
                                <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Sim{{ $responsibility->assinado_em ? ' — ' . $responsibility->assinado_em->format('d/m/Y \à\s H:i') : '' }}
                                </span>
                            @else
                                <span class="text-yellow-600 font-medium">Pendente</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Equipamentos</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $responsibility->assets->count() }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Lista de equipamentos --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Equipamentos</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº Série</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($responsibility->assets as $asset)
                        <tr>
                            <td class="px-6 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $asset->codigo_patrimonio }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $asset->descricao }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $asset->modelo ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $asset->numero_serie ?? '—' }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$asset->status" type="patrimonio" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Termo --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Declaração</h3>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $responsibility->termo_responsabilidade }}</div>
            </div>

            {{-- Assinatura já realizada --}}
            @if($responsibility->assinado && $responsibility->assinatura_base64)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Assinatura Digital</h3>
                <div class="inline-block border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-white">
                    <img src="{{ $responsibility->assinatura_base64 }}" alt="Assinatura" class="max-h-32">
                </div>
                @if($responsibility->assinado_em)
                <p class="text-xs text-gray-400 mt-2">
                    Assinado em {{ $responsibility->assinado_em->format('d/m/Y \à\s H:i') }}
                    @if($responsibility->assinado_ip) &mdash; IP: {{ $responsibility->assinado_ip }} @endif
                </p>
                @endif
            </div>
            @endif

            {{-- Canvas de assinatura (só para o funcionário do termo, enquanto não assinou) --}}
            @if(!$responsibility->assinado && auth()->user()->isEmployee() && auth()->user()->employee?->id === $responsibility->funcionario_id)
            <div
                x-data="{
                    pad: null,
                    isEmpty: true,
                    init() {
                        const canvas = this.\$refs.canvas;
                        this.pad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)', penColor: 'rgb(30,30,30)' });
                        this.pad.addEventListener('beginStroke', () => { this.isEmpty = false; });
                        this.resize();
                    },
                    resize() {
                        const c = this.\$refs.canvas;
                        const r = Math.max(window.devicePixelRatio || 1, 1);
                        const data = this.pad.toData();
                        c.width = c.offsetWidth * r;
                        c.height = c.offsetHeight * r;
                        c.getContext('2d').scale(r, r);
                        this.pad.clear();
                        this.pad.fromData(data);
                        this.isEmpty = this.pad.isEmpty();
                    },
                    clear() { this.pad.clear(); this.isEmpty = true; },
                    submit() {
                        if (this.pad.isEmpty()) return;
                        this.\$refs.sigInput.value = this.pad.toDataURL('image/png');
                        this.\$refs.sigForm.submit();
                    }
                }"
                @resize.window.debounce.300ms="resize()"
                class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">

                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Assinatura Digital</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leia o termo acima e assine no campo abaixo para confirmar o recebimento dos equipamentos.</p>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-500 rounded-xl overflow-hidden bg-white" style="height:180px">
                    <canvas x-ref="canvas" class="w-full h-full touch-none" style="cursor:crosshair"></canvas>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <button type="button" @click="clear()" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Limpar</button>
                    <form x-ref="sigForm" action="{{ route('responsibilities.assinar', $responsibility) }}" method="POST">
                        @csrf
                        <input type="hidden" name="assinatura_base64" x-ref="sigInput">
                        <button
                            type="button"
                            @click="submit()"
                            :disabled="isEmpty"
                            :class="isEmpty ? 'opacity-40 cursor-not-allowed' : 'hover:bg-indigo-700'"
                            class="px-5 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white transition-colors">
                            Confirmar Assinatura
                        </button>
                    </form>
                </div>
            </div>
            @endif
