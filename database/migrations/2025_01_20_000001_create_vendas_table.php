<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Sistema completo de vendas para o marketplace
     * Integrado com produtos, clientes e pagamentos existentes
     */
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            // Identificação principal
            $table->id();
            $table->uuid('uuid')->unique()->comment('UUID único para identificação externa');
            
            // Relacionamentos principais
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('usuario_id')->comment('Vendedor/atendente responsável');
            $table->unsignedBigInteger('cliente_id')->nullable()->comment('Referência para tabela pessoas');
            $table->unsignedInteger('caixa_id')->nullable();
            $table->unsignedInteger('mesa_id')->nullable();
            
            // Identificação da venda
            $table->string('numero_venda', 50)->unique()->comment('Número sequencial da venda');
            $table->enum('tipo_venda', ['balcao', 'delivery', 'mesa', 'online', 'whatsapp'])->default('balcao');
            $table->enum('origem', ['pdv', 'manual', 'delivery', 'api', 'whatsapp'])->default('pdv');
            
            // Valores financeiros
            $table->decimal('subtotal', 15, 4)->default(0)->comment('Soma dos itens sem desconto');
            $table->decimal('desconto_percentual', 5, 2)->default(0);
            $table->decimal('desconto_valor', 15, 4)->default(0);
            $table->decimal('acrescimo_percentual', 5, 2)->default(0);
            $table->decimal('acrescimo_valor', 15, 4)->default(0);
            $table->decimal('total_impostos', 15, 4)->default(0);
            $table->decimal('total_comissao', 15, 4)->default(0);
            $table->decimal('valor_total', 15, 4)->default(0)->comment('Valor final da venda');
            
            // Pagamento e status
            $table->enum('status_venda', ['orcamento', 'pendente', 'confirmada', 'paga', 'entregue', 'finalizada', 'cancelada'])->default('pendente');
            $table->enum('status_pagamento', ['pendente', 'parcial', 'pago', 'estornado'])->default('pendente');
            $table->enum('status_entrega', ['pendente', 'preparando', 'pronto', 'saiu_entrega', 'entregue', 'cancelado'])->default('pendente');
            
            // Datas importantes
            $table->datetime('data_venda');
            $table->datetime('data_entrega_prevista')->nullable();
            $table->datetime('data_entrega_realizada')->nullable();
            
            // Informações complementares
            $table->text('observacoes')->nullable();
            $table->text('observacoes_internas')->nullable();
            $table->string('cupom_desconto', 50)->nullable();
            $table->json('dados_entrega')->nullable()->comment('Endereço, contato, etc');
            $table->json('metadados')->nullable()->comment('Dados específicos por canal');
            
            // Controle de comissão do marketplace
            $table->decimal('percentual_comissao', 5, 4)->default(0);
            $table->decimal('valor_comissao_marketplace', 15, 4)->default(0);
            $table->boolean('comissao_calculada')->default(false);
            
            // Nota fiscal
            $table->boolean('nf_gerada')->default(false);
            $table->string('nf_numero', 50)->nullable();
            $table->string('nf_chave', 44)->nullable();
            $table->datetime('nf_data_emissao')->nullable();
            $table->text('nf_xml_path')->nullable();
            
            // Auditoria
            $table->softDeletes();
            $table->timestamps();
            
            // Índices para performance
            $table->index(['empresa_id', 'data_venda'], 'idx_empresa_data');
            $table->index(['cliente_id', 'data_venda'], 'idx_cliente_data');
            $table->index(['usuario_id', 'data_venda'], 'idx_vendedor_data');
            $table->index(['status_venda', 'data_venda'], 'idx_status_data');
            $table->index(['tipo_venda', 'origem'], 'idx_tipo_origem');
            $table->index(['numero_venda'], 'idx_numero_venda');
            $table->index(['nf_numero'], 'idx_nf_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};