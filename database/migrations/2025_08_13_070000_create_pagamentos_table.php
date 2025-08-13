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
        if (!Schema::hasTable('pagamentos')) {
            Schema::create('pagamentos', function (Blueprint $table) {
                $table->id();
                
                // Relacionamento 1:N com lancamentos
                $table->unsignedBigInteger('lancamento_id');
                $table->foreign('lancamento_id')->references('id')->on('lancamentos')->onDelete('cascade');
                
                // Dados do pagamento
                $table->decimal('valor', 15, 2);
                $table->datetime('data_pagamento');
                
                // Forma de pagamento
                $table->unsignedBigInteger('forma_pagamento_id')->nullable();
                $table->unsignedBigInteger('bandeira_id')->nullable();
                
                // Dados bancários
                $table->unsignedBigInteger('conta_bancaria_id')->nullable();
                
                // Taxas e tarifas
                $table->decimal('taxa', 5, 4)->nullable(); // Percentual da taxa
                $table->decimal('valor_taxa', 15, 2)->default(0);
                
                // Observações
                $table->text('observacoes')->nullable();
                
                // Dados de confirmação/comprovante
                $table->string('numero_comprovante', 100)->nullable();
                $table->json('dados_confirmacao')->nullable(); // Para guardar dados específicos de cada forma de pagamento
                
                // Controle
                $table->unsignedBigInteger('usuario_id')->nullable(); // Quem registrou o pagamento
                $table->enum('status', ['confirmado', 'pendente', 'cancelado'])->default('confirmado');
                
                // Campos de sincronização
                $table->timestamp('sync_data')->useCurrent();
                $table->string('sync_hash', 32)->nullable();
                $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
                
                $table->timestamps();
                
                // Índices para performance
                $table->index(['lancamento_id']);
                $table->index(['data_pagamento']);
                $table->index(['forma_pagamento_id']);
                $table->index(['conta_bancaria_id']);
                $table->index(['status']);
                $table->index(['usuario_id']);
                $table->index(['sync_status', 'sync_data']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};