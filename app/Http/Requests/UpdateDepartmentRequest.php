<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
    }

    public function rules(): array
    {
        return [
            'nome'      => ['required', 'string', 'max:100',
                Rule::unique('departments', 'nome')->ignore($this->route('department'))],
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
