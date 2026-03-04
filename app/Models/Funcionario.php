<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funcionario extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'cargo',
        'user_id',
    ];

    // ---------- Relacionamentos ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responsabilidades(): HasMany
    {
        return $this->hasMany(Responsabilidade::class);
    }

    public function chamados(): HasMany
    {
        return $this->hasMany(Chamado::class);
    }
}
