<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fatura gerada para uma assinatura.
 *
 * @property int         $id
 * @property int         $company_id
 * @property int         $subscription_id
 * @property float       $amount
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon|null $payment_date
 * @property string      $status  pending|paid|overdue|canceled
 * @property string|null $description
 */
class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'company_id',
        'subscription_id',
        'amount',
        'due_date',
        'payment_date',
        'status',
        'description',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'due_date'     => 'date',
        'payment_date' => 'date',
    ];

    public const STATUS_PENDING  = 'pending';
    public const STATUS_PAID     = 'paid';
    public const STATUS_OVERDUE  = 'overdue';
    public const STATUS_CANCELED = 'canceled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING  => 'Pendente',
            self::STATUS_PAID     => 'Paga',
            self::STATUS_OVERDUE  => 'Vencida',
            self::STATUS_CANCELED => 'Cancelada',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_PENDING  => 'yellow',
            self::STATUS_PAID     => 'green',
            self::STATUS_OVERDUE  => 'red',
            self::STATUS_CANCELED => 'gray',
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

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->due_date->isPast();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }
}
