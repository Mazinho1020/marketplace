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
        Schema::create('notificacao_enviada', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('corpo');
            $table->unsignedBigInteger('empresa_relacionada_id');
            $table->unsignedBigInteger('aplicacao_id');
            $table->string('canal'); // in_app, push, email, sms
            $table->string('tipo')->nullable(); // sistema, funcionalidade, manutencao, etc
            $table->timestamp('lido_em')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['empresa_relacionada_id', 'aplicacao_id']);
            $table->index(['created_at']);
            $table->index(['lido_em']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacao_enviada');
    }
};
