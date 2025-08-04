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
        Schema::create('notificacao_aplicacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->string('codigo', 50)->comment('Código único (customer, company, admin, delivery, loyalty)');
            $table->string('nome', 100)->comment('Nome da aplicação');
            $table->text('descricao')->nullable()->comment('Descrição da aplicação');
            $table->string('icone_classe', 100)->nullable()->comment('Classe do ícone (ex: fas fa-user)');
            $table->string('cor_hex', 7)->nullable()->comment('Cor tema da aplicação (#28a745)');
            $table->string('webhook_url', 500)->nullable()->comment('URL para receber webhooks (apps externas)');
            $table->string('api_key', 255)->nullable()->comment('Chave de autenticação para webhooks');
            $table->json('configuracoes')->nullable()->comment('Configurações específicas da aplicação');
            $table->boolean('ativo')->default(true)->comment('Se a aplicação está ativa');
            $table->integer('ordem_exibicao')->default(0)->comment('Ordem de exibição');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'codigo'], 'idx_empresa_codigo');
            $table->index(['empresa_id', 'ativo'], 'idx_empresa_ativo');
            $table->index('ordem_exibicao', 'idx_ordem_exibicao');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chave única
            $table->unique(['empresa_id', 'codigo', 'deleted_at'], 'unique_empresa_codigo');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_aplicacoes');
    }
};
