<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardGlobalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardGlobalController extends Controller
{
    public function __construct(private DashboardGlobalService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $kpis              = $this->service->kpis();
        $porEmpresa        = $this->service->patrimoniosPorEmpresa();
        $crescimento       = $this->service->crescimentoMensal();
        $topEmpresas       = $this->service->topEmpresasPorPatrimonio();

        return view('dashboards.global.index', compact('kpis', 'porEmpresa', 'crescimento', 'topEmpresas'));
    }
}
