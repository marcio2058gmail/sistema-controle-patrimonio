<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;

class DashboardEmpresaService
{
    public function kpis(int $companyId): array
    {
        $base = Asset::forCompany($companyId);

        $statusCounts = (clone $base)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $valorTotal = (clone $base)->sum(DB::raw('COALESCE(valor_atual, valor_aquisicao, 0)'));

        return [
            'total'         => array_sum($statusCounts),
            'disponivel'    => $statusCounts['disponivel']    ?? 0,
            'em_uso'        => $statusCounts['em_uso']        ?? 0,
            'manutencao'    => $statusCounts['manutencao']    ?? 0,
            'descartado'    => $statusCounts['descartado']    ?? 0,
            'valor_total'   => $valorTotal,
        ];
    }

    public function patrimoniosPorStatus(int $companyId): array
    {
        $rows = Asset::forCompany($companyId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $labels_map = Asset::statusLabels();
        $labels = [];
        $data   = [];
        foreach ($labels_map as $key => $label) {
            $labels[] = $label;
            $data[]   = $rows[$key] ?? 0;
        }

        return compact('labels', 'data');
    }

    public function patrimoniosPorDepartamento(int $companyId): array
    {
        $rows = DB::table('patrimonios')
            ->join('termo_patrimonios', 'termo_patrimonios.patrimonio_id', '=', 'patrimonios.id')
            ->join('termos', 'termos.id', '=', 'termo_patrimonios.termo_id')
            ->join('funcionarios', 'funcionarios.id', '=', 'termos.funcionario_id')
            ->join('departamentos', 'departamentos.id', '=', 'funcionarios.departamento_id')
            ->where('patrimonios.empresa_id', $companyId)
            ->whereNull('termos.data_devolucao')
            ->select('departamentos.nome', DB::raw('count(distinct patrimonios.id) as total'))
            ->groupBy('departamentos.id', 'departamentos.nome')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('nome')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    public function crescimentoMensal(int $companyId): array
    {
        $rows = Asset::forCompany($companyId)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
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
}
