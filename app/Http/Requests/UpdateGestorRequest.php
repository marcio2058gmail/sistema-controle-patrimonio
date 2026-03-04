<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGestorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        $gestorId     = $this->route('gestor')?->id;
        $funcId       = $this->route('gestor')?->funcionario?->id;

        return [
            'name'            => ['required', 'string', 'max:150'],
            'email'           => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($gestorId),
                Rule::unique('funcionarios', 'email')->ignore($funcId),
            ],
            'password'        => ['nullable', 'string', 'min:8', 'confirmed'],
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
            'departamento_id' => 'departamento',
        ];
    }
}
