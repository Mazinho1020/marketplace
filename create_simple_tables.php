<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CRIANDO AS 11 TABELAS RESTANTES (VERSÃO SIMPLIFICADA) ===\n\n";

    // Estruturas simplificadas das 11 tabelas que faltam
    $tables = [
        'empresa_cache' => "
        CREATE TABLE `empresa_cache` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `empresa_id` int(11) NOT NULL,
            `key` varchar(255) NOT NULL,
            `value` mediumtext NOT NULL,
            `expiration` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            PRIMARY KEY (`id`),
            UNIQUE KEY `empresa_key` (`empresa_id`,`key`),
            KEY `idx_empresa_id` (`empresa_id`),
            KEY `idx_expiration` (`expiration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_config_seguranca' => "
        CREATE TABLE `empresa_config_seguranca` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `empresa_id` int(11) NOT NULL,
            `tipo_autenticacao` enum('simples','2fa','ldap') DEFAULT 'simples',
            `requer_2fa` tinyint(1) DEFAULT 0,
            `tentativas_login_max` int(11) DEFAULT 5,
            `tempo_bloqueio_minutos` int(11) DEFAULT 30,
            `senha_min_caracteres` int(11) DEFAULT 8,
            `senha_requer_especiais` tinyint(1) DEFAULT 1,
            `sessao_timeout_minutos` int(11) DEFAULT 480,
            `ip_permitidos` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_papeis' => "
        CREATE TABLE `empresa_papeis` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(100) NOT NULL,
            `descricao` text DEFAULT NULL,
            `empresa_id` int(11) NOT NULL,
            `ativo` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `empresa_nome` (`empresa_id`,`nome`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_permissoes' => "
        CREATE TABLE `empresa_permissoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` varchar(100) NOT NULL,
            `modulo` varchar(50) NOT NULL,
            `acao` varchar(50) NOT NULL,
            `descricao` text DEFAULT NULL,
            `empresa_id` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `empresa_modulo_acao` (`empresa_id`,`modulo`,`acao`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_papel_permissoes' => "
        CREATE TABLE `empresa_papel_permissoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `papel_id` int(11) NOT NULL,
            `permissao_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `papel_permissao` (`papel_id`,`permissao_id`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuarios_activity_log' => "
        CREATE TABLE `empresa_usuarios_activity_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `acao` varchar(100) NOT NULL,
            `modulo` varchar(50) NOT NULL,
            `descricao` text NOT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_usuario_id` (`usuario_id`),
            KEY `idx_empresa_id` (`empresa_id`),
            KEY `idx_acao` (`acao`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuarios_remember_tokens' => "
        CREATE TABLE `empresa_usuarios_remember_tokens` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `token` varchar(255) NOT NULL,
            `expires_at` timestamp NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `token` (`token`),
            KEY `idx_usuario_id` (`usuario_id`),
            KEY `idx_empresa_id` (`empresa_id`),
            KEY `idx_expires_at` (`expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuarios_security_settings' => "
        CREATE TABLE `empresa_usuarios_security_settings` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `two_factor_enabled` tinyint(1) DEFAULT 0,
            `two_factor_secret` varchar(255) DEFAULT NULL,
            `backup_codes` text DEFAULT NULL,
            `last_password_change` timestamp NULL DEFAULT NULL,
            `force_password_change` tinyint(1) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `usuario_empresa` (`usuario_id`,`empresa_id`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuario_empresas' => "
        CREATE TABLE `empresa_usuario_empresas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `ativo` tinyint(1) DEFAULT 1,
            `is_admin` tinyint(1) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `usuario_empresa` (`usuario_id`,`empresa_id`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuario_papeis' => "
        CREATE TABLE `empresa_usuario_papeis` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `papel_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `usuario_papel` (`usuario_id`,`papel_id`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ",

        'empresa_usuario_permissoes' => "
        CREATE TABLE `empresa_usuario_permissoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `permissao_id` int(11) NOT NULL,
            `empresa_id` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `usuario_permissao` (`usuario_id`,`permissao_id`),
            KEY `idx_empresa_id` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        "
    ];

    $created = 0;
    $errors = 0;

    foreach ($tables as $tableName => $createSQL) {
        echo "🔧 Criando: {$tableName}\n";

        try {
            // Verificar se já existe
            $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($exists)) {
                echo "ℹ️  Já existe: {$tableName}\n\n";
                continue;
            }

            // Executar criação
            DB::unprepared($createSQL);

            // Verificar se foi realmente criada
            $verification = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($verification)) {
                echo "✅ Criada e verificada: {$tableName}\n";
                $created++;
            } else {
                echo "❌ Falha na criação: {$tableName}\n";
                $errors++;
            }
        } catch (Exception $e) {
            $errorMsg = substr($e->getMessage(), 0, 150);
            echo "❌ Erro {$tableName}: {$errorMsg}...\n";
            $errors++;
        }

        echo "\n";
    }

    echo "=== RESUMO FINAL ===\n";
    echo "✅ Tabelas criadas: {$created}\n";
    echo "❌ Erros: {$errors}\n";
    echo "📊 Total tentativas: " . count($tables) . "\n";

    if ($created > 0) {
        echo "\n🎉 Sucesso! {$created} tabelas foram criadas!\n";
        echo "🔍 Execute 'php check_real_tables.php' para verificação final\n";
    }

    if ($errors > 0) {
        echo "\n⚠️  Tabelas com problemas: {$errors}\n";
    }
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
