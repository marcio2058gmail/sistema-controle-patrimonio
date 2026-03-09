<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class DashboardCicloVidaService
{
    public function kpis(int $companyId): array
    {
        $base = Asset::forCompany($companyId);

        $idadeMedia = (clone $base)
            ->whereNotNull('data_aquisicao')
            ->selectRaw('AVG(DATEDIFF(CURDATE(), data_aquisicao) / 365.25) as media')
            ->value('media');

        $garantiasVencendo30 = (clone $base)
            ->whereNotNull('garantia_ate')
            ->whereBetween('garantia_ate', [now(), now()->addDays(30)])
            ->count();

        $garantiasVencidas = (clone $base)
            ->whereNotNull('garantia_ate')
            ->where('garantia_ate', '<', now())
            ->count();

        $comGarantia = (clone $base)
            ->whereNotNull('garantia_ate')
            ->where('garantia_ate', '>=', now())
            ->count();

        return [
            'idade_media'         => round($idadeMedia ?? 0, 1),
            'garantias_vencendo'  => $garantiasVencendo30,
            'garantias_vencidas'  => $garantiasVencidas,
            'com_garantia'        => $comGarantia,
        ];
    }

    public function distribuicaoPorIdade(int $companyId): array
    {
        $faixas = [
            'Menos de 1 ano' => [0, 1],
            '1 a 2 anos'     => [1, 2],
            '2 a 3 anos'     => [2, 3],
            '3 a 5 anos'     => [3, 5],
            'Mais de 5 anos' => [5, 999],
        ];

        $labels = [];
        $data   = [];
        foreach ($faixas as $label => [$min, $max]) {
            $labels[] = $label;
            $data[]   = Asset::forCompany($companyId)
                ->whereNotNull('data_aquisicao')
                ->whereRaw('DATEDIFF(CURDATE(), data_aquisicao) / 365.25 >= ?', [$min])
                ->whereRaw('DATEDIFF(CURDATE(), data_aquisicao) / 365.25 < ?', [$max])
                ->count();
        }

        return compact('labels', 'data');
    }

    public function aquisicoesPorMes(int $companyId): array
    {
        $rows = Asset::forCompany($companyId)
            ->whereNotNull('data_aquisicao')
            ->where('data_aquisicao', '>=', now()->subMonths(24))
            ->select(
                DB::raw("DATE_FORMAT(data_aquisicao, '%Y-%m') as mes"),
                DB::raw('count(*) as total'),
                DB::raw('SUM(COALESCE(valor_aquisicao, 0)) as valor')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $labels = [];
        $qtd    = [];
        $valor  = [];
        for ($i = 23; $i >= 0; $i--) {
            $mes      = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->translatedFormat('M/y');
            $qtd[]    = $rows[$mes]->total ?? 0;
            $valor[]  = round($rows[$mes]->valor ?? 0, 2);
        }

        return compact('labels', 'qtd', 'valor');
    }

    public function garantiasProximas(int $companyId, int $dias = 90): array
    {
        return Asset::forCompany($companyId)
            ->whereNotNull('garantia_ate')
            ->where('garantia_ate', '>=', now())
            ->where('garantia_ate', '<=', now()->addDays($dias))
            ->orderBy('garantia_ate')
            ->get(['codigo_patrimonio', 'descricao', 'garantia_ate', 'fornecedor'])
            ->toArray();
    }
}
