<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResponsibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdminOrManager();
    }

    public function rules(): array
    {
        return [
            'funcionario_id'         => ['required', 'exists:funcionarios,id'],
            'patrimonio_ids'         => ['required', 'array', 'min:1'],
            'patrimonio_ids.*'       => [
                'required',
                'exists:patrimonios,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $asset = \App\Models\Asset::find($value);
                    if ($asset && $asset->status !== 'disponivel') {
                        $fail("O patrimônio {$asset->codigo_patrimonio} não está disponível para entrega.");
                    }
                },
            ],
            'data_entrega'           => ['required', 'date'],
            'data_devolucao'         => ['nullable', 'date', 'after:data_entrega'],
            'termo_responsabilidade' => ['required', 'string', 'min:20'],
            'assinado'               => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'funcionario_id'         => 'funcionário',
            'patrimonio_ids'         => 'patrimônios',
            'patrimonio_ids.*'       => 'patrimônio',
            'data_entrega'           => 'data de entrega',
            'data_devolucao'         => 'data de devolução',
            'termo_responsabilidade' => 'termo de responsabilidade',
        ];
    }
}
