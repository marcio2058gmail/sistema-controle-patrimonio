<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user        = $request->user();
        $employee = $user->employee;

        // Contexto do gestor: departamento ao qual está vinculado
        $department = null;
        $deptId       = null;

        if ($user->isManager() && $employee?->departamento_id) {
            $deptId       = $employee->departamento_id;
            $department = Department::find($deptId);
        }

        // -------------------------------------------------------
        // KPIs — escopos por perfil
        // -------------------------------------------------------
        if ($user->isAdmin()) {
            $totalAssets        = Asset::count();
            $totalEmployees       = Employee::count();
            $totalOpenTickets    = Ticket::where('status', Ticket::STATUS_OPEN)->count();
            $totalResponsibilities  = Responsibility::whereNull('data_devolucao')->count();
            $patrimoniosSemResponsavel = Asset::where('status', Asset::STATUS_AVAILABLE)->count();

            // Breakdown por departamento
            $departmentStats = Department::withCount('employees')
                ->with('employees:id,departamento_id')
                ->orderBy('nome')
                ->get()
                ->map(function ($dept) {
                    $ids = $dept->employees->pluck('id');
                    return [
                        'department'       => $dept,
                        'total_funcionarios' => $dept->employees_count,
                        'patrimonios_em_uso' => $ids->isEmpty() ? 0 :
                            Responsibility::whereIn('funcionario_id', $ids)
                                ->whereNull('data_devolucao')
                                ->distinct('patrimonio_id')
                                ->count('patrimonio_id'),
                        'chamados_abertos'   => $ids->isEmpty() ? 0 :
                            Ticket::where('status', Ticket::STATUS_OPEN)
                                ->whereIn('funcionario_id', $ids)
                                ->count(),
                    ];
                });
            $employeeStats = collect();
        } elseif ($user->isManager() && $deptId) {
            $idsNoDept = Employee::where('departamento_id', $deptId)->pluck('id');

            $idsComPatrimonio = Responsibility::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsNoDept)
                                           ->distinct('funcionario_id')
                                           ->pluck('funcionario_id');

            $totalAssets        = Responsibility::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsNoDept)
                                           ->distinct('patrimonio_id')->count('patrimonio_id');
            $totalEmployees       = $idsNoDept->count();
            $totalOpenTickets    = Ticket::where('status', Ticket::STATUS_OPEN)
                                           ->whereIn('funcionario_id', $idsNoDept)->count();
            $totalResponsibilities  = $idsNoDept->count() - $idsComPatrimonio->count(); // funcionários SEM patrimônio
            $patrimoniosSemResponsavel = null;
            $departmentStats = collect();

            // Tabela de funcionários do depto com seus números
            $employeeStats = Employee::whereIn('id', $idsNoDept)
                ->with('user')
                ->orderBy('nome')
                ->get()
                ->map(function ($emp) {
                    return [
                        'employee'           => $emp,
                        'patrimonios_em_uso' => Responsibility::whereNull('data_devolucao')
                                                    ->where('funcionario_id', $emp->id)
                                                    ->count(),
                        'chamados_abertos'   => Ticket::where('status', Ticket::STATUS_OPEN)
                                                    ->where('funcionario_id', $emp->id)
                                                    ->count(),
                    ];
                });
        } else {
            // funcionário — só os próprios dados
            $idsFunc = $employee ? [$employee->id] : [];

            $totalAssets        = Responsibility::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsFunc)
                                           ->distinct('patrimonio_id')->count('patrimonio_id');
            $totalEmployees       = null;
            $totalOpenTickets    = Ticket::where('status', Ticket::STATUS_OPEN)
                                           ->whereIn('funcionario_id', $idsFunc)->count();
            $totalResponsibilities  = Responsibility::whereNull('data_devolucao')
                                           ->whereIn('funcionario_id', $idsFunc)->count();
            $patrimoniosSemResponsavel = null;
            $departmentStats = collect();
            $employeeStats = collect();
        }

        // -------------------------------------------------------
        // Gráfico: patrimônios por status
        // -------------------------------------------------------
        $assetChartLabels = [];
        $assetChartData   = [];

        if ($user->isAdmin()) {
            $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        } elseif ($user->isManager() && $deptId) {
            // Cobertura: funcionários com vs sem patrimônio ativo no departamento
            $idsNoDeptChart = Employee::where('departamento_id', $deptId)->pluck('id');
            $comPatrimonio  = Responsibility::whereNull('data_devolucao')
                ->whereIn('funcionario_id', $idsNoDeptChart)
                ->distinct('funcionario_id')
                ->count('funcionario_id');
            $semPatrimonio  = $idsNoDeptChart->count() - $comPatrimonio;

            $assetChartLabels = ['Com patrimônio', 'Sem patrimônio'];
            $assetChartData   = [$comPatrimonio, $semPatrimonio];
            // Pula o loop de status abaixo
            goto after_status_loop;
        } else {
            $assetsByStatus = [];
        }

        $statusLabelsPatrimonio = Asset::statusLabels();
        foreach ($statusLabelsPatrimonio as $key => $label) {
            $assetChartLabels[] = $label;
            $assetChartData[]   = $assetsByStatus[$key] ?? 0;
        }

        after_status_loop:

        // -------------------------------------------------------
        // Gráfico: chamados por mês (últimos 6 meses)
        // -------------------------------------------------------
        $chamadosQuery = Ticket::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes');

        if ($user->isManager() && $deptId) {
            $chamadosQuery->whereIn('funcionario_id',
                Employee::where('departamento_id', $deptId)->pluck('id'));
        } elseif ($user->isEmployee()) {
            $chamadosQuery->whereIn('funcionario_id', $employee ? [$employee->id] : [0]);
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
        $latestTicketsQuery = Ticket::with(['employee', 'assets'])
            ->where('status', Ticket::STATUS_OPEN)
            ->latest();

        if ($user->isManager() && $deptId) {
            $latestTicketsQuery->whereIn('funcionario_id',
                Employee::where('departamento_id', $deptId)->pluck('id'));
        } elseif ($user->isEmployee()) {
            $latestTicketsQuery->whereIn('funcionario_id', $employee ? [$employee->id] : [0]);
        }

        $latestTickets = $latestTicketsQuery->take(5)->get();

        return view('dashboard', compact(
            'totalAssets',
            'totalEmployees',
            'totalOpenTickets',
            'totalResponsibilities',
            'assetChartLabels',
            'assetChartData',
            'mesesLabels',
            'mesesData',
            'latestTickets',
            'patrimoniosSemResponsavel',
            'department',
            'departmentStats',
            'employeeStats',
        ));
    }
}
