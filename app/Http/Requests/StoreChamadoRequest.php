<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChamadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'descricao'     => ['required', 'string', 'min:10', 'max:1000'],
            'patrimonio_id' => ['nullable', 'exists:patrimonios,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'descricao'     => 'descrição',
            'patrimonio_id' => 'patrimônio',
        ];
    }
}
