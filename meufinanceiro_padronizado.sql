# Host: localhost  (Version 5.5.5-10.4.32-MariaDB)
# Date: 2025-08-01 20:32:54
# Generator: MySQL-Front 6.0  (Build 2.20)
# Modificado para seguir padrão com sync_hash, sync_status, sync_data, created_at, updated_at

#
# Structure for table "fidelidade_carteiras"
#

DROP TABLE IF EXISTS `fidelidade_carteiras`;

CREATE TABLE `fidelidade_carteiras` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cliente_id` int(11) NOT NULL COMMENT 'Referência ao ID na tabela funforcli',
    `empresa_id` int(11) NOT NULL DEFAULT 1,
    `saldo_cashback` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `saldo_creditos` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `saldo_bloqueado` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `saldo_total_disponivel` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `nivel_atual` varchar(20) NOT NULL DEFAULT 'bronze',
    `xp_total` int(11) NOT NULL DEFAULT 0,
    `status` varchar(20) NOT NULL DEFAULT 'ativa',
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cliente_id` (`cliente_id`, `empresa_id`),
    CONSTRAINT `fk_fidelidade_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `funforcli` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

#
# Data for table "fidelidade_carteiras"
#

INSERT INTO `fidelidade_carteiras` (`id`, `cliente_id`, `empresa_id`, `saldo_cashback`, `saldo_creditos`, `saldo_bloqueado`, `saldo_total_disponivel`, `nivel_atual`, `xp_total`, `status`, `created_at`, `updated_at`) VALUES 
(1,39,1,150.00,98.00,0.00,150.00,'bronze',363,'ativa','2025-08-01 23:06:37','2025-08-01 23:06:37'),
(2,49,1,105.00,114.00,0.00,105.00,'prata',955,'ativa','2025-08-01 23:12:10','2025-08-01 23:12:10'),
(3,50,1,876.00,121.00,0.00,876.00,'ouro',1625,'ativa','2025-08-01 23:12:10','2025-08-01 23:12:10'),
(4,51,1,148.00,148.00,0.00,148.00,'ouro',2983,'ativa','2025-08-01 23:12:10','2025-08-01 23:12:10');

#
# Structure for table "fidelidade_cashback_regras"
#

DROP TABLE IF EXISTS `fidelidade_cashback_regras`;

CREATE TABLE `fidelidade_cashback_regras` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
    `nome` varchar(255) NOT NULL,
    `descricao` text DEFAULT NULL,
    `tipo_cashback` varchar(30) NOT NULL DEFAULT 'percentual',
    `valor_cashback` decimal(10, 2) NOT NULL,
    `valor_minimo` decimal(10, 2) DEFAULT NULL,
    `valor_maximo` decimal(10, 2) DEFAULT NULL,
    `limite_mensal` decimal(10, 2) DEFAULT NULL,
    `data_inicio` date DEFAULT NULL,
    `data_fim` date DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'ativo',
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_cashback_regras_empresa_id_index` (`empresa_id`),
    KEY `fidelidade_cashback_regras_status_index` (`status`),
    KEY `fidelidade_cashback_regras_data_inicio_data_fim_index` (`data_inicio`, `data_fim`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_cashback_regras"
#

INSERT INTO `fidelidade_cashback_regras` (`id`, `empresa_id`, `nome`, `descricao`, `tipo_cashback`, `valor_cashback`, `valor_minimo`, `valor_maximo`, `limite_mensal`, `data_inicio`, `data_fim`, `status`, `created_at`, `updated_at`) VALUES 
(1,1,'Cashback Padrão','Cashback de 2% em todas as compras','percentual',2.00,10.00,NULL,NULL,NULL,NULL,'ativo','2025-08-01 12:58:39','2025-08-01 12:58:39'),
(2,1,'Cashback Premium','Cashback de 5% para compras acima de R$ 100','percentual',5.00,100.00,NULL,NULL,NULL,NULL,'ativo','2025-08-01 12:58:39','2025-08-01 12:58:39');

#
# Structure for table "fidelidade_cashback_transacoes"
#

DROP TABLE IF EXISTS `fidelidade_cashback_transacoes`;

CREATE TABLE `fidelidade_cashback_transacoes` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cliente_id` bigint(20) unsigned NOT NULL,
    `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
    `pedido_id` int(11) DEFAULT NULL,
    `tipo` enum('credito', 'debito') NOT NULL,
    `valor` decimal(10, 2) NOT NULL,
    `valor_pedido_original` decimal(10, 2) DEFAULT NULL,
    `percentual_aplicado` decimal(5, 2) DEFAULT NULL,
    `saldo_anterior` decimal(10, 2) DEFAULT NULL,
    `saldo_posterior` decimal(10, 2) DEFAULT NULL,
    `data_expiracao` date DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'disponivel',
    `observacoes` varchar(255) DEFAULT NULL,
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_cashback_transacoes_cliente_id_index` (`cliente_id`),
    KEY `fidelidade_cashback_transacoes_empresa_id_index` (`empresa_id`),
    KEY `fidelidade_cashback_transacoes_pedido_id_index` (`pedido_id`),
    KEY `fidelidade_cashback_transacoes_status_index` (`status`),
    KEY `fidelidade_cashback_transacoes_data_expiracao_index` (`data_expiracao`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_cashback_transacoes"
#

#
# Structure for table "fidelidade_cliente_conquistas"
#

DROP TABLE IF EXISTS `fidelidade_cliente_conquistas`;

CREATE TABLE `fidelidade_cliente_conquistas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cliente_id` bigint(20) unsigned NOT NULL,
    `conquista_id` bigint(20) unsigned NOT NULL,
    `data_desbloqueio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `recompensa_resgatada` tinyint(1) NOT NULL DEFAULT 0,
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cliente_conquista` (`cliente_id`, `conquista_id`),
    KEY `fidelidade_cliente_conquistas_cliente_id_index` (`cliente_id`),
    KEY `fidelidade_cliente_conquistas_conquista_id_index` (`conquista_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_cliente_conquistas"
#

#
# Structure for table "fidelidade_conquistas"
#

DROP TABLE IF EXISTS `fidelidade_conquistas`;

CREATE TABLE `fidelidade_conquistas` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `descricao` text DEFAULT NULL,
    `icone` varchar(50) DEFAULT NULL,
    `pontos_recompensa` int(11) NOT NULL DEFAULT 0,
    `credito_recompensa` decimal(10, 2) NOT NULL DEFAULT 0.00,
    `tipo_requisito` varchar(50) DEFAULT NULL,
    `valor_requisito` int(11) DEFAULT NULL,
    `ativo` tinyint(1) NOT NULL DEFAULT 1,
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_conquistas_ativo_index` (`ativo`),
    KEY `fidelidade_conquistas_tipo_requisito_index` (`tipo_requisito`)
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_conquistas"
#

INSERT INTO `fidelidade_conquistas` (`id`, `nome`, `descricao`, `icone`, `pontos_recompensa`, `credito_recompensa`, `tipo_requisito`, `valor_requisito`, `ativo`, `created_at`, `updated_at`) VALUES 
(1,'Primeira Compra','Realize sua primeira compra','trophy',100,5.00,'primeira_compra',1,1,'2025-08-01 12:58:40','2025-08-01 12:58:40'),
(2,'Cliente Fiel','Realize 10 compras','star',500,25.00,'total_compras',10,1,'2025-08-01 12:58:40','2025-08-01 12:58:40'),
(3,'Grande Comprador','Gaste mais de R$ 500 em compras','diamond',1000,50.00,'valor_total',500,1,'2025-08-01 12:58:40','2025-08-01 12:58:40');

#
# Structure for table "fidelidade_creditos"
#

DROP TABLE IF EXISTS `fidelidade_creditos`;

CREATE TABLE `fidelidade_creditos` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cliente_id` bigint(20) unsigned NOT NULL,
    `tipo` varchar(30) NOT NULL DEFAULT 'comprado',
    `valor_original` decimal(10, 2) NOT NULL,
    `valor_atual` decimal(10, 2) NOT NULL,
    `codigo_ativacao` varchar(50) DEFAULT NULL,
    `data_expiracao` date DEFAULT NULL,
    `pedido_origem_id` int(11) DEFAULT NULL,
    `observacoes` text DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'ativo',
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_creditos_cliente_id_index` (`cliente_id`),
    KEY `fidelidade_creditos_codigo_ativacao_index` (`codigo_ativacao`),
    KEY `fidelidade_creditos_status_index` (`status`),
    KEY `fidelidade_creditos_data_expiracao_index` (`data_expiracao`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_creditos"
#

#
# Structure for table "fidelidade_cupons"
#

DROP TABLE IF EXISTS `fidelidade_cupons`;

CREATE TABLE `fidelidade_cupons` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
    `codigo` varchar(50) NOT NULL,
    `nome` varchar(100) NOT NULL,
    `descricao` varchar(255) DEFAULT NULL,
    `tipo` varchar(30) NOT NULL DEFAULT 'desconto_sacola',
    `valor_desconto` decimal(10, 2) DEFAULT NULL,
    `percentual_desconto` decimal(5, 2) DEFAULT NULL,
    `valor_minimo_pedido` decimal(10, 2) DEFAULT NULL,
    `quantidade_maxima_uso` int(11) DEFAULT NULL,
    `quantidade_usada` int(11) NOT NULL DEFAULT 0,
    `uso_por_cliente` int(11) NOT NULL DEFAULT 1,
    `data_inicio` datetime DEFAULT NULL,
    `data_fim` datetime DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'ativo',
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_cupons_empresa_id_index` (`empresa_id`),
    KEY `fidelidade_cupons_codigo_index` (`codigo`),
    KEY `fidelidade_cupons_status_index` (`status`),
    KEY `fidelidade_cupons_data_inicio_data_fim_index` (`data_inicio`, `data_fim`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_cupons"
#

INSERT INTO `fidelidade_cupons` (`id`, `empresa_id`, `codigo`, `nome`, `descricao`, `tipo`, `valor_desconto`, `percentual_desconto`, `valor_minimo_pedido`, `quantidade_maxima_uso`, `quantidade_usada`, `uso_por_cliente`, `data_inicio`, `data_fim`, `status`, `created_at`, `updated_at`) VALUES 
(1,1,'BEMVINDO10','Cupom de Boas-vindas','Desconto de 10% na primeira compra','desconto_sacola',NULL,10.00,50.00,NULL,0,1,'2025-08-01 12:58:40','2025-08-31 12:58:40','ativo','2025-08-01 12:58:40','2025-08-01 12:58:40'),
(2,1,'FIDELIDADE20','Cupom Fidelidade','Desconto de 20% para clientes fiéis','desconto_sacola',NULL,20.00,100.00,NULL,0,1,'2025-08-01 12:58:40','2025-09-30 12:58:40','ativo','2025-08-01 12:58:40','2025-08-01 12:58:40');

#
# Structure for table "fidelidade_cupons_uso"
#

DROP TABLE IF EXISTS `fidelidade_cupons_uso`;

CREATE TABLE `fidelidade_cupons_uso` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `cupom_id` bigint(20) unsigned NOT NULL,
    `cliente_id` bigint(20) unsigned NOT NULL,
    `pedido_id` int(11) DEFAULT NULL,
    `valor_desconto_aplicado` decimal(10, 2) DEFAULT NULL,
    `data_uso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sync_hash` varchar(255) NULL DEFAULT NULL,
    `sync_status` enum('pending', 'synced', 'error') NOT NULL DEFAULT 'pending',
    `sync_data` json NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fidelidade_cupons_uso_cupom_id_index` (`cupom_id`),
    KEY `fidelidade_cupons_uso_cliente_id_index` (`cliente_id`),
    KEY `fidelidade_cupons_uso_pedido_id_index` (`pedido_id`),
    KEY `fidelidade_cupons_uso_data_uso_index` (`data_uso`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

#
# Data for table "fidelidade_cupons_uso"
#