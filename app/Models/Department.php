<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departamentos';

    protected $fillable = ['nome', 'descricao', 'empresa_id'];

    // ---------- Scope de empresa ----------

    public function scopeForCompany($query, ?int $companyId = null)
    {
        $companyId = $companyId ?? (int) session('empresa_ativa_id');
        if ($companyId) {
            $query->where('empresa_id', $companyId);
        }
        return $query;
    }

    // ---------- Relationships ----------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'departamento_id');
    }

    public function totalAssetsInUse(): int
    {
        return Responsibility::whereIn('funcionario_id', $this->employees()->pluck('id'))
            ->whereNull('data_devolucao')
            ->count();
    }
}
