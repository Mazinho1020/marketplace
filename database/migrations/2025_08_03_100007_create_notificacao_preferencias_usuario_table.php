<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificacao_preferencias_usuario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('aplicacao_id');
            $table->boolean('websocket_habilitado')->default(true);
            $table->boolean('push_habilitado')->default(true);
            $table->boolean('email_habilitado')->default(true);
            $table->boolean('sms_habilitado')->default(false);
            $table->boolean('in_app_habilitado')->default(true);
            $table->time('horario_silencio_inicio')->nullable();
            $table->time('horario_silencio_fim')->nullable();
            $table->enum('frequencia_digest', ['nunca', 'diario', 'semanal', 'mensal'])->default('nunca');
            $table->time('horario_digest')->default('09:00');
            $table->json('tipos_evento_bloqueados')->nullable();
            $table->json('configuracoes_adicionais')->nullable();
            $table->string('sync_hash')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('synced');
            $table->timestamp('sync_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('empresa_usuarios')->onDelete('cascade');
            $table->foreign('aplicacao_id')->references('id')->on('notificacao_aplicacoes')->onDelete('cascade');

            $table->unique(['usuario_id', 'aplicacao_id']);
            $table->index(['empresa_id', 'usuario_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificacao_preferencias_usuario');
    }
};
