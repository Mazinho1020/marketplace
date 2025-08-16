-- ========================================
-- TABELA DE LANÇAMENTOS OTIMIZADA
-- VERSÃO ADAPTADA PARA SUA ESTRUTURA EXISTENTE
-- ========================================

-- Estrutura principal otimizada (SEM lancamento_movimentacoes)
CREATE TABLE IF NOT EXISTS `lancamentos` (
  -- Identificação principal
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL COMMENT 'UUID único para identificação externa',

-- Relacionamentos principais
`empresa_id` int unsigned NOT NULL,
`usuario_id` int unsigned NOT NULL,
`mesa_id` int unsigned DEFAULT NULL,
`caixa_id` int unsigned DEFAULT NULL,

-- Identificação da pessoa/entidade
`pessoa_id` bigint unsigned DEFAULT NULL,
`pessoa_tipo` enum(
    'cliente',
    'fornecedor',
    'funcionario',
    'empresa'
) DEFAULT NULL,
`funcionario_id` bigint unsigned DEFAULT NULL,

-- Classificação do lançamento
`tipo_lancamento_id` int unsigned DEFAULT NULL,
`conta_gerencial_id` int unsigned DEFAULT NULL,
`natureza_financeira` enum('entrada', 'saida') NOT NULL COMMENT 'entrada=receber, saida=pagar',
`categoria` enum(
    'venda',
    'compra',
    'servico',
    'taxa',
    'imposto',
    'transferencia',
    'ajuste',
    'outros'
) NOT NULL DEFAULT 'outros',
`origem` enum(
    'pdv',
    'manual',
    'delivery',
    'api',
    'importacao',
    'recorrencia'
) NOT NULL DEFAULT 'manual',

-- Informações financeiras principais
`valor_bruto` decimal(15, 4) NOT NULL COMMENT 'Valor original sem descontos/acréscimos',
`valor_desconto` decimal(15, 4) NOT NULL DEFAULT '0.0000',
`valor_acrescimo` decimal(15, 4) NOT NULL DEFAULT '0.0000',
`valor_juros` decimal(15, 4) NOT NULL DEFAULT '0.0000',
`valor_multa` decimal(15, 4) NOT NULL DEFAULT '0.0000',
`valor_liquido` decimal(15, 4) GENERATED ALWAYS AS (
    `valor_bruto` - `valor_desconto` + `valor_acrescimo` + `valor_juros` + `valor_multa`
) STORED COMMENT 'Valor final calculado automaticamente',

-- Controle de pagamentos (calculado da SUA tabela pagamentos)
`valor_pago` decimal(15, 4) NOT NULL DEFAULT '0.0000',
`valor_saldo` decimal(15, 4) GENERATED ALWAYS AS (
    `valor_liquido` - `valor_pago`
) STORED,
`situacao_financeira` enum(
    'pendente',
    'pago',
    'parcialmente_pago',
    'vencido',
    'cancelado',
    'em_negociacao',
    'estornado'
) NOT NULL DEFAULT 'pendente',

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
`frequencia_recorrencia` enum(
    'diaria',
    'semanal',
    'quinzenal',
    'mensal',
    'bimestral',
    'trimestral',
    'semestral',
    'anual'
) DEFAULT NULL,
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
`status_aprovacao` enum(
    'pendente_aprovacao',
    'aprovado',
    'rejeitado',
    'nao_requer'
) NOT NULL DEFAULT 'nao_requer',
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
`sync_status` enum(
    'pendente',
    'sincronizado',
    'erro',
    'processando'
) NOT NULL DEFAULT 'pendente',
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
UNIQUE KEY `uk_empresa_numero_documento` (
    `empresa_id`,
    `numero_documento`
),

-- Índices de performance
KEY `idx_empresa_situacao` (
    `empresa_id`,
    `situacao_financeira`
),
KEY `idx_empresa_natureza_situacao` (
    `empresa_id`,
    `natureza_financeira`,
    `situacao_financeira`
),
KEY `idx_vencimento_situacao` (
    `data_vencimento`,
    `situacao_financeira`
),
KEY `idx_pessoa_tipo` (`pessoa_id`, `pessoa_tipo`),
KEY `idx_grupo_parcelas` (`grupo_parcelas`),
KEY `idx_recorrencia` (
    `e_recorrente`,
    `recorrencia_ativa`,
    `proxima_recorrencia`
),
KEY `idx_cobranca_automatica` (
    `cobranca_automatica`,
    `data_proxima_cobranca`
),
KEY `idx_aprovacao` (
    `status_aprovacao`,
    `data_aprovacao`
),
KEY `idx_sync` (
    `sync_status`,
    `sync_tentativas`
),
KEY `idx_datas_competencia` (
    `data_competencia`,
    `empresa_id`
),
KEY `idx_categoria_origem` (`categoria`, `origem`),
KEY `idx_boleto` (
    `boleto_gerado`,
    `boleto_nosso_numero`
),
KEY `idx_exclusao` (`data_exclusao`),

-- Índices compostos para relatórios
KEY `idx_relatorio_financeiro` (
    `empresa_id`,
    `natureza_financeira`,
    `data_competencia`,
    `situacao_financeira`
),
KEY `idx_fluxo_caixa` (
    `empresa_id`,
    `data_vencimento`,
    `situacao_financeira`,
    `valor_liquido`
),

-- Foreign Keys (adicionar conforme sua estrutura)
CONSTRAINT `fk_lancamentos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `fk_lancamentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
ROW_FORMAT=DYNAMIC 
COMMENT='Tabela unificada de lançamentos financeiros';

-- ========================================
-- TRIGGERS PARA AUTOMAÇÃO
-- ========================================

DELIMITER $$

-- Trigger para gerar UUID automaticamente
CREATE TRIGGER `tr_lancamentos_before_insert` 
BEFORE INSERT ON `lancamentos` 
FOR EACH ROW 
BEGIN
    IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
        SET NEW.uuid = UUID();
    END IF;
    
    -- Definir usuário de criação se não informado
    IF NEW.usuario_criacao IS NULL THEN
        SET NEW.usuario_criacao = NEW.usuario_id;
    END IF;
END$$

-- Trigger para atualizar situação baseada no valor pago (calculado da tabela pagamentos)
CREATE TRIGGER `tr_lancamentos_after_update` 
AFTER UPDATE ON `lancamentos` 
FOR EACH ROW 
BEGIN
    DECLARE nova_situacao ENUM('pendente','pago','parcialmente_pago','vencido','cancelado','em_negociacao','estornado');
    
    -- Calcular nova situação baseada no valor pago
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
    
    -- Atualizar se necessário
    IF nova_situacao != NEW.situacao_financeira AND NEW.situacao_financeira NOT IN ('cancelado', 'estornado') THEN
        UPDATE lancamentos 
        SET situacao_financeira = nova_situacao 
        WHERE id = NEW.id;
    END IF;
END$$

-- Trigger para atualizar valor_pago baseado na SUA tabela pagamentos
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
END$$

-- Trigger para atualizar valor_pago quando pagamento é modificado
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
END$$

DELIMITER;

-- ========================================
-- TABELA DE ITENS OTIMIZADA
-- ========================================

-- Tabela de itens mantida (sem nome_produto pois você não tem)
CREATE TABLE IF NOT EXISTS `lancamento_itens` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `lancamento_id` bigint unsigned NOT NULL,
    `produto_id` int unsigned DEFAULT NULL,
    `produto_variacao_id` int unsigned DEFAULT NULL,
    `quantidade` decimal(10, 4) NOT NULL,
    `valor_unitario` decimal(15, 4) NOT NULL,
    `valor_total` decimal(15, 4) GENERATED ALWAYS AS (
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
    CONSTRAINT `fk_lancamento_itens_lancamento` FOREIGN KEY (`lancamento_id`) REFERENCES `lancamentos` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ========================================
-- VIEWS PARA FACILITAR CONSULTAS
-- ========================================

-- View para dashboard financeiro
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
WHERE
    l.data_exclusao IS NULL
GROUP BY
    l.empresa_id,
    l.natureza_financeira,
    l.situacao_financeira,
    periodo;

-- View para fluxo de caixa
CREATE OR REPLACE VIEW `vw_fluxo_caixa` AS
SELECT
    l.empresa_id,
    l.data_vencimento,
    l.natureza_financeira,
    SUM(
        CASE
            WHEN l.situacao_financeira = 'pendente' THEN l.valor_liquido
            ELSE 0
        END
    ) as valor_previsto,
    SUM(l.valor_pago) as valor_realizado
FROM lancamentos l
WHERE
    l.data_exclusao IS NULL
    AND l.situacao_financeira IN (
        'pendente',
        'pago',
        'parcialmente_pago'
    )
GROUP BY
    l.empresa_id,
    l.data_vencimento,
    l.natureza_financeira;

-- View que integra com SUA tabela pagamentos
CREATE OR REPLACE VIEW `vw_lancamentos_pagamentos` AS
SELECT
    l.*,
    COUNT(p.id) as total_pagamentos,
    SUM(
        CASE
            WHEN p.status_pagamento = 'confirmado' THEN p.valor
            ELSE 0
        END
    ) as valor_pago_confirmado,
    SUM(
        CASE
            WHEN p.status_pagamento = 'estornado' THEN p.valor
            ELSE 0
        END
    ) as valor_estornado,
    MAX(p.data_pagamento) as ultima_data_pagamento
FROM lancamentos l
    LEFT JOIN pagamentos p ON l.id = p.lancamento_id
GROUP BY
    l.id;

-- ========================================
-- COMENTÁRIOS DE INTEGRAÇÃO
-- ========================================

/*
INTEGRAÇÃO COM SUA ESTRUTURA EXISTENTE:

1. SUA tabela 'pagamentos' é mantida INTACTA
2. Relacionamento: pagamentos.lancamento_id -> lancamentos.id
3. Triggers automáticos calculam valor_pago dos pagamentos confirmados
4. Status de estorno usa sua lógica existente (status_pagamento = 'estornado')
5. Formas de pagamento e bandeiras mantidas em pagamentos
6. Parcelas controladas via numero_parcela_pagamento

VANTAGENS:
✅ Zero migração de dados existentes
✅ Mantém todo histórico de pagamentos
✅ Aproveita lógica de estorno existente
✅ Performance otimizada (sem duplicação)
✅ Compatibilidade total com sistema atual

COMO USAR:
- Lançamentos são criados na tabela 'lancamentos'
- Pagamentos são registrados na SUA tabela 'pagamentos' existente
- Triggers mantêm valor_pago atualizado automaticamente
- Views facilitam consultas complexas
*/