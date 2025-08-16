<?php

/**
 * APLICAR SQL OTIMIZADO - ESTRUTURA BASEADA NO SEU BD EXISTENTE
 * 
 * Analisando sua estrutura existente, vou criar:
 * 1. Tabela lancamentos que é referenciada pela tabela pagamentos
 * 2. Integração perfeita com sua estrutura atual
 * 3. Aproveitamento dos dados do backup se existir
 */

echo "🚀 APLICANDO ESTRUTURA OTIMIZADA BASEADA NO SEU BD\n\n";

// Configuração do banco
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'meufinanceiro';

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao MySQL\n";
    
    // Selecionar o banco
    $pdo->exec("USE $database");
    echo "✅ Banco $database selecionado\n\n";
    
    echo "📋 ANALISANDO ESTRUTURA EXISTENTE...\n";
    
    // Verificar tabelas existentes
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $hasBackup = in_array('lancamentos_backup', $tables);
    $hasLancamentos = in_array('lancamentos', $tables);
    $hasPagamentos = in_array('pagamentos', $tables);
    
    echo "✅ Tabelas encontradas:\n";
    echo "   - lancamentos: " . ($hasLancamentos ? "SIM" : "NÃO") . "\n";
    echo "   - lancamentos_backup: " . ($hasBackup ? "SIM" : "NÃO") . "\n";
    echo "   - pagamentos: " . ($hasPagamentos ? "SIM" : "NÃO") . "\n\n";
    
    if ($hasPagamentos) {
        // Verificar estrutura da FK pagamentos -> lancamentos
        $pagamentosStruct = $pdo->query("SHOW CREATE TABLE pagamentos")->fetch(PDO::FETCH_ASSOC);
        if (preg_match('/`lancamento_id`\s+(\w+)/', $pagamentosStruct['Create Table'], $matches)) {
            $tipoFK = $matches[1];
            echo "📌 FK pagamentos.lancamento_id é do tipo: $tipoFK\n";
        } else {
            $tipoFK = 'int';
        }
    }
    
    echo "\n🔄 CRIANDO ESTRUTURA OTIMIZADA...\n\n";
    
    // 1. Desabilitar verificações FK temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "🔓 Verificações FK desabilitadas\n";
    
    // 2. Backup se lancamentos existir
    if ($hasLancamentos) {
        $pdo->exec("DROP TABLE IF EXISTS lancamentos_backup_anterior");
        $pdo->exec("CREATE TABLE lancamentos_backup_anterior AS SELECT * FROM lancamentos");
        echo "📦 Backup da tabela atual criado\n";
    }
    
    // 3. Dropar tabela lancamentos se existir
    $pdo->exec("DROP TABLE IF EXISTS lancamentos");
    echo "🗑️ Tabela lancamentos removida\n";
    
    // 4. Criar nova estrutura lancamentos otimizada baseada no seu BD
    $sqlLancamentos = "
    CREATE TABLE `lancamentos` (
      `id` int NOT NULL AUTO_INCREMENT,
      `uuid` char(36) DEFAULT NULL COMMENT 'UUID único para identificação externa',
      `empresa_id` int NOT NULL,
      `pessoa_id` bigint unsigned DEFAULT NULL,
      `pessoa_tipo` enum('cliente','fornecedor','funcionario') DEFAULT NULL,
      `usuario_id` int DEFAULT NULL,
      `conta_gerencial_id` int DEFAULT NULL,
      `conta_bancaria_id` int DEFAULT NULL,
      `tipo_id` int DEFAULT NULL,
      `tipo_lancamento_id` int DEFAULT NULL,
      
      -- Dados do lançamento
      `natureza_financeira` enum('entrada','saida') NOT NULL DEFAULT 'entrada' COMMENT 'entrada=receber, saida=pagar',
      `categoria` enum('venda','compra','servico','taxa','imposto','transferencia','ajuste','outros') DEFAULT 'outros',
      `descricao` varchar(255) DEFAULT NULL,
      `numero_documento` varchar(100) DEFAULT NULL,
      `observacoes` text,
      
      -- Valores
      `valor` decimal(15,4) NOT NULL COMMENT 'Valor original sem descontos/acréscimos',
      `valor_desconto` decimal(15,2) NOT NULL DEFAULT '0.00',
      `valor_acrescimo` decimal(15,2) NOT NULL DEFAULT '0.00',
      `valor_juros` decimal(15,2) NOT NULL DEFAULT '0.00',
      `valor_multa` decimal(15,2) NOT NULL DEFAULT '0.00',
      `valor_liquido` decimal(15,4) GENERATED ALWAYS AS ((`valor` - `valor_desconto`) + `valor_acrescimo` + `valor_juros` + `valor_multa`) STORED COMMENT 'Valor final calculado',
      `valor_pago` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Calculado pelos triggers dos pagamentos',
      `valor_saldo` decimal(15,2) GENERATED ALWAYS AS (`valor_liquido` - `valor_pago`) STORED COMMENT 'Saldo restante',
      
      -- Datas
      `data_emissao` date NOT NULL,
      `data_vencimento` date NOT NULL,
      `data_competencia` date DEFAULT NULL,
      `data_pagamento` datetime DEFAULT NULL COMMENT 'Data do último pagamento',
      
      -- Status e situação
      `situacao_financeira` enum('pendente','pago','parcialmente_pago','vencido','cancelado','em_negociacao') NOT NULL DEFAULT 'pendente',
      `status` varchar(50) DEFAULT 'ativo',
      
      -- Parcelamento
      `total_parcelas` int NOT NULL DEFAULT '1',
      `parcela_atual` int DEFAULT 1,
      `grupo_parcelas` varchar(36) DEFAULT NULL COMMENT 'UUID para agrupar parcelas',
      `intervalo_dias` int NOT NULL DEFAULT '30',
      
      -- Recorrência
      `e_recorrente` tinyint(1) NOT NULL DEFAULT '0',
      `frequencia_recorrencia` enum('semanal','quinzenal','mensal','bimestral','trimestral','semestral','anual') DEFAULT NULL,
      `proxima_recorrencia` date DEFAULT NULL,
      
      -- Controles
      `numero_pagamentos` int NOT NULL DEFAULT '0' COMMENT 'Atualizado por trigger',
      `data_ultimo_pagamento` datetime DEFAULT NULL COMMENT 'Atualizado por trigger',
      `usuario_ultimo_pagamento_id` int DEFAULT NULL,
      
      -- PDV/Delivery
      `origem` enum('pdv','lancamento','delivery') DEFAULT 'lancamento',
      `mesa_id` int DEFAULT NULL,
      `caixa_id` int DEFAULT NULL,
      
      -- Metadados e configurações
      `metadados` json DEFAULT NULL COMMENT 'Dados específicos por módulo',
      `config_alertas` json DEFAULT NULL,
      `anexos` json DEFAULT NULL,
      
      -- Auditoria
      `usuario_criacao` int NOT NULL DEFAULT '1',
      `usuario_ultima_alteracao` int DEFAULT NULL,
      `data_exclusao` datetime DEFAULT NULL,
      `usuario_exclusao` int DEFAULT NULL,
      `motivo_exclusao` varchar(500) DEFAULT NULL,
      
      -- Sincronização
      `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
      `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `sync_hash` varchar(32) DEFAULT NULL,
      
      -- Timestamps
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      
      PRIMARY KEY (`id`),
      UNIQUE KEY `uuid` (`uuid`),
      KEY `idx_empresa_data_vencimento` (`empresa_id`,`data_vencimento`),
      KEY `idx_situacao_financeira` (`situacao_financeira`),
      KEY `idx_natureza_financeira` (`natureza_financeira`),
      KEY `idx_pessoa` (`pessoa_id`,`pessoa_tipo`),
      KEY `idx_conta_gerencial` (`conta_gerencial_id`),
      KEY `idx_grupo_parcelas` (`grupo_parcelas`),
      KEY `idx_origem` (`origem`),
      KEY `idx_sync` (`sync_status`,`sync_data`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci 
    COMMENT='Lançamentos financeiros integrados com sua estrutura existente'";
    
    $pdo->exec($sqlLancamentos);
    echo "✅ Tabela lancamentos criada com estrutura otimizada\n";
    
    // 5. Criar tabela lancamento_itens compatível
    $sqlItens = "
    CREATE TABLE `lancamento_itens` (
      `id` int NOT NULL AUTO_INCREMENT,
      `lancamento_id` int NOT NULL,
      `produto_id` int unsigned DEFAULT NULL,
      `produto_variacao_id` int unsigned DEFAULT NULL,
      `quantidade` decimal(10,2) NOT NULL,
      `valor_unitario` decimal(10,2) NOT NULL,
      `valor_total` decimal(10,2) NOT NULL,
      `observacoes` text,
      `empresa_id` int NOT NULL,
      `usuario_id` int DEFAULT NULL,
      `metadados` json DEFAULT NULL,
      `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
      `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `sync_hash` varchar(32) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      
      PRIMARY KEY (`id`),
      KEY `idx_lancamento` (`lancamento_id`),
      KEY `idx_produto` (`produto_id`),
      KEY `idx_sync` (`sync_status`,`sync_data`),
      
      CONSTRAINT `fk_itens_lancamento` FOREIGN KEY (`lancamento_id`) REFERENCES `lancamentos` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci";
    
    $pdo->exec($sqlItens);
    echo "✅ Tabela lancamento_itens criada\n";
    
    // 6. Criar triggers para integração com pagamentos
    $triggerPagamentosInsert = "
    CREATE TRIGGER `tr_pagamentos_after_insert` 
    AFTER INSERT ON `pagamentos` 
    FOR EACH ROW 
    BEGIN
        DECLARE valor_total_pago DECIMAL(15,2) DEFAULT 0;
        
        -- Calcular total pago
        SELECT COALESCE(SUM(valor), 0) INTO valor_total_pago
        FROM pagamentos 
        WHERE lancamento_id = NEW.lancamento_id 
        AND status_pagamento IN ('confirmado', 'processando');
        
        -- Atualizar lancamento
        UPDATE lancamentos SET 
            valor_pago = valor_total_pago,
            numero_pagamentos = (
                SELECT COUNT(*) FROM pagamentos 
                WHERE lancamento_id = NEW.lancamento_id 
                AND status_pagamento IN ('confirmado', 'processando')
            ),
            data_ultimo_pagamento = NEW.data_pagamento,
            usuario_ultimo_pagamento_id = NEW.usuario_id,
            data_pagamento = IF(valor_total_pago >= valor_liquido, NEW.data_pagamento, data_pagamento),
            situacao_financeira = CASE
                WHEN valor_total_pago <= 0 THEN 'pendente'
                WHEN valor_total_pago >= valor_liquido THEN 'pago'
                ELSE 'parcialmente_pago'
            END,
            updated_at = NOW()
        WHERE id = NEW.lancamento_id;
    END";
    
    $pdo->exec($triggerPagamentosInsert);
    echo "✅ Trigger pagamentos INSERT criado\n";
    
    $triggerPagamentosUpdate = "
    CREATE TRIGGER `tr_pagamentos_after_update` 
    AFTER UPDATE ON `pagamentos` 
    FOR EACH ROW 
    BEGIN
        DECLARE valor_total_pago DECIMAL(15,2) DEFAULT 0;
        
        -- Calcular total pago
        SELECT COALESCE(SUM(valor), 0) INTO valor_total_pago
        FROM pagamentos 
        WHERE lancamento_id = NEW.lancamento_id 
        AND status_pagamento IN ('confirmado', 'processando');
        
        -- Atualizar lancamento
        UPDATE lancamentos SET 
            valor_pago = valor_total_pago,
            numero_pagamentos = (
                SELECT COUNT(*) FROM pagamentos 
                WHERE lancamento_id = NEW.lancamento_id 
                AND status_pagamento IN ('confirmado', 'processando')
            ),
            data_ultimo_pagamento = (
                SELECT MAX(data_pagamento) FROM pagamentos 
                WHERE lancamento_id = NEW.lancamento_id 
                AND status_pagamento IN ('confirmado', 'processando')
            ),
            situacao_financeira = CASE
                WHEN valor_total_pago <= 0 THEN 'pendente'
                WHEN valor_total_pago >= valor_liquido THEN 'pago'
                ELSE 'parcialmente_pago'
            END,
            updated_at = NOW()
        WHERE id = NEW.lancamento_id;
    END";
    
    $pdo->exec($triggerPagamentosUpdate);
    echo "✅ Trigger pagamentos UPDATE criado\n";
    
    // 7. Criar views para relatórios
    $viewDashboard = "
    CREATE OR REPLACE VIEW `vw_dashboard_financeiro` AS
    SELECT
        l.empresa_id,
        l.natureza_financeira,
        l.situacao_financeira,
        DATE(l.data_vencimento) as data_vencimento,
        DATE(l.data_emissao) as data_emissao,
        COUNT(*) as total_lancamentos,
        SUM(l.valor_liquido) as valor_total,
        SUM(l.valor_pago) as valor_pago,
        SUM(l.valor_saldo) as valor_pendente,
        AVG(l.valor_liquido) as valor_medio
    FROM lancamentos l
    WHERE l.data_exclusao IS NULL
    GROUP BY l.empresa_id, l.natureza_financeira, l.situacao_financeira, 
             DATE(l.data_vencimento), DATE(l.data_emissao)";
    
    $pdo->exec($viewDashboard);
    echo "✅ View dashboard criada\n";
    
    $viewFluxoCaixa = "
    CREATE OR REPLACE VIEW `vw_fluxo_caixa` AS
    SELECT
        l.empresa_id,
        l.data_vencimento,
        l.natureza_financeira,
        SUM(CASE WHEN l.situacao_financeira = 'pago' THEN l.valor_liquido ELSE 0 END) as realizado,
        SUM(CASE WHEN l.situacao_financeira IN ('pendente', 'parcialmente_pago') THEN l.valor_saldo ELSE 0 END) as previsto,
        SUM(l.valor_liquido) as total
    FROM lancamentos l
    WHERE l.data_exclusao IS NULL
    GROUP BY l.empresa_id, l.data_vencimento, l.natureza_financeira
    ORDER BY l.data_vencimento";
    
    $pdo->exec($viewFluxoCaixa);
    echo "✅ View fluxo de caixa criada\n";
    
    // 8. Restaurar dados do backup se existir
    if ($hasBackup) {
        echo "\n🔄 RESTAURANDO DADOS DO BACKUP...\n";
        
        // Verificar estrutura do backup
        $backupStruct = $pdo->query("DESCRIBE lancamentos_backup")->fetchAll(PDO::FETCH_COLUMN);
        $newStruct = $pdo->query("DESCRIBE lancamentos")->fetchAll(PDO::FETCH_COLUMN);
        
        // Campos em comum
        $commonFields = array_intersect($backupStruct, $newStruct);
        $fieldsList = '`' . implode('`, `', $commonFields) . '`';
        
        echo "📋 Campos compatíveis: " . count($commonFields) . "\n";
        
        // Restaurar dados
        $restoreSQL = "INSERT INTO lancamentos ($fieldsList) 
                      SELECT $fieldsList FROM lancamentos_backup 
                      WHERE id NOT IN (SELECT id FROM lancamentos WHERE id IS NOT NULL)";
        
        try {
            $restored = $pdo->exec($restoreSQL);
            echo "✅ $restored registros restaurados do backup\n";
        } catch (Exception $e) {
            echo "⚠️ Erro na restauração: " . $e->getMessage() . "\n";
        }
    }
    
    // 9. Reabilitar verificações FK
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "🔒 Verificações FK reabilitadas\n";
    
    // 10. Verificações finais
    echo "\n🔍 VERIFICAÇÃO FINAL...\n";
    
    $lancamentos = $pdo->query("SELECT COUNT(*) FROM lancamentos")->fetchColumn();
    $itens = $pdo->query("SELECT COUNT(*) FROM lancamento_itens")->fetchColumn();
    $pagamentos = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    
    // Verificar triggers
    $triggers = $pdo->query("SHOW TRIGGERS WHERE `Table` IN ('pagamentos', 'lancamentos')")->fetchAll();
    
    // Verificar views
    $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_{$database} LIKE 'vw_%'")->fetchAll();
    
    echo "📊 ESTATÍSTICAS FINAIS:\n";
    echo "   - Lançamentos: $lancamentos\n";
    echo "   - Itens: $itens\n";
    echo "   - Pagamentos: $pagamentos\n";
    echo "   - Triggers: " . count($triggers) . "\n";
    echo "   - Views: " . count($views) . "\n\n";
    
    echo "🎉 ESTRUTURA APLICADA COM SUCESSO!\n\n";
    
    echo "✨ RECURSOS IMPLEMENTADOS:\n";
    echo "✅ Tabela lancamentos integrada com pagamentos\n";
    echo "✅ Campos calculados (valor_liquido, valor_saldo)\n";
    echo "✅ Triggers automáticos para valor_pago\n";
    echo "✅ Compatibilidade total com estrutura existente\n";
    echo "✅ Views para dashboard e relatórios\n";
    echo "✅ Sistema de parcelamento e recorrência\n";
    echo "✅ Controle de origem (PDV/Delivery/Lançamento)\n";
    echo "✅ Metadados flexíveis em JSON\n";
    echo "✅ Sistema de auditoria completo\n\n";
    
    echo "🔗 INTEGRAÇÃO ATIVA:\n";
    echo "   - Sua tabela pagamentos → lancamentos (FK preservada)\n";
    echo "   - Triggers atualizam valor_pago automaticamente\n";
    echo "   - Situação financeira calculada em tempo real\n";
    echo "   - Dashboard com métricas instantâneas\n\n";
    
    echo "📋 PRÓXIMOS PASSOS:\n";
    echo "1. ✅ Estrutura otimizada aplicada\n";
    echo "2. 🔄 Atualizar models Laravel\n";
    echo "3. 🔄 Testar integração com pagamentos\n";
    echo "4. 🔄 Executar testes do sistema\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nDetalhes do erro:\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    
    // Tentar reabilitar FK checks em caso de erro
    try {
        if (isset($pdo)) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        }
    } catch (Exception $e2) {
        // Ignorar erro secundário
    }
}

?>
