-- Tabelas faltantes no banco de dados
-- Extraídas do arquivo teste2.sql
-- Data: 2025-08-03

-- 1. afi_plan_assinaturas
CREATE TABLE IF NOT EXISTS `afi_plan_assinaturas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `funforcli_id` int(11) NOT NULL,
    `plano_id` int(11) NOT NULL,
    `ciclo_cobranca` enum(
        'mensal',
        'anual',
        'vitalicio'
    ) DEFAULT 'mensal',
    `valor` decimal(10, 2) NOT NULL,
    `status` enum(
        'trial',
        'ativo',
        'suspenso',
        'expirado',
        'cancelado'
    ) DEFAULT 'trial',
    `trial_expira_em` timestamp NULL DEFAULT NULL,
    `iniciado_em` timestamp NULL DEFAULT NULL,
    `expira_em` timestamp NULL DEFAULT NULL,
    `proxima_cobranca_em` timestamp NULL DEFAULT NULL,
    `ultima_cobranca_em` timestamp NULL DEFAULT NULL,
    `cancelado_em` timestamp NULL DEFAULT NULL,
    `renovacao_automatica` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_funforcli` (`funforcli_id`),
    KEY `idx_plano` (`plano_id`),
    KEY `idx_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 2. afi_plan_configuracoes
CREATE TABLE IF NOT EXISTS `afi_plan_configuracoes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `chave` varchar(100) NOT NULL,
    `valor` text DEFAULT NULL,
    `tipo` enum(
        'string',
        'number',
        'boolean',
        'json'
    ) DEFAULT 'string',
    `descricao` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_empresa_chave` (`empresa_id`, `chave`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 3. afi_plan_gateways
CREATE TABLE IF NOT EXISTS `afi_plan_gateways` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `codigo` varchar(50) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `provedor` varchar(50) NOT NULL,
    `ambiente` enum('sandbox', 'producao') DEFAULT 'sandbox',
    `ativo` tinyint(1) DEFAULT 1,
    `credenciais` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`credenciais`)),
    `configuracoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracoes`)),
    `url_webhook` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_empresa_codigo` (`empresa_id`, `codigo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 4. afi_plan_planos
CREATE TABLE IF NOT EXISTS `afi_plan_planos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `codigo` varchar(50) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `descricao` text DEFAULT NULL,
    `preco_mensal` decimal(10, 2) DEFAULT 0.00,
    `preco_anual` decimal(10, 2) DEFAULT 0.00,
    `preco_vitalicio` decimal(10, 2) DEFAULT 0.00,
    `dias_trial` int(11) DEFAULT 0,
    `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recursos`)),
    `limites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`limites`)),
    `ativo` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_empresa_codigo` (`empresa_id`, `codigo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 5. afi_plan_transacoes
CREATE TABLE IF NOT EXISTS `afi_plan_transacoes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `uuid` varchar(36) NOT NULL,
    `empresa_id` int(11) NOT NULL,
    `codigo_transacao` varchar(100) NOT NULL,
    `cliente_id` int(11) DEFAULT NULL,
    `gateway_id` int(11) DEFAULT NULL,
    `gateway_transacao_id` varchar(255) DEFAULT NULL,
    `tipo_origem` enum(
        'nova_assinatura',
        'renovacao_assinatura',
        'comissao_afiliado',
        'venda_avulsa'
    ) DEFAULT 'venda_avulsa',
    `id_origem` int(11) DEFAULT NULL,
    `valor_original` decimal(10, 2) NOT NULL,
    `valor_desconto` decimal(10, 2) DEFAULT 0.00,
    `valor_taxas` decimal(10, 2) DEFAULT 0.00,
    `valor_final` decimal(10, 2) NOT NULL,
    `moeda` varchar(3) DEFAULT 'BRL',
    `forma_pagamento` varchar(50) DEFAULT NULL,
    `status` enum(
        'rascunho',
        'pendente',
        'processando',
        'aprovado',
        'recusado',
        'cancelado',
        'estornado'
    ) DEFAULT 'rascunho',
    `gateway_status` varchar(50) DEFAULT NULL,
    `cliente_nome` varchar(255) DEFAULT NULL,
    `cliente_email` varchar(255) DEFAULT NULL,
    `descricao` text DEFAULT NULL,
    `metadados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadados`)),
    `expira_em` timestamp NULL DEFAULT NULL,
    `processado_em` timestamp NULL DEFAULT NULL,
    `aprovado_em` timestamp NULL DEFAULT NULL,
    `cancelado_em` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo_transacao` (`codigo_transacao`),
    KEY `idx_status` (`status`),
    KEY `idx_cliente` (`cliente_id`),
    KEY `idx_gateway` (`gateway_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 6. afi_plan_vendas
CREATE TABLE IF NOT EXISTS `afi_plan_vendas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `afiliado_id` int(11) NOT NULL,
    `cliente_id` int(11) NOT NULL,
    `assinatura_id` int(11) DEFAULT NULL,
    `transacao_id` int(11) DEFAULT NULL,
    `valor_venda` decimal(10, 2) NOT NULL,
    `taxa_comissao` decimal(5, 2) NOT NULL,
    `valor_comissao` decimal(10, 2) NOT NULL,
    `status` enum(
        'pendente',
        'confirmado',
        'cancelado',
        'estornado'
    ) DEFAULT 'pendente',
    `confirmado_em` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_afiliado` (`afiliado_id`),
    KEY `idx_cliente` (`cliente_id`),
    KEY `idx_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 7. caixas
CREATE TABLE IF NOT EXISTS `caixas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `data_abertura` datetime NOT NULL DEFAULT current_timestamp(),
    `data_fechamento` datetime DEFAULT NULL,
    `valor_abertura` decimal(10, 2) NOT NULL,
    `valor_informado` decimal(10, 2) DEFAULT NULL,
    `status` enum('aberto', 'fechado') NOT NULL DEFAULT 'aberto',
    `observacoes` text DEFAULT NULL,
    `valor_vendas` decimal(10, 2) DEFAULT NULL,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_sync` (`sync_status`, `sync_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 8. caixa_fechamento
CREATE TABLE IF NOT EXISTS `caixa_fechamento` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `caixa_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `data_fechamento` datetime NOT NULL,
    `observacoes` text DEFAULT NULL,
    `conferido` tinyint(1) DEFAULT 0,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_sync` (`sync_status`, `sync_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 9. caixa_fechamento_formas
CREATE TABLE IF NOT EXISTS `caixa_fechamento_formas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `fechamento_id` int(11) NOT NULL,
    `forma_pagamento_id` int(11) NOT NULL,
    `bandeira_id` int(11) DEFAULT NULL,
    `valor_informado` decimal(10, 2) NOT NULL,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_sync` (`sync_status`, `sync_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 10. caixa_movimentos
CREATE TABLE IF NOT EXISTS `caixa_movimentos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `caixa_id` int(11) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    `tipo` enum('sangria', 'suprimento') NOT NULL,
    `valor` decimal(10, 2) NOT NULL,
    `observacao` varchar(255) DEFAULT NULL,
    `data_movimento` datetime NOT NULL DEFAULT current_timestamp(),
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_sync` (`sync_status`, `sync_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 11. categorias_sugeridas
CREATE TABLE IF NOT EXISTS `categorias_sugeridas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `palavra_chave` varchar(255) NOT NULL,
    `conta_gerencial_id` int(11) NOT NULL,
    `empresa_id` int(11) DEFAULT NULL,
    `usuario_id` int(11) DEFAULT NULL,
    `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
    `funcionario_id` int(11) DEFAULT NULL,
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 12. classificacoes_dre
CREATE TABLE IF NOT EXISTS `classificacoes_dre` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) DEFAULT NULL,
    `descricao` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `tipo_id` int(11) DEFAULT NULL,
    `empresa_id` int(11) DEFAULT NULL,
    `sync_hash` varchar(32) DEFAULT NULL,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_sync` (`sync_status`, `sync_data`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 13. clientes
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `telefone` varchar(20) DEFAULT NULL,
    `documento` varchar(20) DEFAULT NULL,
    `endereco` text DEFAULT NULL,
    `empresa_id` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 14. com_planos
CREATE TABLE IF NOT EXISTS `com_planos` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `codigo` varchar(50) NOT NULL,
    `nome` varchar(100) DEFAULT NULL,
    `descricao` text DEFAULT NULL,
    `preco` decimal(10, 2) DEFAULT 0.00,
    `ativo` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_empresa_codigo` (`empresa_id`, `codigo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

-- 15. config
CREATE TABLE IF NOT EXISTS `config` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `value` text DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `empresa_id` int(11) DEFAULT NULL,
    `origem` varchar(50) DEFAULT NULL,
    `ativo` tinyint(1) DEFAULT 1,
    `sync_data` timestamp NOT NULL DEFAULT current_timestamp(),
    `sync_hash` varchar(32) DEFAULT NULL,
    `sync_status` enum(
        'pendente',
        'sincronizado',
        'erro'
    ) DEFAULT 'pendente',
    `codigo_sistema` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- 16. config_definitions
CREATE TABLE IF NOT EXISTS `config_definitions` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `empresa_id` int(11) NOT NULL,
    `chave` varchar(255) NOT NULL,
    `nome` varchar(255) NOT NULL,
    `descricao` text DEFAULT NULL,
    `tipo` varchar(50) DEFAULT 'string',
    `grupo_id` int(11) DEFAULT NULL,
    `valor_padrao` text DEFAULT NULL,
    `obrigatorio` tinyint(1) DEFAULT 0,
    `min_length` int(11) DEFAULT NULL,
    `max_length` int(11) DEFAULT NULL,
    `regex_validacao` varchar(255) DEFAULT NULL,
    `opcoes` text DEFAULT NULL,
    `editavel` tinyint(1) DEFAULT 1,
    `avancado` tinyint(1) DEFAULT 0,
    `ordem` int(11) DEFAULT 0,
    `dica` text DEFAULT NULL,
    `ajuda` text DEFAULT NULL,
    `ativo` tinyint(1) DEFAULT 1,
    `sync_hash` varchar(32) DEFAULT NULL,
    `sync_status` varchar(50) DEFAULT 'pendente',
    `sync_data` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `config_definitions_grupo_id_index` (`grupo_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;