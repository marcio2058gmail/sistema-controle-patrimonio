<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('chamados', function (Blueprint $table) {
        $table->id();
        $table->foreignId('funcionario_id')->constrained();
        $table->text('descricao');
        $table->enum('status', ['aberto', 'aprovado', 'negado', 'entregue'])
              ->default('aberto');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chamados');
    }
};
