<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de atribuições de patrimônios a funcionários.
 * Permite rastrear o histórico de quem usou cada patrimônio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->cascadeOnDelete();
            $table->timestamp('atribuido_em')->useCurrent();
            $table->timestamp('devolvido_em')->nullable();
            $table->timestamps();

            $table->index('empresa_id');
            $table->index(['patrimonio_id', 'devolvido_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atribuicoes');
    }
};
