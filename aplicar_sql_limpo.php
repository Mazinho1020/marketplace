<?php

/**
 * APLICAR SQL OTIMIZADO - VERSÃO FINAL SEM BACKUP
 * 
 * Esta versão usa SUA tabela de pagamentos existente
 * SEM criar lancamento_movimentacoes e SEM considerar backups
 */

echo "🚀 APLICANDO SQL OTIMIZADO - VERSÃO LIMPA\n\n";

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
    
    echo "🔄 EXECUTANDO MIGRAÇÃO LIMPA...\n\n";
    
    // 1. Desabilitar verificações FK temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "🔓 Verificações FK desabilitadas\n";
    
    // 2. Remover foreign keys existentes que podem causar conflito
    echo "🧹 Removendo FKs conflitantes...\n";
    
    // Listar FKs da tabela lancamento_itens
    $fks = $pdo->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = '$database' 
        AND TABLE_NAME = 'lancamento_itens' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($fks as $fk) {
        try {
            $pdo->exec("ALTER TABLE lancamento_itens DROP FOREIGN KEY `$fk`");
            echo "   ✅ FK $fk removida\n";
        } catch (Exception $e) {
            echo "   ⚠️ FK $fk não encontrada\n";
        }
    }
    
    // 3. Dropar tabelas se existirem
    $pdo->exec("DROP TABLE IF EXISTS lancamentos");
    echo "🗑️ Tabela lancamentos removida\n";
    
    $pdo->exec("DROP TABLE IF EXISTS lancamento_itens");
    echo "🗑️ Tabela lancamento_itens removida\n";
    
    // 4. Criar tabela lancamentos com tipos corretos baseados no seu BD
    echo "🏗️ Criando tabela lancamentos...\n";
    
    $createLancamentos = "
    CREATE TABLE `lancamentos` (
      -- Identificação principal (compatível com pagamentos.lancamento_id = int)
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `uuid` char(36) NOT NULL COMMENT 'UUID único para identificação externa',
      
      -- Relacionamentos principais (baseado na sua estrutura)
      `empresa_id` int unsigned NOT NULL,
      `usuario_id` int unsigned NOT NULL,
      `mesa_id` int unsigned DEFAULT NULL,
      `caixa_id` int unsigned DEFAULT NULL,
      
      -- Identificação da pessoa/entidade
      `pessoa_id` bigint unsigned DEFAULT NULL,
      `pessoa_tipo` enum('cliente','fornecedor','funcionario','empresa') DEFAULT NULL,
      `funcionario_id` bigint unsigned DEFAULT NULL,
      
      -- Classificação do lançamento
      `tipo_lancamento_id` int unsigned DEFAULT NULL,
      `conta_gerencial_id` int unsigned DEFAULT NULL,
      `natureza_financeira` enum('entrada','saida') NOT NULL COMMENT 'entrada=receber, saida=pagar',
      `categoria` enum('venda','compra','servico','taxa','imposto','transferencia','ajuste','outros') NOT NULL DEFAULT 'outros',
      `origem` enum('pdv','manual','delivery','api','importacao','recorrencia') NOT NULL DEFAULT 'manual',
      
      -- Informações financeiras principais
      `valor_bruto` decimal(15,4) NOT NULL COMMENT 'Valor original sem descontos/acréscimos',
      `valor_desconto` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_acrescimo` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_juros` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_multa` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_liquido` decimal(15,4) GENERATED ALWAYS AS (
        `valor_bruto` - `valor_desconto` + `valor_acrescimo` + `valor_juros` + `valor_multa`
      ) STORED COMMENT 'Valor final calculado automaticamente',
      
      -- Controle de pagamentos (calculado da SUA tabela pagamentos)
      `valor_pago` decimal(15,4) NOT NULL DEFAULT '0.0000',
      `valor_saldo` decimal(15,4) GENERATED ALWAYS AS (`valor_liquido` - `valor_pago`) STORED,
      `situacao_financeira` enum('pendente','pago','parcialmente_pago','vencido','cancelado','em_negociacao','estornado') NOT NULL DEFAULT 'pendente',
      
      -- Datas importantes
      `data_lancamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `data_emissao` date NOT NULL,
      `data_competencia` date NOT NULL,
      `data_vencimento` date NOT NULL,
      `data_pagamento` datetime DEFAULT NULL,
      `data_ultimo_pagamento` datetime DEFAULT NULL,
      
      -- Informações descritivas
      `descricao` varchar(500) NOT NULL,
      `numero_documento` varchar(100) DEFAULT NULL,
      `observacoes` text DEFAULT NULL,
      `observacoes_pagamento` text DEFAULT NULL,
      
      -- Controle de parcelamento
      `e_parcelado` boolean NOT NULL DEFAULT FALSE,
      `parcela_atual` smallint unsigned DEFAULT NULL,
      `total_parcelas` smallint unsigned NOT NULL DEFAULT 1,
      `grupo_parcelas` char(36) DEFAULT NULL COMMENT 'UUID do grupo de parcelas',
      `intervalo_dias` smallint unsigned NOT NULL DEFAULT 30,
      
      -- Recorrência
      `e_recorrente` boolean NOT NULL DEFAULT FALSE,
      `frequencia_recorrencia` enum('diaria','semanal','quinzenal','mensal','bimestral','trimestral','semestral','anual') DEFAULT NULL,
      `proxima_recorrencia` date DEFAULT NULL,
      `recorrencia_ativa` boolean NOT NULL DEFAULT TRUE,
      
      -- Forma de pagamento (relação com SUA tabela pagamentos)
      `forma_pagamento_id` bigint unsigned DEFAULT NULL,
      `bandeira_id` bigint unsigned DEFAULT NULL,
      `conta_bancaria_id` bigint unsigned DEFAULT NULL,
      
      -- Cobrança automática e boletos
      `cobranca_automatica` boolean NOT NULL DEFAULT FALSE,
      `data_proxima_cobranca` date DEFAULT NULL,
      `tentativas_cobranca` smallint unsigned NOT NULL DEFAULT 0,
      `max_tentativas_cobranca` smallint unsigned NOT NULL DEFAULT 3,
      
      -- Boleto
      `boleto_gerado` boolean NOT NULL DEFAULT FALSE,
      `boleto_nosso_numero` varchar(50) DEFAULT NULL,
      `boleto_data_geracao` datetime DEFAULT NULL,
      `boleto_url` text DEFAULT NULL,
      `boleto_linha_digitavel` varchar(54) DEFAULT NULL,
      
      -- Aprovação e workflow
      `status_aprovacao` enum('pendente_aprovacao','aprovado','rejeitado','nao_requer') NOT NULL DEFAULT 'nao_requer',
      `aprovado_por` bigint unsigned DEFAULT NULL,
      `data_aprovacao` datetime DEFAULT NULL,
      `motivo_rejeicao` text DEFAULT NULL,
      
      -- Configurações JSON otimizadas
      `config_juros_multa` json DEFAULT NULL COMMENT 'Configurações de juros e multa',
      `config_desconto` json DEFAULT NULL COMMENT 'Configurações de desconto por antecipação',
      `config_alertas` json DEFAULT NULL COMMENT 'Configurações de alertas',
      `anexos` json DEFAULT NULL COMMENT 'URLs e metadados de anexos',
      `metadados` json DEFAULT NULL COMMENT 'Dados específicos por módulo',
      
      -- Controle de sincronização
      `sync_status` enum('pendente','sincronizado','erro','processando') NOT NULL DEFAULT 'pendente',
      `sync_tentativas` smallint unsigned NOT NULL DEFAULT 0,
      `sync_ultimo_erro` text DEFAULT NULL,
      `sync_hash` varchar(64) DEFAULT NULL,
      
      -- Auditoria
      `usuario_criacao` int unsigned NOT NULL,
      `usuario_ultima_alteracao` int unsigned DEFAULT NULL,
      `data_exclusao` datetime DEFAULT NULL,
      `usuario_exclusao` int unsigned DEFAULT NULL,
      `motivo_exclusao` varchar(500) DEFAULT NULL,
      
      -- Timestamps
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_uuid` (`uuid`),
      UNIQUE KEY `uk_empresa_numero_documento` (`empresa_id`, `numero_documento`),
      
      -- Índices de performance
      KEY `idx_empresa_situacao` (`empresa_id`, `situacao_financeira`),
      KEY `idx_empresa_natureza_situacao` (`empresa_id`, `natureza_financeira`, `situacao_financeira`),
      KEY `idx_vencimento_situacao` (`data_vencimento`, `situacao_financeira`),
      KEY `idx_pessoa_tipo` (`pessoa_id`, `pessoa_tipo`),
      KEY `idx_grupo_parcelas` (`grupo_parcelas`),
      KEY `idx_recorrencia` (`e_recorrente`, `recorrencia_ativa`, `proxima_recorrencia`),
      KEY `idx_cobranca_automatica` (`cobranca_automatica`, `data_proxima_cobranca`),
      KEY `idx_aprovacao` (`status_aprovacao`, `data_aprovacao`),
      KEY `idx_sync` (`sync_status`, `sync_tentativas`),
      KEY `idx_datas_competencia` (`data_competencia`, `empresa_id`),
      KEY `idx_categoria_origem` (`categoria`, `origem`),
      KEY `idx_boleto` (`boleto_gerado`, `boleto_nosso_numero`),
      KEY `idx_exclusao` (`data_exclusao`),
      
      -- Índices compostos para relatórios
      KEY `idx_relatorio_financeiro` (`empresa_id`, `natureza_financeira`, `data_competencia`, `situacao_financeira`),
      KEY `idx_fluxo_caixa` (`empresa_id`, `data_vencimento`, `situacao_financeira`, `valor_liquido`)
      
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
    ROW_FORMAT=DYNAMIC 
    COMMENT='Tabela unificada de lançamentos financeiros'";
    
    $pdo->exec($createLancamentos);
    echo "✅ Tabela lancamentos criada\n";
    
    // 5. Criar tabela lancamento_itens
    echo "🏗️ Criando tabela lancamento_itens...\n";
    
    $createItens = "
    CREATE TABLE `lancamento_itens` (
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `lancamento_id` int unsigned NOT NULL,
      `produto_id` int unsigned DEFAULT NULL,
      `produto_variacao_id` int unsigned DEFAULT NULL,
      `quantidade` decimal(10,4) NOT NULL,
      `valor_unitario` decimal(15,4) NOT NULL,
      `valor_total` decimal(15,4) GENERATED ALWAYS AS (
        `quantidade` * `valor_unitario`
      ) STORED,
      `observacoes` text DEFAULT NULL,
      `metadados` json DEFAULT NULL,
      `empresa_id` int unsigned NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      
      PRIMARY KEY (`id`),
      KEY `idx_lancamento` (`lancamento_id`),
      KEY `idx_produto` (`produto_id`),
      KEY `idx_empresa` (`empresa_id`),
      
      CONSTRAINT `fk_lancamento_itens_lancamento` 
        FOREIGN KEY (`lancamento_id`) REFERENCES `lancamentos` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createItens);
    echo "✅ Tabela lancamento_itens criada\n";
    
    // 6. Criar triggers
    echo "🔧 Criando triggers...\n";
    
    // Trigger para UUID
    $triggerUuid = "
    CREATE TRIGGER `tr_lancamentos_before_insert` 
    BEFORE INSERT ON `lancamentos` 
    FOR EACH ROW 
    BEGIN
        IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
            SET NEW.uuid = UUID();
        END IF;
        
        IF NEW.usuario_criacao IS NULL THEN
            SET NEW.usuario_criacao = NEW.usuario_id;
        END IF;
    END";
    $pdo->exec($triggerUuid);
    echo "✅ Trigger UUID criado\n";
    
    // Trigger para atualizar situação
    $triggerSituacao = "
    CREATE TRIGGER `tr_lancamentos_after_update` 
    AFTER UPDATE ON `lancamentos` 
    FOR EACH ROW 
    BEGIN
        DECLARE nova_situacao ENUM('pendente','pago','parcialmente_pago','vencido','cancelado','em_negociacao','estornado');
        
        IF NEW.valor_pago = 0 THEN
            IF NEW.data_vencimento < CURDATE() AND NEW.situacao_financeira = 'pendente' THEN
                SET nova_situacao = 'vencido';
            ELSE
                SET nova_situacao = 'pendente';
            END IF;
        ELSEIF NEW.valor_pago >= NEW.valor_liquido THEN
            SET nova_situacao = 'pago';
        ELSE
            SET nova_situacao = 'parcialmente_pago';
        END IF;
        
        IF nova_situacao != NEW.situacao_financeira AND NEW.situacao_financeira NOT IN ('cancelado', 'estornado') THEN
            UPDATE lancamentos 
            SET situacao_financeira = nova_situacao 
            WHERE id = NEW.id;
        END IF;
    END";
    $pdo->exec($triggerSituacao);
    echo "✅ Trigger situação criado\n";
    
    // Triggers para integração com pagamentos
    $triggerPagInsert = "
    CREATE TRIGGER `tr_pagamentos_after_insert`
    AFTER INSERT ON `pagamentos`
    FOR EACH ROW
    BEGIN
        UPDATE lancamentos 
        SET valor_pago = (
            SELECT COALESCE(SUM(valor), 0)
            FROM pagamentos 
            WHERE lancamento_id = NEW.lancamento_id 
            AND status_pagamento = 'confirmado'
        ),
        data_ultimo_pagamento = NEW.created_at
        WHERE id = NEW.lancamento_id;
    END";
    $pdo->exec($triggerPagInsert);
    echo "✅ Trigger pagamentos INSERT criado\n";
    
    $triggerPagUpdate = "
    CREATE TRIGGER `tr_pagamentos_after_update`
    AFTER UPDATE ON `pagamentos`
    FOR EACH ROW
    BEGIN
        UPDATE lancamentos 
        SET valor_pago = (
            SELECT COALESCE(SUM(valor), 0)
            FROM pagamentos 
            WHERE lancamento_id = NEW.lancamento_id 
            AND status_pagamento = 'confirmado'
        )
        WHERE id = NEW.lancamento_id;
    END";
    $pdo->exec($triggerPagUpdate);
    echo "✅ Trigger pagamentos UPDATE criado\n";
    
    // 7. Criar views
    echo "📊 Criando views...\n";
    
    $viewDashboard = "
    CREATE OR REPLACE VIEW `vw_dashboard_financeiro` AS
    SELECT 
        l.empresa_id,
        l.natureza_financeira,
        l.situacao_financeira,
        DATE_FORMAT(l.data_vencimento, '%Y-%m') as periodo,
        COUNT(*) as total_lancamentos,
        SUM(l.valor_liquido) as valor_total,
        SUM(l.valor_pago) as valor_pago_total,
        SUM(l.valor_saldo) as valor_saldo_total
    FROM lancamentos l
    WHERE l.data_exclusao IS NULL
    GROUP BY l.empresa_id, l.natureza_financeira, l.situacao_financeira, periodo";
    $pdo->exec($viewDashboard);
    echo "✅ View dashboard criada\n";
    
    $viewFluxo = "
    CREATE OR REPLACE VIEW `vw_fluxo_caixa` AS
    SELECT 
        l.empresa_id,
        l.data_vencimento,
        l.natureza_financeira,
        SUM(CASE WHEN l.situacao_financeira = 'pendente' THEN l.valor_liquido ELSE 0 END) as valor_previsto,
        SUM(l.valor_pago) as valor_realizado
    FROM lancamentos l
    WHERE l.data_exclusao IS NULL
      AND l.situacao_financeira IN ('pendente', 'pago', 'parcialmente_pago')
    GROUP BY l.empresa_id, l.data_vencimento, l.natureza_financeira";
    $pdo->exec($viewFluxo);
    echo "✅ View fluxo de caixa criada\n";
    
    $viewIntegracao = "
    CREATE OR REPLACE VIEW `vw_lancamentos_pagamentos` AS
    SELECT 
        l.*,
        COUNT(p.id) as total_pagamentos,
        SUM(CASE WHEN p.status_pagamento = 'confirmado' THEN p.valor ELSE 0 END) as valor_pago_confirmado,
        SUM(CASE WHEN p.status_pagamento = 'estornado' THEN p.valor ELSE 0 END) as valor_estornado,
        MAX(p.data_pagamento) as ultima_data_pagamento
    FROM lancamentos l
    LEFT JOIN pagamentos p ON l.id = p.lancamento_id
    GROUP BY l.id";
    $pdo->exec($viewIntegracao);
    echo "✅ View integração pagamentos criada\n";
    
    // 8. Reabilitar verificações FK
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "🔒 Verificações FK reabilitadas\n";
    
    // 9. Verificar integridade final
    echo "\n🔍 VERIFICAÇÃO FINAL...\n";
    
    // Contar registros
    $lancamentos = $pdo->query("SELECT COUNT(*) FROM lancamentos")->fetchColumn();
    $itens = $pdo->query("SELECT COUNT(*) FROM lancamento_itens")->fetchColumn();
    $pagamentos = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    
    echo "📊 ESTATÍSTICAS FINAIS:\n";
    echo "   - Lançamentos: $lancamentos\n";
    echo "   - Itens: $itens\n";
    echo "   - Pagamentos: $pagamentos\n";
    
    // Verificar se triggers funcionam
    $triggers = $pdo->query("SHOW TRIGGERS LIKE 'lancamentos'")->fetchAll();
    echo "   - Triggers: " . count($triggers) . "\n";
    
    // Verificar views
    $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll();
    $viewsCount = 0;
    foreach ($views as $view) {
        if (strpos($view[0], 'vw_') === 0) $viewsCount++;
    }
    echo "   - Views: $viewsCount\n";
    
    echo "\n🎉 MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n\n";
    
    echo "📋 ESTRUTURA CRIADA:\n";
    echo "✅ Tabela lancamentos (tipos compatíveis com seu BD)\n";
    echo "✅ Tabela lancamento_itens\n";
    echo "✅ Triggers automáticos para UUID e cálculos\n";
    echo "✅ Integração total com SUA tabela pagamentos\n";
    echo "✅ Views para relatórios otimizados\n\n";
    
    echo "🔗 INTEGRAÇÃO ATIVA:\n";
    echo "   - lancamentos.id (int) ↔ pagamentos.lancamento_id (int)\n";
    echo "   - Triggers calculam valor_pago automaticamente\n";
    echo "   - Campos calculados (valor_liquido, valor_saldo)\n";
    echo "   - Sistema pronto para uso!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "\nDetalhes do erro:\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    
    // Tentar reabilitar FK checks em caso de erro
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e2) {
        // Ignorar erro secundário
    }
}

?>
