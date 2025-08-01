<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->unsignedBigInteger('empresa_id')->default(1);
            $table->integer('pedido_id')->nullable();
            $table->enum('tipo', ['credito', 'debito']);
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_pedido_original', 10, 2)->nullable();
            $table->decimal('percentual_aplicado', 5, 2)->nullable();
            $table->decimal('saldo_anterior', 10, 2)->nullable();
            $table->decimal('saldo_posterior', 10, 2)->nullable();
            $table->date('data_expiracao')->nullable();
            $table->string('status', 20)->default('disponivel');
            $table->string('observacoes', 255)->nullable();
            $table->datetime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['cliente_id']);
            $table->index(['empresa_id']);
            $table->index(['pedido_id']);
            $table->index(['status']);
            $table->index(['data_expiracao']);
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
