<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar nova tabela unificada
        Schema::create('lancamentos_unificados', function (Blueprint $table) {
            // Identificação
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('usuario_id');
            
            // Pessoa/Entidade
            $table->unsignedBigInteger('pessoa_id')->nullable();
            $table->enum('pessoa_tipo', ['cliente', 'fornecedor', 'funcionario', 'empresa'])->nullable();
            $table->unsignedInteger('funcionario_id')->nullable();
            
            // Classificação
            $table->unsignedInteger('conta_gerencial_id')->nullable();
            $table->unsignedInteger('categoria_id')->nullable();
            $table->enum('natureza_financeira', ['entrada', 'saida'])->comment('entrada=receber, saida=pagar');
            $table->enum('categoria_operacao', ['venda', 'compra', 'servico', 'taxa', 'imposto', 'transferencia', 'ajuste', 'outros'])->default('outros');
            $table->enum('origem', ['pdv', 'manual', 'delivery', 'api', 'importacao', 'recorrencia'])->default('manual');
            
            // Valores financeiros
            $table->decimal('valor_bruto', 15, 4);
            $table->decimal('valor_desconto', 15, 4)->default(0);
            $table->decimal('valor_acrescimo', 15, 4)->default(0);
            $table->decimal('valor_juros', 15, 4)->default(0);
            $table->decimal('valor_multa', 15, 4)->default(0);
            $table->decimal('valor_liquido', 15, 4)->storedAs('valor_bruto - valor_desconto + valor_acrescimo + valor_juros + valor_multa');
            $table->decimal('valor_pago', 15, 4)->default(0);
            $table->decimal('valor_saldo', 15, 4)->storedAs('valor_bruto - valor_desconto + valor_acrescimo + valor_juros + valor_multa - valor_pago');
            
            // Situação
            $table->enum('situacao_financeira', ['pendente', 'pago', 'parcialmente_pago', 'vencido', 'cancelado', 'em_negociacao', 'estornado'])->default('pendente');
            
            // Datas
            $table->timestamp('data_lancamento')->useCurrent();
            $table->date('data_emissao');
            $table->date('data_competencia');
            $table->date('data_vencimento');
            $table->datetime('data_pagamento')->nullable();
            
            // Descrições
            $table->string('descricao', 500);
            $table->string('numero_documento', 100)->nullable();
            $table->text('observacoes')->nullable();
            
            // Parcelamento
            $table->boolean('e_parcelado')->default(false);
            $table->unsignedSmallInteger('parcela_atual')->nullable();
            $table->unsignedSmallInteger('total_parcelas')->default(1);
            $table->uuid('grupo_parcelas')->nullable();
            
            // Recorrência
            $table->boolean('e_recorrente')->default(false);
            $table->enum('frequencia_recorrencia', ['diaria', 'semanal', 'quinzenal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'])->nullable();
            $table->date('proxima_recorrencia')->nullable();
            
            // Configurações JSON
            $table->json('config_juros_multa')->nullable();
            $table->json('config_desconto')->nullable();
            $table->json('metadados')->nullable();
            
            // Controle
            $table->enum('sync_status', ['pendente', 'sincronizado', 'erro'])->default('pendente');
            $table->string('sync_hash', 64)->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['empresa_id', 'natureza_financeira']);
            $table->index(['empresa_id', 'situacao_financeira']);
            $table->index(['data_vencimento', 'situacao_financeira']);
            $table->index(['pessoa_id', 'pessoa_tipo']);
            $table->index(['grupo_parcelas']);
        });

        // 2. Tabela de movimentações (pagamentos/recebimentos)
        Schema::create('lancamento_movimentacoes_unificadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lancamento_id')->constrained('lancamentos_unificados')->onDelete('cascade');
            $table->enum('tipo', ['pagamento', 'recebimento', 'estorno']);
            $table->decimal('valor', 15, 4);
            $table->datetime('data_movimentacao');
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->unsignedBigInteger('conta_bancaria_id')->nullable();
            $table->string('numero_documento', 100)->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('empresa_id');
            $table->timestamps();
            
            $table->index(['lancamento_id']);
            $table->index(['data_movimentacao']);
        });

        // 3. Tabela de itens (para vendas futuras)
        Schema::create('lancamento_itens_unificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lancamento_id')->constrained('lancamentos_unificados')->onDelete('cascade');
            $table->unsignedInteger('produto_id')->nullable();
            $table->string('codigo_produto', 50)->nullable();
            $table->string('nome_produto');
            $table->decimal('quantidade', 10, 4);
            $table->decimal('valor_unitario', 15, 4);
            $table->decimal('valor_desconto_item', 15, 4)->default(0);
            $table->decimal('valor_total', 15, 4)->storedAs('(quantidade * valor_unitario) - valor_desconto_item');
            $table->text('observacoes')->nullable();
            $table->json('metadados')->nullable();
            $table->unsignedInteger('empresa_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamento_itens_unificados');
        Schema::dropIfExists('lancamento_movimentacoes_unificadas');
        Schema::dropIfExists('lancamentos_unificados');
    }
};