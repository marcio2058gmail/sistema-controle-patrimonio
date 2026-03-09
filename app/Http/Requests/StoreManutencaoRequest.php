<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManutencaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'patrimonio_id'    => ['required', 'exists:patrimonios,id'],
            'tipo'             => ['required', 'in:preventiva,corretiva'],
            'status'           => ['required', 'in:agendada,em_andamento,concluida,cancelada'],
            'descricao'        => ['nullable', 'string', 'max:1000'],
            'data_abertura'    => ['required', 'date'],
            'data_conclusao'   => ['nullable', 'date', 'after_or_equal:data_abertura'],
            'custo'            => ['nullable', 'numeric', 'min:0'],
            'tecnico_fornecedor' => ['nullable', 'string', 'max:255'],
            'observacoes'      => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patrimonio_id'    => 'patrimônio',
            'tipo'             => 'tipo',
            'status'           => 'status',
            'descricao'        => 'descrição',
            'data_abertura'    => 'data de abertura',
            'data_conclusao'   => 'data de conclusão',
            'custo'            => 'custo',
            'tecnico_fornecedor' => 'técnico / fornecedor',
            'observacoes'      => 'observações',
        ];
    }
}
