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
            // Campos específicos para gestão de vendas
            $table->string('numero_venda', 50)->unique()->nullable()->after('uuid');
            $table->enum('canal_venda', ['pdv', 'online', 'delivery', 'telefone', 'whatsapp', 'presencial'])
                  ->default('pdv')->after('origem');
            
            // Datas de entrega
            $table->datetime('data_entrega_prevista')->nullable()->after('data_competencia');
            $table->datetime('data_entrega_realizada')->nullable()->after('data_entrega_prevista');
            
            // Integração com fidelidade
            $table->unsignedBigInteger('cupom_fidelidade_id')->nullable()->after('metadados');
            $table->integer('pontos_utilizados')->default(0)->after('cupom_fidelidade_id');
            $table->integer('pontos_gerados')->default(0)->after('pontos_utilizados');
            $table->decimal('cashback_aplicado', 10, 2)->default(0.00)->after('pontos_gerados');
            $table->decimal('cashback_gerado', 10, 2)->default(0.00)->after('cashback_aplicado');
            
            // Dados de transporte e entrega
            $table->string('transportadora', 100)->nullable()->after('cashback_gerado');
            $table->string('codigo_rastreamento', 100)->nullable()->after('transportadora');
            $table->enum('tipo_entrega', ['balcao', 'entrega', 'correios', 'transportadora'])
                  ->default('balcao')->after('codigo_rastreamento');
            
            // Prioridade do pedido
            $table->enum('prioridade', ['baixa', 'normal', 'alta', 'urgente'])
                  ->default('normal')->after('tipo_entrega');
            
            // Chaves estrangeiras
            $table->foreign('cupom_fidelidade_id')
                  ->references('id')
                  ->on('fidelidade_cupons')
                  ->onDelete('set null');
                  
            // Índices para performance
            $table->index(['numero_venda'], 'idx_numero_venda');
            $table->index(['canal_venda', 'empresa_id'], 'idx_canal_empresa');
            $table->index(['data_entrega_prevista'], 'idx_entrega_prevista');
            $table->index(['tipo_entrega', 'empresa_id'], 'idx_tipo_entrega_empresa');
            $table->index(['prioridade', 'situacao_financeira'], 'idx_prioridade_situacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex('idx_numero_venda');
            $table->dropIndex('idx_canal_empresa');
            $table->dropIndex('idx_entrega_prevista');
            $table->dropIndex('idx_tipo_entrega_empresa');
            $table->dropIndex('idx_prioridade_situacao');
            
            // Remover chave estrangeira
            $table->dropForeign(['cupom_fidelidade_id']);
            
            // Remover campos
            $table->dropColumn([
                'numero_venda',
                'canal_venda',
                'data_entrega_prevista',
                'data_entrega_realizada',
                'cupom_fidelidade_id',
                'pontos_utilizados',
                'pontos_gerados',
                'cashback_aplicado',
                'cashback_gerado',
                'transportadora',
                'codigo_rastreamento',
                'tipo_entrega',
                'prioridade'
            ]);
        });
    }
};