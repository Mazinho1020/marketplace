# Sistema de Fidelidade Completo - Laravel Marketplace
# Data: 2025-08-01
# Estrutura completa com campos padronizados

# ================================================
# ESTRUTURA DAS TABELAS - SISTEMA DE FIDELIDADE
# ================================================

# Limpar tabelas existentes
DROP TABLE IF EXISTS `fidelidade_cupons_uso`;

DROP TABLE IF EXISTS `fidelidade_cliente_conquistas`;

DROP TABLE IF EXISTS `fidelidade_cashback_transacoes`;

DROP TABLE IF EXISTS `fidelidade_creditos`;

DROP TABLE IF EXISTS `fidelidade_cupons`;

DROP TABLE IF EXISTS `fidelidade_conquistas`;

DROP TABLE IF EXISTS `fidelidade_cashback_regras`;

DROP TABLE IF EXISTS `fidelidade_carteiras`;

DROP TABLE IF EXISTS `fidelidade_cartoes`;

DROP TABLE IF EXISTS `fidelidade_programas`;

# ================================================
# 1. PROGRAMAS DE FIDELIDADE
# ================================================
CREATE TABLE `fidelidade_programas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL DEFAULT 1,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `data_inicio` datetime DEFAULT current_timestamp(),
  `data_fim` datetime DEFAULT NULL,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 2. CARTÕES DE FIDELIDADE
# ================================================
CREATE TABLE `fidelidade_cartoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL DEFAULT 1,
  `codigo_cartao` varchar(50) NOT NULL,
  `nivel_cartao` varchar(20) NOT NULL DEFAULT 'bronze',
  `saldo_cartao` decimal(10,2) NOT NULL DEFAULT 0.00,
  `xp_acumulado` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_cartao` (`codigo_cartao`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 3. CARTEIRAS DE FIDELIDADE
# ================================================
CREATE TABLE `fidelidade_carteiras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL DEFAULT 1,
  `saldo_cashback` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_creditos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_bloqueado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_total_disponivel` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nivel_atual` varchar(20) NOT NULL DEFAULT 'bronze',
  `xp_total` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'ativa',
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cliente_empresa` (`cliente_id`,`empresa_id`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 4. REGRAS DE CASHBACK
# ================================================
CREATE TABLE `fidelidade_cashback_regras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo_cashback` varchar(30) NOT NULL DEFAULT 'percentual',
  `valor_cashback` decimal(10,2) NOT NULL,
  `valor_minimo` decimal(10,2) DEFAULT NULL,
  `valor_maximo` decimal(10,2) DEFAULT NULL,
  `limite_mensal` decimal(10,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `status_index` (`status`),
  KEY `data_inicio_data_fim_index` (`data_inicio`,`data_fim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 5. TRANSAÇÕES DE CASHBACK
# ================================================
CREATE TABLE `fidelidade_cashback_transacoes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `pedido_id` int(11) DEFAULT NULL,
  `tipo` enum('credito','debito') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_pedido_original` decimal(10,2) DEFAULT NULL,
  `percentual_aplicado` decimal(5,2) DEFAULT NULL,
  `saldo_anterior` decimal(10,2) DEFAULT NULL,
  `saldo_posterior` decimal(10,2) DEFAULT NULL,
  `data_expiracao` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'disponivel',
  `observacoes` varchar(255) DEFAULT NULL,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `pedido_id_index` (`pedido_id`),
  KEY `status_index` (`status`),
  KEY `data_expiracao_index` (`data_expiracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 6. CRÉDITOS DE FIDELIDADE
# ================================================
CREATE TABLE `fidelidade_creditos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `tipo` varchar(30) NOT NULL DEFAULT 'comprado',
  `valor_original` decimal(10,2) NOT NULL,
  `valor_atual` decimal(10,2) NOT NULL,
  `codigo_ativacao` varchar(50) DEFAULT NULL,
  `data_expiracao` date DEFAULT NULL,
  `pedido_origem_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `codigo_ativacao_index` (`codigo_ativacao`),
  KEY `status_index` (`status`),
  KEY `data_expiracao_index` (`data_expiracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 7. CONQUISTAS/TROFÉUS
# ================================================
CREATE TABLE `fidelidade_conquistas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `pontos_recompensa` int(11) NOT NULL DEFAULT 0,
  `credito_recompensa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tipo_requisito` varchar(50) DEFAULT NULL,
  `valor_requisito` int(11) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `ativo_index` (`ativo`),
  KEY `tipo_requisito_index` (`tipo_requisito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 8. CONQUISTAS DOS CLIENTES
# ================================================
CREATE TABLE `fidelidade_cliente_conquistas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `conquista_id` bigint(20) unsigned NOT NULL,
  `data_desbloqueio` datetime NOT NULL DEFAULT current_timestamp(),
  `recompensa_resgatada` tinyint(1) NOT NULL DEFAULT 0,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cliente_conquista` (`cliente_id`,`empresa_id`,`conquista_id`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `conquista_id_index` (`conquista_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 9. CUPONS DE DESCONTO
# ================================================
CREATE TABLE `fidelidade_cupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `tipo` varchar(30) NOT NULL DEFAULT 'desconto_sacola',
  `valor_desconto` decimal(10,2) DEFAULT NULL,
  `percentual_desconto` decimal(5,2) DEFAULT NULL,
  `valor_minimo_pedido` decimal(10,2) DEFAULT NULL,
  `quantidade_maxima_uso` int(11) DEFAULT NULL,
  `quantidade_usada` int(11) NOT NULL DEFAULT 0,
  `uso_por_cliente` int(11) NOT NULL DEFAULT 1,
  `data_inicio` datetime DEFAULT NULL,
  `data_fim` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_unique` (`codigo`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `codigo_index` (`codigo`),
  KEY `status_index` (`status`),
  KEY `data_inicio_data_fim_index` (`data_inicio`,`data_fim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# 10. USO DE CUPONS
# ================================================
CREATE TABLE `fidelidade_cupons_uso` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cupom_id` bigint(20) unsigned NOT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `pedido_id` int(11) DEFAULT NULL,
  `valor_desconto_aplicado` decimal(10,2) DEFAULT NULL,
  `data_uso` datetime NOT NULL DEFAULT current_timestamp(),
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` varchar(20) DEFAULT NULL,
  `sync_data` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cupom_id_index` (`cupom_id`),
  KEY `cliente_id_index` (`cliente_id`),
  KEY `empresa_id_index` (`empresa_id`),
  KEY `pedido_id_index` (`pedido_id`),
  KEY `data_uso_index` (`data_uso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

# ================================================
# DADOS DE EXEMPLO PARA TESTES
# ================================================

# Programas de exemplo
INSERT INTO `fidelidade_programas` VALUES 
(1,1,'Fidelidade Ouro','Programa premium de fidelidade','ativo','2025-01-01 00:00:00',NULL,'hash1','ok','2025-08-01 11:00:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,2,'Fidelidade Prata','Programa básico de fidelidade','ativo','2025-02-01 00:00:00',NULL,'hash2','ok','2025-08-01 11:00:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Cartões de exemplo
INSERT INTO `fidelidade_cartoes` VALUES 
(1,101,1,'CARTAO1011','bronze',120.00,350,'ativo','hA1','ok','2025-08-01 12:10:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,102,1,'CARTAO1021','ouro',1000.00,3000,'ativo','hA2','ok','2025-08-01 12:11:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(3,103,2,'CARTAO1032','prata',220.00,800,'ativa','hA3','ok','2025-08-01 12:12:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Carteiras de exemplo
INSERT INTO `fidelidade_carteiras` VALUES 
(1,101,1,120.00,30.00,0.00,150.00,'bronze',350,'ativa','carteira1','ok','2025-08-01 12:13:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,102,1,900.00,100.00,0.00,1000.00,'ouro',3000,'ativa','carteira2','ok','2025-08-01 12:14:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(3,103,2,200.00,20.00,0.00,220.00,'prata',800,'ativa','carteira3','ok','2025-08-01 12:15:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Cupons de exemplo
INSERT INTO `fidelidade_cupons` VALUES 
(1,1,'BEMVINDO10','Bem-vindo','Desconto de 10% na primeira compra','desconto_sacola',NULL,10.00,50.00,100,0,1,'2025-08-01 00:00:00','2025-09-01 00:00:00','ativo','cup1','ok','2025-08-01 12:29:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,1,'VIP20','Cliente VIP','Desconto de 20% para clientes VIP','desconto_sacola',NULL,20.00,100.00,50,0,1,'2025-08-01 00:00:00','2025-09-30 00:00:00','ativo','cup2','ok','2025-08-01 12:30:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(3,2,'SUPER5','Super Oferta','Desconto especial de R$ 5','desconto_sacola',5.00,NULL,30.00,200,0,2,'2025-08-01 00:00:00','2025-08-31 00:00:00','ativo','cup3','ok','2025-08-01 12:31:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Regras de cashback de exemplo
INSERT INTO `fidelidade_cashback_regras` VALUES 
(1,1,'Regra Ouro','5% cashback para clientes ouro','percentual',5.00,100.00,1000.00,5000.00,'2025-01-01',NULL,'ativo','r1','ok','2025-08-01 12:16:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,2,'Regra Prata','2% cashback para todos','percentual',2.00,20.00,500.00,2000.00,'2025-02-01',NULL,'ativo','r2','ok','2025-08-01 12:17:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Conquistas de exemplo
INSERT INTO `fidelidade_conquistas` VALUES 
(1,1,'Primeira Compra','Cliente fez a primeira compra','trophy',100,10.00,'primeira_compra',1,1,'cq1','ok','2025-08-01 12:22:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,1,'Compras VIP','Cliente fez 10 compras','star',250,25.00,'total_compras',10,1,'cq2','ok','2025-08-01 12:23:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(3,2,'Valor Expressivo','Cliente gastou mais de R$500','diamond',500,50.00,'valor_total',500,1,'cq3','ok','2025-08-01 12:24:00','2025-08-01 21:18:58','2025-08-01 21:18:58');

# Créditos de exemplo
INSERT INTO `fidelidade_creditos` VALUES 
(1,101,1,'promo',20.00,20.00,'PROMO20','2025-12-31',NULL,'Bônus de promoção','ativo','cr1','ok','2025-08-01 12:20:00','2025-08-01 21:18:58','2025-08-01 21:18:58'),
(2,102,1,'ajuste',30.00,30.00,NULL,NULL,NULL,'Ajuste manual','ativo','cr2','ok','2025-08-01 12:21:00','2025-08-01 21:18:58','2025-08-01 21:18:58');