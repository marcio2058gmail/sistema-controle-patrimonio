<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardEmpresaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardEmpresaController extends Controller
{
    public function __construct(private DashboardEmpresaService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isSuperAdmin(), 403);

        $companyId = (int) session('empresa_ativa_id');

        $kpis        = $this->service->kpis($companyId);
        $porStatus   = $this->service->patrimoniosPorStatus($companyId);
        $porDepto    = $this->service->patrimoniosPorDepartamento($companyId);
        $crescimento = $this->service->crescimentoMensal($companyId);

        return view('dashboards.empresa.index', compact('kpis', 'porStatus', 'porDepto', 'crescimento'));
    }
}
