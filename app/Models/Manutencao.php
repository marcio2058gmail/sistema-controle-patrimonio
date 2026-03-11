<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manutencao extends Model
{
    use BelongsToCompany;

    protected $table = 'manutencoes';

    protected $fillable = [
        'empresa_id',
        'patrimonio_id',
        'tipo',
        'status',
        'descricao',
        'data_abertura',
        'data_conclusao',
        'custo',
        'tecnico_fornecedor',
        'observacoes',
    ];

    protected $casts = [
        'data_abertura'  => 'date',
        'data_conclusao' => 'date',
        'custo'          => 'decimal:2',
    ];

    public const TIPOS = [
        'preventiva' => 'Preventiva',
        'corretiva'  => 'Corretiva',
    ];

    public const STATUS = [
        'agendada'     => 'Agendada',
        'em_andamento' => 'Em Andamento',
        'concluida'    => 'Concluída',
        'cancelada'    => 'Cancelada',
    ];

    public function patrimonio(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'patrimonio_id');
    }
}
