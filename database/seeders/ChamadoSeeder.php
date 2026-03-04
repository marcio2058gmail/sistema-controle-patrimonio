<?php

namespace Database\Seeders;

use App\Models\Chamado;
use App\Models\Funcionario;
use App\Models\Patrimonio;
use Illuminate\Database\Seeder;

class ChamadoSeeder extends Seeder
{
    public function run(): void
    {
        $funcionarios = Funcionario::all();
        $patrimonios  = Patrimonio::all();

        $chamados = [
            [
                'descricao' => 'Solicito notebook para uso durante o projeto Alpha. Meu equipamento atual está com problemas de hardware.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Preciso de um monitor adicional para trabalho com múltiplas telas. Aumentaria minha produtividade significativamente.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Solicitação de impressora para o departamento. O equipamento atual está com defeito de papel.',
                'status'    => 'aprovado',
            ],
            [
                'descricao' => 'Meu notebook está com a bateria danificada e precisa de manutenção urgente.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Solicito tablet para apresentações em campo. Precisamos para as visitas aos clientes.',
                'status'    => 'negado',
            ],
            [
                'descricao' => 'Necessito de mouse e teclado novos. Os atuais estão com problemas de digitação.',
                'status'    => 'entregue',
            ],
            [
                'descricao' => 'Preciso de um no-break para o servidor da filial. Tivemos várias quedas de energia recentemente.',
                'status'    => 'aprovado',
            ],
            [
                'descricao' => 'Solicitação de câmera para o setor de segurança do almoxarifado.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Preciso de um switch de rede para expandir a infraestrutura do escritório.',
                'status'    => 'entregue',
            ],
            [
                'descricao' => 'Solicito desktop para novo colaborador que inicia na próxima semana.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Notebook com falha na placa de vídeo. Preciso de substituição temporária.',
                'status'    => 'aprovado',
            ],
            [
                'descricao' => 'Roteador para home office — trabalhando remotamente e minha conexão está instável.',
                'status'    => 'negado',
            ],
            [
                'descricao' => 'Solicito monitor curvo para o estúdio de design. Facilitaria o trabalho com edição.',
                'status'    => 'aberto',
            ],
            [
                'descricao' => 'Preciso de iPad para demonstrações no estande da feira do setor.',
                'status'    => 'entregue',
            ],
            [
                'descricao' => 'Teclado derramou líquido e parou de funcionar. Preciso de reposição urgente.',
                'status'    => 'aberto',
            ],
        ];

        foreach ($chamados as $index => $data) {
            $funcionario = $funcionarios[$index % $funcionarios->count()];

            $chamado = Chamado::create([
                'funcionario_id' => $funcionario->id,
                'descricao'      => $data['descricao'],
                'status'         => $data['status'],
                'created_at'     => now()->subDays(rand(0, 60)),
            ]);

            // Associa 0, 1 ou 2 patrimônios aleatórios ao chamado
            $qtd = rand(0, 2);
            if ($qtd > 0 && $patrimonios->count() > 0) {
                $ids = $patrimonios->random(min($qtd, $patrimonios->count()))->pluck('id')->toArray();
                $chamado->patrimonios()->sync($ids);
            }
        }
    }
}
