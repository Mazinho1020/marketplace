<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ADICIONANDO COLUNAS FALTANTES ===\n\n";

    // 1. Adicionar colunas na tabela afi_plan_transacoes
    echo "1. Adicionando colunas na tabela afi_plan_transacoes...\n";

    $transacoesColumns = [
        'gateway_id' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN gateway_id int(11) DEFAULT NULL AFTER empresa_id',
        'valor_final' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN valor_final decimal(10,2) DEFAULT NULL AFTER gateway_id',
        'status' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN status enum("pendente","processando","aprovado","recusado","cancelado") DEFAULT "pendente" AFTER valor_final',
        'cliente_email' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN cliente_email varchar(255) DEFAULT NULL AFTER status',
        'cliente_nome' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN cliente_nome varchar(255) DEFAULT NULL AFTER cliente_email',
        'forma_pagamento' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN forma_pagamento varchar(50) DEFAULT NULL AFTER cliente_nome',
        'updated_at' => 'ALTER TABLE afi_plan_transacoes ADD COLUMN updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    ];

    foreach ($transacoesColumns as $column => $sql) {
        try {
            // Verificar se a coluna já existe
            $exists = DB::select("SHOW COLUMNS FROM afi_plan_transacoes LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 2. Adicionar colunas na tabela afi_plan_gateways
    echo "\n2. Adicionando colunas na tabela afi_plan_gateways...\n";

    $gatewaysColumns = [
        'provedor' => 'ALTER TABLE afi_plan_gateways ADD COLUMN provedor varchar(50) DEFAULT NULL AFTER nome',
        'url_webhook' => 'ALTER TABLE afi_plan_gateways ADD COLUMN url_webhook varchar(255) DEFAULT NULL AFTER provedor',
        'configuracao' => 'ALTER TABLE afi_plan_gateways ADD COLUMN configuracao text DEFAULT NULL AFTER url_webhook',
        'updated_at' => 'ALTER TABLE afi_plan_gateways ADD COLUMN updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    ];

    foreach ($gatewaysColumns as $column => $sql) {
        try {
            // Verificar se a coluna já existe
            $exists = DB::select("SHOW COLUMNS FROM afi_plan_gateways LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 3. Adicionar índices para melhor performance
    echo "\n3. Adicionando índices...\n";

    $indexes = [
        'afi_plan_transacoes' => [
            'idx_gateway_id' => 'ALTER TABLE afi_plan_transacoes ADD INDEX idx_gateway_id (gateway_id)',
            'idx_status' => 'ALTER TABLE afi_plan_transacoes ADD INDEX idx_status (status)',
            'idx_cliente_email' => 'ALTER TABLE afi_plan_transacoes ADD INDEX idx_cliente_email (cliente_email)'
        ],
        'afi_plan_gateways' => [
            'idx_codigo' => 'ALTER TABLE afi_plan_gateways ADD INDEX idx_codigo (codigo)',
            'idx_ativo' => 'ALTER TABLE afi_plan_gateways ADD INDEX idx_ativo (ativo)'
        ]
    ];

    foreach ($indexes as $table => $tableIndexes) {
        foreach ($tableIndexes as $indexName => $sql) {
            try {
                // Verificar se o índice já existe
                $exists = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
                if (empty($exists)) {
                    DB::statement($sql);
                    echo "  ✅ Índice {$indexName} adicionado em {$table}\n";
                } else {
                    echo "  ℹ️  Índice {$indexName} já existe em {$table}\n";
                }
            } catch (Exception $e) {
                echo "  ❌ Erro ao adicionar índice {$indexName}: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n=== VERIFICAÇÃO FINAL ===\n";

    // Verificar estruturas finais
    echo "\nEstrutura final afi_plan_transacoes:\n";
    $finalTransacoes = DB::select('DESCRIBE afi_plan_transacoes');
    foreach ($finalTransacoes as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }

    echo "\nEstrutura final afi_plan_gateways:\n";
    $finalGateways = DB::select('DESCRIBE afi_plan_gateways');
    foreach ($finalGateways as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }

    echo "\n🎉 Atualização das tabelas concluída!\n";
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
