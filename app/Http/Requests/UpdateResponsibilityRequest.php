<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResponsibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
    }

    public function rules(): array
    {
        return [
            'data_devolucao'         => ['nullable', 'date'],
            'termo_responsabilidade' => ['sometimes', 'string', 'min:20'],
            'assinado'               => ['boolean'],
            'patrimonio_ids'         => ['sometimes', 'array'],
            'patrimonio_ids.*'       => ['exists:patrimonios,id'],
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
