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
        Schema::table('patrimonios', function (Blueprint $table) {
            $table->decimal('valor_aquisicao', 10, 2)->nullable()->after('status');
            $table->date('data_aquisicao')->nullable()->after('valor_aquisicao');
            $table->string('fornecedor', 255)->nullable()->after('data_aquisicao');
            $table->string('numero_nota_fiscal', 100)->nullable()->after('fornecedor');
            $table->date('garantia_ate')->nullable()->after('numero_nota_fiscal');
            $table->decimal('valor_atual', 10, 2)->nullable()->after('garantia_ate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patrimonios', function (Blueprint $table) {
            $table->dropColumn(['valor_aquisicao', 'data_aquisicao', 'fornecedor', 'numero_nota_fiscal', 'garantia_ate', 'valor_atual']);
        });
    }
};
