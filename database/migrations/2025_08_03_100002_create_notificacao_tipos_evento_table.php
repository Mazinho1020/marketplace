<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('codigo', 100)->unique();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('categoria', 50)->default('geral');
            $table->boolean('automatico')->default(false);
            $table->string('agendamento_cron')->nullable();
            $table->json('aplicacoes_padrao')->nullable();
            $table->json('variaveis_disponiveis')->nullable();
            $table->json('condicoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->index(['empresa_id', 'ativo']);
            $table->index(['codigo', 'empresa_id']);
            $table->index('automatico');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_tipos_evento');
    }
};
