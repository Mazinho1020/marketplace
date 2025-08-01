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
            $table->unsignedBigInteger('programa_id')->nullable();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo_cashback', ['percentual', 'fixo', 'escalonado'])->default('percentual');
            $table->decimal('valor_cashback', 10, 2);
            $table->decimal('valor_minimo', 10, 2)->nullable();
            $table->decimal('valor_maximo', 10, 2)->nullable();
            $table->decimal('limite_mensal', 10, 2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->enum('status', ['ativo', 'inativo', 'pausado'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['programa_id']);
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
