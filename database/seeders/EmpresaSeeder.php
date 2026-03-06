<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin global
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@patrimonio.test'],
            [
                'name'              => 'Super Admin',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'super_admin',
            ]
        );

        // Empresa de demonstração
        $empresa = Company::firstOrCreate(
            ['nome' => 'Empresa Demonstração'],
            [
                'cnpj'     => '00.000.000/0001-00',
                'email'    => 'contato@demo.test',
                'telefone' => '(11) 99999-0000',
                'ativa'    => true,
            ]
        );

        // Vincula todos os usuários existentes (exceto super_admin) à empresa
        $usuarios = User::where('role', '!=', 'super_admin')->get();

        foreach ($usuarios as $user) {
            $role = match ($user->role) {
                'admin'    => 'admin',
                'manager'  => 'manager',
                default    => 'employee',
            };

            $empresa->users()->syncWithoutDetaching([
                $user->id => ['role' => $role],
            ]);
        }

        $this->command->info("Empresa '{$empresa->nome}' criada e {$usuarios->count()} usuários vinculados.");
        $this->command->info("Super Admin: superadmin@patrimonio.test / password");
    }
}
