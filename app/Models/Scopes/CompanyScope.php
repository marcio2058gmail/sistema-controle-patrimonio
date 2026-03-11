<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * CompanyScope — Global Scope multiempresa.
 *
 * Filtra automaticamente todas as queries de models que usam a
 * Trait BelongsToCompany pelo empresa_id da sessão ativa.
 *
 * SuperAdmin (role = super_admin) não recebe nenhum filtro, podendo
 * visualizar dados de todas as empresas.
 */
class CompanyScope implements Scope
{
    /**
     * @param Builder<Model> $builder
     * @param Model          $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Sem usuário autenticado → não aplica filtro (ex: comandos artisan, seeders)
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        // SuperAdmin enxerga tudo
        /** @var User $user */
        if ($user->isSuperAdmin()) {
            return;
        }

        // Obtém a empresa ativa da sessão
        $empresaId = (int) session('empresa_ativa_id');

        if ($empresaId) {
            /** @var Model $model */
            $builder->where($model->getTable() . '.empresa_id', $empresaId);
        }
    }
}
