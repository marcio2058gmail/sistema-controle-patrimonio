<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardCicloVidaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardCicloVidaController extends Controller
{
    public function __construct(private DashboardCicloVidaService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isSuperAdmin(), 403);

        $companyId       = (int) session('empresa_ativa_id');

        $kpis            = $this->service->kpis($companyId);
        $porIdade        = $this->service->distribuicaoPorIdade($companyId);
        $aquisicoes      = $this->service->aquisicoesPorMes($companyId);
        $garantiasProximas = $this->service->garantiasProximas($companyId, 90);

        return view('dashboards.ciclovida.index', compact('kpis', 'porIdade', 'aquisicoes', 'garantiasProximas'));
    }
}
