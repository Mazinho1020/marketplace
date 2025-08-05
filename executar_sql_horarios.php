<?php

/**
 * Script para criar estrutura completa do sistema de horários de funcionamento
 * Execute este arquivo para criar/atualizar todas as tabelas necessárias
 */

require_once 'vendor/autoload.php';

try {
    // Configuração do banco de dados
    $host = '127.0.0.1';
    $dbname = 'fidelidade_sistema';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🔗 Conectado ao banco de dados com sucesso!\n\n";

    // 1. Criar tabela de dias da semana
    $sql_dias_semana = "
    CREATE TABLE IF NOT EXISTS `empresa_dias_semana` (
        `id` int NOT NULL AUTO_INCREMENT,
        `nome` varchar(20) NOT NULL,
        `nome_curto` varchar(3) NOT NULL,
        `numero` int NOT NULL,
        `ativo` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_numero` (`numero`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_dias_semana);
    echo "✅ Tabela empresa_dias_semana criada/verificada\n";

    // Inserir dados dos dias da semana
    $insert_dias = "
    INSERT IGNORE INTO `empresa_dias_semana` (`id`, `nome`, `nome_curto`, `numero`, `ativo`) VALUES
    (1, 'Segunda-feira', 'SEG', 1, 1),
    (2, 'Terça-feira', 'TER', 2, 1),
    (3, 'Quarta-feira', 'QUA', 3, 1),
    (4, 'Quinta-feira', 'QUI', 4, 1),
    (5, 'Sexta-feira', 'SEX', 5, 1),
    (6, 'Sábado', 'SAB', 6, 1),
    (7, 'Domingo', 'DOM', 7, 1);
    ";

    $pdo->exec($insert_dias);
    echo "✅ Dados dos dias da semana inseridos\n";

    // 2. Criar tabela principal de horários
    $sql_horarios = "
    CREATE TABLE IF NOT EXISTS `empresa_horarios_funcionamento` (
        `id` int NOT NULL AUTO_INCREMENT,
        `empresa_id` int NOT NULL,
        `dia_semana_id` int NULL COMMENT 'NULL para exceções',
        `sistema` enum('TODOS','PDV','FINANCEIRO','ONLINE') NOT NULL DEFAULT 'TODOS',
        `aberto` tinyint(1) NOT NULL DEFAULT 1,
        `hora_abertura` time NULL,
        `hora_fechamento` time NULL,
        `is_excecao` tinyint(1) NOT NULL DEFAULT 0,
        `data_excecao` date NULL COMMENT 'Usado apenas para exceções',
        `descricao_excecao` varchar(255) NULL,
        `observacoes` text NULL,
        `ativo` tinyint(1) NOT NULL DEFAULT 1,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_empresa_sistema` (`empresa_id`, `sistema`),
        KEY `idx_empresa_dia_semana` (`empresa_id`, `dia_semana_id`),
        KEY `idx_empresa_excecao_data` (`empresa_id`, `is_excecao`, `data_excecao`),
        KEY `idx_ativo` (`ativo`),
        FOREIGN KEY (`dia_semana_id`) REFERENCES `empresa_dias_semana`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_horarios);
    echo "✅ Tabela empresa_horarios_funcionamento criada/verificada\n";

    // 3. Criar tabela de logs de auditoria
    $sql_logs = "
    CREATE TABLE IF NOT EXISTS `empresa_horarios_logs` (
        `id` int NOT NULL AUTO_INCREMENT,
        `empresa_id` int NOT NULL,
        `horario_id` int NULL,
        `acao` enum('CREATE','UPDATE','DELETE','VIEW') NOT NULL,
        `dados_anteriores` json NULL,
        `dados_novos` json NULL,
        `usuario_id` int NULL,
        `usuario_nome` varchar(100) NULL,
        `ip_address` varchar(45) NULL,
        `user_agent` text NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_empresa_acao` (`empresa_id`, `acao`),
        KEY `idx_horario_id` (`horario_id`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql_logs);
    echo "✅ Tabela empresa_horarios_logs criada/verificada\n";

    // 4. Inserir dados de exemplo para empresa ID 1
    $dados_exemplo = "
    INSERT IGNORE INTO `empresa_horarios_funcionamento` 
    (`empresa_id`, `dia_semana_id`, `sistema`, `aberto`, `hora_abertura`, `hora_fechamento`, `observacoes`) 
    VALUES
    -- Horários PDV Segunda a Sexta
    (1, 1, 'PDV', 1, '08:00:00', '18:00:00', 'Horário comercial padrão'),
    (1, 2, 'PDV', 1, '08:00:00', '18:00:00', 'Horário comercial padrão'),
    (1, 3, 'PDV', 1, '08:00:00', '18:00:00', 'Horário comercial padrão'),
    (1, 4, 'PDV', 1, '08:00:00', '18:00:00', 'Horário comercial padrão'),
    (1, 5, 'PDV', 1, '08:00:00', '18:00:00', 'Horário comercial padrão'),
    -- Sábado meio período
    (1, 6, 'PDV', 1, '08:00:00', '12:00:00', 'Meio período aos sábados'),
    -- Domingo fechado
    (1, 7, 'PDV', 0, NULL, NULL, 'Fechado aos domingos'),
    
    -- Sistema Online 24h
    (1, 1, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 2, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 3, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 4, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 5, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 6, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    (1, 7, 'ONLINE', 1, '00:00:00', '23:59:59', 'Sistema online 24h'),
    
    -- Sistema Financeiro
    (1, 1, 'FINANCEIRO', 1, '09:00:00', '17:00:00', 'Horário administrativo'),
    (1, 2, 'FINANCEIRO', 1, '09:00:00', '17:00:00', 'Horário administrativo'),
    (1, 3, 'FINANCEIRO', 1, '09:00:00', '17:00:00', 'Horário administrativo'),
    (1, 4, 'FINANCEIRO', 1, '09:00:00', '17:00:00', 'Horário administrativo'),
    (1, 5, 'FINANCEIRO', 1, '09:00:00', '17:00:00', 'Horário administrativo'),
    (1, 6, 'FINANCEIRO', 0, NULL, NULL, 'Fechado aos sábados'),
    (1, 7, 'FINANCEIRO', 0, NULL, NULL, 'Fechado aos domingos');
    ";

    $pdo->exec($dados_exemplo);
    echo "✅ Dados de exemplo inseridos\n";

    // 5. Inserir uma exceção de exemplo (Natal)
    $excecao_exemplo = "
    INSERT IGNORE INTO `empresa_horarios_funcionamento` 
    (`empresa_id`, `dia_semana_id`, `sistema`, `aberto`, `is_excecao`, `data_excecao`, `descricao_excecao`, `observacoes`) 
    VALUES
    (1, NULL, 'TODOS', 0, 1, '2024-12-25', 'Feriado de Natal', 'Empresa fechada para o feriado de Natal');
    ";

    $pdo->exec($excecao_exemplo);
    echo "✅ Exceção de exemplo inserida (Natal)\n";

    echo "\n🎉 Estrutura do sistema de horários criada com sucesso!\n";
    echo "📊 Dados de exemplo inseridos para empresa ID: 1\n";
    echo "🔗 Acesse o sistema através das rotas do Laravel\n\n";

    // Mostrar resumo das tabelas criadas
    $stmt = $pdo->query("SHOW TABLES LIKE '%horario%' OR SHOW TABLES LIKE '%dias_semana%'");
    $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "📋 Tabelas criadas:\n";
    foreach ($tabelas as $tabela) {
        $count_stmt = $pdo->query("SELECT COUNT(*) FROM $tabela");
        $count = $count_stmt->fetchColumn();
        echo "   - $tabela ($count registros)\n";
    }
} catch (PDOException $e) {
    echo "❌ Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
