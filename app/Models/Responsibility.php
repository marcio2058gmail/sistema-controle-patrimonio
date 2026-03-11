<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Responsibility extends Model
{
    use BelongsToCompany;

    protected $table = 'termos';

    protected $fillable = [
        'empresa_id',
        'funcionario_id',
        'data_entrega',
        'data_devolucao',
        'observacao_devolucao',
        'termo_responsabilidade',
        'assinado',
        'assinatura_base64',
        'assinado_em',
        'assinado_ip',
    ];

    protected $casts = [
        'data_entrega'   => 'date',
        'data_devolucao' => 'date',
        'assinado'       => 'boolean',
        'assinado_em'    => 'datetime',
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
