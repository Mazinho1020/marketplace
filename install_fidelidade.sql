-- Sistema de Fidelidade Completo
-- Estrutura de tabelas com campos padronizados

-- Programas de Fidelidade
CREATE TABLE IF NOT EXISTS `fidelidade_programas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `empresa_id` bigint(20) unsigned NOT NULL,
    `nome` varchar(255) NOT NULL,
    `descricao` text,
    `tipo` enum(
        'pontos',
        'cashback',
        'hibrido'
    ) NOT NULL DEFAULT 'pontos',
    `regra_pontos_real` decimal(10, 2) DEFAULT 1.00 COMMENT 'Quantos pontos por R$1',
    `regra_real_pontos` decimal(10, 2) DEFAULT 100.00 COMMENT 'Quantos pontos valem R$1',
    `pontos_minimo_resgate` int(11) DEFAULT 100,
    `cashback_percentual` decimal(5, 2) DEFAULT 0.00,
    `status` enum('ativo', 'inativo', 'pausado') DEFAULT 'ativo',
    `data_inicio` date NOT NULL,
    `data_fim` date NULL,
    `configuracoes_json` json NULL COMMENT 'Configurações específicas',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_empresa_id` (`empresa_id`),
    KEY `idx_status` (`status`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Cartões de Fidelidade (identificação do cliente)
CREATE TABLE IF NOT EXISTS `fidelidade_cartoes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `programa_id` bigint(20) unsigned NOT NULL,
    `cliente_id` bigint(20) unsigned NOT NULL,
    `numero_cartao` varchar(100) NOT NULL UNIQUE,
    `codigo_barras` varchar(255) NULL,
    `qr_code` text NULL,
    `tipo` enum('fisico', 'virtual', 'app') DEFAULT 'virtual',
    `nivel_atual` varchar(50) DEFAULT 'bronze',
    `pontos_totais` int(11) DEFAULT 0,
    `xp_atual` int(11) DEFAULT 0,
    `status` enum(
        'ativo',
        'bloqueado',
        'cancelado'
    ) DEFAULT 'ativo',
    `data_ativacao` timestamp NULL,
    `data_ultimo_uso` timestamp NULL,
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_numero_cartao` (`numero_cartao`),
    KEY `idx_programa_cliente` (`programa_id`, `cliente_id`),
    KEY `idx_status` (`status`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Carteiras de Fidelidade (saldos e valores)
CREATE TABLE IF NOT EXISTS `fidelidade_carteiras` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cartao_id` bigint(20) unsigned NOT NULL,
    `saldo_pontos` int(11) DEFAULT 0,
    `saldo_cashback` decimal(10, 2) DEFAULT 0.00,
    `saldo_creditos` decimal(10, 2) DEFAULT 0.00,
    `saldo_bloqueado` decimal(10, 2) DEFAULT 0.00 COMMENT 'Valores em análise',
    `xp_total` int(11) DEFAULT 0,
    `nivel_atual` varchar(50) DEFAULT 'bronze',
    `xp_proximo_nivel` int(11) DEFAULT 1000,
    `data_ultima_movimentacao` timestamp NULL,
    `data_expiracao_pontos` date NULL COMMENT 'Próxima expiração de pontos',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cartao_id` (`cartao_id`),
    KEY `idx_nivel` (`nivel_atual`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Regras de Cashback
CREATE TABLE IF NOT EXISTS `fidelidade_cashback_regras` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `programa_id` bigint(20) unsigned NOT NULL,
    `nome` varchar(255) NOT NULL,
    `descricao` text NULL,
    `tipo` enum(
        'percentual',
        'fixo',
        'progressivo'
    ) DEFAULT 'percentual',
    `valor` decimal(10, 2) NOT NULL COMMENT 'Percentual ou valor fixo',
    `valor_minimo_compra` decimal(10, 2) DEFAULT 0.00,
    `valor_maximo_compra` decimal(10, 2) NULL,
    `limite_diario` decimal(10, 2) NULL,
    `limite_mensal` decimal(10, 2) NULL,
    `limite_por_transacao` decimal(10, 2) NULL,
    `aplicar_em` enum(
        'total',
        'produtos',
        'categorias',
        'marcas'
    ) DEFAULT 'total',
    `produtos_ids` json NULL COMMENT 'IDs específicos quando aplicar_em=produtos',
    `categorias_ids` json NULL COMMENT 'IDs específicos quando aplicar_em=categorias',
    `marcas_ids` json NULL COMMENT 'IDs específicos quando aplicar_em=marcas',
    `dias_semana` json NULL COMMENT '[1,2,3,4,5,6,7] - null = todos os dias',
    `horario_inicio` time NULL,
    `horario_fim` time NULL,
    `data_inicio` date NOT NULL,
    `data_fim` date NULL,
    `nivel_minimo_cliente` varchar(50) NULL COMMENT 'bronze, prata, ouro, etc',
    `prioridade` int(11) DEFAULT 1 COMMENT 'Ordem de aplicação das regras',
    `status` enum('ativo', 'inativo', 'pausado') DEFAULT 'ativo',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_programa_id` (`programa_id`),
    KEY `idx_status` (`status`),
    KEY `idx_datas` (`data_inicio`, `data_fim`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Transações de Cashback
CREATE TABLE IF NOT EXISTS `fidelidade_cashback_transacoes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `carteira_id` bigint(20) unsigned NOT NULL,
    `regra_id` bigint(20) unsigned NULL COMMENT 'Regra que gerou o cashback',
    `pedido_id` bigint(20) unsigned NULL COMMENT 'Venda que originou',
    `lancamento_id` bigint(20) unsigned NULL COMMENT 'Lançamento financeiro',
    `tipo` enum(
        'credito',
        'debito',
        'estorno',
        'ajuste',
        'expiracao'
    ) NOT NULL,
    `valor_transacao` decimal(10, 2) NOT NULL COMMENT 'Valor da compra que gerou',
    `valor_cashback` decimal(10, 2) NOT NULL COMMENT 'Valor do cashback calculado',
    `percentual_aplicado` decimal(5, 2) NULL COMMENT 'Percentual usado no cálculo',
    `saldo_anterior` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `saldo_posterior` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `status` enum(
        'pendente',
        'confirmado',
        'cancelado',
        'expirado'
    ) DEFAULT 'pendente',
    `data_vencimento` date NULL COMMENT 'Data de expiração do cashback',
    `data_confirmacao` timestamp NULL,
    `data_utilizacao` timestamp NULL,
    `motivo` varchar(500) NULL COMMENT 'Descrição da transação',
    `dados_complementares` json NULL COMMENT 'Dados extras da transação',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_carteira_id` (`carteira_id`),
    KEY `idx_pedido_id` (`pedido_id`),
    KEY `idx_status` (`status`),
    KEY `idx_data_vencimento` (`data_vencimento`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Créditos Adicionais
CREATE TABLE IF NOT EXISTS `fidelidade_creditos` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `carteira_id` bigint(20) unsigned NOT NULL,
    `tipo` enum(
        'bonus',
        'promocao',
        'ajuste',
        'compra',
        'resgate',
        'presente'
    ) NOT NULL,
    `valor` decimal(10, 2) NOT NULL,
    `descricao` varchar(500) NOT NULL,
    `origem` varchar(100) NULL COMMENT 'Sistema, Admin, API, etc',
    `referencia_externa` varchar(255) NULL COMMENT 'ID externo de referência',
    `data_vencimento` date NULL,
    `status` enum(
        'ativo',
        'usado',
        'expirado',
        'cancelado'
    ) DEFAULT 'ativo',
    `data_utilizacao` timestamp NULL,
    `utilizado_em` varchar(500) NULL COMMENT 'Onde foi usado o crédito',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_carteira_id` (`carteira_id`),
    KEY `idx_tipo` (`tipo`),
    KEY `idx_status` (`status`),
    KEY `idx_data_vencimento` (`data_vencimento`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Conquistas/Troféus do Sistema
CREATE TABLE IF NOT EXISTS `fidelidade_conquistas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `programa_id` bigint(20) unsigned NOT NULL,
    `nome` varchar(255) NOT NULL,
    `descricao` text NOT NULL,
    `icone` varchar(255) NULL COMMENT 'Arquivo ou código do ícone',
    `categoria` varchar(100) NULL COMMENT 'Categoria da conquista',
    `tipo_requisito` enum(
        'compras_quantidade',
        'compras_valor',
        'pontos_acumulados',
        'visitas_frequencia',
        'produtos_especificos',
        'tempo_fidelidade',
        'indicacoes',
        'nivel_atingido',
        'custom'
    ) NOT NULL,
    `valor_requisito` decimal(15, 2) NOT NULL COMMENT 'Valor necessário para conquistar',
    `unidade_requisito` varchar(50) NULL COMMENT 'dias, reais, pontos, unidades, etc',
    `requisitos_detalhados` json NULL COMMENT 'Detalhes específicos da conquista',
    `recompensa_tipo` enum(
        'pontos',
        'cashback',
        'credito',
        'cupom',
        'desconto',
        'produto',
        'badge'
    ) NOT NULL,
    `recompensa_valor` decimal(10, 2) DEFAULT 0.00,
    `recompensa_descricao` varchar(500) NULL,
    `nivel_minimo` varchar(50) NULL COMMENT 'Nível mínimo para tentar a conquista',
    `limite_conquistas` int(11) DEFAULT 1 COMMENT 'Quantas vezes pode conquistar (1=única vez)',
    `validade_dias` int(11) NULL COMMENT 'Dias para usar a recompensa',
    `ordem_exibicao` int(11) DEFAULT 0,
    `status` enum(
        'ativo',
        'inativo',
        'rascunho'
    ) DEFAULT 'ativo',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_programa_id` (`programa_id`),
    KEY `idx_tipo_requisito` (`tipo_requisito`),
    KEY `idx_categoria` (`categoria`),
    KEY `idx_status` (`status`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Conquistas dos Clientes
CREATE TABLE IF NOT EXISTS `fidelidade_cliente_conquistas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cartao_id` bigint(20) unsigned NOT NULL,
    `conquista_id` bigint(20) unsigned NOT NULL,
    `data_conquista` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `progresso_atual` decimal(15, 2) DEFAULT 0.00 COMMENT 'Progresso em direção à conquista',
    `progresso_necessario` decimal(15, 2) NOT NULL COMMENT 'Total necessário',
    `status` enum(
        'em_progresso',
        'conquistado',
        'recompensa_resgatada',
        'expirado'
    ) DEFAULT 'em_progresso',
    `data_resgate_recompensa` timestamp NULL,
    `recompensa_detalhes` json NULL COMMENT 'Detalhes da recompensa concedida',
    `dados_conquista` json NULL COMMENT 'Dados específicos quando conquistou',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cartao_conquista` (`cartao_id`, `conquista_id`),
    KEY `idx_status` (`status`),
    KEY `idx_data_conquista` (`data_conquista`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Cupons de Desconto
CREATE TABLE IF NOT EXISTS `fidelidade_cupons` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `programa_id` bigint(20) unsigned NOT NULL,
    `codigo` varchar(50) NOT NULL UNIQUE,
    `nome` varchar(255) NOT NULL,
    `descricao` text NULL,
    `tipo_desconto` enum(
        'percentual',
        'fixo',
        'frete_gratis',
        'produto_gratis'
    ) DEFAULT 'percentual',
    `valor` decimal(10, 2) NOT NULL COMMENT 'Valor ou percentual do desconto',
    `valor_minimo_compra` decimal(10, 2) DEFAULT 0.00,
    `valor_maximo_desconto` decimal(10, 2) NULL COMMENT 'Limite máximo do desconto',
    `quantidade_total` int(11) NULL COMMENT 'Total de cupons disponíveis (null=ilimitado)',
    `quantidade_usada` int(11) DEFAULT 0,
    `limite_uso_cliente` int(11) DEFAULT 1 COMMENT 'Quantas vezes cada cliente pode usar',
    `clientes_permitidos` json NULL COMMENT 'IDs específicos (null=todos)',
    `produtos_aplicaveis` json NULL COMMENT 'IDs de produtos (null=todos)',
    `categorias_aplicaveis` json NULL COMMENT 'IDs de categorias (null=todas)',
    `marcas_aplicaveis` json NULL COMMENT 'IDs de marcas (null=todas)',
    `nivel_minimo_cliente` varchar(50) NULL COMMENT 'Nível mínimo para usar',
    `pontos_necessarios` int(11) DEFAULT 0 COMMENT 'Pontos necessários para resgatar',
    `data_inicio` date NOT NULL,
    `data_fim` date NULL,
    `horario_inicio` time NULL,
    `horario_fim` time NULL,
    `dias_semana` json NULL COMMENT '[1,2,3,4,5,6,7] - null = todos os dias',
    `primeiro_pedido_apenas` boolean DEFAULT FALSE,
    `combina_com_outras_promocoes` boolean DEFAULT TRUE,
    `status` enum(
        'ativo',
        'inativo',
        'pausado',
        'esgotado'
    ) DEFAULT 'ativo',
    `sync_hash` varchar(64) NULL,
    `sync_status` enum('pending', 'synced', 'error') DEFAULT 'pending',
    `sync_data` json NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo` (`codigo`),
    KEY `idx_programa_id` (`programa_id`),
    KEY `idx_status` (`status`),
    KEY `idx_datas` (`data_inicio`, `data_fim`),
    KEY `idx_tipo_desconto` (`tipo_desconto`),
    KEY `idx_sync_status` (`sync_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;