<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EmpresaSeeder;
use Database\Seeders\DepartamentoSeeder;
use Database\Seeders\FuncionarioSeeder;
use Database\Seeders\PatrimonioSeeder;
use Database\Seeders\ChamadoSeeder;
use Database\Seeders\ResponsabilidadeSeeder;

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
            UserSeeder::class,        // 1. Cria usuários (admin, manager, employees)
            EmpresaSeeder::class,     // 2. Cria empresa + super_admin + vincula usuários
            DepartamentoSeeder::class,// 3. Departamentos (usa Company::first())
            FuncionarioSeeder::class, // 4. Funcionários (usa Company::first())
            PatrimonioSeeder::class,  // 5. Patrimônios (usa Company::first())
            ChamadoSeeder::class,
            ResponsabilidadeSeeder::class,
        ]);
    }
}

