<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardManutencaoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardManutencaoController extends Controller
{
    public function __construct(private DashboardManutencaoService $service) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->isSuperAdmin(), 403);

        $companyId   = (int) session('empresa_ativa_id');

        $kpis        = $this->service->kpis($companyId);
        $porStatus   = $this->service->porStatus($companyId);
        $porMes      = $this->service->porMes($companyId);
        $porEquip    = $this->service->porEquipamento($companyId, 10);
        $recentes    = $this->service->recentes($companyId, 10);

        return view('dashboards.manutencao.index', compact('kpis', 'porStatus', 'porMes', 'porEquip', 'recentes'));
    }
}
