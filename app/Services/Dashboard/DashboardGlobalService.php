<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardGlobalService
{
    public function kpis(): array
    {
        return [
            'total_empresas'    => Company::where('ativa', true)->count(),
            'total_usuarios'    => User::count(),
            'total_patrimonios' => Asset::count(),
            'valor_total'       => Asset::whereNotNull('valor_atual')->sum('valor_atual')
                                   ?: Asset::whereNotNull('valor_aquisicao')->sum('valor_aquisicao'),
        ];
    }

    public function patrimoniosPorEmpresa(): array
    {
        $rows = DB::table('patrimonios')
            ->join('empresas', 'empresas.id', '=', 'patrimonios.empresa_id')
            ->select('empresas.nome', DB::raw('count(*) as total'))
            ->groupBy('empresas.id', 'empresas.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('nome')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    public function crescimentoMensal(): array
    {
        $rows = Asset::select(
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

    public function topEmpresasPorPatrimonio(int $limit = 8): array
    {
        return DB::table('patrimonios')
            ->join('empresas', 'empresas.id', '=', 'patrimonios.empresa_id')
            ->select('empresas.nome', DB::raw('count(*) as total'), DB::raw('SUM(COALESCE(valor_atual, valor_aquisicao, 0)) as valor'))
            ->groupBy('empresas.id', 'empresas.nome')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
