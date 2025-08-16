<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabela de pagamentos das vendas
     * Conecta vendas com o sistema de pagamentos existente
     */
    public function up(): void
    {
        Schema::create('venda_pagamentos', function (Blueprint $table) {
            // Identificação principal
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->onDelete('cascade');
            $table->unsignedBigInteger('pagamento_id')->nullable()->comment('Referência ao pagamento na tabela pagamentos');
            
            // Informações do pagamento
            $table->unsignedBigInteger('forma_pagamento_id');
            $table->unsignedBigInteger('bandeira_id')->nullable();
            $table->decimal('valor_pagamento', 15, 4);
            $table->integer('parcelas')->default(1);
            $table->decimal('valor_parcela', 15, 4)->nullable();
            
            // Datas
            $table->datetime('data_pagamento');
            $table->datetime('data_compensacao')->nullable();
            
            // Status e controle
            $table->enum('status_pagamento', ['processando', 'confirmado', 'cancelado', 'estornado'])->default('confirmado');
            $table->string('referencia_externa', 100)->nullable();
            $table->string('autorizacao', 100)->nullable();
            $table->string('nsu', 50)->nullable();
            
            // Taxa e comissão
            $table->decimal('taxa_percentual', 5, 4)->default(0);
            $table->decimal('valor_taxa', 15, 4)->default(0);
            $table->decimal('valor_liquido', 15, 4)->comment('Valor após dedução das taxas');
            
            // Observações
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            
            // Auditoria
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('usuario_id');
            $table->timestamps();
            
            // Índices
            $table->index(['venda_id'], 'idx_venda_pagamento');
            $table->index(['pagamento_id'], 'idx_pagamento_ref');
            $table->index(['forma_pagamento_id'], 'idx_forma_pagamento');
            $table->index(['data_pagamento'], 'idx_data_pagamento');
            $table->index(['status_pagamento'], 'idx_status_pagamento');
            $table->index(['empresa_id'], 'idx_empresa_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_pagamentos');
    }
};