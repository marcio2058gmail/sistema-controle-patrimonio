<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'funcionario_id',
        'descricao',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_ABERTO   = 'aberto';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_NEGADO   = 'negado';
    public const STATUS_ENTREGUE = 'entregue';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ABERTO   => 'Aberto',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_NEGADO   => 'Negado',
            self::STATUS_ENTREGUE => 'Entregue',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // ---------- Relationships ----------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'funcionario_id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'chamado_patrimonio', 'chamado_id', 'patrimonio_id')
            ->withTimestamps();
    }
}
