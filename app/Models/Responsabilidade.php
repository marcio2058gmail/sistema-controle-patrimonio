<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsabilidade extends Model
{
    protected $fillable = [
        'funcionario_id',
        'patrimonio_id',
        'data_entrega',
        'data_devolucao',
        'termo_responsabilidade',
        'assinado',
    ];

    protected $casts = [
        'data_entrega'    => 'date',
        'data_devolucao'  => 'date',
        'assinado'        => 'boolean',
    ];

    // ---------- Relacionamentos ----------

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function patrimonio(): BelongsTo
    {
        return $this->belongsTo(Patrimonio::class);
    }
}
