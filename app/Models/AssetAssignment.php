<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Atribuicao (AssetAssignment) — rastreia o histórico de uso de um patrimônio por funcionários.
 *
 * @property int         $id
 * @property int         $empresa_id
 * @property int         $patrimonio_id
 * @property int         $funcionario_id
 * @property \Carbon\Carbon $atribuido_em
 * @property \Carbon\Carbon|null $devolvido_em
 */
class AssetAssignment extends Model
{
    use BelongsToCompany;

    protected $table = 'atribuicoes';

    protected $fillable = [
        'empresa_id',
        'patrimonio_id',
        'funcionario_id',
        'atribuido_em',
        'devolvido_em',
    ];

    protected $casts = [
        'atribuido_em' => 'datetime',
        'devolvido_em' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Apenas atribuições ativas (sem devolução).
     */
    public function scopeAtivas($query)
    {
        return $query->whereNull('devolvido_em');
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'patrimonio_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'funcionario_id');
    }
}
