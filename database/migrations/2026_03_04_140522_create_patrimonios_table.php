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
    Schema::create('patrimonios', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_patrimonio')->unique();
        $table->string('descricao');
        $table->string('modelo')->nullable();
        $table->string('numero_serie')->nullable();
        $table->enum('status', ['disponivel', 'em_uso', 'manutencao'])
              ->default('disponivel');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonios');
    }
};
