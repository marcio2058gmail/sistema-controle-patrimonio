<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona empresa_id à tabela termos, populando via funcionario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            if (! Schema::hasColumn('termos', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                $table->index('empresa_id');
            }
        });

        // Popula empresa_id baseado no funcionario_id
        DB::statement('
            UPDATE termos t
            JOIN funcionarios f ON f.id = t.funcionario_id
            SET t.empresa_id = f.empresa_id
            WHERE t.empresa_id IS NULL
        ');

        // Adiciona FK após popular
        Schema::table('termos', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropIndex(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
