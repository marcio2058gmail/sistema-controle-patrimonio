<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            ['nome' => 'Tecnologia da Informação', 'descricao' => 'Responsável pela infraestrutura e sistemas de TI da organização.'],
            ['nome' => 'Recursos Humanos',          'descricao' => 'Gestão de pessoas, recrutamento e benefícios.'],
            ['nome' => 'Financeiro',                'descricao' => 'Controle orçamentário, contabilidade e tesouraria.'],
            ['nome' => 'Operações',                 'descricao' => 'Processos operacionais e logística.'],
            ['nome' => 'Comercial',                 'descricao' => 'Vendas, atendimento ao cliente e parcerias.'],
            ['nome' => 'Administrativo',            'descricao' => 'Suporte administrativo geral e facilities.'],
        ];

        $empresa = Company::first();

        foreach ($departamentos as $data) {
            Department::firstOrCreate(
                ['nome' => $data['nome']],
                array_merge($data, ['empresa_id' => $empresa?->id])
            );
        }
    }
}
