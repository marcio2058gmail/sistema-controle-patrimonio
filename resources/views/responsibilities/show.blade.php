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

            {{-- Canvas de assinatura (para o responsável pelo termo — funcionário ou gestor — enquanto não assinou) --}}
            @if(!$responsibility->assinado && !auth()->user()->isAdmin() && auth()->user()->employee?->id === $responsibility->funcionario_id)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6" id="sig-block">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Assinatura Digital</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leia o termo acima e assine no campo abaixo para confirmar o recebimento dos equipamentos.</p>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-500 rounded-xl bg-white" id="sig-wrap" style="height:180px; position:relative;">
                    <canvas id="sig-canvas" style="position:absolute;top:0;left:0;cursor:crosshair;touch-action:none;"></canvas>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <button type="button" id="sig-clear" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Limpar</button>
                    <form id="sig-form" action="{{ route('responsibilities.assinar', $responsibility) }}" method="POST">
                        @csrf
                        <input type="hidden" name="assinatura_base64" id="sig-input">
                        <button type="button" id="sig-submit" disabled
                            class="px-5 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white transition-colors opacity-40 cursor-not-allowed">
                            Confirmar Assinatura
                        </button>
                    </form>
                </div>
            </div>

            <script>
            (function () {
                function initPad() {
                    // Aguarda window.SignaturePad (carregado pelo bundle Vite como module)
                    if (typeof window.SignaturePad === 'undefined') { setTimeout(initPad, 50); return; }

                    var wrap   = document.getElementById('sig-wrap');
                    var canvas = document.getElementById('sig-canvas');
                    if (!wrap || !canvas) return;

                    var w = wrap.clientWidth;
                    var h = wrap.clientHeight;
                    if (w === 0 || h === 0) { setTimeout(initPad, 50); return; }

                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width  = w * ratio;
                    canvas.height = h * ratio;
                    canvas.style.width  = w + 'px';
                    canvas.style.height = h + 'px';
                    canvas.getContext('2d').scale(ratio, ratio);

                    var pad = new window.SignaturePad(canvas, {
                        backgroundColor: 'rgb(255,255,255)',
                        penColor: 'rgb(20,20,20)',
                        minWidth: 1,
                        maxWidth: 3
                    });

                    var submitBtn = document.getElementById('sig-submit');

                    pad.addEventListener('endStroke', function () {
                        if (!pad.isEmpty()) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-40', 'cursor-not-allowed');
                            submitBtn.classList.add('hover:bg-indigo-700');
                        }
                    });

                    document.getElementById('sig-clear').addEventListener('click', function () {
                        pad.clear();
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-40', 'cursor-not-allowed');
                        submitBtn.classList.remove('hover:bg-indigo-700');
                    });

                    submitBtn.addEventListener('click', function () {
                        if (pad.isEmpty()) return;
                        document.getElementById('sig-input').value = pad.toDataURL('image/png');
                        document.getElementById('sig-form').submit();
                    });

                    window.addEventListener('resize', function () {
                        var data = pad.toData();
                        var nw = wrap.clientWidth;
                        canvas.width  = nw * ratio;
                        canvas.height = h  * ratio;
                        canvas.style.width  = nw + 'px';
                        canvas.style.height = h  + 'px';
                        canvas.getContext('2d').scale(ratio, ratio);
                        pad.clear();
                        pad.fromData(data);
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initPad);
                } else {
                    initPad();
                }
            })();
            </script>
            @endif
        </div>
    </div>
</x-app-layout>