-- SCRIPT PARA CRIAR TABELAS DO MARKETPLACE
-- Execute este script no seu HeidiSQL ou phpMyAdmin

-- ====================================
-- 1. TABELA MARCAS
-- ====================================
CREATE TABLE IF NOT EXISTS `marcas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da marca (ex: Pizzaria Tradição)',
    `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL amigável (ex: pizzaria-tradicao)',
    `descricao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrição da marca',
    `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL do logo da marca',
    `identidade_visual` json DEFAULT NULL COMMENT 'Cores, fontes, etc {"cor_primaria": "#ff0000"}',
    `pessoa_fisica_id` bigint(20) unsigned NOT NULL COMMENT 'FK para empresa_usuarios.id (dono da marca)',
    `status` enum(
        'ativa',
        'inativa',
        'suspensa'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
    `configuracoes` json DEFAULT NULL COMMENT 'Configurações específicas da marca',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `marcas_slug_unique` (`slug`),
    KEY `marcas_pessoa_fisica_status_idx` (`pessoa_fisica_id`, `status`),
    KEY `marcas_slug_idx` (`slug`),
    CONSTRAINT `marcas_pessoa_fisica_id_foreign` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ====================================
-- 2. TABELA EMPRESAS (Renomeando para não conflitar)
-- ====================================
CREATE TABLE IF NOT EXISTS `empresas_marketplace` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da empresa (ex: Pizzaria Tradição Concórdia)',
  `nome_fantasia` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome fantasia se diferente',
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CNPJ da unidade',
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL amigável (ex: pizzaria-tradicao-concordia)',
  `marca_id` bigint(20) unsigned DEFAULT NULL COMMENT 'FK para marcas.id (marca da empresa)',
  `proprietario_id` bigint(20) unsigned NOT NULL COMMENT 'FK para empresa_usuarios.id (dono da empresa)',

-- Endereço completo
`endereco_cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_logradouro` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`endereco_estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

-- Contato
`telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`website` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

-- Status e configurações
`status` enum('ativa','inativa','suspensa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `configuracoes` json DEFAULT NULL COMMENT 'Configurações específicas da empresa',
  `horario_funcionamento` json DEFAULT NULL COMMENT 'Horários por dia da semana',
  
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_marketplace_cnpj_unique` (`cnpj`),
  UNIQUE KEY `empresas_marketplace_slug_unique` (`slug`),
  KEY `empresas_marketplace_marca_status_idx` (`marca_id`, `status`),
  KEY `empresas_marketplace_proprietario_status_idx` (`proprietario_id`, `status`),
  KEY `empresas_marketplace_cnpj_idx` (`cnpj`),
  KEY `empresas_marketplace_slug_idx` (`slug`),
  
  CONSTRAINT `empresas_marketplace_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `empresas_marketplace_proprietario_id_foreign` FOREIGN KEY (`proprietario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- 3. TABELA EMPRESA_USER_VINCULOS
-- ====================================
CREATE TABLE IF NOT EXISTS `empresa_user_vinculos` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `empresa_id` bigint(20) unsigned NOT NULL COMMENT 'FK para empresas_marketplace.id',
    `user_id` bigint(20) unsigned NOT NULL COMMENT 'FK para empresa_usuarios.id',
    `perfil` enum(
        'proprietario',
        'administrador',
        'gerente',
        'colaborador'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colaborador' COMMENT 'Papel do usuário na empresa',
    `status` enum(
        'ativo',
        'inativo',
        'suspenso'
    ) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
    `permissoes` json DEFAULT NULL COMMENT 'Permissões específicas ["produtos.create", "vendas.view"]',
    `data_vinculo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `empresa_user_vinculo_unique` (`empresa_id`, `user_id`),
    KEY `empresa_user_vinculos_user_status_idx` (`user_id`, `status`),
    CONSTRAINT `empresa_user_vinculos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_marketplace` (`id`) ON DELETE CASCADE,
    CONSTRAINT `empresa_user_vinculos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ====================================
-- 4. DADOS DE EXEMPLO
-- ====================================

-- Verificar se existe usuário para exemplo (ajuste o ID conforme necessário)
-- INSERT INTO `marcas` (`nome`, `slug`, `descricao`, `pessoa_fisica_id`, `status`) VALUES
-- ('Pizzaria Tradição', 'pizzaria-tradicao', 'Rede de pizzarias tradicionais com receitas familiares', 3, 'ativa');

-- INSERT INTO `empresas_marketplace` (`nome`, `slug`, `marca_id`, `proprietario_id`, `endereco_cidade`, `endereco_estado`, `telefone`, `status`) VALUES
-- ('Pizzaria Tradição Concórdia', 'pizzaria-tradicao-concordia', 1, 3, 'Concórdia', 'SC', '(47) 3442-1234', 'ativa'),
-- ('Pizzaria Tradição Praça Central', 'pizzaria-tradicao-praca-central', 1, 3, 'Concórdia', 'SC', '(47) 3442-5678', 'ativa');

-- ====================================
-- VERIFICAÇÕES FINAIS
-- ====================================

-- Verificar se as tabelas foram criadas
SHOW TABLES LIKE '%marca%';

SHOW TABLES LIKE '%empresa%';

-- Verificar estrutura
-- DESCRIBE marcas;
-- DESCRIBE empresas_marketplace;
-- DESCRIBE empresa_user_vinculos;