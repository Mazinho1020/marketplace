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
        Schema::create('fidelidade_carteiras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('empresa_id')->default(1);
            $table->decimal('saldo_cashback', 10, 2)->default(0.00);
            $table->decimal('saldo_creditos', 10, 2)->default(0.00);
            $table->decimal('saldo_bloqueado', 10, 2)->default(0.00);
            $table->decimal('saldo_total_disponivel', 10, 2)->default(0.00);
            $table->string('nivel_atual', 20)->default('bronze');
            $table->integer('xp_total')->default(0);
            $table->string('status', 20)->default('ativa');
            $table->datetime('criado_em')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('atualizado_em')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->unique(['cliente_id', 'empresa_id']);
            $table->index(['status']);
            $table->index(['nivel_atual']);
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
