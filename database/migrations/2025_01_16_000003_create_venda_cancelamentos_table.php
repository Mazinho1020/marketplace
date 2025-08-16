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
        Schema::create('venda_cancelamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('lancamento_id');
            $table->enum('tipo_cancelamento', ['total', 'parcial']);
            $table->enum('motivo_categoria', [
                'cliente_desistiu', 
                'produto_indisponivel', 
                'erro_preco', 
                'problema_pagamento', 
                'outros'
            ]);
            $table->text('motivo_detalhado');
            $table->decimal('valor_cancelado', 10, 2);
            $table->decimal('valor_reembolso', 10, 2)->default(0.00);
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('aprovado_por_id')->nullable();
            $table->datetime('data_cancelamento')->useCurrent();
            $table->datetime('data_reembolso')->nullable();
            $table->text('observacoes')->nullable();
            
            // Campos de sincronização
            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->timestamp('sync_data')->useCurrent();
            
            // Timestamps
            $table->timestamps();
            
            // Índices para performance
            $table->index(['empresa_id'], 'idx_cancelamento_empresa');
            $table->index(['lancamento_id'], 'idx_cancelamento_lancamento');
            $table->index(['usuario_id'], 'idx_cancelamento_usuario');
            $table->index(['aprovado_por_id'], 'idx_cancelamento_aprovado_por');
            $table->index(['tipo_cancelamento'], 'idx_cancelamento_tipo');
            $table->index(['motivo_categoria'], 'idx_cancelamento_motivo');
            $table->index(['data_cancelamento'], 'idx_cancelamento_data');
            $table->index(['empresa_id', 'data_cancelamento'], 'idx_cancelamento_empresa_data');
            
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
                  
            $table->foreign('aprovado_por_id')
                  ->references('id')
                  ->on('empresa_usuarios')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_cancelamentos');
    }
};