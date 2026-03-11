<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Company;
use App\Models\Subscription;
use Carbon\Carbon;

/**
 * PlanoSeeder — cria os planos SaaS e uma assinatura inicial para cada empresa existente.
 */
class PlanoSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------ //
        // 1. Planos
        // ------------------------------------------------------------------ //
        $plans = [
            [
                'nome'               => 'Básico',
                'limite_patrimonios' => 50,
                'preco'              => 49.90,
                'ativo'              => true,
            ],
            [
                'nome'               => 'Profissional',
                'limite_patrimonios' => 200,
                'preco'              => 129.90,
                'ativo'              => true,
            ],
            [
                'nome'               => 'Empresarial',
                'limite_patrimonios' => 1000,
                'preco'              => 299.90,
                'ativo'              => true,
            ],
            [
                'nome'               => 'Ilimitado',
                'limite_patrimonios' => 999999,
                'preco'              => 599.90,
                'ativo'              => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['nome' => $plan['nome']], $plan);
        }

        $this->command->info('✓ Planos SaaS criados.');

        // ------------------------------------------------------------------ //
        // 2. Assinatura inicial para cada empresa sem assinatura ativa
        // ------------------------------------------------------------------ //
        $planoProfissional = Plan::where('nome', 'Profissional')->first();

        Company::all()->each(function (Company $company) use ($planoProfissional) {
            $temAssinatura = Subscription::where('empresa_id', $company->id)
                ->whereIn('status', ['active', 'trial'])
                ->exists();

            if (! $temAssinatura) {
                Subscription::create([
                    'empresa_id'         => $company->id,
                    'plano_id'           => $planoProfissional->id,
                    'preco_mensal'       => $planoProfissional->preco,
                    'inicio_em'          => Carbon::today(),
                    'proximo_vencimento' => Carbon::today()->addMonth(),
                    'status'             => 'active',
                ]);

                $this->command->info("  ✓ Assinatura criada para empresa: {$company->nome}");
            }
        });
    }
}
