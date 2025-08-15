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
        Schema::table('pagamentos', function (Blueprint $table) {
            // Garantir que lancamento_id tenha constraint de foreign key
            if (!Schema::hasColumn('pagamentos', 'foreign_keys')) {
                $table->foreign('lancamento_id')->references('id')->on('lancamentos')->onDelete('cascade');
            }

            // Adicionar campos para melhor controle
            if (!Schema::hasColumn('pagamentos', 'numero_parcela_pagamento')) {
                $table->integer('numero_parcela_pagamento')->default(1)->after('lancamento_id');
            }

            if (!Schema::hasColumn('pagamentos', 'valor_principal')) {
                $table->decimal('valor_principal', 15, 2)->default(0)->after('valor');
            }

            if (!Schema::hasColumn('pagamentos', 'valor_juros')) {
                $table->decimal('valor_juros', 15, 2)->default(0)->after('valor_principal');
            }

            if (!Schema::hasColumn('pagamentos', 'valor_multa')) {
                $table->decimal('valor_multa', 15, 2)->default(0)->after('valor_juros');
            }

            if (!Schema::hasColumn('pagamentos', 'valor_desconto')) {
                $table->decimal('valor_desconto', 15, 2)->default(0)->after('valor_multa');
            }

            if (!Schema::hasColumn('pagamentos', 'data_compensacao')) {
                $table->date('data_compensacao')->nullable()->after('data_pagamento');
            }

            if (!Schema::hasColumn('pagamentos', 'comprovante_pagamento')) {
                $table->text('comprovante_pagamento')->nullable()->after('observacao');
            }

            if (!Schema::hasColumn('pagamentos', 'status_pagamento')) {
                $table->enum('status_pagamento', ['processando', 'confirmado', 'cancelado', 'estornado'])
                    ->default('confirmado')->after('comprovante_pagamento');
            }

            if (!Schema::hasColumn('pagamentos', 'referencia_externa')) {
                $table->string('referencia_externa', 100)->nullable()->after('status_pagamento');
            }
        });

        // Adicionar índices para performance
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->index(['lancamento_id', 'data_pagamento'], 'idx_lancamento_data');
            $table->index(['empresa_id', 'data_pagamento'], 'idx_empresa_data_pagamento');
            $table->index(['forma_pagamento_id', 'data_pagamento'], 'idx_forma_data');
            $table->index(['status_pagamento'], 'idx_status_pagamento');
            $table->index(['referencia_externa'], 'idx_referencia_externa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex('idx_lancamento_data');
            $table->dropIndex('idx_empresa_data_pagamento');
            $table->dropIndex('idx_forma_data');
            $table->dropIndex('idx_status_pagamento');
            $table->dropIndex('idx_referencia_externa');

            // Remover foreign key
            $table->dropForeign(['lancamento_id']);

            // Remover campos adicionados
            $table->dropColumn([
                'numero_parcela_pagamento',
                'valor_principal',
                'valor_juros',
                'valor_multa',
                'valor_desconto',
                'data_compensacao',
                'comprovante_pagamento',
                'status_pagamento',
                'referencia_externa'
            ]);
        });
    }
};
