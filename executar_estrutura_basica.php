<?php

echo "=== EXECUÇÃO SIMPLIFICADA DO SQL OTIMIZADO ===\n\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=meufinanceiro;charset=utf8mb4", 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Conectado ao banco: meufinanceiro\n\n";
    
    // Desabilitar verificação de foreign keys
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    
    // 1. Criar tabela lançamentos
    echo "1. CRIANDO TABELA LANÇAMENTOS...\n";
    
    $createLancamentos = "
    CREATE TABLE IF NOT EXISTS `lancamentos` (
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `uuid` char(36) NOT NULL COMMENT 'UUID único para identificação externa',
      `empresa_id` int unsigned NOT NULL,
      `usuario_id` int unsigned NOT NULL,
      `mesa_id` int unsigned DEFAULT NULL,
      `caixa_id` int unsigned DEFAULT NULL,
      `pessoa_id` bigint unsigned DEFAULT NULL,
      `pessoa_tipo` enum('cliente','fornecedor','funcionario','empresa') DEFAULT NULL,
      `funcionario_id` bigint unsigned DEFAULT NULL,
      `tipo_lancamento_id` int unsigned DEFAULT NULL,
      `conta_gerencial_id` int unsigned DEFAULT NULL,
      `natureza_financeira` enum('entrada','saida') NOT NULL COMMENT 'entrada=receber, saida=pagar',
      `categoria` enum('venda','compra','servico','taxa','imposto','transferencia','ajuste','outros') NOT NULL DEFAULT 'outros',
      `origem` enum('pdv','manual','delivery','api','importacao','recorrencia') NOT NULL DEFAULT 'manual',
      `valor_bruto` decimal(15,4) NOT NULL COMMENT 'Valor original sem descontos/acréscimos',
      `valor_desconto` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_acrescimo` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_juros` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_multa` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_pago` decimal(15,4) NOT NULL DEFAULT '0.0000' COMMENT 'Calculado automaticamente via triggers',
      `valor_liquido` decimal(15,4) GENERATED ALWAYS AS ((`valor_bruto` - `valor_desconto`) + `valor_acrescimo`) STORED COMMENT 'Valor final para pagamento',
      `valor_saldo` decimal(15,4) GENERATED ALWAYS AS (`valor_liquido` - `valor_pago`) STORED COMMENT 'Saldo restante',
      `data_lancamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `data_emissao` date NOT NULL,
      `data_competencia` date NOT NULL,
      `data_vencimento` date NOT NULL,
      `data_pagamento` datetime DEFAULT NULL,
      `data_ultimo_pagamento` datetime DEFAULT NULL,
      `descricao` text NOT NULL,
      `numero_documento` varchar(100) DEFAULT NULL,
      `observacoes` text DEFAULT NULL,
      `observacoes_pagamento` text DEFAULT NULL,
      `e_parcelado` tinyint(1) NOT NULL DEFAULT '0',
      `parcela_atual` int unsigned DEFAULT '1',
      `total_parcelas` int unsigned DEFAULT '1',
      `grupo_parcelas` varchar(50) DEFAULT NULL,
      `intervalo_dias` int unsigned DEFAULT '30',
      `e_recorrente` tinyint(1) NOT NULL DEFAULT '0',
      `frequencia_recorrencia` enum('diario','semanal','quinzenal','mensal','bimestral','trimestral','semestral','anual') DEFAULT NULL,
      `proxima_recorrencia` date DEFAULT NULL,
      `recorrencia_ativa` tinyint(1) DEFAULT '1',
      `forma_pagamento_id` int unsigned DEFAULT NULL,
      `bandeira_id` int unsigned DEFAULT NULL,
      `conta_bancaria_id` int unsigned DEFAULT NULL,
      `situacao_financeira` enum('pendente','parcialmente_pago','pago','vencido','cancelado','estornado') NOT NULL DEFAULT 'pendente',
      `status_aprovacao` enum('nao_requer','pendente','aprovado','rejeitado') NOT NULL DEFAULT 'nao_requer',
      `config_juros_multa` json DEFAULT NULL,
      `sync_status` enum('pendente','sincronizado','erro','desabilitado') NOT NULL DEFAULT 'pendente',
      `usuario_criacao` int unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_lancamentos_uuid` (`uuid`),
      KEY `idx_lancamentos_empresa` (`empresa_id`),
      KEY `idx_lancamentos_pessoa` (`pessoa_id`),
      KEY `idx_lancamentos_datas` (`data_vencimento`,`data_pagamento`),
      KEY `idx_lancamentos_situacao` (`situacao_financeira`),
      KEY `idx_lancamentos_parcelas` (`grupo_parcelas`,`parcela_atual`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createLancamentos);
    echo "   ✓ Tabela lançamentos criada\n";
    
    // 2. Criar tabela itens
    echo "\n2. CRIANDO TABELA ITENS...\n";
    
    $createItens = "
    CREATE TABLE IF NOT EXISTS `lancamento_itens` (
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `lancamento_id` bigint unsigned NOT NULL,
      `produto_id` bigint unsigned DEFAULT NULL,
      `produto_variacao_id` bigint unsigned DEFAULT NULL,
      `quantidade` decimal(10,4) NOT NULL DEFAULT '1.0000',
      `valor_unitario` decimal(15,4) NOT NULL,
      `observacoes` text DEFAULT NULL,
      `metadados` json DEFAULT NULL COMMENT 'Dados extras do produto no momento da venda',
      `empresa_id` int unsigned NOT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_itens_lancamento` (`lancamento_id`),
      KEY `idx_itens_produto` (`produto_id`),
      KEY `idx_itens_empresa` (`empresa_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createItens);
    echo "   ✓ Tabela lancamento_itens criada\n";
    
    // 3. Criar view principal
    echo "\n3. CRIANDO VIEW FINANCEIRA...\n";
    
    $createView = "
    CREATE OR REPLACE VIEW `v_lancamentos_financeiro` AS
    SELECT 
        l.*,
        -- Informações dos pagamentos (SUA tabela existente)
        COALESCE(p.total_pagamentos, 0) as total_pagamentos_registrados,
        COALESCE(p.valor_pago_confirmado, 0) as valor_efetivamente_pago,
        COALESCE(p.valor_estornado, 0) as valor_estornado,
        p.ultima_data_pagamento,
        
        -- Status calculado
        CASE 
            WHEN l.valor_pago >= l.valor_liquido THEN 'pago'
            WHEN l.valor_pago > 0 THEN 'parcialmente_pago'
            WHEN l.data_vencimento < CURDATE() THEN 'vencido'
            ELSE 'pendente'
        END as status_calculado
    FROM lancamentos l
    LEFT JOIN (
        SELECT 
            p.lancamento_id,
            COUNT(*) as total_pagamentos,
            SUM(CASE WHEN p.status_pagamento = 'confirmado' THEN p.valor ELSE 0 END) as valor_pago_confirmado,
            SUM(CASE WHEN p.status_pagamento = 'estornado' THEN p.valor ELSE 0 END) as valor_estornado,
            MAX(p.data_pagamento) as ultima_data_pagamento
        FROM pagamentos p
        GROUP BY p.lancamento_id
    ) p ON l.id = p.lancamento_id";
    
    $pdo->exec($createView);
    echo "   ✓ View v_lancamentos_financeiro criada\n";
    
    // 4. Reabilitar foreign keys
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    
    // 5. Testar estrutura
    echo "\n4. TESTANDO ESTRUTURA...\n";
    
    $tables = $pdo->query("SHOW TABLES LIKE 'lancament%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "   ✓ Tabelas criadas: " . implode(', ', $tables) . "\n";
    
    $views = $pdo->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW' AND Tables_in_meufinanceiro LIKE '%lancamento%'")->fetchAll(PDO::FETCH_COLUMN);
    if ($views) {
        echo "   ✓ Views criadas: " . implode(', ', $views) . "\n";
    }
    
    // Testar inserção simples
    $testUuid = 'test-' . uniqid();
    $pdo->exec("
        INSERT INTO lancamentos (
            uuid, empresa_id, usuario_id, pessoa_id, natureza_financeira,
            valor_bruto, data_emissao, data_competencia, data_vencimento,
            descricao, usuario_criacao
        ) VALUES (
            '$testUuid', 1, 1, 1, 'entrada',
            100.00, CURDATE(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY),
            'Teste da estrutura otimizada', 1
        )
    ");
    
    $teste = $pdo->query("SELECT id, uuid, valor_bruto, valor_liquido, valor_saldo FROM lancamentos WHERE uuid = '$testUuid'")->fetch();
    if ($teste) {
        echo "   ✓ Teste OK - ID: {$teste['id']}, Líquido: R$ " . number_format($teste['valor_liquido'], 2) . "\n";
        
        // Limpar teste
        $pdo->exec("DELETE FROM lancamentos WHERE uuid = '$testUuid'");
    }
    
    echo "\n✅ ESTRUTURA BÁSICA APLICADA COM SUCESSO!\n\n";
    
    echo "📋 O QUE FOI CRIADO:\n";
    echo "   ✅ Tabela `lancamentos` com campos computed\n";
    echo "   ✅ Tabela `lancamento_itens` para produtos\n";
    echo "   ✅ View `v_lancamentos_financeiro` integrada com SUA tabela pagamentos\n";
    echo "   ✅ Estrutura testada e funcional\n\n";
    
    echo "📊 INTEGRAÇÃO COM PAGAMENTOS:\n";
    $pagamentosCount = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    echo "   ✅ Sua tabela `pagamentos` mantida: $pagamentosCount registros\n";
    echo "   ✅ View integra automaticamente com lancamentos\n";
    echo "   ✅ Pronto para receber triggers de sincronização\n\n";
    
    echo "🚀 PRÓXIMOS PASSOS:\n";
    echo "   1. Atualizar models Laravel\n";
    echo "   2. Implementar triggers de sincronização\n";
    echo "   3. Migrar dados existentes\n";
    echo "   4. Testar com dados reais\n\n";
    
} catch (PDOException $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

?>
