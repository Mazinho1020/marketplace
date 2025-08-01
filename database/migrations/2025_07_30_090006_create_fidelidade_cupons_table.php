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
        Schema::create('fidelidade_cupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->default(1);
            $table->string('codigo', 50);
            $table->string('nome', 100);
            $table->string('descricao', 255)->nullable();
            $table->string('tipo', 30)->default('desconto_sacola');
            $table->decimal('valor_desconto', 10, 2)->nullable();
            $table->decimal('percentual_desconto', 5, 2)->nullable();
            $table->decimal('valor_minimo_pedido', 10, 2)->nullable();
            $table->integer('quantidade_maxima_uso')->nullable();
            $table->integer('quantidade_usada')->default(0);
            $table->integer('uso_por_cliente')->default(1);
            $table->datetime('data_inicio')->nullable();
            $table->datetime('data_fim')->nullable();
            $table->string('status', 20)->default('ativo');
            $table->datetime('criado_em')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['empresa_id']);
            $table->index(['codigo']);
            $table->index(['status']);
            $table->index(['data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fidelidade_cupons');
    }
};
