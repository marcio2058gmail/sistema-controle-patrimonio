<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PlanController — CRUD de planos SaaS.
 * Acesso restrito a SuperAdmin.
 */
class PlanController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $plans = Plan::orderBy('preco')->get();

        return view('plans.index', compact('plans'));
    }

    public function create(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        return view('plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $data = $request->validate([
            'nome'               => ['required', 'string', 'max:100'],
            'limite_patrimonios' => ['required', 'integer', 'min:1'],
            'preco'              => ['required', 'numeric', 'min:0'],
            'ativo'              => ['boolean'],
        ]);

        Plan::create($data);

        return redirect()->route('plans.index')
            ->with('sucesso', 'Plano criado com sucesso.');
    }

    public function edit(Request $request, Plan $plan): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $data = $request->validate([
            'nome'               => ['required', 'string', 'max:100'],
            'limite_patrimonios' => ['required', 'integer', 'min:1'],
            'preco'              => ['required', 'numeric', 'min:0'],
            'ativo'              => ['boolean'],
        ]);

        $plan->update($data);

        return redirect()->route('plans.index')
            ->with('sucesso', 'Plano atualizado com sucesso.');
    }

    public function destroy(Request $request, Plan $plan): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->isSuperAdmin(), 403);

        $plan->delete();

        return redirect()->route('plans.index')
            ->with('sucesso', 'Plano removido.');
    }
}
