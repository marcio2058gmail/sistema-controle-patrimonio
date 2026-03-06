<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    protected $table = 'chamados';

    protected $fillable = [
        'funcionario_id',
        'descricao',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_OPEN   = 'aberto';
    public const STATUS_APPROVED = 'aprovado';
    public const STATUS_DENIED   = 'negado';
    public const STATUS_DELIVERED = 'entregue';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_OPEN   => 'Aberto',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_DENIED   => 'Negado',
            self::STATUS_DELIVERED => 'Entregue',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

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
        return $this->belongsToMany(Asset::class, 'chamado_patrimonio', 'chamado_id', 'patrimonio_id')
            ->withTimestamps();
    }
}
