<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Plano SaaS — define os limites e preços disponíveis para assinatura.
 *
 * @property int    $id
 * @property string $nome
 * @property int    $limite_patrimonios
 * @property float  $preco
 * @property bool   $ativo
 */
class Plan extends Model
{
    protected $table = 'planos';

    protected $fillable = [
        'nome',
        'limite_patrimonios',
        'preco',
        'ativo',
    ];

    protected $casts = [
        'limite_patrimonios' => 'integer',
        'preco'              => 'decimal:2',
        'ativo'              => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plano_id');
    }
}
