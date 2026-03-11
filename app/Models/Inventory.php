<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Inventario — representa uma conferência periódica do estoque de patrimônios de uma empresa.
 *
 * @property int         $id
 * @property int         $empresa_id
 * @property string|null $descricao
 * @property string      $status  em_andamento|concluido|cancelado
 * @property \Carbon\Carbon $iniciado_em
 * @property \Carbon\Carbon|null $finalizado_em
 */
class Inventory extends Model
{
    use BelongsToCompany;

    protected $table = 'inventarios';

    protected $fillable = [
        'empresa_id',
        'descricao',
        'status',
        'iniciado_em',
        'finalizado_em',
    ];

    protected $casts = [
        'iniciado_em'   => 'datetime',
        'finalizado_em' => 'datetime',
    ];

    public const STATUS_IN_PROGRESS = 'em_andamento';
    public const STATUS_FINISHED    = 'concluido';
    public const STATUS_CANCELLED   = 'cancelado';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_FINISHED    => 'Concluído',
            self::STATUS_CANCELLED   => 'Cancelado',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'inventario_id');
    }
}
