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
     * Migration de limpeza da tabela lancamentos conforme especificado:
     * - Remover campos desnecessários (forma_pagamento, data_pagamento)
     * - Ajustar campos existentes  
     * - Garantir consistência dos enums
     */
    public function up(): void
    {
        // Verificar se a tabela existe
        if (!Schema::hasTable('lancamentos')) {
            return;
        }

        Schema::table('lancamentos', function (Blueprint $table) {
            
            // ===== REMOVER ÍNDICES PRIMEIRO =====
            
            // Remover índices que dependem dos campos que serão removidos
            try {
                $table->dropIndex('idx_forma_pagamento');
            } catch (\Exception $e) {
                // Ignorar se não existir
            }
            
            // ===== REMOVER CAMPOS DESNECESSÁRIOS =====
            
            // Remover forma_pagamento (redundante com tabela pagamentos)
            if (Schema::hasColumn('lancamentos', 'forma_pagamento')) {
                $table->dropColumn('forma_pagamento');
            }
            
            // Remover data_pagamento (calculado pela última data dos pagamentos)
            // Este campo será calculado dinamicamente através dos pagamentos
            if (Schema::hasColumn('lancamentos', 'data_pagamento')) {
                $table->dropColumn('data_pagamento');
            }

            // ===== ÍNDICES PARA PERFORMANCE =====
            
            // Adicionar índices se não existirem
            if (!$this->indexExists('lancamentos', 'idx_empresa_natureza')) {
                $table->index(['empresa_id', 'natureza_financeira'], 'idx_empresa_natureza');
            }
            
            if (!$this->indexExists('lancamentos', 'idx_situacao_vencimento')) {
                $table->index(['situacao_financeira', 'data_vencimento'], 'idx_situacao_vencimento');
            }
        });

        // ===== LIMPEZA DE DADOS =====
        $this->limparDadosInconsistentes();
        
        // ===== ATUALIZAR SITUAÇÕES BASEADO NOS PAGAMENTOS =====
        $this->atualizarSituacoesFinanceiras();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamentos', function (Blueprint $table) {
            
            // Restaurar campos removidos
            if (!Schema::hasColumn('lancamentos', 'forma_pagamento')) {
                $table->string('forma_pagamento', 100)->nullable();
            }
            
            if (!Schema::hasColumn('lancamentos', 'data_pagamento')) {
                $table->datetime('data_pagamento')->nullable();
            }

            // Remover índices adicionados
            if ($this->indexExists('lancamentos', 'idx_empresa_natureza')) {
                $table->dropIndex('idx_empresa_natureza');
            }
            
            if ($this->indexExists('lancamentos', 'idx_situacao_vencimento')) {
                $table->dropIndex('idx_situacao_vencimento');
            }
        });
    }

    /**
     * Verificar se um índice existe (versão simplificada para SQLite)
     */
    private function indexExists(string $table, string $index): bool
    {
        // Para SQLite, vamos assumir que não existe e tentar criar
        // Se já existir, o comando falhará silenciosamente
        return false;
    }

    /**
     * Limpar dados inconsistentes
     */
    private function limparDadosInconsistentes(): void
    {
        try {
            // Remover lançamentos sem empresa_id
            DB::statement("DELETE FROM lancamentos WHERE empresa_id IS NULL");
            
            // Corrigir valores nulos para zero
            DB::statement("UPDATE lancamentos SET valor_desconto = 0 WHERE valor_desconto IS NULL");
            DB::statement("UPDATE lancamentos SET valor_acrescimo = 0 WHERE valor_acrescimo IS NULL");
            DB::statement("UPDATE lancamentos SET valor_juros = 0 WHERE valor_juros IS NULL");
            DB::statement("UPDATE lancamentos SET valor_multa = 0 WHERE valor_multa IS NULL");
            
            // Calcular valor_final se estiver nulo
            DB::statement("
                UPDATE lancamentos 
                SET valor_final = COALESCE(valor_original, valor) + 
                                  COALESCE(valor_acrescimo, 0) + 
                                  COALESCE(valor_juros, 0) + 
                                  COALESCE(valor_multa, 0) - 
                                  COALESCE(valor_desconto, 0)
                WHERE valor_final IS NULL
            ");

        } catch (\Exception $e) {
            // Log do erro mas continuar a migração
            \Log::error('Erro na limpeza de dados da migração: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar situações financeiras baseado nos pagamentos existentes
     */
    private function atualizarSituacoesFinanceiras(): void
    {
        try {
            // Se a tabela pagamentos existir, atualizar situações
            if (Schema::hasTable('pagamentos')) {
                // Marcar como pago lançamentos que têm pagamentos confirmados >= valor_final
                DB::statement("
                    UPDATE lancamentos l
                    SET situacao_financeira = 'pago'
                    WHERE l.id IN (
                        SELECT l2.id 
                        FROM lancamentos l2
                        LEFT JOIN (
                            SELECT lancamento_id, SUM(valor) as total_pago
                            FROM pagamentos 
                            WHERE status = 'confirmado'
                            GROUP BY lancamento_id
                        ) p ON l2.id = p.lancamento_id
                        WHERE COALESCE(p.total_pago, 0) >= l2.valor_final
                    )
                ");
                
                // Marcar como vencido lançamentos pendentes com data_vencimento < hoje
                DB::statement("
                    UPDATE lancamentos 
                    SET situacao_financeira = 'vencido'
                    WHERE situacao_financeira = 'pendente' 
                    AND data_vencimento < CURRENT_DATE
                ");
            }

        } catch (\Exception $e) {
            // Log do erro mas continuar a migração
            \Log::error('Erro na atualização de situações financeiras: ' . $e->getMessage());
        }
    }
};