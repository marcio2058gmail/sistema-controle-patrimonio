<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de assinaturas — vincula uma empresa a um plano.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('plano_id')->constrained('planos');
            $table->decimal('preco_mensal', 10, 2);
            $table->date('inicio_em');
            $table->date('proximo_vencimento');
            $table->enum('status', ['active', 'past_due', 'cancelled', 'trial'])->default('active');
            $table->timestamps();

            $table->index('empresa_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};
