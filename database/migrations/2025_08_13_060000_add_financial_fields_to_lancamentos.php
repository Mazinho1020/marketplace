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
        Schema::table('lancamentos', function (Blueprint $table) {
            // CAMPOS PARA CONTAS A PAGAR E RECEBER

            // Natureza do lançamento
            if (!Schema::hasColumn('lancamentos', 'natureza_financeira')) {
                $table->enum('natureza_financeira', ['pagar', 'receber'])->nullable()->after('tipo_id');
            }

            // Pessoa relacionada (cliente, fornecedor, funcionário)
            if (!Schema::hasColumn('lancamentos', 'pessoa_id')) {
                $table->unsignedBigInteger('pessoa_id')->nullable()->after('funcionario_id');
            }
            if (!Schema::hasColumn('lancamentos', 'pessoa_tipo')) {
                $table->enum('pessoa_tipo', ['cliente', 'fornecedor', 'funcionario'])->nullable()->after('pessoa_id');
            }

            // Documento/Referência
            if (!Schema::hasColumn('lancamentos', 'numero_documento')) {
                $table->string('numero_documento', 100)->nullable()->after('descricao');
            }
            if (!Schema::hasColumn('lancamentos', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('numero_documento');
            }

            // VALORES FINANCEIROS
            if (!Schema::hasColumn('lancamentos', 'valor_original')) {
                $table->decimal('valor_original', 15, 2)->nullable()->after('valor');
            }
            if (!Schema::hasColumn('lancamentos', 'valor_desconto')) {
                $table->decimal('valor_desconto', 15, 2)->default(0)->after('valor_original');
            }
            if (!Schema::hasColumn('lancamentos', 'valor_acrescimo')) {
                $table->decimal('valor_acrescimo', 15, 2)->default(0)->after('valor_desconto');
            }
            if (!Schema::hasColumn('lancamentos', 'valor_juros')) {
                $table->decimal('valor_juros', 15, 2)->default(0)->after('valor_acrescimo');
            }
            if (!Schema::hasColumn('lancamentos', 'valor_multa')) {
                $table->decimal('valor_multa', 15, 2)->default(0)->after('valor_juros');
            }
            if (!Schema::hasColumn('lancamentos', 'valor_final')) {
                $table->decimal('valor_final', 15, 2)->nullable()->after('valor_multa');
            }

            // DATAS
            if (!Schema::hasColumn('lancamentos', 'data_emissao')) {
                $table->date('data_emissao')->nullable()->after('data');
            }
            if (!Schema::hasColumn('lancamentos', 'data_competencia')) {
                $table->date('data_competencia')->nullable()->after('data_emissao');
            }
            if (!Schema::hasColumn('lancamentos', 'data_pagamento')) {
                $table->datetime('data_pagamento')->nullable()->after('data_vencimento');
            }

            // PARCELAMENTO
            if (!Schema::hasColumn('lancamentos', 'parcela_atual')) {
                $table->integer('parcela_atual')->nullable()->after('parcela_referencia');
            }
            if (!Schema::hasColumn('lancamentos', 'total_parcelas')) {
                $table->integer('total_parcelas')->default(1)->after('parcela_atual');
            }
            if (!Schema::hasColumn('lancamentos', 'grupo_parcelas')) {
                $table->string('grupo_parcelas', 36)->nullable()->after('total_parcelas'); // UUID para agrupar parcelas
            }
            if (!Schema::hasColumn('lancamentos', 'intervalo_parcelas')) {
                $table->integer('intervalo_parcelas')->default(30)->after('grupo_parcelas'); // dias entre parcelas
            }

            // SITUAÇÃO FINANCEIRA
            if (!Schema::hasColumn('lancamentos', 'situacao_financeira')) {
                $table->enum('situacao_financeira', ['pendente', 'pago', 'vencido', 'cancelado', 'em_negociacao'])->default('pendente')->after('status');
            }

            // FORMA DE PAGAMENTO/RECEBIMENTO
            if (!Schema::hasColumn('lancamentos', 'forma_pagamento')) {
                $table->string('forma_pagamento', 100)->nullable()->after('situacao_financeira');
            }
            if (!Schema::hasColumn('lancamentos', 'conta_bancaria_id')) {
                $table->unsignedBigInteger('conta_bancaria_id')->nullable()->after('forma_pagamento');
            }

            // RECORRÊNCIA
            if (!Schema::hasColumn('lancamentos', 'e_recorrente')) {
                $table->boolean('e_recorrente')->default(false)->after('conta_bancaria_id');
            }
            if (!Schema::hasColumn('lancamentos', 'frequencia_recorrencia')) {
                $table->enum('frequencia_recorrencia', ['semanal', 'quinzenal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'])->nullable()->after('e_recorrente');
            }
            if (!Schema::hasColumn('lancamentos', 'proxima_recorrencia')) {
                $table->date('proxima_recorrencia')->nullable()->after('frequencia_recorrencia');
            }

            // CONFIGURAÇÕES DE JUROS E MULTA
            if (!Schema::hasColumn('lancamentos', 'juros_multa_config')) {
                $table->json('juros_multa_config')->nullable()->after('proxima_recorrencia');
            }

            // DESCONTO POR ANTECIPAÇÃO
            if (!Schema::hasColumn('lancamentos', 'desconto_antecipacao')) {
                $table->json('desconto_antecipacao')->nullable()->after('juros_multa_config');
            }

            // ALERTAS E COBRANÇA
            if (!Schema::hasColumn('lancamentos', 'config_alertas')) {
                $table->json('config_alertas')->nullable()->after('desconto_antecipacao');
            }

            // ANEXOS
            if (!Schema::hasColumn('lancamentos', 'anexos')) {
                $table->json('anexos')->nullable()->after('config_alertas');
            }

            // CAMPO DE APROVAÇÃO (para futuro workflow)
            if (!Schema::hasColumn('lancamentos', 'status_aprovacao')) {
                $table->enum('status_aprovacao', ['pendente_aprovacao', 'aprovado', 'rejeitado'])->nullable()->after('anexos');
            }
            if (!Schema::hasColumn('lancamentos', 'aprovado_por')) {
                $table->unsignedBigInteger('aprovado_por')->nullable()->after('status_aprovacao');
            }
            if (!Schema::hasColumn('lancamentos', 'data_aprovacao')) {
                $table->datetime('data_aprovacao')->nullable()->after('aprovado_por');
            }

            // ÍNDICES PARA PERFORMANCE
            $table->index(['natureza_financeira', 'situacao_financeira'], 'idx_natureza_situacao');
            $table->index(['pessoa_id', 'pessoa_tipo'], 'idx_pessoa');
            $table->index(['data_vencimento', 'situacao_financeira'], 'idx_vencimento_situacao');
            $table->index(['grupo_parcelas'], 'idx_grupo_parcelas');
            $table->index(['e_recorrente', 'proxima_recorrencia'], 'idx_recorrencia');
            $table->index(['data_emissao', 'data_competencia'], 'idx_datas');
            $table->index(['forma_pagamento'], 'idx_forma_pagamento');
            $table->index(['status_aprovacao'], 'idx_status_aprovacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            // Remover índices primeiro
            $table->dropIndex(['natureza_financeira', 'situacao_financeira']);
            $table->dropIndex(['pessoa_id', 'pessoa_tipo']);
            $table->dropIndex(['data_vencimento', 'situacao_financeira']);
            $table->dropIndex(['grupo_parcelas']);
            $table->dropIndex(['e_recorrente', 'proxima_recorrencia']);
            $table->dropIndex(['data_emissao', 'data_competencia']);
            $table->dropIndex(['forma_pagamento']);
            $table->dropIndex(['status_aprovacao']);

            // Remover colunas
            $table->dropColumn([
                'natureza_financeira',
                'pessoa_id',
                'pessoa_tipo',
                'numero_documento',
                'observacoes',
                'valor_original',
                'valor_desconto',
                'valor_acrescimo',
                'valor_juros',
                'valor_multa',
                'valor_final',
                'data_emissao',
                'data_competencia',
                'data_pagamento',
                'parcela_atual',
                'total_parcelas',
                'grupo_parcelas',
                'intervalo_parcelas',
                'situacao_financeira',
                'forma_pagamento',
                'conta_bancaria_id',
                'e_recorrente',
                'frequencia_recorrencia',
                'proxima_recorrencia',
                'juros_multa_config',
                'desconto_antecipacao',
                'config_alertas',
                'anexos',
                'status_aprovacao',
                'aprovado_por',
                'data_aprovacao'
            ]);
        });
    }
};
