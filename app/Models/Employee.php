<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'funcionarios';

    protected $fillable = [
        'nome',
        'email',
        'cargo',
        'user_id',
        'departamento_id',
        'empresa_id',
    ];

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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'funcionario_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'funcionario_id');
    }
}
