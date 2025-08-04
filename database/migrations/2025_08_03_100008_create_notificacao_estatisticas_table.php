<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_estatisticas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('aplicacao_id')->nullable();
            $table->unsignedBigInteger('tipo_evento_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->date('data_referencia');
            $table->integer('hora_referencia')->nullable();
            $table->enum('tipo_metrica', ['envio', 'entrega', 'leitura', 'clique', 'conversao'])->default('envio');
            $table->string('canal')->nullable();
            $table->integer('total_eventos')->default(0);
            $table->integer('total_usuarios_unicos')->default(0);
            $table->decimal('taxa_sucesso', 5, 2)->default(0.00);
            $table->decimal('tempo_medio_entrega', 8, 2)->default(0.00);
            $table->decimal('tempo_medio_leitura', 8, 2)->default(0.00);
            $table->json('dados_detalhados')->nullable();
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('set null');
            $table->foreign('tipo_evento_id')->references('id')->on('notificacao_tipos_evento')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('notificacao_templates')->onDelete('set null');

            $table->unique(['empresa_id', 'data_referencia', 'hora_referencia', 'aplicacao_id', 'tipo_evento_id', 'tipo_metrica', 'canal'], 'notificacao_estatisticas_unique');
            $table->index(['data_referencia', 'tipo_metrica']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_estatisticas');
    }
};
