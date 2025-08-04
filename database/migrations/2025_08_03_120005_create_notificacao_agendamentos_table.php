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
        Schema::create('notificacao_agendamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->unsignedBigInteger('tipo_evento_id')->comment('ID do tipo de evento');
            $table->string('nome', 100)->comment('Nome do agendamento');
            $table->text('descricao')->nullable()->comment('Descrição do agendamento');

            // Configuração do agendamento
            $table->enum('tipo_agendamento', ['cron', 'intervalo', 'data', 'evento'])->default('cron')->comment('Tipo de agendamento');
            $table->string('expressao_cron', 100)->nullable()->comment('Expressão cron (ex: 0 9 * * *)');
            $table->integer('minutos_intervalo')->nullable()->comment('Intervalo em minutos (para tipo_agendamento=intervalo)');
            $table->timestamp('data_especifica')->nullable()->comment('Data específica (para tipo_agendamento=data)');

            // Filtros e condições
            $table->json('aplicacoes_alvo')->comment('Aplicações alvo para este agendamento');
            $table->json('filtros_usuario')->nullable()->comment('Filtros de usuários (ex: apenas clientes ativos)');
            $table->json('condicoes')->nullable()->comment('Condições específicas para execução');

            // Configurações de execução
            $table->integer('maximo_destinatarios')->nullable()->comment('Máximo de destinatários por execução');
            $table->integer('tamanho_lote')->default(100)->comment('Tamanho do lote para processamento');
            $table->integer('tentativas_retry')->default(3)->comment('Tentativas em caso de erro');
            $table->integer('timeout_minutos')->default(60)->comment('Timeout da execução');

            // Status e controle
            $table->boolean('ativo')->default(true)->comment('Se o agendamento está ativo');
            $table->timestamp('ultima_execucao_em')->nullable()->comment('Última execução');
            $table->timestamp('proxima_execucao_em')->nullable()->comment('Próxima execução prevista');
            $table->enum('status_ultima_execucao', ['sucesso', 'falhou', 'parcial'])->nullable()->comment('Status da última execução');
            $table->integer('destinatarios_ultima_execucao')->nullable()->comment('Destinatários da última execução');
            $table->text('erros_ultima_execucao')->nullable()->comment('Erros da última execução');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'ativo'], 'idx_empresa_ativo');
            $table->index(['empresa_id', 'tipo_agendamento'], 'idx_empresa_tipo');
            $table->index(['proxima_execucao_em', 'ativo'], 'idx_proxima_execucao');
            $table->index('ultima_execucao_em', 'idx_ultima_execucao');
            $table->index('tipo_evento_id', 'idx_tipo_evento');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_agendamentos');
    }
};
