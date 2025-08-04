<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Criando tabelas individualmente...\n\n";

$tables = [
    'afi_plan_assinaturas' => "CREATE TABLE IF NOT EXISTS `afi_plan_assinaturas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `empresa_id` int(11) NOT NULL,
        `funforcli_id` int(11) NOT NULL,
        `plano_id` int(11) NOT NULL,
        `ciclo_cobranca` enum('mensal','anual','vitalicio') DEFAULT 'mensal',
        `valor` decimal(10,2) NOT NULL,
        `status` enum('trial','ativo','suspenso','expirado','cancelado') DEFAULT 'trial',
        `trial_expira_em` timestamp NULL DEFAULT NULL,
        `iniciado_em` timestamp NULL DEFAULT NULL,
        `expira_em` timestamp NULL DEFAULT NULL,
        `proxima_cobranca_em` timestamp NULL DEFAULT NULL,
        `ultima_cobranca_em` timestamp NULL DEFAULT NULL,
        `cancelado_em` timestamp NULL DEFAULT NULL,
        `renovacao_automatica` tinyint(1) DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_funforcli` (`funforcli_id`),
        KEY `idx_plano` (`plano_id`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

    'afi_plan_configuracoes' => "CREATE TABLE IF NOT EXISTS `afi_plan_configuracoes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `empresa_id` int(11) NOT NULL,
        `chave` varchar(100) NOT NULL,
        `valor` text DEFAULT NULL,
        `tipo` enum('string','number','boolean','json') DEFAULT 'string',
        `descricao` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_empresa_chave` (`empresa_id`,`chave`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

    'caixas' => "CREATE TABLE IF NOT EXISTS `caixas` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `empresa_id` int(11) NOT NULL,
        `usuario_id` int(11) NOT NULL,
        `data_abertura` datetime NOT NULL DEFAULT current_timestamp(),
        `data_fechamento` datetime DEFAULT NULL,
        `valor_abertura` decimal(10,2) NOT NULL,
        `valor_informado` decimal(10,2) DEFAULT NULL,
        `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto',
        `observacoes` text DEFAULT NULL,
        `valor_vendas` decimal(10,2) DEFAULT NULL,
        `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
        `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
        `sync_hash` varchar(32) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `idx_sync` (`sync_status`,`sync_data`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",

    'config_definitions' => "CREATE TABLE IF NOT EXISTS `config_definitions` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `empresa_id` int(11) NOT NULL,
        `chave` varchar(255) NOT NULL,
        `nome` varchar(255) NOT NULL,
        `descricao` text DEFAULT NULL,
        `tipo` varchar(50) DEFAULT 'string',
        `grupo_id` int(11) DEFAULT NULL,
        `valor_padrao` text DEFAULT NULL,
        `obrigatorio` tinyint(1) DEFAULT 0,
        `min_length` int(11) DEFAULT NULL,
        `max_length` int(11) DEFAULT NULL,
        `regex_validacao` varchar(255) DEFAULT NULL,
        `opcoes` text DEFAULT NULL,
        `editavel` tinyint(1) DEFAULT 1,
        `avancado` tinyint(1) DEFAULT 0,
        `ordem` int(11) DEFAULT 0,
        `dica` text DEFAULT NULL,
        `ajuda` text DEFAULT NULL,
        `ativo` tinyint(1) DEFAULT 1,
        `sync_hash` varchar(32) DEFAULT NULL,
        `sync_status` varchar(50) DEFAULT 'pendente',
        `sync_data` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `deleted_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `config_definitions_grupo_id_index` (`grupo_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

$created = 0;
$existed = 0;
$errors = 0;

foreach ($tables as $tableName => $sql) {
    try {
        // Verificar se a tabela já existe
        $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");

        if (!empty($exists)) {
            echo "ℹ️  Tabela já existe: {$tableName}\n";
            $existed++;
        } else {
            DB::statement($sql);
            echo "✅ Tabela criada: {$tableName}\n";
            $created++;
        }
    } catch (Exception $e) {
        echo "❌ Erro ao criar {$tableName}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== RESUMO ===\n";
echo "Tabelas criadas: {$created}\n";
echo "Tabelas já existiam: {$existed}\n";
echo "Erros: {$errors}\n";

if ($errors === 0) {
    echo "\n🎉 Processo concluído com sucesso!\n";
}
