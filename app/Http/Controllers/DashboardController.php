<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\Departamento;
use App\Models\Funcionario;
use App\Models\Patrimonio;
use App\Models\Responsabilidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user        = $request->user();
        $funcionario = $user->funcionario;

        // Contexto do gestor: departamento ao qual está vinculado
        $departamento = null;
        $deptId       = null;

        if ($user->isGestor() && $funcionario?->departamento_id) {
            $deptId       = $funcionario->departamento_id;
            $departamento = Departamento::find($deptId);
        }

        // -------------------------------------------------------
        // KPIs — escopos por perfil
        // -------------------------------------------------------
        if ($user->isAdmin()) {
            $totalPatrimonios        = Patrimonio::count();
            $totalFuncionarios       = Funcionario::count();
            $totalChamadosAbertos    = Chamado::where('status', Chamado::STATUS_ABERTO)->count();
            $totalResponsabilidades  = Responsabilidade::whereNull('data_devolucao')->count();
            $patrimoniosSemResponsavel = Patrimonio::where('status', Patrimonio::STATUS_DISPONIVEL)->count();

            // Breakdown por departamento
            $departamentosStats = Departamento::withCount('funcionarios')
                ->with('funcionarios:id,departamento_id')
                ->orderBy('nome')
                ->get()
                ->map(function ($dept) {
                    $ids = $dept->funcionarios->pluck('id');
                    return [
                        'departamento'       => $dept,
                        'total_funcionarios' => $dept->funcionarios_count,
                        'patrimonios_em_uso' => $ids->isEmpty() ? 0 :
                            Responsabilidade::whereIn('funcionario_id', $ids)
                                ->whereNull('data_devolucao')
                                ->distinct('patrimonio_id')
                                ->count('patrimonio_id'),
                        'chamados_abertos'   => $ids->isEmpty() ? 0 :
                            Chamado::where('status', Chamado::STATUS_ABERTO)
                                ->whereIn('funcionario_id', $ids)
                                ->count(),
                    ];
                });
        } elseif ($user->isGestor() && $deptId) {
            $idsNoDept = Funcionario::where('departamento_id', $deptId)->pluck('id');

            $totalPatrimonios        = Responsabilidade::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsNoDept)
                                           ->distinct('patrimonio_id')->count('patrimonio_id');
            $totalFuncionarios       = $idsNoDept->count();
            $totalChamadosAbertos    = Chamado::where('status', Chamado::STATUS_ABERTO)
                                           ->whereIn('funcionario_id', $idsNoDept)->count();
            $totalResponsabilidades  = Responsabilidade::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsNoDept)->count();
            $patrimoniosSemResponsavel = null; // não se aplica na visão do departamento
            $departamentosStats = collect();
        } else {
            // funcionário — só os próprios dados
            $idsFunc = $funcionario ? [$funcionario->id] : [];

            $totalPatrimonios        = Responsabilidade::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsFunc)
                                           ->distinct('patrimonio_id')->count('patrimonio_id');
            $totalFuncionarios       = null;
            $totalChamadosAbertos    = Chamado::where('status', Chamado::STATUS_ABERTO)
                                           ->whereIn('funcionario_id', $idsFunc)->count();
            $totalResponsabilidades  = Responsabilidade::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsFunc)->count();
            $patrimoniosSemResponsavel = null;
            $departamentosStats = collect();
        }

        // -------------------------------------------------------
        // Gráfico: patrimônios por status
        // -------------------------------------------------------
        $patrimonioChartLabels = [];
        $patrimonioChartData   = [];

        if ($user->isAdmin()) {
            $patrimoniosPorStatus = Patrimonio::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        } elseif ($user->isGestor() && $deptId) {
            // Patrimônios nas responsabilidades ativas do departamento, agrupados por status
            $patrimoniosPorStatus = Responsabilidade::whereNull('data_devolucao')
                ->whereIn('funcionario_id', Funcionario::where('departamento_id', $deptId)->pluck('id'))
                ->join('patrimonios', 'patrimonios.id', '=', 'responsabilidades.patrimonio_id')
                ->select('patrimonios.status', DB::raw('count(distinct patrimonios.id) as total'))
                ->groupBy('patrimonios.status')
                ->pluck('total', 'patrimonios.status')
                ->toArray();
        } else {
            $patrimoniosPorStatus = [];
        }

        $statusLabelsPatrimonio = Patrimonio::statusLabels();
        foreach ($statusLabelsPatrimonio as $key => $label) {
            $patrimonioChartLabels[] = $label;
            $patrimonioChartData[]   = $patrimoniosPorStatus[$key] ?? 0;
        }

        // -------------------------------------------------------
        // Gráfico: chamados por mês (últimos 6 meses)
        // -------------------------------------------------------
        $chamadosQuery = Chamado::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes');

        if ($user->isGestor() && $deptId) {
            $chamadosQuery->whereIn('funcionario_id',
                Funcionario::where('departamento_id', $deptId)->pluck('id'));
        } elseif ($user->isFuncionario()) {
            $chamadosQuery->whereIn('funcionario_id', $funcionario ? [$funcionario->id] : [0]);
        }

        $chamadosPorMes = $chamadosQuery->pluck('total', 'mes')->toArray();

        $mesesLabels = [];
        $mesesData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('Y-m');
            $mesesLabels[] = now()->subMonths($i)->translatedFormat('M/Y');
            $mesesData[]   = $chamadosPorMes[$mes] ?? 0;
        }

        // -------------------------------------------------------
        // Últimos chamados abertos
        // -------------------------------------------------------
        $ultimosChamadosQuery = Chamado::with(['funcionario', 'patrimonios'])
            ->where('status', Chamado::STATUS_ABERTO)
            ->latest();

        if ($user->isGestor() && $deptId) {
            $ultimosChamadosQuery->whereIn('funcionario_id',
                Funcionario::where('departamento_id', $deptId)->pluck('id'));
        } elseif ($user->isFuncionario()) {
            $ultimosChamadosQuery->whereIn('funcionario_id', $funcionario ? [$funcionario->id] : [0]);
        }

        $ultimosChamados = $ultimosChamadosQuery->take(5)->get();

        return view('dashboard', compact(
            'totalPatrimonios',
            'totalFuncionarios',
            'totalChamadosAbertos',
            'totalResponsabilidades',
            'patrimonioChartLabels',
            'patrimonioChartData',
            'mesesLabels',
            'mesesData',
            'ultimosChamados',
            'patrimoniosSemResponsavel',
            'departamento',
            'departamentosStats',
        ));
    }
}
