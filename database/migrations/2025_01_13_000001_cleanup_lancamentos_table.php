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
            // Remover campos que serão movidos para a tabela pagamentos
            if (Schema::hasColumn('lancamentos', 'forma_pagamento')) {
                $table->dropColumn('forma_pagamento');
            }

            // data_pagamento será calculada dinamicamente da tabela pagamentos
            // Mantemos o campo para compatibilidade, mas será usado apenas para consulta

            // Adicionar campos necessários para o novo sistema
            if (!Schema::hasColumn('lancamentos', 'valor_pago')) {
                $table->decimal('valor_pago', 15, 2)->default(0)->after('valor_final');
            }

            if (!Schema::hasColumn('lancamentos', 'valor_saldo')) {
                $table->decimal('valor_saldo', 15, 2)->default(0)->after('valor_pago');
            }

            // Campos para controle de pagamentos
            if (!Schema::hasColumn('lancamentos', 'numero_pagamentos')) {
                $table->integer('numero_pagamentos')->default(0)->after('valor_saldo');
            }

            if (!Schema::hasColumn('lancamentos', 'data_ultimo_pagamento')) {
                $table->datetime('data_ultimo_pagamento')->nullable()->after('numero_pagamentos');
            }

            if (!Schema::hasColumn('lancamentos', 'usuario_ultimo_pagamento_id')) {
                $table->bigInteger('usuario_ultimo_pagamento_id')->nullable()->after('data_ultimo_pagamento');
            }

            // Campos para cobrança automática
            if (!Schema::hasColumn('lancamentos', 'cobranca_automatica')) {
                $table->boolean('cobranca_automatica')->default(false)->after('e_recorrente');
            }

            if (!Schema::hasColumn('lancamentos', 'data_proxima_cobranca')) {
                $table->date('data_proxima_cobranca')->nullable()->after('cobranca_automatica');
            }

            if (!Schema::hasColumn('lancamentos', 'tentativas_cobranca')) {
                $table->integer('tentativas_cobranca')->default(0)->after('data_proxima_cobranca');
            }

            // Campos para boleto
            if (!Schema::hasColumn('lancamentos', 'boleto_gerado')) {
                $table->boolean('boleto_gerado')->default(false)->after('tentativas_cobranca');
            }

            if (!Schema::hasColumn('lancamentos', 'boleto_nosso_numero')) {
                $table->string('boleto_nosso_numero', 50)->nullable()->after('boleto_gerado');
            }

            if (!Schema::hasColumn('lancamentos', 'boleto_data_geracao')) {
                $table->datetime('boleto_data_geracao')->nullable()->after('boleto_nosso_numero');
            }

            if (!Schema::hasColumn('lancamentos', 'boleto_url')) {
                $table->text('boleto_url')->nullable()->after('boleto_data_geracao');
            }
        });

        // Adicionar índices para performance
        Schema::table('lancamentos', function (Blueprint $table) {
            $table->index(['empresa_id', 'situacao_financeira'], 'idx_empresa_situacao');
            $table->index(['empresa_id', 'data_vencimento'], 'idx_empresa_vencimento');
            $table->index(['empresa_id', 'natureza_financeira', 'situacao_financeira'], 'idx_empresa_natureza_situacao');
            $table->index(['cobranca_automatica', 'data_proxima_cobranca'], 'idx_cobranca_automatica');
            $table->index(['boleto_gerado', 'boleto_nosso_numero'], 'idx_boleto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex('idx_empresa_situacao');
            $table->dropIndex('idx_empresa_vencimento');
            $table->dropIndex('idx_empresa_natureza_situacao');
            $table->dropIndex('idx_cobranca_automatica');
            $table->dropIndex('idx_boleto');

            // Remover campos adicionados
            $table->dropColumn([
                'valor_pago',
                'valor_saldo',
                'numero_pagamentos',
                'data_ultimo_pagamento',
                'usuario_ultimo_pagamento_id',
                'cobranca_automatica',
                'data_proxima_cobranca',
                'tentativas_cobranca',
                'boleto_gerado',
                'boleto_nosso_numero',
                'boleto_data_geracao',
                'boleto_url'
            ]);

            // Restaurar campo forma_pagamento
            $table->string('forma_pagamento', 100)->nullable()->after('situacao_financeira');
        });
    }
};
