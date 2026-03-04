<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $table = 'patrimonios';

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

    public const STATUS_DISPONIVEL = 'disponivel';
    public const STATUS_EM_USO     = 'em_uso';
    public const STATUS_MANUTENCAO = 'manutencao';

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

    // ---------- Scopes ----------

    public function scopeDisponivel(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISPONIVEL);
    }

    public function scopeEmUso(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EM_USO);
    }

    // ---------- Relationships ----------

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class, 'patrimonio_id');
    }

    public function activeResponsibility(): ?Responsibility
    {
        return $this->responsibilities()
            ->whereNull('data_devolucao')
            ->latest()
            ->first();
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'chamado_patrimonio', 'patrimonio_id', 'chamado_id')
            ->withTimestamps();
    }
}
