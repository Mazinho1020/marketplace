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
        Schema::create('produto_precos_quantidade', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('variacao_id')->nullable();
            $table->decimal('quantidade_minima', 10, 3);
            $table->decimal('quantidade_maxima', 10, 3)->nullable();
            $table->decimal('preco', 10, 2);
            $table->decimal('desconto_percentual', 5, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index(['empresa_id', 'produto_id']);
            $table->index(['produto_id', 'ativo']);
            $table->index('quantidade_minima');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('produto_id')->references('id')->on('produtos')->onDelete('cascade');
            $table->foreign('variacao_id')->references('id')->on('produto_variacoes_combinacoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_precos_quantidade');
    }
};
