<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de itens de inventário — cada patrimônio verificado em um inventário.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('inventario_id')->constrained('inventarios')->cascadeOnDelete();
            $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
            $table->enum('status', ['encontrado', 'nao_encontrado', 'avariado', 'pendente'])
                  ->default('pendente');
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->index('empresa_id');
            $table->unique(['inventario_id', 'patrimonio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_itens');
    }
};
