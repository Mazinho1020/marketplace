<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('tipo_evento_id');
            $table->unsignedBigInteger('aplicacao_id');
            $table->string('nome', 100);
            $table->string('categoria', 50)->default('geral');
            $table->string('titulo', 255);
            $table->text('mensagem');
            $table->string('subtitulo')->nullable();
            $table->string('texto_acao', 100)->nullable();
            $table->string('url_acao')->nullable();
            $table->json('canais')->default('["in_app"]');
            $table->enum('prioridade', ['baixa', 'normal', 'alta', 'urgente'])->default('normal');
            $table->integer('expira_em_minutos')->nullable();
            $table->json('variaveis')->nullable();
            $table->json('condicoes')->nullable();
            $table->json('segmentos_usuario')->nullable();
            $table->string('icone_classe', 100)->nullable();
            $table->string('cor_hex', 7)->default('#007bff');
            $table->string('arquivo_som')->nullable();
            $table->string('url_imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('padrao')->default(false);
            $table->integer('versao')->default(1);
            $table->decimal('percentual_ab_test', 5, 2)->default(100.00);
            $table->integer('total_uso')->default(0);
            $table->decimal('taxa_conversao', 5, 2)->default(0.00);
            $table->timestamp('ultimo_uso_em')->nullable();
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');

            $table->index(['empresa_id', 'ativo']);
            $table->index(['tipo_evento_id', 'aplicacao_id']);
            $table->index('padrao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_templates');
    }
};
