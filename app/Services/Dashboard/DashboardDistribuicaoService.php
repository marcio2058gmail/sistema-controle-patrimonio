<?php

namespace App\Services\Dashboard;

use App\Models\Asset;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class DashboardDistribuicaoService
{
    public function kpis(int $companyId): array
    {
        $totalAtivos = Asset::forCompany($companyId)->count();

        $emUso = DB::table('termo_patrimonios')
            ->join('termos', 'termos.id', '=', 'termo_patrimonios.termo_id')
            ->join('patrimonios', 'patrimonios.id', '=', 'termo_patrimonios.patrimonio_id')
            ->where('patrimonios.empresa_id', $companyId)
            ->whereNull('termos.data_devolucao')
            ->distinct('termo_patrimonios.patrimonio_id')
            ->count('termo_patrimonios.patrimonio_id');

        $semAtribuicao = $totalAtivos - $emUso;

        $totalFuncionarios = Employee::forCompany($companyId)->count();

        $funcionariosComPatrimonio = DB::table('termos')
            ->join('funcionarios', 'funcionarios.id', '=', 'termos.funcionario_id')
            ->where('funcionarios.empresa_id', $companyId)
            ->whereNull('termos.data_devolucao')
            ->distinct('termos.funcionario_id')
            ->count('termos.funcionario_id');

        $funcionariosSemPatrimonio = max(0, $totalFuncionarios - $funcionariosComPatrimonio);

        return compact('totalAtivos', 'emUso', 'semAtribuicao', 'totalFuncionarios', 'funcionariosComPatrimonio', 'funcionariosSemPatrimonio');
    }

    public function top10Funcionarios(int $companyId): array
    {
        return DB::table('termos')
            ->join('funcionarios', 'funcionarios.id', '=', 'termos.funcionario_id')
            ->join('termo_patrimonios', 'termo_patrimonios.termo_id', '=', 'termos.id')
            ->where('funcionarios.empresa_id', $companyId)
            ->whereNull('termos.data_devolucao')
            ->select('funcionarios.nome', DB::raw('count(distinct termo_patrimonios.patrimonio_id) as total'))
            ->groupBy('funcionarios.id', 'funcionarios.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function patrimoniosPorDepartamento(int $companyId): array
    {
        $rows = DB::table('termo_patrimonios')
            ->join('termos', 'termos.id', '=', 'termo_patrimonios.termo_id')
            ->join('funcionarios', 'funcionarios.id', '=', 'termos.funcionario_id')
            ->join('departamentos', 'departamentos.id', '=', 'funcionarios.departamento_id')
            ->join('patrimonios', 'patrimonios.id', '=', 'termo_patrimonios.patrimonio_id')
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

    public function patrimoniosPorFuncionario(int $companyId): array
    {
        $rows = DB::table('termos')
            ->join('funcionarios', 'funcionarios.id', '=', 'termos.funcionario_id')
            ->join('termo_patrimonios', 'termo_patrimonios.termo_id', '=', 'termos.id')
            ->where('funcionarios.empresa_id', $companyId)
            ->whereNull('termos.data_devolucao')
            ->select('funcionarios.nome', DB::raw('count(distinct termo_patrimonios.patrimonio_id) as total'))
            ->groupBy('funcionarios.id', 'funcionarios.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('nome')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }
}
