<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar dados existentes de patrimonio_id para a tabela pivot antes de remover a coluna
        if (Schema::hasColumn('chamados', 'patrimonio_id')) {
            $chamados = DB::table('chamados')->whereNotNull('patrimonio_id')->get();

            Schema::create('chamado_patrimonio', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chamado_id')->constrained('chamados')->cascadeOnDelete();
                $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['chamado_id', 'patrimonio_id']);
            });

            foreach ($chamados as $chamado) {
                DB::table('chamado_patrimonio')->insert([
                    'chamado_id'    => $chamado->id,
                    'patrimonio_id' => $chamado->patrimonio_id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            Schema::table('chamados', function (Blueprint $table) {
                $table->dropForeign(['patrimonio_id']);
                $table->dropColumn('patrimonio_id');
            });
        } else {
            Schema::create('chamado_patrimonio', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chamado_id')->constrained('chamados')->cascadeOnDelete();
                $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['chamado_id', 'patrimonio_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            $table->foreignId('patrimonio_id')->nullable()->after('funcionario_id')
                ->constrained('patrimonios')->nullOnDelete();
        });

        // Restaurar dados da pivot para a coluna
        $pivot = DB::table('chamado_patrimonio')->get();
        foreach ($pivot as $row) {
            DB::table('chamados')
                ->where('id', $row->chamado_id)
                ->update(['patrimonio_id' => $row->patrimonio_id]);
        }

        Schema::dropIfExists('chamado_patrimonio');
    }
};
