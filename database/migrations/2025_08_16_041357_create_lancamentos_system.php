<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Sistema completo de lançamentos financeiros
     * - Unificado para contas a pagar e receber
     * - Suporte a parcelamento e recorrência
     * - Workflow de aprovação
     * - Auditoria completa
     */
    public function up(): void
    {
        // Tabela principal de lançamentos
        Schema::create('lancamentos', function (Blueprint $table) {
            // Identificação principal
            $table->id();
            $table->uuid('uuid')->unique()->comment('UUID único para identificação externa');
            
            // Relacionamentos principais
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('mesa_id')->nullable();
            $table->unsignedInteger('caixa_id')->nullable();
            
            // Identificação da pessoa/entidade
            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->enum('pessoa_tipo', ['cliente', 'fornecedor', 'funcionario', 'empresa'])->nullable();
            $table->unsignedBigInteger('funcionario_id')->nullable();
            
            // Classificação do lançamento
            $table->unsignedInteger('tipo_lancamento_id')->nullable();
            $table->unsignedInteger('conta_gerencial_id')->nullable();
            $table->enum('natureza_financeira', ['entrada', 'saida'])->comment('entrada=receber, saida=pagar');
            $table->enum('categoria', ['venda', 'compra', 'servico', 'taxa', 'imposto', 'transferencia', 'ajuste', 'outros'])->default('outros');
            $table->enum('origem', ['pdv', 'manual', 'delivery', 'api', 'importacao', 'recorrencia'])->default('manual');
            
            // Informações financeiras principais
            $table->decimal('valor_bruto', 15, 4)->comment('Valor original sem descontos/acréscimos');
            $table->decimal('valor_desconto', 15, 4)->default(0);
            $table->decimal('valor_acrescimo', 15, 4)->default(0);
            $table->decimal('valor_juros', 15, 4)->default(0);
            $table->decimal('valor_multa', 15, 4)->default(0);
            // Campo calculado removido para compatibilidade com SQLite
            $table->decimal('valor_liquido', 15, 4)->default(0);
            
            // Controle de pagamentos
            $table->decimal('valor_pago', 15, 4)->default(0);
            $table->decimal('valor_saldo', 15, 4)->default(0);
            $table->enum('situacao_financeira', ['pendente', 'pago', 'parcialmente_pago', 'vencido', 'cancelado', 'em_negociacao', 'estornado'])->default('pendente');
            
            // Datas importantes
            $table->timestamp('data_lancamento')->useCurrent();
            $table->date('data_emissao');
            $table->date('data_competencia');
            $table->date('data_vencimento');
            $table->datetime('data_pagamento')->nullable();
            $table->datetime('data_ultimo_pagamento')->nullable();
            
            // Informações descritivas
            $table->string('descricao', 500);
            $table->string('numero_documento', 100)->nullable();
            $table->text('observacoes')->nullable();
            $table->text('observacoes_pagamento')->nullable();
            
            // Controle de parcelamento
            $table->boolean('e_parcelado')->default(false);
            $table->unsignedSmallInteger('parcela_atual')->nullable();
            $table->unsignedSmallInteger('total_parcelas')->default(1);
            $table->uuid('grupo_parcelas')->nullable()->comment('UUID do grupo de parcelas');
            $table->unsignedSmallInteger('intervalo_dias')->default(30);
            
            // Recorrência
            $table->boolean('e_recorrente')->default(false);
            $table->enum('frequencia_recorrencia', ['diaria', 'semanal', 'quinzenal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'])->nullable();
            $table->date('proxima_recorrencia')->nullable();
            $table->boolean('recorrencia_ativa')->default(true);
            
            // Forma de pagamento
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->unsignedBigInteger('bandeira_id')->nullable();
            $table->unsignedBigInteger('conta_bancaria_id')->nullable();
            
            // Cobrança automática e boletos
            $table->boolean('cobranca_automatica')->default(false);
            $table->date('data_proxima_cobranca')->nullable();
            $table->unsignedSmallInteger('tentativas_cobranca')->default(0);
            $table->unsignedSmallInteger('max_tentativas_cobranca')->default(3);
            
            // Boleto
            $table->boolean('boleto_gerado')->default(false);
            $table->string('boleto_nosso_numero', 50)->nullable();
            $table->datetime('boleto_data_geracao')->nullable();
            $table->text('boleto_url')->nullable();
            $table->string('boleto_linha_digitavel', 54)->nullable();
            
            // Aprovação e workflow
            $table->enum('status_aprovacao', ['pendente_aprovacao', 'aprovado', 'rejeitado', 'nao_requer'])->default('nao_requer');
            $table->unsignedBigInteger('aprovado_por')->nullable();
            $table->datetime('data_aprovacao')->nullable();
            $table->text('motivo_rejeicao')->nullable();
            
            // Configurações JSON otimizadas
            $table->json('config_juros_multa')->nullable()->comment('Configurações de juros e multa');
            $table->json('config_desconto')->nullable()->comment('Configurações de desconto por antecipação');
            $table->json('config_alertas')->nullable()->comment('Configurações de alertas');
            $table->json('anexos')->nullable()->comment('URLs e metadados de anexos');
            $table->json('metadados')->nullable()->comment('Dados específicos por módulo');
            
            // Controle de sincronização
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro', 'processando'])->default('pendente');
            $table->unsignedSmallInteger('sync_tentativas')->default(0);
            $table->text('sync_ultimo_erro')->nullable();
            $table->string('sync_hash', 64)->nullable();
            
            // Auditoria
            $table->unsignedInteger('usuario_criacao');
            $table->unsignedInteger('usuario_ultima_alteracao')->nullable();
            $table->datetime('data_exclusao')->nullable();
            $table->unsignedInteger('usuario_exclusao')->nullable();
            $table->string('motivo_exclusao', 500)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices principais para performance
            $table->index(['empresa_id', 'situacao_financeira'], 'idx_empresa_situacao');
            $table->index(['empresa_id', 'natureza_financeira', 'situacao_financeira'], 'idx_empresa_natureza_situacao');
            $table->index(['data_vencimento', 'situacao_financeira'], 'idx_vencimento_situacao');
            $table->index(['pessoa_id', 'pessoa_tipo'], 'idx_pessoa_tipo');
            $table->index(['grupo_parcelas'], 'idx_grupo_parcelas');
            $table->index(['e_recorrente', 'recorrencia_ativa', 'proxima_recorrencia'], 'idx_recorrencia');
            $table->index(['cobranca_automatica', 'data_proxima_cobranca'], 'idx_cobranca_automatica');
            $table->index(['status_aprovacao'], 'idx_status_aprovacao');
            $table->index(['sync_status'], 'idx_sync_status');
            $table->index(['data_competencia', 'empresa_id'], 'idx_competencia_empresa');
            $table->index(['categoria', 'origem'], 'idx_categoria_origem');
            $table->index(['data_exclusao'], 'idx_data_exclusao');
        });

        // Tabela de itens dos lançamentos
        Schema::create('lancamento_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lancamento_id')->constrained('lancamentos')->onDelete('cascade');
            $table->unsignedInteger('produto_id')->nullable();
            $table->unsignedInteger('produto_variacao_id')->nullable();
            $table->string('codigo_produto', 50)->nullable();
            $table->string('nome_produto', 255);
            $table->decimal('quantidade', 10, 4);
            $table->decimal('valor_unitario', 15, 4);
            $table->decimal('valor_desconto_item', 15, 4)->default(0);
            $table->decimal('valor_total', 15, 4)->default(0);
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            $table->unsignedInteger('empresa_id');
            $table->timestamps();
            
            $table->index(['lancamento_id'], 'idx_lancamento_itens');
            $table->index(['produto_id'], 'idx_produto_item');
            $table->index(['empresa_id'], 'idx_empresa_item');
        });

        // Tabela de movimentações removida - usando tabela 'pagamentos' existente
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('lancamento_movimentacoes'); // Tabela removida - usando 'pagamentos'
        Schema::dropIfExists('lancamento_itens');
        Schema::dropIfExists('lancamentos');
    }
};
