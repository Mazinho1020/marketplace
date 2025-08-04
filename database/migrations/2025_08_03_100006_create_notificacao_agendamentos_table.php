<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_agendamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('tipo_evento_id');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('expressao_cron');
            $table->json('aplicacoes_alvo')->nullable();
            $table->json('condicoes')->nullable();
            $table->json('parametros')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('max_execucoes')->nullable();
            $table->integer('total_execucoes')->default(0);
            $table->timestamp('proxima_execucao')->nullable();
            $table->timestamp('ultima_execucao')->nullable();
            $table->enum('status_ultima_execucao', ['sucesso', 'erro', 'parcial'])->nullable();
            $table->text('log_ultima_execucao')->nullable();
            $table->integer('timeout_segundos')->default(300);
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('cascade');

            $table->index(['empresa_id', 'ativo']);
            $table->index('proxima_execucao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_agendamentos');
    }
};
