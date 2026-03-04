<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChamadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrGestor();
    }

    public function rules(): array
    {
        return [
            'status'        => ['required', Rule::in(['aberto', 'aprovado', 'negado', 'entregue'])],
            'patrimonio_id' => ['nullable', 'exists:patrimonios,id'],
            'descricao'     => ['sometimes', 'string', 'min:10', 'max:1000'],
        ];
    }
}
