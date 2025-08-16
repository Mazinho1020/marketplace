<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabela de itens das vendas
     * Relaciona produtos com as vendas realizadas
     */
    public function up(): void
    {
        Schema::create('venda_itens', function (Blueprint $table) {
            // Identificação principal
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->onDelete('cascade');
            
            // Relacionamentos com produtos
            $table->unsignedInteger('produto_id');
            $table->unsignedInteger('produto_variacao_id')->nullable();
            $table->string('codigo_produto', 50)->nullable();
            $table->string('nome_produto', 255)->comment('Nome do produto no momento da venda');
            
            // Quantidades e valores
            $table->decimal('quantidade', 10, 4);
            $table->decimal('valor_unitario', 15, 4)->comment('Preço unitário no momento da venda');
            $table->decimal('valor_unitario_original', 15, 4)->comment('Preço original antes de descontos');
            $table->decimal('desconto_percentual', 5, 2)->default(0);
            $table->decimal('desconto_valor', 15, 4)->default(0);
            $table->decimal('valor_total_item', 15, 4)->comment('Quantidade * valor_unitario');
            
            // Custos e margens
            $table->decimal('custo_unitario', 15, 4)->default(0)->comment('Custo do produto para cálculo de margem');
            $table->decimal('margem_lucro', 15, 4)->default(0)->comment('Margem de lucro do item');
            
            // Impostos
            $table->decimal('aliquota_icms', 5, 2)->default(0);
            $table->decimal('valor_icms', 15, 4)->default(0);
            $table->decimal('aliquota_ipi', 5, 2)->default(0);
            $table->decimal('valor_ipi', 15, 4)->default(0);
            $table->decimal('aliquota_pis', 5, 2)->default(0);
            $table->decimal('valor_pis', 15, 4)->default(0);
            $table->decimal('aliquota_cofins', 5, 2)->default(0);
            $table->decimal('valor_cofins', 15, 4)->default(0);
            
            // Informações complementares
            $table->text('observacoes')->nullable();
            $table->json('configuracoes')->nullable()->comment('Configurações específicas do produto');
            $table->json('personalizacoes')->nullable()->comment('Personalizações do cliente');
            
            // Controle de estoque
            $table->boolean('estoque_baixado')->default(false);
            $table->datetime('data_baixa_estoque')->nullable();
            
            // Comissão
            $table->decimal('percentual_comissao_vendedor', 5, 2)->default(0);
            $table->decimal('valor_comissao_vendedor', 15, 4)->default(0);
            
            // Auditoria
            $table->unsignedInteger('empresa_id');
            $table->timestamps();
            
            // Índices
            $table->index(['venda_id'], 'idx_venda');
            $table->index(['produto_id'], 'idx_produto');
            $table->index(['empresa_id'], 'idx_empresa');
            $table->index(['codigo_produto'], 'idx_codigo_produto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_itens');
    }
};