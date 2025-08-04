<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_enviadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('tipo_evento_id');
            $table->unsignedBigInteger('aplicacao_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('empresa_relacionada_id')->nullable();
            $table->string('usuario_externo_id')->nullable();
            $table->string('email_destinatario')->nullable();
            $table->string('telefone_destinatario')->nullable();
            $table->string('titulo', 255);
            $table->text('mensagem');
            $table->json('dados_processados')->nullable();
            $table->enum('canal', ['websocket', 'push', 'email', 'sms', 'in_app', 'webhook'])->default('in_app');
            $table->enum('prioridade', ['baixa', 'normal', 'alta', 'urgente'])->default('normal');
            $table->timestamp('agendado_para')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('entregue_em')->nullable();
            $table->timestamp('lido_em')->nullable();
            $table->timestamp('clicado_em')->nullable();
            $table->enum('status', ['pendente', 'enviado', 'entregue', 'lido', 'erro', 'expirado'])->default('pendente');
            $table->integer('tentativas')->default(0);
            $table->text('mensagem_erro')->nullable();
            $table->string('id_externo')->nullable();
            $table->timestamp('expira_em')->nullable();
            $table->json('dados_evento_origem')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('endereco_ip', 45)->nullable();
            $table->json('info_dispositivo')->nullable();
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('set null');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('set null');

            $table->index(['empresa_id', 'usuario_id']);
            $table->index(['status', 'agendado_para']);
            $table->index(['canal', 'status']);
            $table->index('lido_em');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_enviadas');
    }
};
