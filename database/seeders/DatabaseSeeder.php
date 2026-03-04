<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Ordem obrigatória:
     * 1. Users (define roles)
     * 2. Funcionarios (vincula ao user)
     * 3. Patrimonios
     * 4. Chamados (depende de funcionario + patrimônio)
     * 5. Responsabilidades (depende de funcionario + patrimônio)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DepartamentoSeeder::class,
            FuncionarioSeeder::class,
            PatrimonioSeeder::class,
            ChamadoSeeder::class,
            ResponsabilidadeSeeder::class,
        ]);
    }
}

