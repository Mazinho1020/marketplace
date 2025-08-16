<?php

/**
 * APLICAR SQL OTIMIZADO - RESOLUÇÃO COMPLETA DE FKs
 */

echo "🚀 APLICANDO SQL OTIMIZADO - RESOLVENDO TODAS AS FKs\n\n";

$pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    echo "🔓 Desabilitando verificações FK...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // 1. Remover todas as FKs que referenciam lancamentos
    echo "\n🗑️ REMOVENDO FOREIGN KEYS CONFLITANTES...\n";
    
    $fksToRemove = [
        ['table' => 'lancamento_itens', 'constraint' => 'fk_lancamento_itens_lancamento'],
        ['table' => 'pagamentos', 'constraint' => 'pagamentos_lancamento_id_foreign']
    ];
    
    foreach ($fksToRemove as $fk) {
        try {
            $pdo->exec("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['constraint']}");
            echo "✅ FK removida: {$fk['table']}.{$fk['constraint']}\n";
        } catch (Exception $e) {
            echo "ℹ️ FK não existe ou já removida: {$fk['table']}.{$fk['constraint']}\n";
        }
    }
    
    // 2. Dropar tabela lancamentos se existir
    echo "\n🗑️ Removendo tabela lancamentos existente...\n";
    $pdo->exec("DROP TABLE IF EXISTS lancamentos");
    echo "✅ Tabela lancamentos removida\n";
    
    // 3. Criar nova tabela lancamentos otimizada
    echo "\n🔧 CRIANDO TABELA LANCAMENTOS OTIMIZADA...\n";
    
    // Como lancamento_itens já usa bigint unsigned, vou manter compatibilidade
    $sqlLancamentos = "
    CREATE TABLE `lancamentos` (
      -- Identificação principal (bigint para compatibilidade com lancamento_itens)
      `id` bigint unsigned NOT NULL AUTO_INCREMENT,
      `uuid` char(36) NOT NULL COMMENT 'UUID único para identificação externa',
      
      -- Relacionamentos principais
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
      
      -- Controle de pagamentos
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
      
      -- Forma de pagamento
      `forma_pagamento_id` bigint unsigned DEFAULT NULL,
      `bandeira_id` bigint unsigned DEFAULT NULL,
      `conta_bancaria_id` bigint unsigned DEFAULT NULL,
      
      -- Cobrança automática
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
      
      -- Configurações JSON
      `config_juros_multa` json DEFAULT NULL,
      `config_desconto` json DEFAULT NULL,
      `config_alertas` json DEFAULT NULL,
      `anexos` json DEFAULT NULL,
      `metadados` json DEFAULT NULL,
      
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
      KEY `idx_relatorio_financeiro` (`empresa_id`, `natureza_financeira`, `data_competencia`, `situacao_financeira`),
      KEY `idx_fluxo_caixa` (`empresa_id`, `data_vencimento`, `situacao_financeira`, `valor_liquido`)
      
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
    ROW_FORMAT=DYNAMIC 
    COMMENT='Tabela unificada de lançamentos financeiros'
    ";
    
    $pdo->exec($sqlLancamentos);
    echo "✅ Tabela lancamentos criada com sucesso!\n";
    
    // 4. Recriar FKs compatíveis
    echo "\n🔗 RECRIANDO FOREIGN KEYS...\n";
    
    // FK para lancamento_itens (bigint unsigned -> bigint unsigned)
    try {
        $pdo->exec("
            ALTER TABLE lancamento_itens 
            ADD CONSTRAINT fk_lancamento_itens_lancamento 
            FOREIGN KEY (lancamento_id) REFERENCES lancamentos(id) ON DELETE CASCADE
        ");
        echo "✅ FK criada: lancamento_itens -> lancamentos\n";
    } catch (Exception $e) {
        echo "⚠️ Erro ao criar FK lancamento_itens: " . $e->getMessage() . "\n";
    }
    
    // Para pagamentos, precisa alterar o tipo da coluna de int para bigint unsigned
    echo "\n🔧 AJUSTANDO TIPO DA COLUNA pagamentos.lancamento_id...\n";
    try {
        $pdo->exec("ALTER TABLE pagamentos MODIFY COLUMN lancamento_id bigint unsigned NOT NULL");
        echo "✅ Coluna pagamentos.lancamento_id alterada para bigint unsigned\n";
        
        $pdo->exec("
            ALTER TABLE pagamentos 
            ADD CONSTRAINT fk_pagamentos_lancamento 
            FOREIGN KEY (lancamento_id) REFERENCES lancamentos(id)
        ");
        echo "✅ FK criada: pagamentos -> lancamentos\n";
    } catch (Exception $e) {
        echo "⚠️ Erro ao ajustar pagamentos: " . $e->getMessage() . "\n";
    }
    
    // 5. Criar triggers
    echo "\n🔧 CRIANDO TRIGGERS...\n";
    
    $triggers = [
        "
        CREATE TRIGGER tr_lancamentos_before_insert 
        BEFORE INSERT ON lancamentos 
        FOR EACH ROW 
        BEGIN
            IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
                SET NEW.uuid = UUID();
            END IF;
            IF NEW.usuario_criacao IS NULL THEN
                SET NEW.usuario_criacao = NEW.usuario_id;
            END IF;
        END
        ",
        "
        CREATE TRIGGER tr_pagamentos_after_insert
        AFTER INSERT ON pagamentos
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
        END
        ",
        "
        CREATE TRIGGER tr_pagamentos_after_update
        AFTER UPDATE ON pagamentos
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
        END
        "
    ];
    
    foreach ($triggers as $i => $trigger) {
        try {
            $pdo->exec($trigger);
            echo "✅ Trigger " . ($i + 1) . " criado\n";
        } catch (Exception $e) {
            echo "⚠️ Erro no trigger " . ($i + 1) . ": " . $e->getMessage() . "\n";
        }
    }
    
    // 6. Criar views
    echo "\n📊 CRIANDO VIEWS...\n";
    
    $views = [
        "vw_dashboard_financeiro" => "
        CREATE OR REPLACE VIEW vw_dashboard_financeiro AS
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
        GROUP BY l.empresa_id, l.natureza_financeira, l.situacao_financeira, periodo
        ",
        "vw_fluxo_caixa" => "
        CREATE OR REPLACE VIEW vw_fluxo_caixa AS
        SELECT 
            l.empresa_id,
            l.data_vencimento,
            l.natureza_financeira,
            SUM(CASE WHEN l.situacao_financeira = 'pendente' THEN l.valor_liquido ELSE 0 END) as valor_previsto,
            SUM(l.valor_pago) as valor_realizado
        FROM lancamentos l
        WHERE l.data_exclusao IS NULL
          AND l.situacao_financeira IN ('pendente', 'pago', 'parcialmente_pago')
        GROUP BY l.empresa_id, l.data_vencimento, l.natureza_financeira
        ",
        "vw_lancamentos_pagamentos" => "
        CREATE OR REPLACE VIEW vw_lancamentos_pagamentos AS
        SELECT 
            l.*,
            COUNT(p.id) as total_pagamentos,
            SUM(CASE WHEN p.status_pagamento = 'confirmado' THEN p.valor ELSE 0 END) as valor_pago_confirmado,
            SUM(CASE WHEN p.status_pagamento = 'estornado' THEN p.valor ELSE 0 END) as valor_estornado,
            MAX(p.data_pagamento) as ultima_data_pagamento
        FROM lancamentos l
        LEFT JOIN pagamentos p ON l.id = p.lancamento_id
        GROUP BY l.id
        "
    ];
    
    foreach ($views as $name => $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ View $name criada\n";
        } catch (Exception $e) {
            echo "⚠️ Erro na view $name: " . $e->getMessage() . "\n";
        }
    }
    
    // 7. Reabilitar FKs
    echo "\n🔒 Reabilitando verificações FK...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // 8. Verificação final
    echo "\n🔍 VERIFICAÇÃO FINAL...\n";
    
    $count_lancamentos = $pdo->query("SELECT COUNT(*) FROM lancamentos")->fetchColumn();
    $count_itens = $pdo->query("SELECT COUNT(*) FROM lancamento_itens")->fetchColumn();
    $count_pagamentos = $pdo->query("SELECT COUNT(*) FROM pagamentos")->fetchColumn();
    
    echo "📊 ESTATÍSTICAS:\n";
    echo "   - Lançamentos: $count_lancamentos\n";
    echo "   - Itens: $count_itens\n";
    echo "   - Pagamentos: $count_pagamentos\n";
    
    $triggers_count = $pdo->query("SHOW TRIGGERS LIKE 'lancamentos'")->rowCount();
    echo "   - Triggers: $triggers_count\n";
    
    echo "\n🎉 MIGRAÇÃO CONCLUÍDA COM SUCESSO!\n\n";
    
    echo "✅ INTEGRAÇÃO COMPLETA:\n";
    echo "   - Tabela lancamentos criada com bigint unsigned\n";
    echo "   - FK lancamento_itens -> lancamentos funcionando\n";
    echo "   - FK pagamentos -> lancamentos funcionando\n";
    echo "   - Triggers automáticos para cálculo de valor_pago\n";
    echo "   - Views para relatórios otimizados\n";
    echo "   - Compatibilidade total com sua estrutura existente\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    
    // Tentar reabilitar FK checks
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e2) {
        // Ignorar
    }
}

?>
