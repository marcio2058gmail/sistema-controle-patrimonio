<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Super admin pode navegar sem empresa selecionada
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $empresas = $user->empresas()->where('ativa', true)->get();

        // Sem acesso a nenhuma empresa ativa
        if ($empresas->isEmpty()) {
            abort(403, 'Você não possui acesso a nenhuma empresa ativa.');
        }

        // Auto-seleciona se só tem uma empresa
        if ($empresas->count() === 1 && !session('empresa_ativa_id')) {
            session(['empresa_ativa_id' => $empresas->first()->id]);
            return $next($request);
        }

        // Precisa selecionar empresa
        if (!session('empresa_ativa_id')) {
            return redirect()->route('companies.select');
        }

        // Verifica se a empresa da sessão ainda é válida para o usuário
        $empresaAtiva = $empresas->firstWhere('id', session('empresa_ativa_id'));
        if (!$empresaAtiva) {
            session()->forget('empresa_ativa_id');
            return redirect()->route('companies.select');
        }

        return $next($request);
    }
}
