<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
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
