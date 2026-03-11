<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use BelongsToCompany;
    protected $table = 'funcionarios';

    protected $fillable = [
        'empresa_id',
        'nome',
        'email',
        'cargo',
        'rg_numero',
        'ctps_numero',
        'ctps_serie',
        'user_id',
        'departamento_id',
    ];

    // ---------- Relationships ----------

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departamento_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
