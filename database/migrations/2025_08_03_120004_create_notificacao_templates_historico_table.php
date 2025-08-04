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
        Schema::create('notificacao_templates_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');
            $table->unsignedBigInteger('template_id')->comment('ID do template');
            $table->enum('acao', ['criado', 'atualizado', 'excluido', 'ativado', 'desativado', 'clonado'])->comment('Ação realizada');

            // Dados da mudança
            $table->json('alteracoes')->nullable()->comment('Campos que foram alterados');
            $table->json('dados_anteriores')->nullable()->comment('Dados anteriores');
            $table->json('dados_novos')->nullable()->comment('Novos dados');
            $table->string('motivo', 255)->nullable()->comment('Motivo da alteração');

            // Contexto da alteração
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('Usuário que fez a alteração');
            $table->string('endereco_ip', 45)->nullable()->comment('IP do usuário');
            $table->text('user_agent')->nullable()->comment('User agent');
            $table->string('sessao_id', 255)->nullable()->comment('ID da sessão');

            // Sincronização Multi-Sites (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending')->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // Timestamps Laravel
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('template_id', 'idx_template');
            $table->index('empresa_id', 'idx_empresa');
            $table->index(['empresa_id', 'acao'], 'idx_empresa_acao');
            $table->index('usuario_id', 'idx_usuario');
            $table->index('created_at', 'idx_created');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('sync_data', 'idx_sync_data');

            // Chaves estrangeiras
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_templates_historico');
    }
};
