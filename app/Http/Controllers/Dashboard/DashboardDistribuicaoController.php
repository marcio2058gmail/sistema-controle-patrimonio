<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardDistribuicaoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardDistribuicaoController extends Controller
{
    public function __construct(private DashboardDistribuicaoService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isSuperAdmin(), 403);

        $companyId       = (int) session('empresa_ativa_id');

        $kpis            = $this->service->kpis($companyId);
        $top10            = $this->service->top10Funcionarios($companyId);
        $porDepartamento = $this->service->patrimoniosPorDepartamento($companyId);
        $porFuncionario  = $this->service->patrimoniosPorFuncionario($companyId);

        return view('dashboards.distribuicao.index', compact('kpis', 'top10', 'porDepartamento', 'porFuncionario'));
    }
}
