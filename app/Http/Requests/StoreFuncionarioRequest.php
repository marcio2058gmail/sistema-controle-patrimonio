<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFuncionarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrGestor();
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:150', 'unique:funcionarios,email'],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ];
    }
}
