<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chamado extends Model
{
    protected $fillable = [
        'funcionario_id',
        'patrimonio_id',
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
