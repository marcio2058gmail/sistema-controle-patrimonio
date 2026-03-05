<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Responsibility extends Model
{
    protected $table = 'termos';

    protected $fillable = [
        'funcionario_id',
        'data_entrega',
        'data_devolucao',
        'termo_responsabilidade',
        'assinado',
    ];

    protected $casts = [
        'data_entrega'   => 'date',
        'data_devolucao' => 'date',
        'assinado'       => 'boolean',
    ];

    // ---------- Relationships ----------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'funcionario_id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'termo_patrimonios', 'termo_id', 'patrimonio_id')
                    ->withTimestamps();
    }
}
