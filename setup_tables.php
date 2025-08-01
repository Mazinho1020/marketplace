<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    echo "🔍 Verificando tabelas necessárias...\n\n";

    // 1. Verificar empresa_usuario_tipos
    $stmt = $pdo->query("SHOW TABLES LIKE 'empresa_usuario_tipos'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "✅ Tabela 'empresa_usuario_tipos' EXISTE\n";
    } else {
        echo "❌ Tabela 'empresa_usuario_tipos' NÃO EXISTE\n";
        echo "🔧 Criando tabela empresa_usuario_tipos...\n";

        $sql = "CREATE TABLE IF NOT EXISTS `empresa_usuario_tipos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `codigo` varchar(50) NOT NULL,
            `nome` varchar(100) NOT NULL,
            `descricao` text,
            `status` varchar(20) DEFAULT 'ativo',
            `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
            `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
            `sync_hash` varchar(32) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `codigo` (`codigo`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

        $pdo->exec($sql);
        echo "✅ Tabela empresa_usuario_tipos criada!\n";

        // Inserir tipos básicos
        $tipos = [
            ['admin', 'Administrador', 'Usuário administrador do sistema'],
            ['user', 'Usuário', 'Usuário comum do sistema'],
            ['comerciante', 'Comerciante', 'Usuário comerciante'],
            ['cliente', 'Cliente', 'Usuário cliente'],
            ['entregador', 'Entregador', 'Usuário entregador']
        ];

        foreach ($tipos as $tipo) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO empresa_usuario_tipos (codigo, nome, descricao) VALUES (?, ?, ?)");
            $stmt->execute($tipo);
        }
        echo "✅ Tipos básicos inseridos!\n";
    }

    // 2. Agora verificar/criar empresa_usuario_tipo_rel
    $stmt = $pdo->query("SHOW TABLES LIKE 'empresa_usuario_tipo_rel'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "✅ Tabela 'empresa_usuario_tipo_rel' EXISTE\n";
    } else {
        echo "❌ Tabela 'empresa_usuario_tipo_rel' NÃO EXISTE\n";
        echo "🔧 Criando tabela empresa_usuario_tipo_rel...\n";

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
        echo "✅ Tabela empresa_usuario_tipo_rel criada!\n";
    }

    // 3. Verificar se usuários têm tipos atribuídos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empresa_usuario_tipo_rel");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($count['total'] == 0) {
        echo "\n🔧 Atribuindo tipos aos usuários existentes...\n";

        // Buscar tipo 'admin' ou 'user'
        $stmt = $pdo->query("SELECT id FROM empresa_usuario_tipos WHERE codigo IN ('admin', 'user') ORDER BY codigo LIMIT 1");
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tipo) {
            // Atribuir tipo a todos os usuários
            $stmt = $pdo->prepare("
                INSERT INTO empresa_usuario_tipo_rel (usuario_id, tipo_id, is_primary) 
                SELECT id, ?, 1 FROM empresa_usuarios WHERE deleted_at IS NULL
            ");
            $stmt->execute([$tipo['id']]);
            echo "✅ Tipos atribuídos aos usuários existentes!\n";
        }
    }

    echo "\n🎉 Todas as tabelas estão prontas!\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
