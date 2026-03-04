<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->string('descricao', 500)->nullable();
            $table->timestamps();
        });

        Schema::table('funcionarios', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->after('cargo')
                ->constrained('departamentos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropColumn('departamento_id');
        });

        Schema::dropIfExists('departamentos');
    }
};
