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
        Schema::table('termos', function (Blueprint $table) {
            $table->text('observacao_devolucao')->nullable()->after('data_devolucao');
        });
    }

    public function down(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            $table->dropColumn('observacao_devolucao');
        });
    }
};
