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
            'codigo_patrimonio'  => ['required', 'string', 'max:50', Rule::unique('patrimonios', 'codigo_patrimonio')->ignore($this->asset)],
            'descricao'          => ['required', 'string', 'max:255'],
            'modelo'             => ['nullable', 'string', 'max:100'],
            'numero_serie'       => ['nullable', 'string', 'max:100'],
            'status'             => ['required', Rule::in(['disponivel', 'em_uso', 'manutencao'])],
            'valor_aquisicao'    => ['nullable', 'numeric', 'min:0'],
            'data_aquisicao'     => ['nullable', 'date'],
            'fornecedor'         => ['nullable', 'string', 'max:255'],
            'numero_nota_fiscal' => ['nullable', 'string', 'max:100'],
            'garantia_ate'       => ['nullable', 'date'],
            'valor_atual'        => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo_patrimonio'  => 'código de patrimônio',
            'descricao'          => 'descrição',
            'valor_aquisicao'    => 'valor de aquisição',
            'data_aquisicao'     => 'data de aquisição',
            'fornecedor'         => 'fornecedor',
            'numero_nota_fiscal' => 'número da nota fiscal',
            'garantia_ate'       => 'garantia até',
            'valor_atual'        => 'valor atual',
        ];
    }
}
