<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patrimonio extends Model
{
    protected $fillable = [
        'codigo_patrimonio',
        'descricao',
        'modelo',
        'numero_serie',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_DISPONIVEL  = 'disponivel';
    public const STATUS_EM_USO      = 'em_uso';
    public const STATUS_MANUTENCAO  = 'manutencao';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DISPONIVEL => 'Disponível',
            self::STATUS_EM_USO     => 'Em Uso',
            self::STATUS_MANUTENCAO => 'Manutenção',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    // ---------- Escopos ----------

    public function scopeDisponivel(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISPONIVEL);
    }

    public function scopeEmUso(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EM_USO);
    }

    // ---------- Relacionamentos ----------

    public function responsabilidades(): HasMany
    {
        return $this->hasMany(Responsabilidade::class);
    }

    public function responsabilidadeAtiva(): ?Responsabilidade
    {
        return $this->responsabilidades()
            ->whereNull('data_devolucao')
            ->latest()
            ->first();
    }

    public function chamados(): BelongsToMany
    {
        return $this->belongsToMany(Chamado::class, 'chamado_patrimonio')
            ->withTimestamps();
    }
}
