<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO TABELAS DE NOTIFICAÇÃO ===\n";

try {
    $tabelas = DB::select('SHOW TABLES');
    $tabelasNotificacao = [];

    foreach ($tabelas as $tabela) {
        $nomeTabela = array_values((array)$tabela)[0];
        if (strpos($nomeTabela, 'notificacao') !== false) {
            $tabelasNotificacao[] = $nomeTabela;
        }
    }

    echo "Tabelas de notificação encontradas:\n";
    foreach ($tabelasNotificacao as $tabela) {
        echo "- $tabela\n";
    }

    // Verificar se a tabela de logs existe
    if (!in_array('notificacao_logs', $tabelasNotificacao)) {
        echo "\n❌ Tabela 'notificacao_logs' não existe!\n";
        echo "Vamos criar a tabela...\n";

        DB::statement("
            CREATE TABLE `notificacao_logs` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `notificacao_id` bigint unsigned DEFAULT NULL,
                `nivel` enum('debug','info','warning','error','critical') NOT NULL DEFAULT 'info',
                `mensagem` text NOT NULL,
                `dados` json DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_notificacao_logs_notificacao_id` (`notificacao_id`),
                KEY `idx_notificacao_logs_nivel` (`nivel`),
                KEY `idx_notificacao_logs_created_at` (`created_at`),
                CONSTRAINT `fk_notificacao_logs_notificacao_id` FOREIGN KEY (`notificacao_id`) REFERENCES `notificacao_enviadas` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        echo "✅ Tabela 'notificacao_logs' criada com sucesso!\n";
    } else {
        echo "\n✅ Tabela 'notificacao_logs' já existe!\n";
    }

    // Verificar estrutura da tabela
    echo "\nEstrutura da tabela notificacao_logs:\n";
    $colunas = DB::select("DESCRIBE notificacao_logs");
    foreach ($colunas as $coluna) {
        echo "- {$coluna->Field} ({$coluna->Type})\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
