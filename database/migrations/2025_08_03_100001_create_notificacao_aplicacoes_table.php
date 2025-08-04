<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_aplicacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->string('codigo', 50)->unique();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->string('icone_classe', 100)->nullable();
            $table->string('cor_hex', 7)->default('#007bff');
            $table->string('webhook_url')->nullable();
            $table->string('api_key')->nullable();
            $table->json('configuracoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem_exibicao')->default(0);
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->index(['empresa_id', 'ativo']);
            $table->index(['codigo', 'empresa_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_aplicacoes');
    }
};
