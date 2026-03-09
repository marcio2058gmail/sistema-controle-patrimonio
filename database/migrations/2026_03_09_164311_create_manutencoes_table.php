<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patrimonio_id')->constrained('patrimonios')->cascadeOnDelete();
            $table->string('tipo', 30); // preventiva | corretiva
            $table->string('status', 30)->default('agendada'); // agendada | em_andamento | concluida | cancelada
            $table->text('descricao')->nullable();
            $table->date('data_abertura');
            $table->date('data_conclusao')->nullable();
            $table->decimal('custo', 10, 2)->nullable();
            $table->string('tecnico_fornecedor', 255)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manutencoes');
    }
};
