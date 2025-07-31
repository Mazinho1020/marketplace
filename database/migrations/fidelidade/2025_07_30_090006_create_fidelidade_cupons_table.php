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
        Schema::create('fidelidade_cupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('businesses')->onDelete('cascade');
            $table->string('codigo', 50);
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['desconto_sacola', 'desconto_entrega', 'desconto_item', 'beneficio_extra']);
            $table->decimal('valor_desconto', 10, 2)->nullable();
            $table->decimal('percentual_desconto', 5, 2)->nullable();
            $table->decimal('valor_minimo_pedido', 10, 2)->default(0.00);
            $table->integer('quantidade_maxima_uso')->nullable();
            $table->integer('quantidade_usada')->default(0);
            $table->integer('uso_por_cliente')->default(1);
            $table->datetime('data_inicio')->nullable();
            $table->datetime('data_fim')->nullable();
            $table->enum('status', ['ativo', 'pausado', 'expirado', 'esgotado'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['codigo', 'empresa_id'], 'uk_codigo_empresa');
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
        Schema::dropIfExists('fidelidade_cupons');
    }
};
