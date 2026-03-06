<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Verifica se o usuário autenticado possui pelo menos um dos perfis exigidos.
     *
     * Uso na rota: middleware('role:admin,gestor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Super admin tem acesso total a qualquer rota protegida por role
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Verifica o papel no contexto da empresa ativa (ou global se sem empresa)
        $userRole = $request->user()->roleInCompany();
        if (! in_array($userRole, $roles)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
