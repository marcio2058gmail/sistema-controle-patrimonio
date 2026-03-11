<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona account_status à tabela empresas para controle de status da conta SaaS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (! Schema::hasColumn('empresas', 'account_status')) {
                $table->enum('account_status', ['active', 'suspended', 'cancelled', 'trial'])
                      ->default('active')
                      ->after('ativa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('account_status');
        });
    }
};
