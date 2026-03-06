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

    // ---------- Scope de empresa ----------

    public function scopeForCompany($query, ?int $companyId = null)
    {
        $companyId = $companyId ?? (int) session('empresa_ativa_id');
        if ($companyId) {
            $query->whereHas('employee', fn ($q) => $q->where('empresa_id', $companyId));
        }
        return $query;
    }

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
