<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EmpresaSeeder;
use Database\Seeders\PlanoSeeder;
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
     * 2. Empresas (vincula usuários)
     * 3. Planos + Assinaturas
     * 4. Departamentos
     * 5. Funcionários
     * 6. Patrimônios
     * 7. Chamados
     * 8. Responsabilidades
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,         // 1. Cria usuários (admin, manager, employees)
            EmpresaSeeder::class,      // 2. Cria empresa + super_admin + vincula usuários
            PlanoSeeder::class,        // 3. Planos SaaS + assinatura inicial por empresa
            DepartamentoSeeder::class, // 4. Departamentos
            FuncionarioSeeder::class,  // 5. Funcionários
            PatrimonioSeeder::class,   // 6. Patrimônios
            ChamadoSeeder::class,      // 7. Chamados
            ResponsabilidadeSeeder::class, // 8. Termos de responsabilidade
        ]);
    }
}

