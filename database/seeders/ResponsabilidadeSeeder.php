<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Asset;
use App\Models\Responsibility;
use Illuminate\Database\Seeder;

class ResponsabilidadeSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos patrimônios que estão em_uso para criar responsabilidades
        $patrimoniosEmUso = Asset::where('status', 'em_uso')->get();
        $funcionarios     = Employee::all();

        $termoBase = "Declaro ter recebido o bem patrimonial descrito neste documento em perfeito estado de conservação e funcionamento. " .
            "Comprometo-me a utilizá-lo exclusivamente para fins profissionais, conservando-o de forma adequada e responsabilizando-me " .
            "por qualquer dano decorrente de mau uso, negligência ou imprudência. " .
            "Em caso de necessidade de manutenção, devolução ou transferência, comunicarei imediatamente ao setor responsável.";

        foreach ($patrimoniosEmUso as $index => $patrimonio) {
            $funcionario   = $funcionarios[$index % $funcionarios->count()];
            $dataEntrega   = now()->subDays(rand(10, 180));

            Responsibility::create([
                'funcionario_id'         => $funcionario->id,
                'patrimonio_id'          => $patrimonio->id,
                'data_entrega'           => $dataEntrega->toDateString(),
                'data_devolucao'         => null,
                'termo_responsabilidade' => $termoBase,
                'assinado'               => (bool) rand(0, 1),
            ]);
        }

        // Algumas responsabilidades já encerradas (com devolução)
        $patrimoniosDisponiveis = Asset::where('status', 'disponivel')->take(3)->get();

        foreach ($patrimoniosDisponiveis as $index => $patrimonio) {
            $funcionario = $funcionarios[($index + 2) % $funcionarios->count()];
            $dataEntrega = now()->subDays(rand(90, 365));
            $dataDev     = $dataEntrega->copy()->addDays(rand(30, 89));

            Responsibility::create([
                'funcionario_id'         => $funcionario->id,
                'patrimonio_id'          => $patrimonio->id,
                'data_entrega'           => $dataEntrega->toDateString(),
                'data_devolucao'         => $dataDev->toDateString(),
                'termo_responsabilidade' => $termoBase . ' Bem devolvido em ' . $dataDev->format('d/m/Y') . '.',
                'assinado'               => true,
            ]);
        }
    }
}
