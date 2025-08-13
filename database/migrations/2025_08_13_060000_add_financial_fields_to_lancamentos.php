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
            $table->enum('natureza_financeira', ['pagar', 'receber'])->nullable()->after('tipo_id');

            // Pessoa relacionada (cliente, fornecedor, funcionário)
            $table->unsignedBigInteger('pessoa_id')->nullable()->after('funcionario_id');
            $table->enum('pessoa_tipo', ['cliente', 'fornecedor', 'funcionario'])->nullable()->after('pessoa_id');

            // Documento/Referência
            $table->string('numero_documento', 100)->nullable()->after('descricao');
            $table->text('observacoes')->nullable()->after('numero_documento');

            // VALORES FINANCEIROS
            $table->decimal('valor_original', 15, 2)->nullable()->after('valor');
            $table->decimal('valor_desconto', 15, 2)->default(0)->after('valor_original');
            $table->decimal('valor_acrescimo', 15, 2)->default(0)->after('valor_desconto');
            $table->decimal('valor_juros', 15, 2)->default(0)->after('valor_acrescimo');
            $table->decimal('valor_multa', 15, 2)->default(0)->after('valor_juros');
            $table->decimal('valor_final', 15, 2)->nullable()->after('valor_multa');

            // DATAS
            $table->date('data_emissao')->nullable()->after('data');
            $table->date('data_competencia')->nullable()->after('data_emissao');
            $table->datetime('data_pagamento')->nullable()->after('data_vencimento');

            // PARCELAMENTO
            $table->integer('parcela_atual')->nullable()->after('parcela_referencia');
            $table->integer('total_parcelas')->default(1)->after('parcela_atual');
            $table->string('grupo_parcelas', 36)->nullable()->after('total_parcelas'); // UUID para agrupar parcelas
            $table->integer('intervalo_parcelas')->default(30)->after('grupo_parcelas'); // dias entre parcelas

            // SITUAÇÃO FINANCEIRA
            $table->enum('situacao_financeira', ['pendente', 'pago', 'vencido', 'cancelado', 'em_negociacao'])->default('pendente')->after('status');

            // FORMA DE PAGAMENTO/RECEBIMENTO
            $table->string('forma_pagamento', 100)->nullable()->after('situacao_financeira');
            $table->unsignedBigInteger('conta_bancaria_id')->nullable()->after('forma_pagamento');

            // RECORRÊNCIA
            $table->boolean('e_recorrente')->default(false)->after('conta_bancaria_id');
            $table->enum('frequencia_recorrencia', ['semanal', 'quinzenal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'])->nullable()->after('e_recorrente');
            $table->date('proxima_recorrencia')->nullable()->after('frequencia_recorrencia');

            // CONFIGURAÇÕES DE JUROS E MULTA
            $table->json('juros_multa_config')->nullable()->after('proxima_recorrencia');

            // DESCONTO POR ANTECIPAÇÃO
            $table->json('desconto_antecipacao')->nullable()->after('juros_multa_config');

            // ALERTAS E COBRANÇA
            $table->json('config_alertas')->nullable()->after('desconto_antecipacao');

            // ANEXOS
            $table->json('anexos')->nullable()->after('config_alertas');

            // CAMPO DE APROVAÇÃO (para futuro workflow)
            $table->enum('status_aprovacao', ['pendente_aprovacao', 'aprovado', 'rejeitado'])->nullable()->after('anexos');
            $table->unsignedBigInteger('aprovado_por')->nullable()->after('status_aprovacao');
            $table->datetime('data_aprovacao')->nullable()->after('aprovado_por');

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
