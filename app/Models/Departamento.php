<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
    protected $fillable = [
        'nome',
        'descricao',
    ];

    // ---------- Relacionamentos ----------

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class);
    }

    // ---------- Helpers ----------

    /** Total de patrimônios em uso pelos funcionários do departamento */
    public function totalPatrimoniosEmUso(): int
    {
        return Responsabilidade::whereIn('funcionario_id', $this->funcionarios()->pluck('id'))
            ->whereNull('data_devolucao')
            ->count();
    }
}
