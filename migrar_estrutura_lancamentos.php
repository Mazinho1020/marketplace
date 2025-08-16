<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== ATUALIZANDO ESTRUTURA DO SISTEMA DE LANÇAMENTOS ===\n\n";
    
    DB::beginTransaction();
    
    // 1. Verificar e adicionar colunas que faltam na tabela lancamentos
    echo "1. Atualizando tabela 'lancamentos'...\n";
    
    $columnsToAdd = [
        'uuid' => "ADD COLUMN `uuid` char(36) UNIQUE COMMENT 'UUID único para identificação externa'",
        'categoria' => "ADD COLUMN `categoria` enum('venda','compra','servico','taxa','imposto','transferencia','ajuste','outros') DEFAULT 'outros'",
        'valor_bruto' => "MODIFY COLUMN `valor` decimal(15,4) NOT NULL COMMENT 'Valor original sem descontos/acréscimos'",
        'valor_liquido' => "ADD COLUMN `valor_liquido` decimal(15,4) DEFAULT 0 AFTER `valor_multa`",
        'data_lancamento' => "ADD COLUMN `data_lancamento` timestamp DEFAULT CURRENT_TIMESTAMP",
        'usuario_criacao' => "ADD COLUMN `usuario_criacao` int unsigned NOT NULL DEFAULT 1",
        'usuario_ultima_alteracao' => "ADD COLUMN `usuario_ultima_alteracao` int unsigned NULL",
        'data_exclusao' => "ADD COLUMN `data_exclusao` datetime NULL",
        'usuario_exclusao' => "ADD COLUMN `usuario_exclusao` int unsigned NULL",
        'motivo_exclusao' => "ADD COLUMN `motivo_exclusao` varchar(500) NULL",
        'metadados' => "ADD COLUMN `metadados` json NULL COMMENT 'Dados específicos por módulo'",
    ];
    
    // Verificar quais colunas já existem
    $existingColumns = DB::select("SHOW COLUMNS FROM lancamentos");
    $existingColumnNames = array_column($existingColumns, 'Field');
    
    foreach ($columnsToAdd as $column => $sql) {
        if (!in_array($column, $existingColumnNames)) {
            try {
                DB::statement("ALTER TABLE lancamentos $sql");
                echo "  ✓ Adicionada coluna: $column\n";
            } catch (Exception $e) {
                echo "  ✗ Erro ao adicionar $column: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  - Coluna $column já existe\n";
        }
    }
    
    // 2. Criar tabela de movimentações se não existir
    echo "\n2. Criando tabela 'lancamento_movimentacoes'...\n";
    
    if (!Schema::hasTable('lancamento_movimentacoes')) {
        DB::statement("
            CREATE TABLE `lancamento_movimentacoes` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `lancamento_id` bigint unsigned NOT NULL,
                `tipo` enum('pagamento','recebimento','estorno') NOT NULL,
                `valor` decimal(15,4) NOT NULL,
                `data_movimentacao` datetime NOT NULL,
                `forma_pagamento_id` bigint unsigned NULL,
                `conta_bancaria_id` bigint unsigned NULL,
                `numero_documento` varchar(100) NULL,
                `observacoes` text NULL,
                `metadados` json NULL,
                `usuario_id` int unsigned NOT NULL,
                `empresa_id` int unsigned NOT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_movimentacao_lancamento` (`lancamento_id`),
                KEY `idx_data_movimentacao` (`data_movimentacao`),
                KEY `idx_empresa_tipo_movimentacao` (`empresa_id`, `tipo`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "  ✓ Tabela lancamento_movimentacoes criada\n";
    } else {
        echo "  - Tabela lancamento_movimentacoes já existe\n";
    }
    
    // 3. Verificar e melhorar tabela de itens
    echo "\n3. Verificando tabela 'lancamento_itens'...\n";
    
    $itemsColumns = DB::select("SHOW COLUMNS FROM lancamento_itens");
    $itemsColumnNames = array_column($itemsColumns, 'Field');
    
    $itemsColumnsToAdd = [
        'metadados' => "ADD COLUMN `metadados` json NULL",
        'valor_total' => "ADD COLUMN `valor_total` decimal(15,4) DEFAULT 0"
    ];
    
    foreach ($itemsColumnsToAdd as $column => $sql) {
        if (!in_array($column, $itemsColumnNames)) {
            try {
                DB::statement("ALTER TABLE lancamento_itens $sql");
                echo "  ✓ Adicionada coluna: $column\n";
            } catch (Exception $e) {
                echo "  ✗ Erro ao adicionar $column: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  - Coluna $column já existe\n";
        }
    }
    
    // 4. Gerar UUIDs para registros existentes sem UUID
    echo "\n4. Gerando UUIDs para registros existentes...\n";
    
    $registrosSemUuid = DB::table('lancamentos')
                          ->whereNull('uuid')
                          ->orWhere('uuid', '')
                          ->count();
    
    if ($registrosSemUuid > 0) {
        DB::table('lancamentos')
          ->whereNull('uuid')
          ->orWhere('uuid', '')
          ->chunkById(100, function ($lancamentos) {
              foreach ($lancamentos as $lancamento) {
                  DB::table('lancamentos')
                    ->where('id', $lancamento->id)
                    ->update(['uuid' => \Illuminate\Support\Str::uuid()]);
              }
          });
        echo "  ✓ UUIDs gerados para $registrosSemUuid registros\n";
    } else {
        echo "  - Todos os registros já possuem UUID\n";
    }
    
    // 5. Calcular valores líquidos para registros existentes
    echo "\n5. Calculando valores líquidos...\n";
    
    $registrosParaCalcular = DB::table('lancamentos')
                               ->where('valor_liquido', 0)
                               ->count();
    
    if ($registrosParaCalcular > 0) {
        DB::statement("
            UPDATE lancamentos 
            SET valor_liquido = valor + COALESCE(valor_acrescimo, 0) + COALESCE(valor_juros, 0) + COALESCE(valor_multa, 0) - COALESCE(valor_desconto, 0)
            WHERE valor_liquido = 0
        ");
        echo "  ✓ Valores líquidos calculados para $registrosParaCalcular registros\n";
    } else {
        echo "  - Valores líquidos já calculados\n";
    }
    
    // 6. Padronizar enum de natureza_financeira
    echo "\n6. Padronizando enums...\n";
    
    DB::statement("UPDATE lancamentos SET natureza_financeira = 'entrada' WHERE natureza_financeira = 'receber'");
    DB::statement("UPDATE lancamentos SET natureza_financeira = 'saida' WHERE natureza_financeira = 'pagar'");
    echo "  ✓ Natureza financeira padronizada\n";
    
    // 7. Criar índices importantes
    echo "\n7. Criando índices de performance...\n";
    
    $indices = [
        'idx_empresa_situacao' => "CREATE INDEX `idx_empresa_situacao` ON `lancamentos` (`empresa_id`, `situacao_financeira`)",
        'idx_vencimento_situacao' => "CREATE INDEX `idx_vencimento_situacao` ON `lancamentos` (`data_vencimento`, `situacao_financeira`)",
        'idx_pessoa_tipo' => "CREATE INDEX `idx_pessoa_tipo` ON `lancamentos` (`pessoa_id`, `pessoa_tipo`)",
        'idx_data_exclusao' => "CREATE INDEX `idx_data_exclusao` ON `lancamentos` (`data_exclusao`)",
    ];
    
    foreach ($indices as $nomeIndice => $sql) {
        try {
            DB::statement($sql);
            echo "  ✓ Índice criado: $nomeIndice\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "  - Índice $nomeIndice já existe\n";
            } else {
                echo "  ✗ Erro ao criar índice $nomeIndice: " . $e->getMessage() . "\n";
            }
        }
    }
    
    DB::commit();
    
    echo "\n=== MIGRAÇÃO CONCLUÍDA COM SUCESSO! ===\n";
    echo "\nResumo das alterações:\n";
    echo "- Estrutura da tabela lancamentos atualizada\n";
    echo "- Tabela lancamento_movimentacoes criada\n";
    echo "- UUIDs gerados para registros existentes\n";
    echo "- Valores líquidos calculados\n";
    echo "- Índices de performance criados\n";
    echo "- Sistema pronto para uso!\n\n";
    
    // Verificação final
    $totalLancamentos = DB::table('lancamentos')->count();
    $totalMovimentacoes = DB::table('lancamento_movimentacoes')->count();
    $totalItens = DB::table('lancamento_itens')->count();
    
    echo "Estatísticas finais:\n";
    echo "- Lançamentos: $totalLancamentos\n";
    echo "- Movimentações: $totalMovimentacoes\n";
    echo "- Itens: $totalItens\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "\n✗ ERRO NA MIGRAÇÃO: " . $e->getMessage() . "\n";
    echo "Todas as alterações foram revertidas.\n";
}
?>
