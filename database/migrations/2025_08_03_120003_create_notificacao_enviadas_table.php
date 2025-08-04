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
        Schema::create('notificacao_enviadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->unsignedBigInteger('template_id')->nullable()->comment('ID do template usado (pode ser NULL se enviado programaticamente)');
            $table->unsignedBigInteger('tipo_evento_id')->nullable()->comment('ID do tipo de evento');
            $table->unsignedBigInteger('aplicacao_id')->comment('ID da aplicação alvo');

            // Destinatário
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('ID do usuário destinatário');
            $table->unsignedBigInteger('empresa_relacionada_id')->nullable()->comment('ID da empresa relacionada (no contexto)');
            $table->string('usuario_externo_id', 100)->nullable()->comment('ID externo (para apps externas)');
            $table->string('email_destinatario', 255)->nullable()->comment('Email do destinatário');
            $table->string('telefone_destinatario', 20)->nullable()->comment('Telefone do destinatário');

            // Conteúdo processado
            $table->string('titulo', 255)->comment('Título processado');
            $table->text('mensagem')->comment('Mensagem processada');
            $table->json('dados_processados')->nullable()->comment('Dados completos processados');

            // Controle de envio
            $table->string('canal', 50)->comment('Canal usado (websocket, push, email, sms, in_app)');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->timestamp('agendado_para')->nullable()->comment('Quando foi agendado para envio');
            $table->timestamp('enviado_em')->nullable()->comment('Quando foi efetivamente enviado');
            $table->timestamp('entregue_em')->nullable()->comment('Quando foi entregue (se suportado pelo canal)');
            $table->timestamp('lido_em')->nullable()->comment('Quando foi lido pelo usuário');
            $table->timestamp('clicado_em')->nullable()->comment('Quando o usuário clicou na ação');

            // Status e controle
            $table->enum('status', ['pendente', 'enviado', 'entregue', 'falhou', 'expirou'])->default('pendente');
            $table->integer('tentativas')->default(0)->comment('Número de tentativas de envio');
            $table->text('mensagem_erro')->nullable()->comment('Mensagem de erro (se falhou)');
            $table->string('id_externo', 255)->nullable()->comment('ID externo do provedor (ex: Firebase)');
            $table->timestamp('expira_em')->nullable()->comment('Quando a notificação expira');

            // Dados contextuais
            $table->json('dados_evento_origem')->nullable()->comment('Dados originais do evento');
            $table->text('user_agent')->nullable()->comment('User agent (para in-app)');
            $table->string('endereco_ip', 45)->nullable()->comment('IP do usuário (para in-app)');
            $table->json('info_dispositivo')->nullable()->comment('Informações do dispositivo');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'usuario_id'], 'idx_empresa_usuario');
            $table->index(['empresa_id', 'aplicacao_id'], 'idx_empresa_app');
            $table->index(['empresa_id', 'canal'], 'idx_empresa_canal');
            $table->index(['empresa_id', 'status'], 'idx_empresa_status');
            $table->index(['empresa_id', 'enviado_em'], 'idx_empresa_enviado');
            $table->index(['usuario_id', 'lido_em'], 'idx_usuario_nao_lido');
            $table->index(['template_id', 'status', 'enviado_em'], 'idx_template_stats');
            $table->index(['agendado_para', 'status'], 'idx_agendado');
            $table->index(['expira_em', 'status'], 'idx_expirado');
            $table->index(['tentativas', 'status'], 'idx_tentativas');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('set null');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('set null');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');
            $table->foreign('empresa_relacionada_id')->references('id')->on('empresas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_enviadas');
    }
};
