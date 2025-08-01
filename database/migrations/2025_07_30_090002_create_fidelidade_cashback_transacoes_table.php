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
        Schema::create('fidelidade_cashback_transacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->integer('pedido_id')->nullable();
            $table->enum('tipo', ['credito', 'debito', 'expiracao', 'bloqueio']);
            $table->decimal('valor_cashback', 10, 2);
            $table->decimal('pontos_ganhos', 10, 2)->default(0);
            $table->decimal('valor_pedido_original', 10, 2)->nullable();
            $table->decimal('percentual_aplicado', 5, 2)->nullable();
            $table->decimal('saldo_anterior', 10, 2)->nullable();
            $table->decimal('saldo_posterior', 10, 2)->nullable();
            $table->date('data_transacao')->nullable();
            $table->date('data_expiracao')->nullable();
            $table->enum('status', ['disponivel', 'usado', 'expirado', 'bloqueado'])->default('disponivel');
            $table->string('motivo_bloqueio')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->string('sync_status', 20)->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            $table->softDeletes();

            $table->index(['cliente_id']);
            $table->index(['programa_id']);
            $table->index(['pedido_id']);
            $table->index(['status']);
            $table->index(['data_expiracao']);
            $table->index(['sync_status', 'sync_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_cashback_transacoes');
    }
};
