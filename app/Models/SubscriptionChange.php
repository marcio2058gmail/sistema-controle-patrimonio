<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Histórico de alterações de plano/status de assinatura.
 *
 * @property int         $id
 * @property int         $subscription_id
 * @property int|null    $old_plan_id
 * @property int|null    $new_plan_id
 * @property string|null $old_status
 * @property string|null $new_status
 * @property float|null  $old_price
 * @property float|null  $new_price
 * @property int         $changed_by
 * @property string|null $reason
 * @property string      $type
 */
class SubscriptionChange extends Model
{
    protected $table = 'subscription_changes';

    public const UPDATED_AT = null; // createdAt only

    protected $fillable = [
        'subscription_id',
        'old_plan_id',
        'new_plan_id',
        'old_status',
        'new_status',
        'old_price',
        'new_price',
        'changed_by',
        'reason',
        'type',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
    ];

    public const TYPE_PLAN_CHANGE   = 'plan_change';
    public const TYPE_STATUS_CHANGE = 'status_change';
    public const TYPE_PRICE_CHANGE  = 'price_change';

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_PLAN_CHANGE   => 'Alteração de Plano',
            self::TYPE_STATUS_CHANGE => 'Alteração de Status',
            self::TYPE_PRICE_CHANGE  => 'Alteração de Preço',
            default                  => $this->type,
        };
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function oldPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'old_plan_id');
    }

    public function newPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'new_plan_id');
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
