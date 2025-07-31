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
        Schema::create('fidelidade_carteiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('businesses')->onDelete('cascade');
            $table->decimal('saldo_cashback', 10, 2)->default(0.00);
            $table->decimal('saldo_creditos', 10, 2)->default(0.00);
            $table->decimal('saldo_bloqueado', 10, 2)->default(0.00);
            $table->decimal('saldo_total_disponivel', 10, 2)->default(0.00);
            $table->enum('nivel_atual', ['bronze', 'prata', 'ouro', 'diamond'])->default('bronze');
            $table->integer('xp_total')->default(0);
            $table->enum('status', ['ativa', 'bloqueada', 'suspensa'])->default('ativa');
            $table->timestamps();
            $table->string('sync_status', 20)->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            $table->softDeletes();

            $table->unique(['cliente_id', 'empresa_id'], 'uk_cliente_empresa');
            $table->index(['empresa_id']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_carteiras');
    }
};
