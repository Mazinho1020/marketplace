<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'empresa_usuario_tipo_rel'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "✅ Tabela 'empresa_usuario_tipo_rel' EXISTE\n";

        // Mostrar estrutura
        $stmt = $pdo->query("DESCRIBE empresa_usuario_tipo_rel");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "\n🔍 Estrutura da tabela:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']}\n";
        }
    } else {
        echo "❌ Tabela 'empresa_usuario_tipo_rel' NÃO EXISTE\n";

        // Criar a tabela manualmente
        echo "\n🔧 Criando tabela...\n";

        $sql = "CREATE TABLE IF NOT EXISTS `empresa_usuario_tipo_rel` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `usuario_id` int(11) NOT NULL,
            `tipo_id` int(11) NOT NULL,
            `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Indica se é o tipo principal',
            `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
            `sync_hash` varchar(32) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `usuario_tipo` (`usuario_id`,`tipo_id`),
            KEY `idx_usuario_id` (`usuario_id`),
            KEY `idx_tipo_id` (`tipo_id`),
            KEY `idx_sync` (`sync_status`,`sync_data`),
            CONSTRAINT `fk_utr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`),
            CONSTRAINT `fk_utr_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `empresa_usuario_tipos` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $pdo->exec($sql);
        echo "✅ Tabela criada com sucesso!\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
