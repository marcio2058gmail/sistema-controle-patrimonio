<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'              => 'Administrador',
            'email'             => 'admin@patrimonio.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'admin',
        ]);

        // Gestor
        User::create([
            'name'              => 'Gestor de Patrimônio',
            'email'             => 'gestor@patrimonio.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'role'              => 'gestor',
        ]);

        // Funcionários
        $funcionarios = [
            ['name' => 'Ana Silva',       'email' => 'ana.silva@empresa.test'],
            ['name' => 'Bruno Oliveira',  'email' => 'bruno.oliveira@empresa.test'],
            ['name' => 'Carla Mendes',    'email' => 'carla.mendes@empresa.test'],
            ['name' => 'Diego Costa',     'email' => 'diego.costa@empresa.test'],
            ['name' => 'Elisa Ferreira',  'email' => 'elisa.ferreira@empresa.test'],
            ['name' => 'Fábio Santos',    'email' => 'fabio.santos@empresa.test'],
            ['name' => 'Gabriela Lima',   'email' => 'gabriela.lima@empresa.test'],
            ['name' => 'Henrique Rocha',  'email' => 'henrique.rocha@empresa.test'],
            ['name' => 'Isabela Nunes',   'email' => 'isabela.nunes@empresa.test'],
            ['name' => 'João Pereira',    'email' => 'joao.pereira@empresa.test'],
        ];

        foreach ($funcionarios as $data) {
            User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'role'              => 'funcionario',
            ]);
        }
    }
}
