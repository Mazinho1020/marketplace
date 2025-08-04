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
        Schema::create('notificacao_estatisticas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->date('data')->comment('Data da estatística');
            $table->tinyInteger('hora')->nullable()->comment('Hora (0-23, NULL para dados diários)');

            // Dimensões
            $table->unsignedBigInteger('aplicacao_id')->nullable()->comment('ID da aplicação (NULL para todas)');
            $table->unsignedBigInteger('tipo_evento_id')->nullable()->comment('ID do tipo de evento (NULL para todos)');
            $table->unsignedBigInteger('template_id')->nullable()->comment('ID do template (NULL para todos)');
            $table->string('canal', 50)->nullable()->comment('Canal (NULL para todos)');

            // Métricas de envio
            $table->integer('notificacoes_enviadas')->default(0)->comment('Total de notificações enviadas');
            $table->integer('notificacoes_entregues')->default(0)->comment('Total entregues');
            $table->integer('notificacoes_falharam')->default(0)->comment('Total falharam');
            $table->integer('notificacoes_expiraram')->default(0)->comment('Total expiraram');

            // Métricas de engajamento
            $table->integer('notificacoes_lidas')->default(0)->comment('Total lidas');
            $table->integer('notificacoes_clicadas')->default(0)->comment('Total clicadas');
            $table->integer('destinatarios_unicos')->default(0)->comment('Destinatários únicos');

            // Taxas calculadas
            $table->decimal('taxa_entrega', 5, 2)->default(0.00)->comment('Taxa de entrega (%)');
            $table->decimal('taxa_abertura', 5, 2)->default(0.00)->comment('Taxa de abertura (%)');
            $table->decimal('taxa_clique', 5, 2)->default(0.00)->comment('Taxa de clique (%)');

            // Tempo médio
            $table->integer('tempo_medio_entrega_segundos')->default(0)->comment('Tempo médio de entrega (segundos)');
            $table->integer('tempo_medio_leitura_segundos')->default(0)->comment('Tempo médio para leitura (segundos)');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'data'], 'idx_empresa_data');
            $table->index(['empresa_id', 'data', 'hora'], 'idx_empresa_data_hora');
            $table->index(['aplicacao_id', 'data'], 'idx_app_data');
            $table->index(['tipo_evento_id', 'data'], 'idx_evento_data');
            $table->index(['template_id', 'data'], 'idx_template_data');
            $table->index(['canal', 'data'], 'idx_canal_data');
            $table->index(['taxa_entrega', 'taxa_abertura', 'taxa_clique'], 'idx_taxas');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chave única para evitar duplicatas
            $table->unique(['empresa_id', 'data', 'hora', 'aplicacao_id', 'tipo_evento_id', 'template_id', 'canal', 'deleted_at'], 'unique_estatisticas');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('set null');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_estatisticas');
    }
};
