<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $table = 'departamentos';

    protected $fillable = ['nome', 'descricao'];

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
