<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrGestor();
    }

    public function rules(): array
    {
        return [
            'nome'      => ['required', 'string', 'max:100',
                Rule::unique('departamentos', 'nome')->ignore($this->route('departamento'))],
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
