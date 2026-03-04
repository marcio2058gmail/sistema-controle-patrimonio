<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:150', 'unique:users,email', 'unique:funcionarios,email'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'            => 'nome',
            'email'           => 'e-mail',
            'password'        => 'senha',
            'cargo'           => 'cargo',
            'departamento_id' => 'department',
        ];
    }
}
