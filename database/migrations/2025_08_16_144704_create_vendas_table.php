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
        Schema::create('vendas', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA (OBRIGATÓRIO)
            $table->id();

            // 2. MULTITENANCY (OBRIGATÓRIO)
            $table->unsignedBigInteger('empresa_id')->comment('ID da empresa (multitenancy)');

            // 3. RELACIONAMENTOS PRINCIPAIS
            $table->unsignedBigInteger('cliente_id')->nullable()->comment('Cliente da venda');

            $table->unsignedBigInteger('vendedor_id')->nullable()->comment('Vendedor responsável');

            $table->unsignedBigInteger('caixa_id')->nullable()->comment('ID do caixa utilizado');

            // 4. IDENTIFICAÇÃO E CONTROLE
            $table->string('numero_venda', 50)->comment('Número sequencial da venda');
            $table->string('codigo_venda', 100)->nullable()->comment('Código customizado da venda');
            $table->uuid('uuid')->unique()->comment('UUID único da venda');

            // 5. DADOS DA VENDA
            $table->enum('tipo_venda', ['balcao', 'delivery', 'online', 'telefone', 'mesa'])
                ->default('balcao')
                ->comment('Tipo da venda');

            $table->enum('status', ['aberta', 'finalizada', 'cancelada', 'em_andamento', 'aguardando_pagamento'])
                ->default('aberta')
                ->comment('Status da venda');

            // 6. VALORES E CÁLCULOS
            $table->decimal('valor_bruto', 10, 2)->default(0)->comment('Valor bruto da venda');
            $table->decimal('valor_desconto', 10, 2)->default(0)->comment('Valor total de desconto');
            $table->decimal('valor_acrescimo', 10, 2)->default(0)->comment('Valor total de acréscimo');
            $table->decimal('valor_frete', 10, 2)->default(0)->comment('Valor do frete');
            $table->decimal('valor_taxa_servico', 10, 2)->default(0)->comment('Taxa de serviço');
            $table->decimal('valor_total', 10, 2)->default(0)->comment('Valor total final');
            $table->decimal('valor_comissao_marketplace', 10, 2)->default(0)->comment('Comissão do marketplace');
            $table->decimal('valor_liquido_vendedor', 10, 2)->default(0)->comment('Valor líquido para o vendedor');

            // 7. IMPOSTOS E TAXAS
            $table->decimal('valor_impostos', 10, 2)->default(0)->comment('Valor total de impostos');
            $table->decimal('aliquota_comissao', 5, 2)->default(0)->comment('Alíquota de comissão (%)');

            // 8. DATAS E CONTROLE TEMPORAL
            $table->timestamp('data_venda')->useCurrent()->comment('Data/hora da venda');
            $table->timestamp('data_finalizacao')->nullable()->comment('Data/hora de finalização');
            $table->timestamp('data_cancelamento')->nullable()->comment('Data/hora de cancelamento');

            // 9. OBSERVAÇÕES E METADADOS
            $table->text('observacoes')->nullable()->comment('Observações da venda');
            $table->text('observacoes_internas')->nullable()->comment('Observações internas');
            $table->json('metadados')->nullable()->comment('Dados adicionais da venda');

            // 10. ENTREGA E LOGÍSTICA
            $table->enum('tipo_entrega', ['retirada', 'delivery', 'correios', 'transportadora'])
                ->nullable()
                ->comment('Tipo de entrega');
            
            $table->json('dados_entrega')->nullable()->comment('Dados de endereço e entrega');
            $table->decimal('tempo_estimado_entrega', 8, 2)->nullable()->comment('Tempo estimado em minutos');

            // 11. ORIGEM E CANAL
            $table->enum('origem_venda', ['pdv', 'app', 'site', 'whatsapp', 'telefone', 'marketplace'])
                ->default('pdv')
                ->comment('Origem da venda');

            $table->string('canal_venda', 100)->nullable()->comment('Canal específico da venda');

            // 12. CONTROLE DE CANCELAMENTO
            $table->string('motivo_cancelamento', 255)->nullable()->comment('Motivo do cancelamento');
            $table->unsignedBigInteger('cancelado_por')->nullable()->comment('Usuário que cancelou');

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
            $table->index(['empresa_id', 'status'], 'idx_empresa_status');
            $table->index('created_at', 'idx_created_at');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');

            // 16. ÍNDICES ESPECÍFICOS
            $table->index(['empresa_id', 'numero_venda'], 'idx_empresa_numero');
            $table->index(['empresa_id', 'data_venda'], 'idx_empresa_data');
            $table->index(['cliente_id', 'status'], 'idx_cliente_status');
            $table->index(['vendedor_id', 'data_venda'], 'idx_vendedor_data');
            $table->index('uuid', 'idx_uuid');
            $table->index(['tipo_venda', 'status'], 'idx_tipo_status');
            $table->index(['origem_venda', 'data_venda'], 'idx_origem_data');

            // Índice único para número da venda por empresa
            $table->unique(['empresa_id', 'numero_venda'], 'unique_empresa_numero_venda');
        });

        // 17. COMENTÁRIO DA TABELA (MySQL only - skip for SQLite)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE vendas COMMENT = 'Vendas do marketplace - Sistema completo de vendas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
