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
        Schema::create('comerciante_notificacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('tipo', 50); // estoque_baixo, estoque_zerado, produto_criado, etc.
            $table->string('titulo');
            $table->text('mensagem');
            $table->json('dados')->nullable(); // dados adicionais da notificação
            $table->string('url_acao')->nullable(); // URL para ação da notificação
            $table->string('icone', 50)->nullable(); // classe do ícone
            $table->string('cor', 20)->nullable(); // cor do badge/notificação
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'critica'])->default('media');
            $table->integer('prioridade_ordem')->default(3); // para ordenação
            $table->string('referencia_tipo', 50)->nullable(); // tipo da referência (produto, venda, etc.)
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID da referência
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamp('expirar_em')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['empresa_id', 'lida', 'created_at']);
            $table->index(['tipo', 'empresa_id']);
            $table->index(['prioridade_ordem', 'created_at']);
            $table->index(['referencia_tipo', 'referencia_id']);

            // Foreign keys (se necessário)
            // $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            // $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comerciante_notificacoes');
    }
};
