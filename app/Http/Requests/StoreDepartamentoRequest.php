<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrGestor();
    }

    public function rules(): array
    {
        return [
            'nome'      => ['required', 'string', 'max:100', 'unique:departamentos,nome'],
            'descricao' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nome'      => 'nome',
            'descricao' => 'descrição',
        ];
    }
}
