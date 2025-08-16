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
        Schema::create('venda_status_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('lancamento_id');
            $table->string('status_anterior', 50)->nullable();
            $table->string('status_novo', 50);
            $table->unsignedInteger('usuario_id');
            $table->string('motivo', 255)->nullable();
            $table->text('observacoes')->nullable();
            $table->json('dados_contexto')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('data_mudanca')->useCurrent();
            
            // Campos de sincronização
            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            
            // Índices para performance
            $table->index(['empresa_id'], 'idx_historico_empresa');
            $table->index(['lancamento_id'], 'idx_historico_lancamento');
            $table->index(['usuario_id'], 'idx_historico_usuario');
            $table->index(['status_novo'], 'idx_historico_status_novo');
            $table->index(['data_mudanca'], 'idx_historico_data');
            $table->index(['empresa_id', 'lancamento_id', 'data_mudanca'], 'idx_historico_empresa_lancamento_data');
            
            // Chaves estrangeiras
            $table->foreign('lancamento_id')
                  ->references('id')
                  ->on('lancamentos')
                  ->onDelete('cascade');
                  
            $table->foreign('empresa_id')
                  ->references('id')
                  ->on('empresas')
                  ->onDelete('cascade');
                  
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('empresa_usuarios')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_status_historico');
    }
};