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
        Schema::create('fidelidade_cashback_regras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->default(1);
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('tipo_cashback', 30)->default('percentual');
            $table->decimal('valor_cashback', 10, 2);
            $table->decimal('valor_minimo', 10, 2)->nullable();
            $table->decimal('valor_maximo', 10, 2)->nullable();
            $table->decimal('limite_mensal', 10, 2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('status', 20)->default('ativo');
            $table->datetime('criado_em')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('atualizado_em')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->index(['empresa_id']);
            $table->index(['status']);
            $table->index(['data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_cashback_regras');
    }
};
