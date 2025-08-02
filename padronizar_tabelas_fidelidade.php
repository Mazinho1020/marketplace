<?php

/**
 * Script para adicionar campos padrão de sincronização nas tabelas de fidelidade
 * Campos adicionados: sync_hash, sync_status, sync_data, created_at, updated_at
 */

try {
    // Configurações do banco
    $host = '127.0.0.1';
    $database = 'meufinanceiro';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Iniciando padronização das tabelas de fidelidade...\n";

    // Array com todas as tabelas e suas alterações
    $tabelas_alteracoes = [
        'fidelidade_carteiras' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `status`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "CHANGE COLUMN `criado_em` `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP",
            "CHANGE COLUMN `atualizado_em` `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ],

        'fidelidade_cashback_regras' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `status`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "CHANGE COLUMN `criado_em` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "CHANGE COLUMN `atualizado_em` `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ],

        'fidelidade_cashback_transacoes' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `observacoes`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        ],

        'fidelidade_cliente_conquistas' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `recompensa_resgatada`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "ADD COLUMN `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sync_data`",
            "ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        ],

        'fidelidade_conquistas' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `ativo`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "CHANGE COLUMN `criado_em` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        ],

        'fidelidade_creditos' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `status`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "CHANGE COLUMN `criado_em` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "CHANGE COLUMN `atualizado_em` `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ],

        'fidelidade_cupons' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `status`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "CHANGE COLUMN `criado_em` `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        ],

        'fidelidade_cupons_uso' => [
            "ADD COLUMN `sync_hash` varchar(255) NULL DEFAULT NULL AFTER `data_uso`",
            "ADD COLUMN `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' AFTER `sync_hash`",
            "ADD COLUMN `sync_data` json NULL DEFAULT NULL AFTER `sync_status`",
            "ADD COLUMN `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sync_data`",
            "ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
        ]
    ];

    // Executar alterações para cada tabela
    foreach ($tabelas_alteracoes as $tabela => $alteracoes) {
        echo "\nProcessando tabela: $tabela\n";

        // Verificar se a tabela existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tabela]);

        if ($stmt->rowCount() == 0) {
            echo "  ⚠️  Tabela $tabela não existe, pulando...\n";
            continue;
        }

        // Executar cada alteração
        foreach ($alteracoes as $alteracao) {
            try {
                $sql = "ALTER TABLE `$tabela` $alteracao";
                $pdo->exec($sql);
                echo "  ✅ $alteracao\n";
            } catch (PDOException $e) {
                // Se o erro for de coluna já existente, apenas avisa
                if (
                    strpos($e->getMessage(), 'Duplicate column name') !== false ||
                    strpos($e->getMessage(), "check that column/key exists") !== false
                ) {
                    echo "  ⚠️  Campo já existe ou não precisa ser alterado: $alteracao\n";
                } else {
                    echo "  ❌ Erro ao executar: $alteracao\n";
                    echo "     Erro: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "\n✅ Padronização concluída!\n";
    echo "\nCampos adicionados/alterados em todas as tabelas:\n";
    echo "- sync_hash: varchar(255) NULL - Hash para controle de sincronização\n";
    echo "- sync_status: enum('pending', 'synced', 'error') - Status da sincronização\n";
    echo "- sync_data: json NULL - Dados adicionais de sincronização\n";
    echo "- created_at: timestamp - Data de criação padronizada\n";
    echo "- updated_at: timestamp - Data de atualização padronizada\n";
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    exit(1);
}
