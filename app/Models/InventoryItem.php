<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * InventoryItem — cada patrimônio verificado dentro de um inventário.
 *
 * @property int    $id
 * @property int    $empresa_id
 * @property int    $inventario_id
 * @property int    $patrimonio_id
 * @property string $status  encontrado|nao_encontrado|avariado|pendente
 * @property string|null $observacao
 */
class InventoryItem extends Model
{
    use BelongsToCompany;

    protected $table = 'inventario_itens';

    protected $fillable = [
        'empresa_id',
        'inventario_id',
        'patrimonio_id',
        'status',
        'observacao',
    ];

    public const STATUS_FOUND     = 'encontrado';
    public const STATUS_NOT_FOUND = 'nao_encontrado';
    public const STATUS_DAMAGED   = 'avariado';
    public const STATUS_PENDING   = 'pendente';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_FOUND     => 'Encontrado',
            self::STATUS_NOT_FOUND => 'Não Encontrado',
            self::STATUS_DAMAGED   => 'Avariado',
            self::STATUS_PENDING   => 'Pendente',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventario_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'patrimonio_id');
    }
}
