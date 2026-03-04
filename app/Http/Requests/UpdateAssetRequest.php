<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
    }

    public function rules(): array
    {
        return [
            'codigo_patrimonio' => ['required', 'string', 'max:50', Rule::unique('assets', 'codigo_patrimonio')->ignore($this->asset)],
            'descricao'         => ['required', 'string', 'max:255'],
            'modelo'            => ['nullable', 'string', 'max:100'],
            'numero_serie'      => ['nullable', 'string', 'max:100'],
            'status'            => ['required', Rule::in(['disponivel', 'em_uso', 'manutencao'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo_patrimonio' => 'código de patrimônio',
            'descricao'         => 'descrição',
        ];
    }
}
