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
        Schema::create('transacoes_pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cartao_fidelidade_id')->constrained('cartoes_fidelidade')->onDelete('cascade');
            $table->foreignId('programa_fidelidade_id')->constrained('programas_fidelidade')->onDelete('cascade');
            $table->enum('tipo', ['acumulo', 'resgate', 'bonus', 'ajuste', 'expiracao']);
            $table->integer('pontos');
            $table->decimal('valor_referencia', 10, 2)->nullable();
            $table->string('descricao')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('pdv_sale_id')->nullable()->constrained('pdv_sales')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cartao_fidelidade_id', 'tipo']);
            $table->index(['programa_fidelidade_id', 'created_at']);
            $table->index(['tipo', 'created_at']);
            $table->index(['processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_pontos');
    }
};
