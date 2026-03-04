<?php

namespace Database\Seeders;

use App\Models\Departamento;
use App\Models\Funcionario;
use App\Models\User;
use Illuminate\Database\Seeder;

class FuncionarioSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            'Analista de TI',
            'Desenvolvedor Backend',
            'Designer UX/UI',
            'Auxiliar Administrativo',
            'Coordenador de Projetos',
            'Técnico de Suporte',
            'Analista Financeiro',
            'Assistente de RH',
            'Gerente Comercial',
            'Analista de Sistemas',
        ];

        $users          = User::where('role', 'funcionario')->get();
        $departamentos  = Departamento::all();

        foreach ($users as $index => $user) {
            Funcionario::create([
                'user_id'         => $user->id,
                'nome'            => $user->name,
                'email'           => $user->email,
                'cargo'           => $cargos[$index] ?? null,
                'departamento_id' => $departamentos->isNotEmpty()
                    ? $departamentos[$index % $departamentos->count()]->id
                    : null,
            ]);
        }
    }
}
