<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venda_itens', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA (OBRIGATÓRIO)
            $table->id();

            // 2. MULTITENANCY (OBRIGATÓRIO)
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');

            // 3. RELACIONAMENTOS PRINCIPAIS
            $table->unsignedBigInteger('venda_id')->comment('ID da venda');
            $table->unsignedBigInteger('produto_id')->nullable()->comment('ID do produto');
            $table->unsignedBigInteger('produto_variacao_id')->nullable()->comment('ID da variação do produto');

            // 4. DADOS DO PRODUTO NO MOMENTO DA VENDA
            $table->string('codigo_produto', 100)->nullable()->comment('Código do produto no momento da venda');
            $table->string('nome_produto', 255)->comment('Nome do produto no momento da venda');
            $table->text('descricao_produto')->nullable()->comment('Descrição do produto');
            $table->string('unidade_medida', 20)->default('UN')->comment('Unidade de medida');

            // 5. QUANTIDADES E VALORES
            $table->decimal('quantidade', 10, 3)->default(1)->comment('Quantidade vendida');
            $table->decimal('valor_unitario', 10, 2)->comment('Valor unitário no momento da venda');
            $table->decimal('valor_unitario_original', 10, 2)->comment('Valor original sem desconto');
            $table->decimal('valor_desconto_item', 10, 2)->default(0)->comment('Desconto aplicado no item');
            $table->decimal('percentual_desconto', 5, 2)->default(0)->comment('Percentual de desconto (%)');
            $table->decimal('valor_acrescimo_item', 10, 2)->default(0)->comment('Acréscimo aplicado no item');
            $table->decimal('valor_total_item', 10, 2)->comment('Valor total do item (quantidade * valor_unitario)');

            // 6. CUSTOS E MARGENS
            $table->decimal('valor_custo_unitario', 10, 2)->nullable()->comment('Custo unitário do produto');
            $table->decimal('valor_custo_total', 10, 2)->nullable()->comment('Custo total do item');
            $table->decimal('margem_lucro', 8, 2)->nullable()->comment('Margem de lucro (%)');

            // 7. IMPOSTOS POR ITEM
            $table->decimal('valor_impostos_item', 10, 2)->default(0)->comment('Impostos do item');
            $table->decimal('aliquota_icms', 5, 2)->default(0)->comment('Alíquota ICMS (%)');
            $table->decimal('aliquota_ipi', 5, 2)->default(0)->comment('Alíquota IPI (%)');
            $table->decimal('aliquota_pis', 5, 2)->default(0)->comment('Alíquota PIS (%)');
            $table->decimal('aliquota_cofins', 5, 2)->default(0)->comment('Alíquota COFINS (%)');

            // 8. CONTROLE DE ESTOQUE
            $table->boolean('controla_estoque')->default(true)->comment('Se controla estoque para este item');
            $table->decimal('estoque_anterior', 10, 3)->nullable()->comment('Estoque antes da venda');
            $table->decimal('estoque_posterior', 10, 3)->nullable()->comment('Estoque após a venda');

            // 9. DADOS FISCAIS
            $table->string('ncm', 20)->nullable()->comment('Código NCM');
            $table->string('cest', 20)->nullable()->comment('Código CEST');
            $table->string('cfop', 10)->nullable()->comment('Código CFOP');

            // 10. OBSERVAÇÕES E METADADOS
            $table->text('observacoes')->nullable()->comment('Observações do item');
            $table->json('metadados')->nullable()->comment('Dados adicionais do item');
            $table->json('caracteristicas_produto')->nullable()->comment('Características do produto na venda');

            // 11. KITS E COMBOS
            $table->boolean('item_kit')->default(false)->comment('Se é item de kit/combo');
            $table->unsignedBigInteger('kit_pai_id')->nullable()->comment('ID do item pai do kit');
            $table->integer('ordem_item')->default(0)->comment('Ordem do item na venda');

            // 12. DEVOLUÇÕES E CANCELAMENTOS
            $table->decimal('quantidade_devolvida', 10, 3)->default(0)->comment('Quantidade devolvida');
            $table->decimal('quantidade_cancelada', 10, 3)->default(0)->comment('Quantidade cancelada');
            $table->enum('status_item', ['ativo', 'cancelado', 'devolvido', 'trocado'])
                ->default('ativo')
                ->comment('Status do item');

            // 13. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                ->default('pending')
                ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 14. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 15. ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'status_item'], 'idx_venda_itens_empresa_status');
            $table->index('created_at', 'idx_venda_itens_created_at');
            $table->index('sync_status', 'idx_venda_itens_sync_status');
            $table->index('deleted_at', 'idx_venda_itens_deleted_at');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_venda_itens_sync_control');

            // 16. ÍNDICES ESPECÍFICOS
            $table->index(['venda_id', 'ordem_item'], 'idx_venda_itens_venda_ordem');
            $table->index(['produto_id', 'created_at'], 'idx_venda_itens_produto_data');
            $table->index(['venda_id', 'produto_id'], 'idx_venda_itens_venda_produto');
            $table->index(['empresa_id', 'produto_id'], 'idx_venda_itens_empresa_produto');
            $table->index('kit_pai_id', 'idx_venda_itens_kit_pai');
            $table->index(['status_item', 'created_at'], 'idx_venda_itens_status_data');
        });

        // 17. COMENTÁRIO DA TABELA (MySQL only - skip for SQLite)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE venda_itens COMMENT = 'Itens das vendas - Detalhamento de produtos vendidos'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venda_itens');
    }
};
