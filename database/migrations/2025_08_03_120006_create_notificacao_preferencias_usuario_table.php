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
        Schema::create('notificacao_preferencias_usuario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->unsignedBigInteger('usuario_id')->comment('ID do usuário');
            $table->unsignedBigInteger('aplicacao_id')->comment('ID da aplicação');

            // Preferências por canal
            $table->boolean('websocket_ativo')->default(true)->comment('WebSocket ativo');
            $table->boolean('push_ativo')->default(true)->comment('Push notifications ativo');
            $table->boolean('email_ativo')->default(true)->comment('Email ativo');
            $table->boolean('sms_ativo')->default(false)->comment('SMS ativo');
            $table->boolean('in_app_ativo')->default(true)->comment('In-app ativo');

            // Preferências por tipo de evento
            $table->json('preferencias_evento')->nullable()->comment('Preferências específicas por tipo de evento');

            // Configurações de horário
            $table->time('horario_silencio_inicio')->nullable()->comment('Início do período de silêncio');
            $table->time('horario_silencio_fim')->nullable()->comment('Fim do período de silêncio');
            $table->string('fuso_horario', 50)->nullable()->comment('Fuso horário do usuário');

            // Configurações de frequência
            $table->enum('frequencia_resumo', ['nunca', 'imediato', 'horario', 'diario', 'semanal'])->default('imediato')->comment('Frequência do resumo');
            $table->integer('maximo_notificacoes_hora')->default(10)->comment('Máximo de notificações por hora');

            // Dispositivos
            $table->json('tokens_push')->nullable()->comment('Tokens para push notifications');
            $table->json('info_dispositivos')->nullable()->comment('Informações dos dispositivos');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'usuario_id'], 'idx_empresa_usuario');
            $table->index(['empresa_id', 'usuario_id', 'aplicacao_id'], 'idx_empresa_usuario_app');
            $table->index('push_ativo', 'idx_push_ativo');
            $table->index('email_ativo', 'idx_email_ativo');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chave única
            $table->unique(['empresa_id', 'usuario_id', 'aplicacao_id', 'deleted_at'], 'unique_usuario_app');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_preferencias_usuario');
    }
};
