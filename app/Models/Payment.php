<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pagamento registrado para uma fatura.
 *
 * @property int         $id
 * @property int         $invoice_id
 * @property float       $amount
 * @property string      $method  pix|boleto|card|manual
 * @property string|null $transaction_id
 * @property \Carbon\Carbon $paid_at
 * @property string|null $notes
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'invoice_id',
        'amount',
        'method',
        'transaction_id',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const METHOD_PIX    = 'pix';
    public const METHOD_BOLETO = 'boleto';
    public const METHOD_CARD   = 'card';
    public const METHOD_MANUAL = 'manual';

    public static function methodLabels(): array
    {
        return [
            self::METHOD_PIX    => 'PIX',
            self::METHOD_BOLETO => 'Boleto',
            self::METHOD_CARD   => 'Cartão',
            self::METHOD_MANUAL => 'Manual',
        ];
    }

    public function getMethodLabelAttribute(): string
    {
        return self::methodLabels()[$this->method] ?? $this->method;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
