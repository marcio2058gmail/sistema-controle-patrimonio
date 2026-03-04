<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\Funcionario;
use App\Models\Patrimonio;
use App\Models\Responsabilidade;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // KPIs principais
        $totalPatrimonios    = Patrimonio::count();
        $totalFuncionarios   = Funcionario::count();
        $totalChamadosAbertos = Chamado::where('status', Chamado::STATUS_ABERTO)->count();
        $totalResponsabilidades = Responsabilidade::whereNull('data_devolucao')->count();

        // Distribuição de status dos patrimônios
        $patrimoniosPorStatus = Patrimonio::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusLabelsPatrimonio = Patrimonio::statusLabels();

        $patrimonioChartLabels = [];
        $patrimonioChartData   = [];
        foreach ($statusLabelsPatrimonio as $key => $label) {
            $patrimonioChartLabels[] = $label;
            $patrimonioChartData[]   = $patrimoniosPorStatus[$key] ?? 0;
        }

        // Chamados por mês (últimos 6 meses)
        $chamadosPorMes = Chamado::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $mesesLabels = [];
        $mesesData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i)->format('Y-m');
            $mesesLabels[] = now()->subMonths($i)->translatedFormat('M/Y');
            $mesesData[]   = $chamadosPorMes[$mes] ?? 0;
        }

        // Últimos chamados abertos (para tabela no dashboard)
        $ultimosChamados = Chamado::with(['funcionario', 'patrimonios'])
            ->where('status', Chamado::STATUS_ABERTO)
            ->latest()
            ->take(5)
            ->get();

        // Patrimônios sem responsável ativo
        $patrimoniosSemResponsavel = Patrimonio::where('status', Patrimonio::STATUS_DISPONIVEL)->count();

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
        ));
    }
}
