<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assinatura — vincula uma empresa a um plano SaaS.
 *
 * @property int    $id
 * @property int    $empresa_id
 * @property int    $plano_id
 * @property float  $preco_mensal
 * @property string $inicio_em
 * @property string $proximo_vencimento
 * @property string $status  active|trial|overdue|suspended|canceled
 * @property int|null $changed_by
 */
class Subscription extends Model
{
    protected $table = 'assinaturas';

    protected $fillable = [
        'empresa_id',
        'plano_id',
        'preco_mensal',
        'inicio_em',
        'proximo_vencimento',
        'status',
        'changed_by',
    ];

    protected $casts = [
        'preco_mensal'       => 'decimal:2',
        'inicio_em'          => 'date',
        'proximo_vencimento' => 'date',
    ];

    // Novos status canonicos
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_TRIAL     = 'trial';
    public const STATUS_OVERDUE   = 'overdue';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CANCELED  = 'canceled';

    // Legados (retrocompatibilidade)
    public const STATUS_PAST_DUE  = 'past_due';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_ACTIVE    => 'Ativa',
            self::STATUS_TRIAL     => 'Trial',
            self::STATUS_OVERDUE   => 'Inadimplente',
            self::STATUS_SUSPENDED => 'Suspensa',
            self::STATUS_CANCELED  => 'Cancelada',
            self::STATUS_PAST_DUE  => 'Em Atraso',
            self::STATUS_CANCELLED => 'Cancelada',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_ACTIVE    => 'green',
            self::STATUS_TRIAL     => 'blue',
            self::STATUS_OVERDUE   => 'orange',
            self::STATUS_SUSPENDED => 'yellow',
            self::STATUS_CANCELED  => 'red',
            self::STATUS_PAST_DUE  => 'orange',
            self::STATUS_CANCELLED => 'red',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'gray';
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIAL]);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAtivas($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIAL]);
    }

    public function scopeInadimplentes($query)
    {
        return $query->whereIn('status', [self::STATUS_OVERDUE, self::STATUS_PAST_DUE]);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'empresa_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plano_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function changes(): HasMany
    {
        return $this->hasMany(SubscriptionChange::class, 'subscription_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }
}
