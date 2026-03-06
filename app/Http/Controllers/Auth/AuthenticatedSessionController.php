<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Super admin vai para gestão de empresas
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('companies.index'));
        }

        $empresas = $user->empresas()->where('ativa', true)->get();

        // Sem empresa: vai direto para seleção (que mostrará mensagem de erro)
        if ($empresas->isEmpty()) {
            return redirect()->route('companies.select');
        }

        // Auto-seleciona se só tem uma empresa
        if ($empresas->count() === 1) {
            session(['empresa_ativa_id' => $empresas->first()->id]);
            $home = $user->isAdmin() ? route('dashboard', absolute: false) : route('tickets.index', absolute: false);
            return redirect()->intended($home);
        }

        // Múltiplas empresas: vai para seleção
        return redirect()->route('companies.select');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
