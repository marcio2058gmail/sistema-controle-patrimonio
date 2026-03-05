<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
    }

    public function rules(): array
    {
        return [
            'nome'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:150', Rule::unique('funcionarios', 'email')->ignore($this->employee)],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ];
    }
}
