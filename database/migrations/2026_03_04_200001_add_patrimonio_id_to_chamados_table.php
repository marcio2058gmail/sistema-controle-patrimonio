<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            $table->foreignId('patrimonio_id')->nullable()->after('funcionario_id')
                ->constrained('patrimonios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chamados', function (Blueprint $table) {
            $table->dropForeign(['patrimonio_id']);
            $table->dropColumn('patrimonio_id');
        });
    }
};
