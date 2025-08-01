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
        Schema::create('programas_fidelidade', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('pontos_por_real', 8, 2)->default(1.00);
            $table->decimal('valor_ponto', 8, 4)->default(0.01);
            $table->enum('status', ['ativo', 'inativo', 'pausado'])->default('ativo');
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->json('regras')->nullable();
            $table->json('configuracoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['data_inicio', 'data_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas_fidelidade');
    }
};
