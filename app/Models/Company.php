<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    // ---------- Relacionamentos ----------

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'empresa_user', 'empresa_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

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
}
