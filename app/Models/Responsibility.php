<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsibility extends Model
{
    protected $table = 'responsabilidades';

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

    // ---------- Relationships ----------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'funcionario_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'patrimonio_id');
    }
}
