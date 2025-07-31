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
        Schema::create('fidelidade_cashback_regras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('businesses')->onDelete('cascade');
            $table->enum('tipo_regra', ['categoria', 'produto', 'dia_semana', 'horario', 'primeira_compra']);
            $table->integer('referencia_id')->nullable();
            $table->integer('dia_semana')->nullable();
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->decimal('percentual_cashback', 5, 2);
            $table->decimal('valor_maximo_cashback', 10, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empresa_id']);
            $table->index(['tipo_regra']);
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
