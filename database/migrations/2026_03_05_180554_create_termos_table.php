<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela principal de termos (sem patrimonio_id)
        Schema::create('termos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();
            $table->date('data_entrega');
            $table->date('data_devolucao')->nullable();
            $table->text('termo_responsabilidade');
            $table->boolean('assinado')->default(false);
            $table->timestamps();
        });

        // 2. Tabela pivot: termo <-> patrimônio
        Schema::create('termo_patrimonios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termo_id')->constrained('termos')->cascadeOnDelete();
            $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Migrar dados existentes de responsabilidades → termos + pivot
        if (Schema::hasTable('responsabilidades')) {
            $rows = DB::table('responsabilidades')->get();
            foreach ($rows as $row) {
                $termoId = DB::table('termos')->insertGetId([
                    'funcionario_id'         => $row->funcionario_id,
                    'data_entrega'           => $row->data_entrega,
                    'data_devolucao'         => $row->data_devolucao,
                    'termo_responsabilidade' => $row->termo_responsabilidade,
                    'assinado'               => $row->assinado,
                    'created_at'             => $row->created_at,
                    'updated_at'             => $row->updated_at,
                ]);
                DB::table('termo_patrimonios')->insert([
                    'termo_id'      => $termoId,
                    'patrimonio_id' => $row->patrimonio_id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // 4. Remover tabela antiga
            Schema::dropIfExists('responsabilidades');
        }
    }

    public function down(): void
    {
        // Restaurar tabela original
        Schema::create('responsabilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();
            $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
            $table->date('data_entrega');
            $table->date('data_devolucao')->nullable();
            $table->text('termo_responsabilidade');
            $table->boolean('assinado')->default(false);
            $table->timestamps();
        });

        // Migrar de volta: cada pivot vira uma linha em responsabilidades
        $termos = DB::table('termos')->get();
        foreach ($termos as $termo) {
            $pivots = DB::table('termo_patrimonios')->where('termo_id', $termo->id)->get();
            foreach ($pivots as $pivot) {
                DB::table('responsabilidades')->insert([
                    'funcionario_id'         => $termo->funcionario_id,
                    'patrimonio_id'          => $pivot->patrimonio_id,
                    'data_entrega'           => $termo->data_entrega,
                    'data_devolucao'         => $termo->data_devolucao,
                    'termo_responsabilidade' => $termo->termo_responsabilidade,
                    'assinado'               => $termo->assinado,
                    'created_at'             => $termo->created_at,
                    'updated_at'             => $termo->updated_at,
                ]);
            }
        }

        Schema::dropIfExists('termo_patrimonios');
        Schema::dropIfExists('termos');
    }
};
