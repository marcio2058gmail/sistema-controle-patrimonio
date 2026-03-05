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
            $table->mediumText('assinatura_base64')->nullable()->after('assinado');
            $table->timestamp('assinado_em')->nullable()->after('assinatura_base64');
            $table->string('assinado_ip', 45)->nullable()->after('assinado_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termos', function (Blueprint $table) {
            $table->dropColumn(['assinatura_base64', 'assinado_em', 'assinado_ip']);
        });
    }
};
