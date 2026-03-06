<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            $table->text('termo_responsabilidade')->nullable()->change();
        });

        // Limpa textos genéricos do seeder para usar o modelo padrão do PDF
        DB::table('termos')->update(['termo_responsabilidade' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            $table->text('termo_responsabilidade')->nullable(false)->change();
        });
    }
};
