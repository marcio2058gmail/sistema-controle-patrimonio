<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use BelongsToCompany;

    protected $table = 'departamentos';

    protected $fillable = ['nome', 'descricao', 'empresa_id'];

    // ---------- Relationships ----------

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
