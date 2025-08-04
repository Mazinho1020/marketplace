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
        Schema::create('notificacao_tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->string('codigo', 100)->comment('Código único (pedido_criado, pagamento_confirmado)');
            $table->string('nome', 150)->comment('Nome amigável do evento');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do evento');
            $table->string('categoria', 50)->default('geral')->comment('Categoria do evento (pedido, pagamento, usuario, sistema)');
            $table->boolean('automatico')->default(false)->comment('Se é um evento automático (cron)');
            $table->string('agendamento_cron', 100)->nullable()->comment('Expressão cron (se automático)');
            $table->json('aplicacoes_padrao')->nullable()->comment('Aplicações padrão que recebem este evento');
            $table->json('variaveis_disponiveis')->nullable()->comment('Variáveis disponíveis para templates');
            $table->json('condicoes')->nullable()->comment('Condições para disparar o evento');
            $table->boolean('ativo')->default(true)->comment('Se o tipo de evento está ativo');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'codigo'], 'idx_empresa_codigo');
            $table->index(['empresa_id', 'categoria'], 'idx_empresa_categoria');
            $table->index(['empresa_id', 'automatico'], 'idx_empresa_automatico');
            $table->index(['empresa_id', 'ativo'], 'idx_empresa_ativo');
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
        Schema::dropIfExists('notificacao_tipos_evento');
    }
};
