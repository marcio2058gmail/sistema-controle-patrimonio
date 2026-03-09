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
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->string('rg_numero', 20)->nullable()->after('email');
            $table->string('ctps_numero', 20)->nullable()->after('rg_numero');
            $table->string('ctps_serie', 10)->nullable()->after('ctps_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn(['rg_numero', 'ctps_numero', 'ctps_serie']);
        });
    }
};
