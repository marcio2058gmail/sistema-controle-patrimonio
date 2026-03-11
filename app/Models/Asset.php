<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use BelongsToCompany;
    protected $table = 'patrimonios';

    protected $fillable = [
        'empresa_id',
        'codigo_patrimonio',
        'descricao',
        'modelo',
        'numero_serie',
        'status',
        'valor_aquisicao',
        'data_aquisicao',
        'fornecedor',
        'numero_nota_fiscal',
        'garantia_ate',
        'valor_atual',
    ];

    protected $casts = [
        'status'           => 'string',
        'valor_aquisicao'  => 'decimal:2',
        'valor_atual'      => 'decimal:2',
        'data_aquisicao'   => 'date',
        'garantia_ate'     => 'date',
    ];

    public const STATUS_AVAILABLE = 'disponivel';
    public const STATUS_IN_USE     = 'em_uso';
    public const STATUS_MAINTENANCE = 'manutencao';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Disponível',
            self::STATUS_IN_USE     => 'Em Uso',
            self::STATUS_MAINTENANCE => 'Manutenção',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // ---------- Scopes ----------

    public function scopeDisponivel(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeEmUso(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_IN_USE);
    }

    // ---------- Relationships ----------

    public function responsibilities(): BelongsToMany
    {
        return $this->belongsToMany(Responsibility::class, 'termo_patrimonios', 'patrimonio_id', 'termo_id')
                    ->withTimestamps();
    }

    public function activeResponsibility(): ?Responsibility
    {
        return $this->responsibilities()
            ->whereNull('data_devolucao')
            ->latest('termos.created_at')
            ->first();
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'chamado_patrimonio', 'patrimonio_id', 'chamado_id')
            ->withTimestamps();
    }

    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class, 'patrimonio_id');
    }
}
