<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de inventários — representa uma conferência periódica do estoque de patrimônios.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('descricao', 255)->nullable();
            $table->enum('status', ['em_andamento', 'concluido', 'cancelado'])->default('em_andamento');
            $table->timestamp('iniciado_em')->useCurrent();
            $table->timestamp('finalizado_em')->nullable();
            $table->timestamps();

            $table->index('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
