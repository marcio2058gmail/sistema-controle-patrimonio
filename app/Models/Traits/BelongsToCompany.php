<?php

namespace App\Models\Traits;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * BelongsToCompany
 *
 * Trait que deve ser usada em todos os Models de negócio que pertencem
 * a uma empresa (funcionarios, patrimonios, chamados, termos, etc.).
 *
 * Responsabilidades:
 *  - Registrar o CompanyScope como Global Scope automático.
 *  - Preencher empresa_id automaticamente ao criar um registro.
 *  - Expor o relacionamento company() para eager loading.
 *  - Expor o scope withAllCompanies() para remover o filtro quando necessário.
 */
trait BelongsToCompany
{
    /**
     * Boot do trait — registra o Global Scope e o listener de criação.
     */
    public static function bootBelongsToCompany(): void
    {
        // Aplica o filtro global de empresa em todas as queries
        static::addGlobalScope(new CompanyScope());

        // Preenche empresa_id automaticamente ao criar um novo registro
        static::creating(function (self $model) {
            if (empty($model->empresa_id)) {
                /** @var User|null $authUser */
                $authUser = Auth::user();
                if (Auth::check() && $authUser && ! $authUser->isSuperAdmin()) {
                    $model->empresa_id = (int) session('empresa_ativa_id');
                }
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relationship
    // -------------------------------------------------------------------------

    /**
     * Relacionamento com a empresa dona deste registro.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Remove o Global Scope de empresa para esta query.
     * Útil para SuperAdmin buscar em todas as empresas explicitamente.
     *
     * Uso: Model::withAllCompanies()->get();
     */
    public static function withAllCompanies()
    {
        return static::withoutGlobalScope(CompanyScope::class);
    }

    /**
     * Filtra pelo empresa_id informado (ou pela sessão ativa se omitido).
     * Versão local scope para uso manual quando necessário.
     *
     * Uso: Model::forCompany(5)->get();
     */
    public function scopeForCompany($query, ?int $empresaId = null)
    {
        $empresaId = $empresaId ?? (int) session('empresa_ativa_id');

        if ($empresaId) {
            $query->where($this->getTable() . '.empresa_id', $empresaId);
        }

        return $query;
    }
}
