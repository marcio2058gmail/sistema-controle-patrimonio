<?php

namespace App\Services\Dashboard;

use App\Models\Manutencao;
use Illuminate\Support\Facades\DB;

class DashboardManutencaoService
{
    public function kpis(int $companyId): array
    {
        $base = $this->baseQuery($companyId);

        $statusCounts = (clone $base)
            ->select('manutencoes.status', DB::raw('count(*) as total'))
            ->groupBy('manutencoes.status')
            ->pluck('total', 'manutencoes.status')
            ->toArray();

        // Tempo médio de resolução em dias
        $tempoMedio = (clone $base)
            ->where('manutencoes.status', 'concluida')
            ->whereNotNull('manutencoes.data_conclusao')
            ->selectRaw('AVG(DATEDIFF(manutencoes.data_conclusao, manutencoes.data_abertura)) as media')
            ->value('media');

        $custoTotal = (clone $base)
            ->whereNotNull('manutencoes.custo')
            ->sum('manutencoes.custo');

        return [
            'agendada'       => $statusCounts['agendada']     ?? 0,
            'em_andamento'   => $statusCounts['em_andamento'] ?? 0,
            'concluida'      => $statusCounts['concluida']    ?? 0,
            'cancelada'      => $statusCounts['cancelada']    ?? 0,
            'tempo_medio'    => round($tempoMedio ?? 0, 1),
            'custo_total'    => round($custoTotal ?? 0, 2),
        ];
    }

    public function porStatus(int $companyId): array
    {
        $rows = $this->baseQuery($companyId)
            ->select('manutencoes.status', DB::raw('count(*) as total'))
            ->groupBy('manutencoes.status')
            ->pluck('total', 'manutencoes.status')
            ->toArray();

        $map = Manutencao::STATUS;
        return [
            'labels' => array_values($map),
            'data'   => array_map(fn ($k) => $rows[$k] ?? 0, array_keys($map)),
        ];
    }

    public function porMes(int $companyId): array
    {
        $rows = $this->baseQuery($companyId)
            ->select(
                DB::raw("DATE_FORMAT(manutencoes.data_abertura, '%Y-%m') as mes"),
                DB::raw('count(*) as total')
            )
            ->where('manutencoes.data_abertura', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $labels = [];
        $data   = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes      = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->translatedFormat('M/y');
            $data[]   = $rows[$mes] ?? 0;
        }

        return compact('labels', 'data');
    }

    public function porEquipamento(int $companyId, int $limit = 10): array
    {
        return $this->baseQuery($companyId)
            ->select('patrimonios.codigo_patrimonio', 'patrimonios.descricao', DB::raw('count(*) as total'), DB::raw('SUM(COALESCE(manutencoes.custo, 0)) as custo'))
            ->groupBy('patrimonios.id', 'patrimonios.codigo_patrimonio', 'patrimonios.descricao')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn($r) => ['codigo_patrimonio' => $r->codigo_patrimonio, 'descricao' => $r->descricao, 'total' => $r->total, 'custo' => $r->custo])
            ->toArray();
    }

    public function recentes(int $companyId, int $limit = 10): array
    {
        return $this->baseQuery($companyId)
            ->select('manutencoes.*', 'patrimonios.codigo_patrimonio', 'patrimonios.descricao as patrimonio_descricao')
            ->orderByDesc('manutencoes.data_abertura')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function baseQuery(int $companyId)
    {
        $q = DB::table('manutencoes')
            ->join('patrimonios', 'patrimonios.id', '=', 'manutencoes.patrimonio_id');

        if ($companyId) {
            $q->where('patrimonios.empresa_id', $companyId);
        }

        return $q;
    }
}
