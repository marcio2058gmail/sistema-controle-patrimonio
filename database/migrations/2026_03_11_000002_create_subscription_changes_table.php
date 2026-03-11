<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de histórico de alterações de plano/assinatura.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('assinaturas')->cascadeOnDelete();
            $table->foreignId('old_plan_id')->nullable()->constrained('planos')->nullOnDelete();
            $table->foreignId('new_plan_id')->nullable()->constrained('planos')->nullOnDelete();
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('new_price', 10, 2)->nullable();
            $table->unsignedBigInteger('changed_by');
            $table->foreign('changed_by')->references('id')->on('users');
            $table->text('reason')->nullable();
            $table->string('type', 30)->default('plan_change')
                  ->comment('plan_change | status_change | price_change');
            $table->timestamps();

            $table->index('subscription_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_changes');
    }
};
