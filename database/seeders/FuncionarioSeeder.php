<?php

namespace Database\Seeders;

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

        $users = User::where('role', 'funcionario')->get();

        foreach ($users as $index => $user) {
            Funcionario::create([
                'user_id' => $user->id,
                'nome'    => $user->name,
                'email'   => $user->email,
                'cargo'   => $cargos[$index] ?? null,
            ]);
        }
    }
}
