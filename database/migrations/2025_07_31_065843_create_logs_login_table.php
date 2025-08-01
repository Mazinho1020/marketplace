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
        Schema::create('logs_login', function (Blueprint $table) {
            $table->id();

            // Dados do usuário
            $table->string('email', 100)->index();
            $table->unsignedBigInteger('empresa_id')->nullable()->index();
            $table->unsignedBigInteger('usuario_id')->nullable()->index();

            // Resultado da tentativa
            $table->boolean('sucesso')->default(false)->index();
            $table->string('motivo')->nullable();

            // Dados da sessão
            $table->string('ip', 45)->index(); // IPv6 support
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();

            // Dados de localização (opcional)
            $table->string('pais', 2)->nullable();
            $table->string('cidade', 100)->nullable();

            // Campos de sincronização (padrão do projeto)
            $table->enum('sync_status', ['pending', 'synced', 'error'])->default('pending');
            $table->timestamp('sync_at')->nullable();
            $table->text('sync_error')->nullable();
            $table->string('hash_sync', 64)->nullable();

            $table->timestamps();

            // Índices compostos para consultas otimizadas
            $table->index(['email', 'created_at']);
            $table->index(['ip', 'created_at']);
            $table->index(['sucesso', 'created_at']);
            $table->index(['empresa_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_login');
    }
};
