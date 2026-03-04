<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Abrir Chamado</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-alert />

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('tickets.store') }}" method="POST" class="space-y-5">
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

                        {{-- Campo de busca --}}
                        <input type="text" id="busca-patrimonio" placeholder="Buscar por código ou descrição..."
                            class="mb-2 block w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300" />

                        <div class="border border-gray-300 dark:border-gray-600 rounded-md overflow-y-auto max-h-56 divide-y divide-gray-100 dark:divide-gray-700"
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

                        {{-- Contador de selecionados --}}
                        <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-400" id="contador-patrimonio"></p>
                        <x-input-error :messages="$errors->get('patrimonio_ids')" class="mt-1" />
                        <x-input-error :messages="$errors->get('patrimonio_ids.*')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="descricao" value="Descrição *" />
                        <textarea id="descricao" name="descricao" rows="5"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-300"
                            required minlength="10" placeholder="Descreva o motivo do chamado...">{{ old('descricao') }}</textarea>
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('tickets.index') }}"
                           class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <x-primary-button>Abrir Chamado</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    // Busca em tempo real nos patrimônios
    const buscaInput = document.getElementById('busca-patrimonio');
    const items = document.querySelectorAll('.patrimonio-item');
    const contador = document.getElementById('contador-patrimonio');

    function atualizarContador() {
        const n = document.querySelectorAll('.patrimonio-item input:checked').length;
        contador.textContent = n > 0 ? n + ' patrimônio(s) selecionado(s)' : '';
    }

    buscaInput?.addEventListener('input', function () {
        const termo = this.value.toLowerCase();
        items.forEach(item => {
            item.style.display = item.dataset.busca.includes(termo) ? '' : 'none';
        });
    });

    items.forEach(item => {
        item.querySelector('input')?.addEventListener('change', atualizarContador);
    });

    atualizarContador();
</script>
@endpush
