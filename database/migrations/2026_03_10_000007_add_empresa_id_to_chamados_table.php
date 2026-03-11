<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona empresa_id à tabela chamados, populando via funcionario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            if (! Schema::hasColumn('chamados', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id');
            }
        });

        // Popula empresa_id baseado no funcionario_id
        DB::statement('
            UPDATE chamados c
            JOIN funcionarios f ON f.id = c.funcionario_id
            SET c.empresa_id = f.empresa_id
            WHERE c.empresa_id IS NULL
        ');

        // Adiciona FK após popular
        Schema::table('chamados', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropIndex(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
