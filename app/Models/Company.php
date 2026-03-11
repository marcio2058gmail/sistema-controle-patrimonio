<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
        'ativa',
        'account_status',
        'modelo_pdf',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->ativa && in_array($this->account_status, ['active', 'trial']);
    }

    // -------------------------------------------------------------------------
    // Relacionamentos — Usuários
    // -------------------------------------------------------------------------

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user', 'empresa_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Relacionamentos — Dados de negócio (empresa_id)
    // -------------------------------------------------------------------------

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'empresa_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'empresa_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'empresa_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'empresa_id');
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'empresa_id');
    }

    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class, 'empresa_id');
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'empresa_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'empresa_id');
    }

    // -------------------------------------------------------------------------
    // Relacionamentos — SaaS
    // -------------------------------------------------------------------------

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'empresa_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'empresa_id')
            ->whereIn('status', ['active', 'trial'])
            ->latestOfMany('inicio_em');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'company_id');
    }

    // Contagem de patrimônios utilizados (para verificação de limites)
    public function getAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }
}
