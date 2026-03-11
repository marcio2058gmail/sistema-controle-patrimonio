<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adiciona os status overdue e suspended à tabela assinaturas.
 * Também adiciona campo changed_by para rastrear quem alterou o status.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL não permite alterar ENUM diretamente com o Blueprint.
        // Executamos SQL raw para modificar a coluna sem perder dados.
        DB::statement("ALTER TABLE assinaturas MODIFY COLUMN status ENUM('active','trial','overdue','suspended','canceled','past_due','cancelled') NOT NULL DEFAULT 'active'");

        Schema::table('assinaturas', function (Blueprint $table) {
            $table->unsignedBigInteger('changed_by')->nullable()->after('status');
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assinaturas', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->dropColumn('changed_by');
        });
        DB::statement("ALTER TABLE assinaturas MODIFY COLUMN status ENUM('active','trial','past_due','cancelled') NOT NULL DEFAULT 'active'");
    }
};
