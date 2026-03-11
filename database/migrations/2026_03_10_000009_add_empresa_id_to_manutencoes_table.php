<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona empresa_id à tabela manutencoes, populando via patrimonio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manutencoes', function (Blueprint $table) {
            if (! Schema::hasColumn('manutencoes', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id');
            }
        });

        // Popula empresa_id baseado no patrimonio_id
        DB::statement('
            UPDATE manutencoes m
            JOIN patrimonios p ON p.id = m.patrimonio_id
            SET m.empresa_id = p.empresa_id
            WHERE m.empresa_id IS NULL
        ');

        // Adiciona FK após popular
        Schema::table('manutencoes', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('manutencoes', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropIndex(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
