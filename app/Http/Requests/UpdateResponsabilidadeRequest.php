<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResponsabilidadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrGestor();
    }

    public function rules(): array
    {
        return [
            'data_devolucao'        => ['nullable', 'date', 'after:data_entrega'],
            'termo_responsabilidade' => ['sometimes', 'string', 'min:20'],
            'assinado'              => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'data_devolucao'        => 'data de devolução',
            'termo_responsabilidade' => 'termo de responsabilidade',
        ];
    }
}
