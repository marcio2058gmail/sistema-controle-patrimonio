@props(['status', 'type' => 'patrimonio'])

@php
    $config = match($type) {
        'patrimonio' => [
            'disponivel'  => ['bg-green-100 text-green-800',  'Disponível'],
            'em_uso'      => ['bg-blue-100 text-blue-800',    'Em Uso'],
            'manutencao'  => ['bg-yellow-100 text-yellow-800','Manutenção'],
        ],
        'chamado' => [
            'aberto'   => ['bg-yellow-100 text-yellow-800', 'Aberto'],
            'aprovado' => ['bg-blue-100 text-blue-800',     'Aprovado'],
            'negado'   => ['bg-red-100 text-red-800',       'Negado'],
            'entregue' => ['bg-green-100 text-green-800',   'Entregue'],
        ],
        default => []
    };
    [$classes, $label] = $config[$status] ?? ['bg-gray-100 text-gray-800', $status];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ $label }}
</span>
