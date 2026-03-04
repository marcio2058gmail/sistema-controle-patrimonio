<?php

namespace Database\Seeders;

use App\Models\Departamento;
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

        foreach ($departamentos as $data) {
            Departamento::firstOrCreate(['nome' => $data['nome']], $data);
        }
    }
}
