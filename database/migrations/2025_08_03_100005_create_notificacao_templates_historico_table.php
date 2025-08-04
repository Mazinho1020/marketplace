<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_templates_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->enum('acao', ['criado', 'editado', 'ativado', 'desativado', 'excluido', 'restaurado'])->default('editado');
            $table->json('alteracoes')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->string('motivo')->nullable();
            $table->string('endereco_ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('set null');

            $table->index(['template_id', 'created_at']);
            $table->index('acao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_templates_historico');
    }
};
