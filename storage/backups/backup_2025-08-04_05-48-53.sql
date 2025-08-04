-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: meufinanceiro
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `afi_plan_assinaturas`
--

DROP TABLE IF EXISTS `afi_plan_assinaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_assinaturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `funforcli_id` int NOT NULL,
  `plano_id` int NOT NULL,
  `ciclo_cobranca` enum('mensal','anual','vitalicio') DEFAULT 'mensal',
  `valor` decimal(10,2) NOT NULL,
  `status` enum('trial','ativo','suspenso','expirado','cancelado') DEFAULT 'trial',
  `trial_expira_em` timestamp NULL DEFAULT NULL,
  `iniciado_em` timestamp NULL DEFAULT NULL,
  `expira_em` timestamp NULL DEFAULT NULL,
  `proxima_cobranca_em` timestamp NULL DEFAULT NULL,
  `ultima_cobranca_em` timestamp NULL DEFAULT NULL,
  `cancelado_em` timestamp NULL DEFAULT NULL,
  `renovacao_automatica` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_funforcli` (`funforcli_id`),
  KEY `idx_plano` (`plano_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_assinaturas`
--

LOCK TABLES `afi_plan_assinaturas` WRITE;
/*!40000 ALTER TABLE `afi_plan_assinaturas` DISABLE KEYS */;
/*!40000 ALTER TABLE `afi_plan_assinaturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afi_plan_configuracoes`
--

DROP TABLE IF EXISTS `afi_plan_configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text,
  `tipo` enum('string','number','boolean','json') DEFAULT 'string',
  `descricao` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_chave` (`empresa_id`,`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_configuracoes`
--

LOCK TABLES `afi_plan_configuracoes` WRITE;
/*!40000 ALTER TABLE `afi_plan_configuracoes` DISABLE KEYS */;
INSERT INTO `afi_plan_configuracoes` VALUES (1,0,'sistema_nome','MarketPlace Payment System','string','Nome do sistema','2025-08-03 08:16:18','2025-08-03 08:16:18'),(2,0,'sistema_email','admin@marketplace.com','string','Email principal do sistema','2025-08-03 08:16:18','2025-08-03 08:16:18'),(3,0,'cache_enabled','1','string','Cache Redis habilitado','2025-08-03 08:16:18','2025-08-03 08:16:18'),(4,0,'cache_ttl_default','3600','string','TTL padrÃ£o do cache em segundos','2025-08-03 08:16:18','2025-08-03 08:16:18'),(5,0,'webhook_timeout','30','string','Timeout para webhooks em segundos','2025-08-03 08:16:18','2025-08-03 08:16:18'),(6,0,'max_upload_size','10','string','Tamanho mÃ¡ximo de upload em MB','2025-08-03 08:16:18','2025-08-03 08:16:18'),(7,0,'backup_enabled','1','string','Backup automÃ¡tico habilitado','2025-08-03 08:16:18','2025-08-03 08:16:18'),(8,0,'backup_frequency','daily','string','FrequÃªncia do backup (daily, weekly, monthly)','2025-08-03 08:16:18','2025-08-03 08:16:18'),(9,0,'api_rate_limit','1000','string','Limite de requisiÃ§Ãµes API por hora','2025-08-03 08:16:18','2025-08-03 08:16:18'),(10,0,'maintenance_mode','0','string','Modo manutenÃ§Ã£o ativo','2025-08-03 08:16:18','2025-08-03 08:16:18');
/*!40000 ALTER TABLE `afi_plan_configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afi_plan_gateways`
--

DROP TABLE IF EXISTS `afi_plan_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_gateways` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `provedor` varchar(50) NOT NULL,
  `ambiente` enum('sandbox','producao') DEFAULT 'sandbox',
  `ativo` tinyint(1) DEFAULT '1',
  `credenciais` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `configuracoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `url_webhook` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_codigo` (`empresa_id`,`codigo`),
  CONSTRAINT `afi_plan_gateways_chk_1` CHECK (json_valid(`credenciais`)),
  CONSTRAINT `afi_plan_gateways_chk_2` CHECK (json_valid(`configuracoes`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_gateways`
--

LOCK TABLES `afi_plan_gateways` WRITE;
/*!40000 ALTER TABLE `afi_plan_gateways` DISABLE KEYS */;
INSERT INTO `afi_plan_gateways` VALUES (1,1,'pix_interno','PIX Interno','pix','producao',1,'{\"chave_pix\":\"\",\"banco\":\"\",\"agencia\":\"\",\"conta\":\"\",\"titular\":\"\",\"cpf_cnpj\":\"\"}','{\"webhook_url\":\"http:\\/\\/localhost\\/webhook\\/pix\",\"qr_code_ttl\":300,\"fees\":{\"pix\":0.99}}',NULL,'2025-08-02 18:12:44','2025-08-02 21:01:29'),(2,1,'boleto_interno','Boleto Interno','boleto','producao',1,'{\"banco\":\"\",\"agencia\":\"\",\"conta\":\"\",\"carteira\":\"\",\"convenio\":\"\",\"cedente\":\"\"}','{\"webhook_url\":\"http:\\/\\/localhost\\/webhook\\/boleto\",\"vencimento_dias\":3,\"fees\":{\"boleto\":3.5}}',NULL,'2025-08-02 18:12:44','2025-08-02 21:01:29'),(3,0,'pagseguro','PagSeguro','pagseguro','sandbox',0,'{\"client_id\":\"\",\"client_secret\":\"\",\"public_key\":\"\"}','{\"webhook_url\":\"http:\\/\\/localhost\\/webhook\\/pagseguro\",\"supported_methods\":[\"credit_card\",\"pix\",\"boleto\"],\"fees\":{\"credit_card\":3.99,\"pix\":0.99,\"boleto\":3.5}}',NULL,'2025-08-03 08:16:18','2025-08-03 08:16:18'),(4,0,'mercadopago','Mercado Pago','mercadopago','sandbox',0,'{\"access_token\":\"\",\"public_key\":\"\",\"webhook_secret\":\"\"}','{\"webhook_url\":\"http:\\/\\/localhost\\/webhook\\/mercadopago\",\"supported_methods\":[\"credit_card\",\"pix\",\"boleto\"],\"fees\":{\"credit_card\":4.99,\"pix\":0.99,\"boleto\":3.99}}',NULL,'2025-08-03 08:16:18','2025-08-03 08:16:18');
/*!40000 ALTER TABLE `afi_plan_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afi_plan_planos`
--

DROP TABLE IF EXISTS `afi_plan_planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_planos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text,
  `preco_mensal` decimal(10,2) DEFAULT '0.00',
  `preco_anual` decimal(10,2) DEFAULT '0.00',
  `preco_vitalicio` decimal(10,2) DEFAULT '0.00',
  `dias_trial` int DEFAULT '0',
  `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `limites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_codigo` (`empresa_id`,`codigo`),
  CONSTRAINT `afi_plan_planos_chk_1` CHECK (json_valid(`recursos`)),
  CONSTRAINT `afi_plan_planos_chk_2` CHECK (json_valid(`limites`))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_planos`
--

LOCK TABLES `afi_plan_planos` WRITE;
/*!40000 ALTER TABLE `afi_plan_planos` DISABLE KEYS */;
INSERT INTO `afi_plan_planos` VALUES (1,1,'basico','Plano BÃ¡sico','Plano bÃ¡sico para iniciantes',50.00,500.00,0.00,0,'{\"pdv_enabled\":true,\"relatorios_basicos\":true,\"suporte_email\":true,\"customizacao_basica\":true,\"api_access\":false,\"webhook_enabled\":false,\"multi_gateway\":false,\"relatorios_avancados\":false,\"programa_afiliados\":false,\"customizacao_avancada\":false}','{\"transacoes_mes\":1000,\"usuarios\":3,\"produtos\":500,\"clientes\":1000,\"storage_mb\":500,\"api_calls_dia\":0,\"webhooks_dia\":0}',1,'2025-08-02 18:12:44','2025-08-03 04:16:18'),(2,1,'premium','Plano Premium','Plano completo com todas as funcionalidades',100.00,1000.00,0.00,0,'{\"pdv_enabled\":true,\"relatorios_basicos\":true,\"relatorios_avancados\":true,\"suporte_email\":true,\"suporte_chat\":true,\"customizacao_basica\":true,\"customizacao_avancada\":true,\"api_access\":true,\"webhook_enabled\":true,\"multi_gateway\":false,\"programa_afiliados\":true,\"backup_automatico\":true}','{\"transacoes_mes\":5000,\"usuarios\":10,\"produtos\":2000,\"clientes\":5000,\"storage_mb\":2000,\"api_calls_dia\":1000,\"webhooks_dia\":100}',1,'2025-08-02 18:12:44','2025-08-03 04:16:18'),(3,1,'enterprise','Plano Enterprise','Plano para grandes empresas',200.00,2000.00,0.00,0,'{\"pdv_enabled\":true,\"relatorios_basicos\":true,\"relatorios_avancados\":true,\"relatorios_customizados\":true,\"suporte_email\":true,\"suporte_chat\":true,\"suporte_telefone\":true,\"suporte_24h\":true,\"customizacao_basica\":true,\"customizacao_avancada\":true,\"customizacao_completa\":true,\"api_access\":true,\"webhook_enabled\":true,\"multi_gateway\":true,\"programa_afiliados\":true,\"backup_automatico\":true,\"multi_empresa\":true,\"white_label\":true}','{\"transacoes_mes\":-1,\"usuarios\":-1,\"produtos\":-1,\"clientes\":-1,\"storage_mb\":-1,\"api_calls_dia\":-1,\"webhooks_dia\":-1}',1,'2025-08-02 18:12:44','2025-08-03 04:16:18');
/*!40000 ALTER TABLE `afi_plan_planos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afi_plan_transacoes`
--

DROP TABLE IF EXISTS `afi_plan_transacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_transacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `empresa_id` int NOT NULL,
  `codigo_transacao` varchar(100) NOT NULL,
  `cliente_id` int DEFAULT NULL,
  `gateway_id` int DEFAULT NULL,
  `gateway_transacao_id` varchar(255) DEFAULT NULL,
  `tipo_origem` enum('nova_assinatura','renovacao_assinatura','comissao_afiliado','venda_avulsa') DEFAULT 'venda_avulsa',
  `id_origem` int DEFAULT NULL,
  `valor_original` decimal(10,2) NOT NULL,
  `valor_desconto` decimal(10,2) DEFAULT '0.00',
  `valor_taxas` decimal(10,2) DEFAULT '0.00',
  `valor_final` decimal(10,2) NOT NULL,
  `moeda` varchar(3) DEFAULT 'BRL',
  `forma_pagamento` varchar(50) DEFAULT NULL,
  `status` enum('rascunho','pendente','processando','aprovado','recusado','cancelado','estornado') DEFAULT 'rascunho',
  `gateway_status` varchar(50) DEFAULT NULL,
  `cliente_nome` varchar(255) DEFAULT NULL,
  `cliente_email` varchar(255) DEFAULT NULL,
  `descricao` text,
  `metadados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `expira_em` timestamp NULL DEFAULT NULL,
  `processado_em` timestamp NULL DEFAULT NULL,
  `aprovado_em` timestamp NULL DEFAULT NULL,
  `cancelado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_codigo_transacao` (`codigo_transacao`),
  KEY `idx_status` (`status`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_gateway` (`gateway_id`),
  CONSTRAINT `afi_plan_transacoes_chk_1` CHECK (json_valid(`metadados`))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_transacoes`
--

LOCK TABLES `afi_plan_transacoes` WRITE;
/*!40000 ALTER TABLE `afi_plan_transacoes` DISABLE KEYS */;
INSERT INTO `afi_plan_transacoes` VALUES (1,'dc644de0-6fcc-11f0-ba80-d09466d7b7e7',1,'TXN_39_202508021416',39,1,NULL,'nova_assinatura',NULL,50.00,0.00,0.00,50.00,'BRL','pix','aprovado',NULL,'Mazinho','12@gmail.com',NULL,NULL,NULL,NULL,'2025-08-02 18:16:51',NULL,'2025-08-02 18:16:51','2025-08-02 18:16:51'),(2,'dc64634a-6fcc-11f0-ba80-d09466d7b7e7',1,'TXN_49_202508021416',49,1,NULL,'nova_assinatura',NULL,50.00,0.00,0.00,50.00,'BRL','pix','aprovado',NULL,'Ana','ana@teste.com',NULL,NULL,NULL,NULL,'2025-08-02 18:16:51',NULL,'2025-08-02 18:16:51','2025-08-02 18:16:51'),(3,'dc646518-6fcc-11f0-ba80-d09466d7b7e7',1,'TXN_50_202508021416',50,1,NULL,'nova_assinatura',NULL,50.00,0.00,0.00,50.00,'BRL','pix','aprovado',NULL,'JoÃ£o','joao@teste.com',NULL,NULL,NULL,NULL,'2025-08-02 18:16:51',NULL,'2025-08-02 18:16:51','2025-08-02 18:16:51'),(4,'dc6465e6-6fcc-11f0-ba80-d09466d7b7e7',1,'TXN_51_202508021416',51,1,NULL,'nova_assinatura',NULL,50.00,0.00,0.00,50.00,'BRL','pix','aprovado',NULL,'Maria','maria@teste.com',NULL,NULL,NULL,NULL,'2025-08-02 18:16:51',NULL,'2025-08-02 18:16:51','2025-08-02 18:16:51');
/*!40000 ALTER TABLE `afi_plan_transacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `afi_plan_vendas`
--

DROP TABLE IF EXISTS `afi_plan_vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `afi_plan_vendas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `afiliado_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `assinatura_id` int DEFAULT NULL,
  `transacao_id` int DEFAULT NULL,
  `valor_venda` decimal(10,2) NOT NULL,
  `taxa_comissao` decimal(5,2) NOT NULL,
  `valor_comissao` decimal(10,2) NOT NULL,
  `status` enum('pendente','confirmado','cancelado','estornado') DEFAULT 'pendente',
  `confirmado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_afiliado` (`afiliado_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `afi_plan_vendas`
--

LOCK TABLES `afi_plan_vendas` WRITE;
/*!40000 ALTER TABLE `afi_plan_vendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `afi_plan_vendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixa_fechamento`
--

DROP TABLE IF EXISTS `caixa_fechamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa_fechamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `caixa_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `data_fechamento` datetime NOT NULL,
  `observacoes` text COLLATE utf8mb3_unicode_ci,
  `conferido` tinyint(1) DEFAULT '0',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa_fechamento`
--

LOCK TABLES `caixa_fechamento` WRITE;
/*!40000 ALTER TABLE `caixa_fechamento` DISABLE KEYS */;
INSERT INTO `caixa_fechamento` VALUES (1,0,19,1,'2025-05-16 14:07:17','teseeeee',0,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-24 07:48:32'),(2,0,21,1,'2025-05-16 14:10:52','ededed',0,'sincronizado','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-23 10:14:04'),(3,0,22,1,'2025-05-16 14:30:49','',1,'sincronizado','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-23 10:14:04'),(4,0,25,1,'2025-05-16 23:58:29','',1,'sincronizado','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-23 10:14:04'),(5,0,26,1,'2025-05-17 01:52:12','',1,'sincronizado','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-23 10:14:04'),(6,0,27,1,'2025-05-17 01:59:20','',1,'sincronizado','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:36','2025-07-23 10:14:04');
/*!40000 ALTER TABLE `caixa_fechamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixa_fechamento_formas`
--

DROP TABLE IF EXISTS `caixa_fechamento_formas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa_fechamento_formas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fechamento_id` int NOT NULL,
  `forma_pagamento_id` int NOT NULL,
  `bandeira_id` int DEFAULT NULL,
  `valor_informado` decimal(10,2) NOT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa_fechamento_formas`
--

LOCK TABLES `caixa_fechamento_formas` WRITE;
/*!40000 ALTER TABLE `caixa_fechamento_formas` DISABLE KEYS */;
INSERT INTO `caixa_fechamento_formas` VALUES (1,1,1,1,30.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 22:27:39'),(2,1,3,5,20.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 12:18:44'),(3,1,5,4,10.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 11:58:44'),(4,1,6,6,10.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 11:58:45'),(5,3,1,1,10.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 22:39:06'),(6,5,1,1,10.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 11:58:45'),(7,5,3,5,10.00,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:52:58','2025-07-23 11:58:45');
/*!40000 ALTER TABLE `caixa_fechamento_formas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixa_movimentos`
--

DROP TABLE IF EXISTS `caixa_movimentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa_movimentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caixa_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo` enum('sangria','suprimento') COLLATE utf8mb3_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data_movimento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa_movimentos`
--

LOCK TABLES `caixa_movimentos` WRITE;
/*!40000 ALTER TABLE `caixa_movimentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `caixa_movimentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caixas`
--

DROP TABLE IF EXISTS `caixas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `data_abertura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_fechamento` datetime DEFAULT NULL,
  `valor_abertura` decimal(10,2) NOT NULL,
  `valor_informado` decimal(10,2) DEFAULT NULL,
  `status` enum('aberto','fechado') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'aberto',
  `observacoes` text COLLATE utf8mb3_unicode_ci,
  `valor_vendas` decimal(10,2) DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixas`
--

LOCK TABLES `caixas` WRITE;
/*!40000 ALTER TABLE `caixas` DISABLE KEYS */;
INSERT INTO `caixas` VALUES (18,1,3,'2025-05-07 02:00:19',NULL,30.00,NULL,'aberto',NULL,NULL,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:54:05','2025-07-23 21:27:23'),(19,1,1,'2025-05-31 00:37:18',NULL,300.00,NULL,'aberto',NULL,NULL,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:54:05','2025-07-23 11:58:47'),(20,1,5,'2025-06-05 11:11:57',NULL,70.00,NULL,'aberto',NULL,NULL,'pendente','2025-07-22 11:54:43',NULL,'2025-07-23 08:54:05','2025-07-23 11:58:47');
/*!40000 ALTER TABLE `caixas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_sugeridas`
--

DROP TABLE IF EXISTS `categorias_sugeridas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_sugeridas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `palavra_chave` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `conta_gerencial_id` int NOT NULL,
  `empresa_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `funcionario_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_sugeridas`
--

LOCK TABLES `categorias_sugeridas` WRITE;
/*!40000 ALTER TABLE `categorias_sugeridas` DISABLE KEYS */;
INSERT INTO `categorias_sugeridas` VALUES (1,'restaurante',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:24'),(2,'lanchonete',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:56'),(3,'pizzaria',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:56'),(4,'padaria',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:56'),(5,'panificadora',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:57'),(6,'churrascaria',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:57'),(7,'burger',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:57'),(8,'sushi',26,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:58'),(9,'mercado',11,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:58'),(10,'supermercado',11,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:58'),(11,'atacadÃ£o',11,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:59'),(12,'comercial',11,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:59'),(13,'feirao',11,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:59'),(14,'material construÃ§Ã£o',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:11:59'),(15,'madeireira',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:00'),(16,'depÃ³sito',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:00'),(17,'construÃ§Ã£o',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:00'),(18,'cimento',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:01'),(19,'tijolo',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:01'),(20,'areia',34,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:01'),(21,'posto',14,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:02'),(22,'combustÃ­vel',14,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:02'),(23,'gasolina',14,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:02'),(24,'pedÃ¡gio',14,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:02'),(25,'transportadora',59,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:03'),(26,'frete',59,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:03'),(27,'taxi',59,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:03'),(28,'uber',59,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:04'),(29,'farmÃ¡cia',54,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:04'),(30,'drogaria',54,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:04'),(31,'hospital',54,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:05'),(32,'clÃ­nica',54,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:05'),(33,'internet',21,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:05'),(34,'telefone',30,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:05'),(35,'celular',30,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:06'),(36,'energia',24,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:06'),(37,'luz',24,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:06'),(38,'Ã¡gua',3,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:07'),(39,'tarifa',29,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:07'),(40,'juros',23,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:07'),(41,'emprÃ©stimo',5,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:08'),(42,'financiamento',6,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:08'),(45,'aluguel',43,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:08'),(46,'iptu',22,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:08'),(47,'seguro',28,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:09'),(48,'manutenÃ§Ã£o',25,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:09'),(49,'oficina',25,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:09'),(50,'escola',55,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:10'),(51,'curso',55,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:10'),(52,'treinamento',55,NULL,NULL,'2025-05-04 07:30:26',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:10'),(53,'TransferÃªncia enviada',66,1,NULL,'2025-05-04 23:23:15',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:11'),(54,'TransferÃªncia recebida',7,1,NULL,'2025-05-04 23:24:58',NULL,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:11');
/*!40000 ALTER TABLE `categorias_sugeridas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classificacoes_dre`
--

DROP TABLE IF EXISTS `classificacoes_dre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classificacoes_dre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tipo_id` int DEFAULT NULL,
  `empresa_id` int NOT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classificacoes_dre`
--

LOCK TABLES `classificacoes_dre` WRITE;
/*!40000 ALTER TABLE `classificacoes_dre` DISABLE KEYS */;
INSERT INTO `classificacoes_dre` VALUES (3,'ALUGUEL CONDOMINIO E IPTU','Despesas relacionadas a aluguel de imÃ³veis, condomÃ­nios e pagamento de IPTU','2025-03-26 04:27:29','2025-07-23 21:27:24',2,1,NULL,'pendente','2025-07-23 09:09:59'),(4,'CUSTO DA MERCADORIA','Custos diretamente relacionados Ã  aquisiÃ§Ã£o de mercadorias para revenda','2025-03-26 04:39:30','2025-07-23 11:12:12',2,1,NULL,'pendente','2025-07-23 09:09:59'),(5,'DESPESAS FINANCEIRAS','Despesas relacionadas a operaÃ§Ãµes financeiras como juros, tarifas bancÃ¡rias, etc','2025-03-26 05:08:19','2025-07-23 11:12:12',2,1,NULL,'pendente','2025-07-23 09:09:59'),(6,'GERAIS E ADMINISTRATIVAS','Despesas gerais e administrativas necessÃ¡rias para operaÃ§Ã£o da empresa','2025-03-26 05:08:50','2025-07-23 11:12:12',2,1,NULL,'pendente','2025-07-23 09:09:59'),(7,'IMPOSTOS E DEVOLUÃ‡Ã•ES','Impostos sobre vendas e devoluÃ§Ãµes de mercadorias','2025-03-26 05:09:09','2025-07-23 11:12:13',2,1,NULL,'pendente','2025-07-23 09:09:59'),(8,'PESSOAL (FUNCIONARIOS)','Todos os custos relacionados Ã  folha de pagamento de funcionÃ¡rios','2025-03-26 05:09:20','2025-07-23 11:12:13',2,1,NULL,'pendente','2025-07-23 09:09:59'),(9,'PRO-LABORE','RemuneraÃ§Ã£o dos sÃ³cios ou proprietÃ¡rios da empresa','2025-03-26 05:09:27','2025-07-23 11:12:13',2,1,NULL,'pendente','2025-07-23 09:09:59'),(10,'PROPAGANDA E MARKETING','Despesas com publicidade, marketing e promoÃ§Ã£o de vendas','2025-03-26 05:10:05','2025-07-23 11:12:14',2,1,NULL,'pendente','2025-07-23 09:09:59'),(11,'ULTILIDADES','Despesas com serviÃ§os pÃºblicos como Ã¡gua, luz, gÃ¡s, etc','2025-03-26 05:10:23','2025-07-23 11:12:14',2,1,NULL,'pendente','2025-07-23 09:09:59'),(12,'RECEITAS FINANCEIRAS','Receitas provenientes de aplicaÃ§Ãµes financeiras ou rendimentos','2025-03-26 05:17:26','2025-07-23 11:12:14',1,1,NULL,'pendente','2025-07-23 09:09:59'),(13,'ENTRADAS VENDAS','Receitas provenientes das vendas de produtos ou serviÃ§os','2025-03-26 05:18:02','2025-07-23 11:12:14',1,1,NULL,'pendente','2025-07-23 09:09:59'),(14,'MERCADORIA REVENDA','Custos com mercadorias adquiridas para revenda','2025-03-26 09:36:33','2025-07-23 11:12:15',2,1,NULL,'pendente','2025-07-23 09:09:59'),(15,'ENTRADAS OUTRAS','Outras receitas nÃ£o relacionadas diretamente com a atividade principal','2025-03-27 09:45:03','2025-07-23 11:12:15',1,1,NULL,'pendente','2025-07-23 09:09:59');
/*!40000 ALTER TABLE `classificacoes_dre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_planos`
--

DROP TABLE IF EXISTS `com_planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `com_planos` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '1',
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text,
  `preco_mensal` decimal(10,2) NOT NULL,
  `preco_anual` decimal(10,2) NOT NULL,
  `preco_vitalicio` decimal(10,2) NOT NULL,
  `recursos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `limites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `dias_trial` int DEFAULT '7',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_codigo` (`empresa_id`,`codigo`),
  CONSTRAINT `com_planos_chk_1` CHECK (json_valid(`recursos`)),
  CONSTRAINT `com_planos_chk_2` CHECK (json_valid(`limites`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_planos`
--

LOCK TABLES `com_planos` WRITE;
/*!40000 ALTER TABLE `com_planos` DISABLE KEYS */;
/*!40000 ALTER TABLE `com_planos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `origem` varchar(25) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ativo` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `codigo_sistema` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Código único do sistema',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'taxa_inss','11.0','Taxa do INSS em percentual','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'sistema',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(2,'taxa_fgts','8.0','Taxa do FGTS em percentual','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'sistema',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(3,'taxa_irrf','7.5','Taxa inicial do IRRF em percentual','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'sistema',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(4,'conta_gerencial_pdv','33',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(5,'mesas_pdv','30',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(6,'preco_custo_baseado_na_fichaTecnica','1','preÃ§o de custo baseado na ficha tecnica 1 para sim 0 para nao ','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'produtos',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(7,'controle_fiado_id','10','informa o id da tabela que controla o fiado \r\n\r\n','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(8,'controle_fiado','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(9,'testeccc','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(11,'tste  rf','1','1				','2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(12,'teste10','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(13,'teste7','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:09:59',NULL,'pendente',NULL),(14,'teste8','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 09:16:08',NULL,'pendente',NULL),(15,'teste 9','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 10:59:08',NULL,'pendente',NULL),(16,'teste11','1',NULL,'2025-07-23 10:32:45','2025-07-23 21:27:29',1,'pdv',1,'2025-07-23 11:32:41',NULL,'pendente',NULL);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_definitions`
--

DROP TABLE IF EXISTS `config_definitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_definitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID da empresa',
  `chave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da chave de configuração',
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome amigável da configuração',
  `descricao` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrição da configuração',
  `tipo` enum('string','integer','float','boolean','array','json','url','email','password') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Tipo de dado',
  `grupo_id` bigint unsigned DEFAULT NULL COMMENT 'Grupo ao qual pertence',
  `valor_padrao` text COLLATE utf8mb4_unicode_ci COMMENT 'Valor padrão',
  `obrigatorio` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se é obrigatória',
  `min_length` int DEFAULT NULL COMMENT 'Tamanho mínimo',
  `max_length` int DEFAULT NULL COMMENT 'Tamanho máximo',
  `regex_validacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Regex para validação',
  `opcoes` text COLLATE utf8mb4_unicode_ci COMMENT 'Opções possíveis (JSON)',
  `editavel` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Se pode ser editado via interface',
  `avancado` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se é uma configuração avançada',
  `ordem` int NOT NULL DEFAULT '0' COMMENT 'Ordem de exibição',
  `dica` text COLLATE utf8mb4_unicode_ci COMMENT 'Dica de ajuda na interface',
  `ajuda` text COLLATE utf8mb4_unicode_ci COMMENT 'Texto de ajuda detalhado',
  `ativo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status da definição',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash para sincronização',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT NULL COMMENT 'Data da última sincronização',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_definitions_empresa_id_chave_unique` (`empresa_id`,`chave`),
  KEY `config_definitions_empresa_id_index` (`empresa_id`),
  KEY `config_definitions_grupo_id_index` (`grupo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_definitions`
--

LOCK TABLES `config_definitions` WRITE;
/*!40000 ALTER TABLE `config_definitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `config_definitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_groups`
--

DROP TABLE IF EXISTS `config_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID da empresa',
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único do grupo',
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome de exibição do grupo',
  `descricao` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrição do grupo',
  `grupo_pai_id` bigint unsigned DEFAULT NULL COMMENT 'ID do grupo pai',
  `icone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe de ícone para interface',
  `icone_class` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe de ícone para interface',
  `ordem` int NOT NULL DEFAULT '0' COMMENT 'Ordem de exibição do grupo',
  `ativo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do grupo',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash para sincronização',
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
  `sync_data` timestamp NULL DEFAULT NULL COMMENT 'Data da última sincronização',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_groups_empresa_id_codigo_unique` (`empresa_id`,`codigo`),
  KEY `config_groups_empresa_id_index` (`empresa_id`),
  KEY `config_groups_grupo_pai_id_index` (`grupo_pai_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_groups`
--

LOCK TABLES `config_groups` WRITE;
/*!40000 ALTER TABLE `config_groups` DISABLE KEYS */;
INSERT INTO `config_groups` VALUES (1,1,'sistema','Sistema','ConfiguraÃ§Ãµes gerais do sistema',NULL,NULL,'fas fa-cogs',1,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(2,1,'empresa','Empresa','Dados da empresa desenvolvedora',NULL,NULL,'fas fa-building',2,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(3,1,'licenca','Licenciamento','ConfiguraÃ§Ãµes de licenciamento',NULL,NULL,'fas fa-key',3,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(4,1,'clientes','Clientes','ConfiguraÃ§Ãµes de clientes',NULL,NULL,'fas fa-users',4,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(5,1,'sync','SincronizaÃ§Ã£o','ConfiguraÃ§Ãµes de sincronizaÃ§Ã£o',NULL,NULL,'fas fa-sync',5,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(6,1,'telegram','Telegram','ConfiguraÃ§Ãµes do Telegram',NULL,NULL,'fab fa-telegram',6,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(7,1,'cloud','Cloud','ConfiguraÃ§Ãµes de armazenamento em nuvem',NULL,NULL,'fas fa-cloud',7,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(8,1,'api','API','ConfiguraÃ§Ãµes das APIs',NULL,NULL,'fas fa-code',8,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(9,2,'sistema','Sistema','ConfiguraÃ§Ãµes gerais do sistema',NULL,NULL,'fas fa-cogs',1,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(10,2,'pdv','PDV','ConfiguraÃ§Ãµes do ponto de venda',NULL,NULL,'fas fa-cash-register',2,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(11,2,'produtos','Produtos','ConfiguraÃ§Ãµes de produtos e estoque',NULL,NULL,'fas fa-box',3,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(12,2,'impostos','Impostos','ConfiguraÃ§Ãµes de impostos e taxas',NULL,NULL,'fas fa-percent',4,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(13,2,'pagamento','Pagamento','ConfiguraÃ§Ãµes de pagamento',NULL,NULL,'fas fa-credit-card',5,1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(14,2,'safe2pay','Safe2Pay Gateway','ConfiguraÃ§Ãµes do gateway de pagamento Safe2Pay',13,NULL,'fas fa-credit-card',1,1,NULL,'pending',NULL,NULL,NULL,NULL),(15,1,'payment','ConfiguraÃ§Ãµes de Pagamento','ConfiguraÃ§Ãµes dos gateways de pagamento',NULL,NULL,NULL,0,1,NULL,'pending',NULL,'2025-08-02 08:50:44','2025-08-02 08:50:44',NULL),(16,1,'planos_sistema','ConfiguraÃ§Ãµes de Planos','ConfiguraÃ§Ãµes gerais dos planos',NULL,NULL,'fas fa-layer-group',1,1,NULL,'pending',NULL,NULL,NULL,NULL),(17,1,'planos_cobranca','ConfiguraÃ§Ãµes de CobranÃ§a','ConfiguraÃ§Ãµes de cobranÃ§a e pagamento',NULL,NULL,'fas fa-money-bill',2,1,NULL,'pending',NULL,NULL,NULL,NULL),(18,1,'planos_trial','ConfiguraÃ§Ãµes Trial','ConfiguraÃ§Ãµes do perÃ­odo trial',NULL,NULL,'fas fa-clock',3,1,NULL,'pending',NULL,NULL,NULL,NULL),(19,1,'comerciantes_geral','ConfiguraÃ§Ãµes de Comerciantes','ConfiguraÃ§Ãµes gerais dos comerciantes',NULL,NULL,'fas fa-store',4,1,NULL,'pending',NULL,NULL,NULL,NULL),(20,1,'comerciantes_recursos','Recursos e Limites','ConfiguraÃ§Ãµes de recursos e limites',NULL,NULL,'fas fa-cubes',5,1,NULL,'pending',NULL,NULL,NULL,NULL),(21,1,'comerciantes_notificacoes','NotificaÃ§Ãµes','ConfiguraÃ§Ãµes de notificaÃ§Ãµes',NULL,NULL,'fas fa-bell',6,1,NULL,'pending',NULL,NULL,NULL,NULL),(22,1,'afiliados_geral','ConfiguraÃ§Ãµes de Afiliados','ConfiguraÃ§Ãµes gerais dos afiliados',NULL,NULL,'fas fa-handshake',7,1,NULL,'pending',NULL,NULL,NULL,NULL),(23,1,'afiliados_comissoes','ComissÃµes','ConfiguraÃ§Ãµes de comissÃµes',NULL,NULL,'fas fa-percentage',8,1,NULL,'pending',NULL,NULL,NULL,NULL),(24,1,'afiliados_pagamentos','Pagamentos','ConfiguraÃ§Ãµes de pagamentos',NULL,NULL,'fas fa-money-check',9,1,NULL,'pending',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `config_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_history`
--

DROP TABLE IF EXISTS `config_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID da empresa',
  `config_id` bigint unsigned NOT NULL COMMENT 'ID da configuração',
  `site_id` bigint unsigned DEFAULT NULL COMMENT 'ID do site',
  `ambiente_id` bigint unsigned DEFAULT NULL COMMENT 'ID do ambiente',
  `acao` enum('create','update','delete') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ação realizada',
  `valor_anterior` text COLLATE utf8mb4_unicode_ci COMMENT 'Valor anterior',
  `valor_novo` text COLLATE utf8mb4_unicode_ci COMMENT 'Novo valor',
  `usuario_id` int DEFAULT NULL COMMENT 'ID do usuário',
  `usuario_nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome do usuário',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP do usuário',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'User-Agent do navegador',
  `contexto_info` text COLLATE utf8mb4_unicode_ci COMMENT 'Informações de contexto',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash para sincronização',
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
  `sync_data` timestamp NULL DEFAULT NULL COMMENT 'Data da última sincronização',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `config_history_empresa_id_index` (`empresa_id`),
  KEY `config_history_config_id_index` (`config_id`),
  KEY `config_history_usuario_id_index` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_history`
--

LOCK TABLES `config_history` WRITE;
/*!40000 ALTER TABLE `config_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `config_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_sites`
--

DROP TABLE IF EXISTS `config_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID da empresa',
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único do site',
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome de exibição do site',
  `descricao` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrição do site',
  `base_url_padrao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL base padrão do site',
  `ativo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Status do site',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash para sincronização',
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
  `sync_data` timestamp NULL DEFAULT NULL COMMENT 'Data da última sincronização',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_sites_empresa_id_codigo_unique` (`empresa_id`,`codigo`),
  KEY `config_sites_empresa_id_index` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_sites`
--

LOCK TABLES `config_sites` WRITE;
/*!40000 ALTER TABLE `config_sites` DISABLE KEYS */;
INSERT INTO `config_sites` VALUES (1,1,'marketplace_web','Marketplace Web','Sistema principal do Marketplace','https://marketplace.exemplo.com',1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(2,1,'admin','Admin','Painel administrativo do Marketplace','https://admin.marketplace.exemplo.com',1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(3,2,'sistema','Sistema','Sistema administrativo da Pizzaria','/sistema',1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(4,2,'pdv','PDV','Ponto de venda da Pizzaria','/pdv',1,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL);
/*!40000 ALTER TABLE `config_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_values`
--

DROP TABLE IF EXISTS `config_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL COMMENT 'ID da empresa',
  `config_id` bigint unsigned NOT NULL COMMENT 'ID da definição da configuração',
  `site_id` bigint unsigned DEFAULT NULL COMMENT 'ID do site específico',
  `ambiente_id` bigint unsigned DEFAULT NULL COMMENT 'ID do ambiente específico',
  `valor` text COLLATE utf8mb4_unicode_ci COMMENT 'Valor da configuração',
  `usuario_id` int DEFAULT NULL COMMENT 'ID do usuário que fez a alteração',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash para sincronização',
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
  `sync_data` timestamp NULL DEFAULT NULL COMMENT 'Data da última sincronização',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_values_empresa_id_config_id_site_id_ambiente_id_unique` (`empresa_id`,`config_id`,`site_id`,`ambiente_id`),
  KEY `config_values_empresa_id_index` (`empresa_id`),
  KEY `config_values_config_id_index` (`config_id`),
  KEY `config_values_site_id_index` (`site_id`),
  KEY `config_values_ambiente_id_index` (`ambiente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_values`
--

LOCK TABLES `config_values` WRITE;
/*!40000 ALTER TABLE `config_values` DISABLE KEYS */;
INSERT INTO `config_values` VALUES (1,1,1,1,1,'MeuFinanceiro',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(2,1,2,1,1,'1.0.0',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(3,1,3,1,1,'false',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(4,1,4,1,1,'false',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(5,1,5,1,1,'E_ALL',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(6,1,6,1,1,'Marketplace Demo Ltda',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(7,1,7,1,1,'12.345.678/0001-90',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(8,1,8,1,1,'admin@marketplace.local',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(9,1,9,1,1,'(99) 99999-9999',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(10,1,10,1,1,'Rua Exemplo, 123 - Centro',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(11,1,11,1,1,'/assets/img/logo.png',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(12,1,12,1,1,'1.0.0',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(13,1,13,1,1,'https://api.meufinanceiro.com/updates',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(14,1,14,1,1,'7',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(15,1,15,1,1,'XYZ-DEV-1234-ABCD-5678',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(16,1,16,1,1,'{\"basic\":{\"nome\":\"BÃ¡sico\",\"usuarios\":5,\"preco\":99},\"standard\":{\"nome\":\"PadrÃ£o\",\"usuarios\":10,\"preco\":199},\"premium\":{\"nome\":\"Premium\",\"usuarios\":30,\"preco\":299}}',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(17,1,17,1,1,'100',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(18,1,18,1,1,'15',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(19,1,19,1,1,'7',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(20,1,20,1,1,'Bem-vindo ao MeuFinanceiro! Seu acesso foi configurado com sucesso.',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(21,1,21,1,1,'Pizzaria',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(22,1,22,1,1,'standard',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(23,1,23,1,1,'10',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(24,1,24,1,1,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(25,1,25,1,1,'2026-07-31',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(26,1,26,1,1,'0',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(27,1,27,1,1,'[\"pdv\",\"financeiro\",\"estoque\",\"produtos\"]',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(28,1,28,1,1,'15',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(29,1,29,1,1,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(30,1,30,1,1,'../backups/exports/',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(31,1,31,1,1,'../temp/auto_import/',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(32,1,32,1,1,'8176661265:AAFkQyV6FrWMA3CLfORs4kAoQGNE26N3Yzk',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(33,1,33,1,1,'7644334347',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(34,1,34,1,1,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(35,2,36,3,4,'false',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(36,2,35,3,4,'Pizzaria App',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(37,2,47,3,4,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(38,2,38,3,4,'8.0',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(39,2,37,3,4,'11.0',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(40,2,39,3,4,'7.5',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(41,2,40,4,4,'33',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(42,2,43,4,4,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(43,2,42,4,4,'10',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(44,2,44,4,4,'1',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(45,2,45,4,4,'imediata',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(46,2,46,4,4,'25',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(47,2,41,4,4,'30',NULL,NULL,'pending',NULL,'2025-07-31 21:04:54','2025-07-31 21:04:54',NULL),(48,1,66,NULL,NULL,'1',NULL,NULL,'pending',NULL,'2025-08-02 08:57:56','2025-08-02 08:57:56',NULL),(49,1,67,NULL,NULL,'sandbox',NULL,NULL,'pending',NULL,'2025-08-02 08:57:56','2025-08-02 08:57:56',NULL),(50,1,68,NULL,NULL,'E8FA28B86AAD45589B80294D01639AE0',NULL,NULL,'pending',NULL,'2025-08-02 08:57:56','2025-08-02 08:57:56',NULL),(51,1,69,NULL,NULL,'84165F50AFDB402FBD5EF8A83109ADC79E6C8B8FD15C40E483E31317B6F7E5BB',NULL,NULL,'pending',NULL,'2025-08-02 08:57:56','2025-08-02 08:57:56',NULL),(52,1,81,NULL,NULL,'20.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(53,1,82,NULL,NULL,'25.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(54,1,83,NULL,NULL,'30.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(55,1,84,NULL,NULL,'35.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(56,1,86,NULL,NULL,'15',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(57,1,89,NULL,NULL,'0',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(58,1,90,NULL,NULL,'30',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(59,1,85,NULL,NULL,'100.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(60,1,87,NULL,NULL,'1',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(61,1,88,NULL,NULL,'1',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(62,1,79,NULL,NULL,'1',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(63,1,80,NULL,NULL,'90',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(64,1,76,NULL,NULL,'7',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(65,1,77,NULL,NULL,'3',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(66,1,78,NULL,NULL,'10',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(67,1,73,NULL,NULL,'16.67',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(68,1,75,NULL,NULL,'3',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(69,1,70,NULL,NULL,'97.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(70,1,71,NULL,NULL,'197.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(71,1,72,NULL,NULL,'397.00',NULL,NULL,'pending',NULL,NULL,NULL,NULL),(72,1,74,NULL,NULL,'7',NULL,NULL,'pending',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `config_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conta_bancaria`
--

DROP TABLE IF EXISTS `conta_bancaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conta_bancaria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `nome_conta` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `banco` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `agencia` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero_conta` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `codigo_sistema` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Código único do sistema',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conta_bancaria`
--

LOCK TABLES `conta_bancaria` WRITE;
/*!40000 ALTER TABLE `conta_bancaria` DISABLE KEYS */;
INSERT INTO `conta_bancaria` VALUES (1,1,'NUBANK','748','0802','78870',636.00,'2025-04-17 10:31:29','2025-07-23 21:27:30','2025-07-23 09:09:59',NULL,'pendente',NULL),(3,1,'ITAU','748','555','544445',0.00,'2025-04-17 20:13:14','2025-07-23 11:52:23','2025-07-23 09:09:59',NULL,'pendente',NULL),(4,1,'CAIXA ( DINHEIRO )','0','0','0',-265.00,'2025-04-20 10:59:54','2025-07-23 11:52:24','2025-07-23 09:09:59',NULL,'pendente',NULL),(5,1,'Conta do thiago','Banco do brasil','2536','220680',0.00,'2025-06-10 18:56:17','2025-07-23 11:52:24','2025-07-23 09:09:59',NULL,'pendente',NULL),(6,1,'teste','kjkjk','22w2w','2w2w2w',10.00,'2025-07-23 11:38:15','2025-07-23 11:52:24','2025-07-23 11:38:15',NULL,'pendente',NULL),(7,1,'teste off','545','5454','545',54.00,'2025-07-23 10:38:52','2025-07-23 11:52:25','2025-07-23 11:52:25',NULL,'pendente',NULL);
/*!40000 ALTER TABLE `conta_bancaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conta_gerencial`
--

DROP TABLE IF EXISTS `conta_gerencial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conta_gerencial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `classificacao_dre_id` int DEFAULT NULL,
  `tipo_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conta_gerencial`
--

LOCK TABLES `conta_gerencial` WRITE;
/*!40000 ALTER TABLE `conta_gerencial` DISABLE KEYS */;
INSERT INTO `conta_gerencial` VALUES (1,'ABATIMENTO','Descontos concedidos nas vendas que reduzem o valor da receita bruta',1,1,7,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:31'),(3,'AGUA','Despesas com consumo de Ã¡gua e esgoto',1,1,11,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:17'),(4,'ALUGUEL RECEITA','Despesas com aluguel de imÃ³veis para operaÃ§Ã£o da empresa',1,1,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:17'),(5,'EMPRESTIMOS E AMORTIZAÃ‡ÃƒO','Pagamentos de emprÃ©stimos e amortizaÃ§Ãµes de dÃ­vidas',1,1,5,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:17'),(6,'FINANCIAMENTOS','Receitas ou despesas relacionadas a operaÃ§Ãµes de financiamento',1,1,12,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:17'),(7,'ENTRADAS','Receitas diversas nÃ£o classificadas em outras contas',1,1,13,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:18'),(10,'COMPRA DE EQUIPAMENTOS','AquisiÃ§Ã£o de equipamentos para uso na empresa',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:18'),(11,'COMPRA DE MERCADORIA REVENDA','AquisiÃ§Ã£o de mercadorias para revenda ou consumo',1,1,14,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:18'),(13,'DIREITOS TRABALHISTAS','Encargos trabalhistas e previdenciÃ¡rios sobre a folha de pagamento',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:19'),(14,'COMBUSTIVEL E PEDÃGIO','Despesas com combustÃ­vel para veÃ­culos e pedÃ¡gios',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:19'),(15,'COMISSÃƒO DE VENDA','Pagamento de comissÃµes sobre vendas realizadas',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:19'),(16,'CONTABILIDADE','HonorÃ¡rios contÃ¡beis e serviÃ§os de escrituraÃ§Ã£o',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:20'),(17,'DEVOLUÃ‡Ã•ES / ABATIMANETOS','DevoluÃ§Ãµes de mercadorias e abatimentos concedidos',1,1,7,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:20'),(18,'MARKETING E PROPAGANDA','Despesas com propaganda e aÃ§Ãµes de marketing',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:20'),(19,'HONORARIOS ADVOGATÃCIOS ','HonorÃ¡rios pagos a advogados e serviÃ§os jurÃ­dicos',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:20'),(20,'IMPOSTOS','Impostos sobre vendas e outras obrigaÃ§Ãµes tributÃ¡rias',1,1,7,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:21'),(21,'INTERNET','Despesas com serviÃ§os de internet e comunicaÃ§Ã£o de dados',1,1,11,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:21'),(22,'IPTU','Imposto Predial e Territorial Urbano sobre imÃ³veis da empresa',1,1,3,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:21'),(23,'JUROS DE EMPRÃ‰STIMOS ','Juros pagos sobre emprÃ©stimos e financiamentos',1,1,5,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:22'),(24,'LUZ / ENERGIA','Despesas com consumo de energia elÃ©trica',1,1,11,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:22'),(25,'MANUTENÃ‡ÃƒO','Despesas com manutenÃ§Ã£o de equipamentos e instalaÃ§Ãµes',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:22'),(26,'INSUMO - MATERIA PRIMA','AquisiÃ§Ã£o de matÃ©rias-primas para produÃ§Ã£o',1,1,4,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:23'),(27,'SALÃRIOS','RemuneraÃ§Ã£o fixa paga aos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:23'),(28,'SEGUROS EM GERAL','PrÃªmios de seguros contratados pela empresa',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:23'),(29,'TARIFAS BANCÃRIAS','Tarifas e taxas cobradas por instituiÃ§Ãµes financeiras',1,1,5,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:23'),(30,'TELEFONE / CELULAR','Despesas com serviÃ§os de telefonia fixa e mÃ³vel',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:24'),(31,'VENDAS ( MAIS DELIVERY )','Receitas de vendas com entrega delivery',1,1,13,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:24'),(32,'VENDAS ( CONSUMER )','Receitas de vendas para consumidores finais',1,1,13,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:24'),(33,'VENDAS (ALLOY)','Receitas de vendas especÃ­ficas para a marca Alloy',1,1,13,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:25'),(34,'MATERIAL DE CONSTRUÃ‡ÃƒO','AquisiÃ§Ã£o de materiais para construÃ§Ã£o ou reforma',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:25'),(35,'CARTÃƒO DE CREDITO','Taxas e despesas relacionadas a operaÃ§Ãµes com cartÃµes',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:25'),(36,'VALE','BenefÃ­cios como vale-transporte e vale-refeiÃ§Ã£o',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:25'),(43,'ALUGUEL DESPESA','Despesas com locaÃ§Ã£o de imÃ³veis para operaÃ§Ã£o da empresa',1,1,3,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:26'),(44,'VENDAS ( DINHEIRO )','Receitas de vendas realizadas em dinheiro',1,1,13,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:26'),(45,'FGTS','Recolhimento do Fundo de Garantia por Tempo de ServiÃ§o',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:26'),(46,'INSS PATRONAL','ContribuiÃ§Ã£o previdenciÃ¡ria patronal sobre a folha',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:27'),(47,'13Âº SALÃRIO','ProvisÃ£o para pagamento do dÃ©cimo terceiro salÃ¡rio',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:27'),(48,'FÃ‰RIAS','ProvisÃ£o para pagamento de fÃ©rias dos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:27'),(49,'RESCISÃ•ES','Pagamentos de rescisÃµes contratuais',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:28'),(50,'PLR','ParticipaÃ§Ã£o nos Lucros ou Resultados aos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:28'),(51,'BONIFICAÃ‡Ã•ES','BonificaÃ§Ãµes e gratificaÃ§Ãµes pagas aos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:28'),(52,'ADICIONAIS','Adicionais como noturno, periculosidade, insalubridade',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:28'),(53,'HORAS EXTRAS','Pagamento de horas extras trabalhadas',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:29'),(54,'ASSISTÃŠNCIA MÃ‰DICA','Custos com planos de saÃºde para funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:29'),(55,'TREINAMENTOS','Despesas com capacitaÃ§Ã£o e desenvolvimento de pessoal',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:29'),(56,'UNIFORMES','Custos com fornecimento de uniformes aos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:30'),(57,'EPI','Equipamentos de ProteÃ§Ã£o Individual para funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:30'),(58,'ALIMENTAÃ‡ÃƒO','Custos com alimentaÃ§Ã£o fornecida aos funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:30'),(59,'FRETE / TRANSPORTE','Custos com transporte de funcionÃ¡rios',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:31'),(60,'DEPRECIAÃ‡ÃƒO DE EQUIPAMENTO','',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:31'),(61,'EMBALAGENS','',1,1,4,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:31'),(62,'MATERIAL ESCRITÃ“RIO','',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:31'),(63,'PROMOÃ‡Ã•ES DE VENDA','',1,1,10,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:32'),(64,'ENTREGADORES','',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:32'),(65,'DIARIAS','',1,1,8,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:32'),(66,'OUTRAS SAIDAS','',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:33'),(67,'OUTRAS ENTRADAS','ENTRADAS DIVERSAS',3,1,15,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:33'),(68,'SISTEMA PIZZARIA','SISTEMA',1,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:33'),(69,'RECEBIMENTO FIADO','',3,1,15,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:34'),(70,'OUTROS PAGAMENTOS PIZZARIA','',3,1,6,2,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:34');
/*!40000 ALTER TABLE `conta_gerencial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conta_gerencial_natureza`
--

DROP TABLE IF EXISTS `conta_gerencial_natureza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conta_gerencial_natureza` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `empresa_id` int DEFAULT NULL,
  `nome_completo` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conta_gerencial_natureza`
--

LOCK TABLES `conta_gerencial_natureza` WRITE;
/*!40000 ALTER TABLE `conta_gerencial_natureza` DISABLE KEYS */;
INSERT INTO `conta_gerencial_natureza` VALUES (1,'despesa_fixa',1,'Despesa Fixa','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:32'),(2,'despesa_variavel',1,'Despesa VariÃ¡vel ','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:35'),(3,'custo_fixo',1,'Custo Fixo','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:35'),(4,'custo_variavel',1,'Capital de giro','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:35'),(5,'receita_vendas',1,'Receita Com vendas ','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:36'),(6,'OUTRAS RECEITAS',1,'Outras Recitas ','2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:36');
/*!40000 ALTER TABLE `conta_gerencial_natureza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conta_gerencial_naturezas`
--

DROP TABLE IF EXISTS `conta_gerencial_naturezas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conta_gerencial_naturezas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `conta_gerencial_id` int NOT NULL,
  `natureza_id` int NOT NULL,
  `empresa_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conta_gerencial_id` (`conta_gerencial_id`,`natureza_id`),
  KEY `natureza_id` (`natureza_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conta_gerencial_naturezas`
--

LOCK TABLES `conta_gerencial_naturezas` WRITE;
/*!40000 ALTER TABLE `conta_gerencial_naturezas` DISABLE KEYS */;
INSERT INTO `conta_gerencial_naturezas` VALUES (10,43,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:33'),(17,45,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:37'),(18,46,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:37'),(19,47,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:37'),(20,48,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:38'),(21,49,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:38'),(22,50,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:38'),(23,51,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:39'),(24,52,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:39'),(25,53,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:39'),(26,54,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:40'),(27,55,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:40'),(28,56,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:40'),(29,57,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:40'),(30,58,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:41'),(31,59,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:41'),(32,36,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:41'),(33,28,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:42'),(34,60,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:42'),(35,25,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:42'),(36,16,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:43'),(37,18,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:43'),(38,26,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:43'),(39,15,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:43'),(40,59,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:44'),(41,24,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:44'),(44,27,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:44'),(45,3,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:45'),(47,28,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:45'),(48,21,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:45'),(49,30,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:46'),(50,25,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:46'),(51,18,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:46'),(52,15,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:46'),(53,62,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:47'),(54,63,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:47'),(55,59,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:47'),(56,64,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:48'),(58,1,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:48'),(60,31,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:48'),(61,32,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:49'),(62,33,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:49'),(63,44,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:49'),(64,65,3,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:49'),(65,66,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:50'),(66,67,6,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:50'),(67,11,4,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:50'),(68,68,1,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:51'),(69,5,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:51'),(70,70,2,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:51'),(71,69,5,1,'2025-07-23 09:09:59',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:51');
/*!40000 ALTER TABLE `conta_gerencial_naturezas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_cache`
--

DROP TABLE IF EXISTS `empresa_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_cache` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_key` (`empresa_id`,`key`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_expiration` (`expiration`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_cache_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_cache`
--

LOCK TABLES `empresa_cache` WRITE;
/*!40000 ALTER TABLE `empresa_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_certificados`
--

DROP TABLE IF EXISTS `empresa_certificados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_certificados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `tipo` enum('e-CNPJ','e-CPF','NF-e','NFC-e') COLLATE utf8mb3_unicode_ci NOT NULL,
  `data_validade` date NOT NULL,
  `senha` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `caminho_arquivo` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `fk_empresa_certificados_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_certificados`
--

LOCK TABLES `empresa_certificados` WRITE;
/*!40000 ALTER TABLE `empresa_certificados` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_certificados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_cnaes`
--

DROP TABLE IF EXISTS `empresa_cnaes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_cnaes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `cnae` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `principal` tinyint(1) DEFAULT '0',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `fk_empresa_cnaes_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_cnaes`
--

LOCK TABLES `empresa_cnaes` WRITE;
/*!40000 ALTER TABLE `empresa_cnaes` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_cnaes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_config_seguranca`
--

DROP TABLE IF EXISTS `empresa_config_seguranca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_config_seguranca` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `senha_tamanho_minimo` int NOT NULL DEFAULT '8',
  `senha_exigir_maiuscula` tinyint(1) NOT NULL DEFAULT '1',
  `senha_exigir_numero` tinyint(1) NOT NULL DEFAULT '1',
  `senha_exigir_especial` tinyint(1) NOT NULL DEFAULT '1',
  `senha_validade_dias` int DEFAULT '90' COMMENT 'NULL = sem expiração',
  `max_tentativas_falhas` int NOT NULL DEFAULT '5',
  `tempo_bloqueio_minutos` int NOT NULL DEFAULT '30',
  `tempo_sessao_minutos` int NOT NULL DEFAULT '120',
  `autenticacao_dois_fatores` tinyint(1) NOT NULL DEFAULT '0',
  `restricao_ip` tinyint(1) NOT NULL DEFAULT '0',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_id` (`empresa_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_seguranca_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_config_seguranca`
--

LOCK TABLES `empresa_config_seguranca` WRITE;
/*!40000 ALTER TABLE `empresa_config_seguranca` DISABLE KEYS */;
INSERT INTO `empresa_config_seguranca` VALUES (1,1,8,1,1,1,90,5,30,120,0,0,'pendente','2025-08-01 00:19:47',NULL,'2025-08-01 00:19:47','2025-08-01 00:19:47');
/*!40000 ALTER TABLE `empresa_config_seguranca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_dias_semana`
--

DROP TABLE IF EXISTS `empresa_dias_semana`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_dias_semana` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `nome` varchar(13) NOT NULL,
  `abreviacao` char(3) NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_dias_semana`
--

LOCK TABLES `empresa_dias_semana` WRITE;
/*!40000 ALTER TABLE `empresa_dias_semana` DISABLE KEYS */;
INSERT INTO `empresa_dias_semana` VALUES (1,'Segunda-feira','Seg','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:33'),(2,'Terça-feira','Ter','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:52'),(3,'Quarta-feira','Qua','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:53'),(4,'Quinta-feira','Qui','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:53'),(5,'Sexta-feira','Sex','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:53'),(6,'Sábado','Sáb','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:54'),(7,'Domingo','Dom','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:12:54');
/*!40000 ALTER TABLE `empresa_dias_semana` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_horarios_funcionamento`
--

DROP TABLE IF EXISTS `empresa_horarios_funcionamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_horarios_funcionamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `dia_semana_id` int DEFAULT NULL,
  `aberto` tinyint(1) DEFAULT '1',
  `hora_abertura` time DEFAULT NULL,
  `hora_fechamento` time DEFAULT NULL,
  `sistema` enum('TODOS','PDV','FINANCEIRO','ONLINE') COLLATE utf8mb3_unicode_ci DEFAULT 'TODOS',
  `is_excecao` tinyint(1) DEFAULT '0',
  `data_excecao` date DEFAULT NULL,
  `descricao_excecao` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  KEY `idx_empresa_data` (`empresa_id`,`data_excecao`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_horarios_funcionamento`
--

LOCK TABLES `empresa_horarios_funcionamento` WRITE;
/*!40000 ALTER TABLE `empresa_horarios_funcionamento` DISABLE KEYS */;
INSERT INTO `empresa_horarios_funcionamento` VALUES (9,1,1,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:03:38','2025-07-23 21:27:34','2025-07-23 09:10:00',NULL,'pendente'),(10,1,2,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:03:42','2025-07-23 11:12:55','2025-07-23 09:10:00',NULL,'pendente'),(13,1,3,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:13:45','2025-07-23 11:12:55','2025-07-23 09:10:00',NULL,'pendente'),(14,1,4,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:13:49','2025-07-23 11:12:55','2025-07-23 09:10:00',NULL,'pendente'),(16,1,6,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:13:55','2025-07-23 11:12:56','2025-07-23 09:10:00',NULL,'pendente'),(17,1,7,1,'18:00:00','23:00:00','PDV',0,NULL,NULL,'2025-07-11 08:13:58','2025-07-23 11:12:56','2025-07-23 09:10:00',NULL,'pendente'),(18,1,1,1,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:23','2025-07-23 11:12:56','2025-07-23 09:10:00',NULL,'pendente'),(19,1,2,1,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:27','2025-07-23 11:12:57','2025-07-23 09:10:00',NULL,'pendente'),(20,1,3,1,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:31','2025-07-23 11:12:57','2025-07-23 09:10:00',NULL,'pendente'),(21,1,4,1,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:33','2025-07-23 11:12:57','2025-07-23 09:10:00',NULL,'pendente'),(22,1,5,1,'01:00:00','08:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:37','2025-07-23 11:12:58','2025-07-23 09:10:00',NULL,'pendente'),(23,1,6,0,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:43','2025-07-23 11:12:58','2025-07-23 09:10:00',NULL,'pendente'),(24,1,7,0,'08:00:00','15:00:00','FINANCEIRO',0,NULL,NULL,'2025-07-11 08:14:47','2025-07-23 11:12:58','2025-07-23 09:10:00',NULL,'pendente'),(25,1,1,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:11','2025-07-23 11:12:58','2025-07-23 09:10:00',NULL,'pendente'),(26,1,2,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:14','2025-07-23 11:12:59','2025-07-23 09:10:00',NULL,'pendente'),(27,1,3,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:17','2025-07-23 11:12:59','2025-07-23 09:10:00',NULL,'pendente'),(28,1,4,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:20','2025-07-23 11:12:59','2025-07-23 09:10:00',NULL,'pendente'),(29,1,5,1,'01:00:00','04:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:22','2025-07-23 11:13:00','2025-07-23 09:10:00',NULL,'pendente'),(30,1,6,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:26','2025-07-23 11:13:00','2025-07-23 09:10:00',NULL,'pendente'),(31,1,7,1,'18:00:00','22:45:00','ONLINE',0,NULL,NULL,'2025-07-11 08:15:29','2025-07-23 11:13:00','2025-07-23 09:10:00',NULL,'pendente'),(33,1,5,1,'01:00:00','08:00:00','PDV',0,NULL,NULL,'2025-07-11 10:04:34','2025-07-23 11:13:00','2025-07-23 09:10:00',NULL,'pendente'),(34,1,NULL,1,'03:00:00','05:00:00','PDV',1,'2025-07-11','wswsws','2025-07-11 10:15:30','2025-07-23 11:13:01','2025-07-23 09:10:00',NULL,'pendente'),(35,1,0,1,'03:00:00','05:00:00','FINANCEIRO',1,'2025-07-11',NULL,'2025-07-11 10:16:53','2025-07-23 11:13:01','2025-07-23 09:10:00',NULL,'pendente');
/*!40000 ALTER TABLE `empresa_horarios_funcionamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_logs_permissoes`
--

DROP TABLE IF EXISTS `empresa_logs_permissoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_logs_permissoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL COMMENT 'Usuário que teve permissão alterada',
  `autor_id` int NOT NULL COMMENT 'Usuário que fez a alteração',
  `empresa_id` int NOT NULL,
  `acao` enum('conceder_papel','revogar_papel','conceder_permissao','revogar_permissao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `alvo_id` int NOT NULL COMMENT 'ID do papel ou permissão',
  `tipo_alvo` enum('papel','permissao') COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalhes` text COLLATE utf8mb4_unicode_ci COMMENT 'Detalhes adicionais em JSON',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_autor_id` (`autor_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_logs_autor` FOREIGN KEY (`autor_id`) REFERENCES `empresa_usuarios` (`id`),
  CONSTRAINT `fk_logs_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_logs_permissoes`
--

LOCK TABLES `empresa_logs_permissoes` WRITE;
/*!40000 ALTER TABLE `empresa_logs_permissoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_logs_permissoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_papeis`
--

DROP TABLE IF EXISTS `empresa_papeis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_papeis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único do papel',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `is_sistema` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se é um papel do sistema (não pode ser excluído)',
  `nivel_acesso` int NOT NULL DEFAULT '0' COMMENT 'Nível hierárquico (0-100)',
  `empresa_id` int DEFAULT NULL COMMENT 'NULL para papéis do sistema, ID da empresa para papéis personalizados',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_codigo` (`empresa_id`,`codigo`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_nivel_acesso` (`nivel_acesso`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_papeis_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_papeis`
--

LOCK TABLES `empresa_papeis` WRITE;
/*!40000 ALTER TABLE `empresa_papeis` DISABLE KEYS */;
INSERT INTO `empresa_papeis` VALUES (1,'Super Administrador','super_admin','Controle total do sistema, incluindo todas as empresas',1,100,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(2,'Administrador','admin','Administrador de uma empresa específica',1,90,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(3,'Gerente','gerente','Gerente com acesso à maioria das funcionalidades',1,70,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(4,'Supervisor','supervisor','Supervisor com acesso a relatórios e algumas configurações',1,60,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(5,'Operador','operador','Operador com acesso às funções básicas do sistema',1,40,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(6,'Vendedor','vendedor','Acesso ao PDV e funções de venda',1,30,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(7,'Caixa','caixa','Acesso às funções de caixa',1,20,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(8,'Cliente','cliente','Cliente com acesso limitado',1,10,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(9,'Visitante','visitante','Acesso apenas para visualização',1,5,NULL,'pendente','2025-08-01 00:13:52',NULL,'2025-08-01 00:13:52','2025-08-01 00:13:52',NULL),(10,'Caixa','caixa','Papel migrado do perfil Caixa',0,20,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(11,'Atendente','atendente','Papel migrado do perfil Atendente',0,30,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(12,'Administrador','administrador','Papel migrado do perfil Administrador',0,90,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL);
/*!40000 ALTER TABLE `empresa_papeis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_papel_permissoes`
--

DROP TABLE IF EXISTS `empresa_papel_permissoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_papel_permissoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `papel_id` int NOT NULL,
  `permissao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` int NOT NULL DEFAULT '1',
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` datetime DEFAULT NULL,
  `sync_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_papel_permissao` (`papel_id`,`permissao`),
  KEY `idx_empresa` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_papel_permissoes`
--

LOCK TABLES `empresa_papel_permissoes` WRITE;
/*!40000 ALTER TABLE `empresa_papel_permissoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_papel_permissoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_permissoes`
--

DROP TABLE IF EXISTS `empresa_permissoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_permissoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome amigável da permissão',
  `codigo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único da permissão (ex: usuarios.criar)',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `categoria` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Categoria para agrupar permissões',
  `is_sistema` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Se é uma permissão do sistema',
  `empresa_id` int DEFAULT NULL COMMENT 'NULL para permissões do sistema, ID da empresa para permissões personalizadas',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_codigo` (`empresa_id`,`codigo`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_permissoes_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_permissoes`
--

LOCK TABLES `empresa_permissoes` WRITE;
/*!40000 ALTER TABLE `empresa_permissoes` DISABLE KEYS */;
INSERT INTO `empresa_permissoes` VALUES (1,'Visualizar Dashboard','dashboard.visualizar','Visualizar o painel principal','Dashboard',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(2,'Listar Usuários','usuarios.listar','Ver lista de usuários','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(3,'Ver Usuário','usuarios.visualizar','Ver detalhes de um usuário','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(4,'Criar Usuário','usuarios.criar','Criar novos usuários','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(5,'Editar Usuário','usuarios.editar','Editar usuários existentes','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(6,'Excluir Usuário','usuarios.excluir','Excluir usuários','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(7,'Gerenciar Papéis','usuarios.gerenciar_papeis','Atribuir papéis aos usuários','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(8,'Gerenciar Permissões','usuarios.gerenciar_permissoes','Atribuir permissões específicas aos usuários','Usuários',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(9,'Listar Empresas','empresas.listar','Ver lista de empresas','Empresas',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(10,'Ver Empresa','empresas.visualizar','Ver detalhes de uma empresa','Empresas',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(11,'Criar Empresa','empresas.criar','Criar novas empresas','Empresas',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(12,'Editar Empresa','empresas.editar','Editar empresas existentes','Empresas',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(13,'Excluir Empresa','empresas.excluir','Excluir empresas','Empresas',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(14,'Acessar PDV','pdv.acessar','Acessar o módulo de PDV','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(15,'Iniciar Venda','pdv.iniciar_venda','Iniciar uma nova venda','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(16,'Adicionar Item','pdv.adicionar_item','Adicionar item a uma venda','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(17,'Remover Item','pdv.remover_item','Remover item de uma venda','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(18,'Aplicar Desconto','pdv.aplicar_desconto','Aplicar desconto em vendas','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(19,'Finalizar Venda','pdv.finalizar_venda','Finalizar uma venda','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(20,'Cancelar Venda','pdv.cancelar_venda','Cancelar uma venda','PDV',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(21,'Abrir Caixa','caixa.abrir','Abrir um caixa','Caixa',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(22,'Fechar Caixa','caixa.fechar','Fechar um caixa','Caixa',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(23,'Realizar Sangria','caixa.sangria','Realizar uma sangria de caixa','Caixa',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(24,'Realizar Suprimento','caixa.suprimento','Realizar um suprimento de caixa','Caixa',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(25,'Ver Relatório de Caixa','caixa.relatorio','Ver relatório de movimentações de caixa','Caixa',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(26,'Listar Produtos','produtos.listar','Ver lista de produtos','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(27,'Ver Produto','produtos.visualizar','Ver detalhes de um produto','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(28,'Criar Produto','produtos.criar','Criar novos produtos','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(29,'Editar Produto','produtos.editar','Editar produtos existentes','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(30,'Excluir Produto','produtos.excluir','Excluir produtos','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(31,'Gerenciar Estoque','produtos.gerenciar_estoque','Gerenciar estoque de produtos','Produtos',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(32,'Listar Clientes','clientes.listar','Ver lista de clientes','Clientes',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(33,'Ver Cliente','clientes.visualizar','Ver detalhes de um cliente','Clientes',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(34,'Criar Cliente','clientes.criar','Criar novos clientes','Clientes',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(35,'Editar Cliente','clientes.editar','Editar clientes existentes','Clientes',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(36,'Excluir Cliente','clientes.excluir','Excluir clientes','Clientes',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(37,'Relatórios de Vendas','relatorios.vendas','Acessar relatórios de vendas','Relatórios',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(38,'Relatórios Financeiros','relatorios.financeiros','Acessar relatórios financeiros','Relatórios',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(39,'Relatórios de Estoque','relatorios.estoque','Acessar relatórios de estoque','Relatórios',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(40,'Relatórios de Clientes','relatorios.clientes','Acessar relatórios de clientes','Relatórios',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(41,'Relatórios Avançados','relatorios.avancados','Acessar relatórios avançados e personalizados','Relatórios',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(42,'Configurações Gerais','configuracoes.gerais','Acessar configurações gerais do sistema','Configurações',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(43,'Configurações de PDV','configuracoes.pdv','Acessar configurações do PDV','Configurações',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(44,'Configurações de Impressão','configuracoes.impressao','Acessar configurações de impressão','Configurações',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(45,'Configurações de Segurança','configuracoes.seguranca','Acessar configurações de segurança','Configurações',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(46,'Backup e Restauração','configuracoes.backup','Realizar backup e restauração de dados','Configurações',1,NULL,'pendente','2025-08-01 00:13:57',NULL,'2025-08-01 00:13:57','2025-08-01 00:13:57',NULL),(47,'Abrir Caixa','caixa-abrir_caixa','Abrir Caixa','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(48,'Excluir Item','caixa-excluir_item','Excluir Item','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(49,'acesso ao sistema ','atendente-acesso_sistema','acesso ao sistema ','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(50,'Caixa->Acessar Pdv','caixa-acesso_pdv','Caixa->Acessar Pdv','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(51,'Form Pagamento','caixa-form_pgto','Form Pagamento','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(52,'caixa-Finalizar_venda','Finalizar venda','caixa-Finalizar_venda','Migrado',0,1,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL);
/*!40000 ALTER TABLE `empresa_permissoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_socios`
--

DROP TABLE IF EXISTS `empresa_socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_socios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `cpf` varchar(14) COLLATE utf8mb3_unicode_ci NOT NULL,
  `participacao` decimal(5,2) DEFAULT NULL,
  `administrador` tinyint(1) DEFAULT '0',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `fk_empresa_socios_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_socios`
--

LOCK TABLES `empresa_socios` WRITE;
/*!40000 ALTER TABLE `empresa_socios` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_socios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuario_empresas`
--

DROP TABLE IF EXISTS `empresa_usuario_empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuario_empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `is_proprietario` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se é proprietário da empresa',
  `is_padrao` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Empresa padrão do usuário',
  `data_associacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acesso` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_empresa` (`usuario_id`,`empresa_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_ue_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ue_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuario_empresas`
--

LOCK TABLES `empresa_usuario_empresas` WRITE;
/*!40000 ALTER TABLE `empresa_usuario_empresas` DISABLE KEYS */;
INSERT INTO `empresa_usuario_empresas` VALUES (1,1,1,1,1,'2025-08-01 00:19:47',NULL,'pendente','2025-08-01 00:19:47',NULL,'2025-08-01 00:19:47','2025-08-01 00:19:47',NULL),(2,3,1,1,1,'2025-08-01 00:19:47',NULL,'pendente','2025-08-01 00:19:47',NULL,'2025-08-01 00:19:47','2025-08-01 00:19:47',NULL),(3,5,1,1,1,'2025-08-01 00:19:47',NULL,'pendente','2025-08-01 00:19:47',NULL,'2025-08-01 00:19:47','2025-08-01 00:19:47',NULL);
/*!40000 ALTER TABLE `empresa_usuario_empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuario_papeis`
--

DROP TABLE IF EXISTS `empresa_usuario_papeis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuario_papeis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `papel_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `is_principal` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se é o papel principal do usuário nesta empresa',
  `atribuido_por` int DEFAULT NULL COMMENT 'ID do usuário que atribuiu o papel',
  `data_atribuicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_expiracao` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração (opcional)',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_papel_empresa` (`usuario_id`,`papel_id`,`empresa_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_papel_id` (`papel_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_up_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_papel` FOREIGN KEY (`papel_id`) REFERENCES `empresa_papeis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuario_papeis`
--

LOCK TABLES `empresa_usuario_papeis` WRITE;
/*!40000 ALTER TABLE `empresa_usuario_papeis` DISABLE KEYS */;
INSERT INTO `empresa_usuario_papeis` VALUES (1,1,7,1,0,NULL,'2025-08-01 00:15:26',NULL,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:27:23',NULL),(2,3,12,1,1,NULL,'2025-08-01 00:15:26',NULL,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(3,5,12,1,1,NULL,'2025-08-01 00:15:26',NULL,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(4,1,10,1,1,NULL,'2025-08-01 00:15:26',NULL,'pendente','2025-08-01 00:15:26',NULL,'2025-08-01 00:15:26','2025-08-01 00:15:26',NULL),(10,2,2,1,1,NULL,'2025-08-01 00:21:43',NULL,'pendente','2025-08-01 00:21:43',NULL,'2025-08-01 00:21:43','2025-08-01 00:21:43',NULL);
/*!40000 ALTER TABLE `empresa_usuario_papeis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuario_permissoes`
--

DROP TABLE IF EXISTS `empresa_usuario_permissoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuario_permissoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `permissao_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `is_concedida` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=concedida, 0=negada (sobrepõe permissões de papel)',
  `atribuido_por` int DEFAULT NULL COMMENT 'ID do usuário que atribuiu a permissão',
  `data_atribuicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_expiracao` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração (opcional)',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_permissao_empresa` (`usuario_id`,`permissao_id`,`empresa_id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_permissao_id` (`permissao_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_up_empresa_id` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_permissao_id` FOREIGN KEY (`permissao_id`) REFERENCES `empresa_permissoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuario_permissoes`
--

LOCK TABLES `empresa_usuario_permissoes` WRITE;
/*!40000 ALTER TABLE `empresa_usuario_permissoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa_usuario_permissoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuarios`
--

DROP TABLE IF EXISTS `empresa_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `perfil_id` int DEFAULT NULL,
  `status` enum('ativo','inativo','pendente','bloqueado') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'pendente',
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int DEFAULT '0',
  `locked_until` timestamp NULL DEFAULT NULL,
  `two_factor_secret` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `require_password_change` tinyint(1) DEFAULT '0',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuarios`
--

LOCK TABLES `empresa_usuarios` WRITE;
/*!40000 ALTER TABLE `empresa_usuarios` DISABLE KEYS */;
INSERT INTO `empresa_usuarios` VALUES (1,NULL,NULL,'Camila','camila@gmail.com','$2y$10$ghC9dst1iVlr5vuTc0vOqO.dCcNOPqLz6mc2TphblwmCRnHAGJDzS','2025-03-24 03:41:03',1,NULL,'inativo',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2025-07-15 22:02:45','2025-07-23 21:27:35',NULL,0,'2025-07-23 09:10:00',NULL,'pendente',NULL),(2,NULL,NULL,'mazinho Pessoal','mazinhoP@gmail.com','$2y$10$u6dlWZSaafFaPvQABwihI.AkWRND4Iv3sLzjV8FGLsZNkTULXKk7u','2025-03-25 01:00:00',1,NULL,'ativo',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2025-07-15 22:02:45','2025-08-01 00:21:14',NULL,0,'2025-07-23 09:10:00',NULL,'pendente',NULL),(3,NULL,NULL,'mazinho','mazinho@gmail.com','$2y$10$7.b1JsI2ywPeMHZbioX7YOoATi9b/LbcVw5QUBhfoZ.X/Mknjbqv2','2025-04-13 14:35:00',1,NULL,'ativo','2025-08-03 08:25:13',NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2025-07-15 22:02:45','2025-08-03 04:25:13',NULL,0,'2025-07-23 09:10:00',NULL,'pendente',NULL),(5,NULL,NULL,'Thiago Souza','thiago@gmail.com','$2y$10$7.b1JsI2ywPeMHZbioX7YOoATi9b/LbcVw5QUBhfoZ.X/Mknjbqv2','2025-04-17 13:32:01',1,NULL,'ativo',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,'2025-07-15 22:02:45','2025-07-24 01:33:41',NULL,0,'2025-07-23 09:10:00',NULL,'pendente',NULL);
/*!40000 ALTER TABLE `empresa_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuarios_activity_log`
--

DROP TABLE IF EXISTS `empresa_usuarios_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuarios_activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT '1',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at_copy` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_activity_log_usuario` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuarios_activity_log`
--

LOCK TABLES `empresa_usuarios_activity_log` WRITE;
/*!40000 ALTER TABLE `empresa_usuarios_activity_log` DISABLE KEYS */;
INSERT INTO `empresa_usuarios_activity_log` VALUES (1,3,'LOGIN','Login realizado com sucesso','168.232.202.200','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-15 07:08:00',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 21:27:36'),(2,3,'LOGIN','Login realizado com sucesso','168.232.202.200','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-15 08:57:46',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:03'),(3,3,'LOGOUT','Logout realizado com sucesso','168.232.202.200','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-15 08:57:52',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:03'),(4,3,'LOGIN','Login realizado com sucesso','168.232.202.200','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-15 09:30:04',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:03'),(5,5,'LOGIN','Login realizado com sucesso','177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-15 18:38:53',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:04'),(6,5,'LOGOUT','Logout realizado com sucesso','177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-15 18:39:04',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:04'),(7,3,'LOGIN','Login realizado com sucesso','168.232.202.125','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-16 01:49:31',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:04'),(8,5,'LOGIN','Login realizado com sucesso','177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-16 16:16:25',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:04'),(9,3,'LOGIN','Login realizado com sucesso','168.232.202.217','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36','2025-07-18 02:17:00',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:05'),(10,3,'LOGIN','Login realizado com sucesso','168.232.202.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-19 00:18:36',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:05'),(11,3,'AUTO_LOGIN','Login automÃ¡tico via token \"lembrar de mim\"','168.232.202.105','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-19 06:17:45',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 11:13:05'),(12,3,'LOGIN','Login realizado com sucesso','168.232.202.242','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-22 11:00:33',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 08:57:16'),(13,3,'LOGIN','Login realizado com sucesso','168.232.202.242','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-22 12:31:43',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 08:57:16','2025-07-23 08:57:16'),(14,3,'LOGIN','Login realizado com sucesso','168.232.202.95','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-23 11:00:06',1,'2025-07-23 11:00:06',NULL,'pendente','2025-07-23 11:00:06','2025-07-23 11:00:06'),(15,5,'LOGIN','Login realizado com sucesso','177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-24 01:33:41',1,'2025-07-24 01:33:41',NULL,'pendente','2025-07-24 01:33:41','2025-07-24 01:33:41'),(16,5,'LOGOUT','Logout realizado com sucesso','177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-24 01:33:47',1,'2025-07-24 01:33:47',NULL,'pendente','2025-07-24 01:33:47','2025-07-24 01:33:47'),(17,3,'LOGIN','Login realizado com sucesso','168.232.202.16','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36','2025-07-27 07:32:19',1,'2025-07-27 07:32:19',NULL,'pendente','2025-07-27 07:32:19','2025-07-27 07:32:19'),(19,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-01 22:18:21',1,'2025-08-01 18:18:21',NULL,'pendente','2025-08-01 18:18:21','2025-08-01 18:18:21'),(20,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-01 23:18:10',1,'2025-08-01 19:18:10',NULL,'pendente','2025-08-01 19:18:10','2025-08-01 19:18:10'),(21,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 02:46:59',1,'2025-08-01 22:46:59',NULL,'pendente','2025-08-01 22:46:59','2025-08-01 22:46:59'),(22,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 03:12:45',1,'2025-08-01 23:12:45',NULL,'pendente','2025-08-01 23:12:45','2025-08-01 23:12:45'),(23,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 03:35:07',1,'2025-08-01 23:35:07',NULL,'pendente','2025-08-01 23:35:07','2025-08-01 23:35:07'),(24,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 05:20:00',1,'2025-08-02 01:20:00',NULL,'pendente','2025-08-02 01:20:00','2025-08-02 01:20:00'),(25,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 06:20:16',1,'2025-08-02 02:20:16',NULL,'pendente','2025-08-02 02:20:16','2025-08-02 02:20:16'),(26,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 06:24:45',1,'2025-08-02 02:24:45',NULL,'pendente','2025-08-02 02:24:45','2025-08-02 02:24:45'),(27,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 09:10:51',1,'2025-08-02 05:10:51',NULL,'pendente','2025-08-02 05:10:51','2025-08-02 05:10:51'),(28,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 12:13:58',1,'2025-08-02 08:13:58',NULL,'pendente','2025-08-02 08:13:58','2025-08-02 08:13:58'),(29,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 22:17:56',1,'2025-08-02 18:17:56',NULL,'pendente','2025-08-02 18:17:56','2025-08-02 18:17:56'),(30,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-02 23:18:12',1,'2025-08-02 19:18:12',NULL,'pendente','2025-08-02 19:18:12','2025-08-02 19:18:12'),(31,3,'LOGIN','Login realizado com sucesso','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-08-03 08:25:13',1,'2025-08-03 04:25:13',NULL,'pendente','2025-08-03 04:25:13','2025-08-03 04:25:13');
/*!40000 ALTER TABLE `empresa_usuarios_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuarios_login_attempts`
--

DROP TABLE IF EXISTS `empresa_usuarios_login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuarios_login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT '1',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_created` (`email`,`created_at`),
  KEY `idx_success` (`success`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuarios_login_attempts`
--

LOCK TABLES `empresa_usuarios_login_attempts` WRITE;
/*!40000 ALTER TABLE `empresa_usuarios_login_attempts` DISABLE KEYS */;
INSERT INTO `empresa_usuarios_login_attempts` VALUES (10,'thiagosouza_5@hotmail.com',0,'177.222.239.26','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0','2025-07-24 01:33:33',1,'2025-07-24 01:33:33',NULL,'pendente','2025-07-24 01:33:33');
/*!40000 ALTER TABLE `empresa_usuarios_login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuarios_remember_tokens`
--

DROP TABLE IF EXISTS `empresa_usuarios_remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuarios_remember_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT '1',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_remember_tokens_usuario` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuarios_remember_tokens`
--

LOCK TABLES `empresa_usuarios_remember_tokens` WRITE;
/*!40000 ALTER TABLE `empresa_usuarios_remember_tokens` DISABLE KEYS */;
INSERT INTO `empresa_usuarios_remember_tokens` VALUES (1,3,'c3b020a844ec65772e96ded402dcbb22251b935b147b32ac2d88b0bd2b05e29f','2025-07-23 21:27:37','2025-07-15 09:30:04',1,'2025-07-23 09:10:00',NULL,'pendente','2025-07-23 21:27:37'),(2,3,'$2y$12$73aGu4oPNmRRYx8/OFDvsOFKBurE6AX8XttQngKzPbFep.uT1FSVy','2025-09-01 09:10:51','2025-08-02 09:10:51',1,'2025-08-02 05:10:51',NULL,'pendente','2025-08-02 05:10:51');
/*!40000 ALTER TABLE `empresa_usuarios_remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa_usuarios_security_settings`
--

DROP TABLE IF EXISTS `empresa_usuarios_security_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa_usuarios_security_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `max_login_attempts` int DEFAULT '5',
  `lockout_duration` int DEFAULT '900',
  `session_timeout` int DEFAULT '3600',
  `password_min_length` int DEFAULT '6',
  `require_password_change` tinyint(1) DEFAULT '0',
  `password_change_days` int DEFAULT '90',
  `enable_two_factor` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `fk_security_settings_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa_usuarios_security_settings`
--

LOCK TABLES `empresa_usuarios_security_settings` WRITE;
/*!40000 ALTER TABLE `empresa_usuarios_security_settings` DISABLE KEYS */;
INSERT INTO `empresa_usuarios_security_settings` VALUES (1,1,5,900,3600,6,0,90,0,'2025-07-15 07:05:17','2025-07-23 21:27:37','2025-07-23 09:10:00',NULL,'pendente');
/*!40000 ALTER TABLE `empresa_usuarios_security_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) DEFAULT NULL,
  `razao_social` varchar(255) DEFAULT NULL,
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `document` varchar(50) DEFAULT NULL,
  `document_type` varchar(20) DEFAULT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `inscricao_estadual` varchar(50) DEFAULT NULL,
  `inscricao_municipal` varchar(50) DEFAULT NULL,
  `data_abertura` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `pais` varchar(255) DEFAULT NULL,
  `regime_tributario` varchar(255) DEFAULT NULL,
  `optante_simples` tinyint(1) DEFAULT NULL,
  `incentivo_fiscal` tinyint(1) DEFAULT NULL,
  `cnae_principal` varchar(50) DEFAULT NULL,
  `banco_nome` varchar(255) DEFAULT NULL,
  `banco_agencia` varchar(20) DEFAULT NULL,
  `banco_conta` varchar(50) DEFAULT NULL,
  `banco_tipo_conta` varchar(50) DEFAULT NULL,
  `banco_pix` varchar(255) DEFAULT NULL,
  `moeda_padrao` varchar(10) DEFAULT NULL,
  `fuso_horario` varchar(50) DEFAULT NULL,
  `idioma_padrao` varchar(10) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `subscription_plan` varchar(50) DEFAULT NULL,
  `trial_ends_at` datetime DEFAULT NULL,
  `subscription_ends_at` datetime DEFAULT NULL,
  `cor_principal` varchar(7) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `data_cadastro` datetime DEFAULT NULL,
  `data_atualizacao` datetime DEFAULT NULL,
  `sync_data` text,
  `sync_hash` varchar(255) DEFAULT NULL,
  `sync_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,NULL,'Pizzaria','Pizzaria',NULL,NULL,'cnpj','11882474000120',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','Simples Nacional',1,0,NULL,NULL,NULL,NULL,NULL,'1','BRL','America/Sao_Paulo','pt-BR',NULL,'ativo','basico',NULL,NULL,'#007bff',1,'2025-07-11 22:17:28','2025-07-23 21:27:38','2025-07-23 09:10:00',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:38');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ficha_tecnica_categorias`
--

DROP TABLE IF EXISTS `ficha_tecnica_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ficha_tecnica_categorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ficha_tecnica_categorias`
--

LOCK TABLES `ficha_tecnica_categorias` WRITE;
/*!40000 ALTER TABLE `ficha_tecnica_categorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `ficha_tecnica_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_carteiras`
--

DROP TABLE IF EXISTS `fidelidade_carteiras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_carteiras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `saldo_cashback` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_creditos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_bloqueado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_total_disponivel` decimal(10,2) NOT NULL DEFAULT '0.00',
  `nivel_atual` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `xp_total` int NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_cliente_empresa` (`cliente_id`,`empresa_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_carteiras`
--

LOCK TABLES `fidelidade_carteiras` WRITE;
/*!40000 ALTER TABLE `fidelidade_carteiras` DISABLE KEYS */;
/*!40000 ALTER TABLE `fidelidade_carteiras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cartoes`
--

DROP TABLE IF EXISTS `fidelidade_cartoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cartoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `codigo_cartao` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_cartao` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze',
  `saldo_cartao` decimal(10,2) NOT NULL DEFAULT '0.00',
  `xp_acumulado` int NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_codigo_cartao` (`codigo_cartao`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_empresa_id` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cartoes`
--

LOCK TABLES `fidelidade_cartoes` WRITE;
/*!40000 ALTER TABLE `fidelidade_cartoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `fidelidade_cartoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cashback_regras`
--

DROP TABLE IF EXISTS `fidelidade_cashback_regras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cashback_regras` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `tipo_cashback` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentual',
  `valor_cashback` decimal(10,2) NOT NULL,
  `valor_minimo` decimal(10,2) DEFAULT NULL,
  `valor_maximo` decimal(10,2) DEFAULT NULL,
  `limite_mensal` decimal(10,2) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_inicio_data_fim` (`data_inicio`,`data_fim`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cashback_regras`
--

LOCK TABLES `fidelidade_cashback_regras` WRITE;
/*!40000 ALTER TABLE `fidelidade_cashback_regras` DISABLE KEYS */;
/*!40000 ALTER TABLE `fidelidade_cashback_regras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cashback_transacoes`
--

DROP TABLE IF EXISTS `fidelidade_cashback_transacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cashback_transacoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `pedido_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('credito','debito') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_pedido_original` decimal(10,2) DEFAULT NULL,
  `percentual_aplicado` decimal(5,2) DEFAULT NULL,
  `saldo_anterior` decimal(10,2) DEFAULT NULL,
  `saldo_posterior` decimal(10,2) DEFAULT NULL,
  `data_expiracao` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponivel',
  `observacoes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_pedido_id` (`pedido_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_expiracao` (`data_expiracao`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cashback_transacoes`
--

LOCK TABLES `fidelidade_cashback_transacoes` WRITE;
/*!40000 ALTER TABLE `fidelidade_cashback_transacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `fidelidade_cashback_transacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cliente_conquistas`
--

DROP TABLE IF EXISTS `fidelidade_cliente_conquistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cliente_conquistas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cartao_id` bigint unsigned NOT NULL,
  `conquista_id` bigint unsigned NOT NULL,
  `data_conquista` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `progresso_atual` decimal(15,2) DEFAULT '0.00' COMMENT 'Progresso em direÃ§Ã£o Ã  conquista',
  `progresso_necessario` decimal(15,2) NOT NULL COMMENT 'Total necessÃ¡rio',
  `status` enum('em_progresso','conquistado','recompensa_resgatada','expirado') COLLATE utf8mb4_unicode_ci DEFAULT 'em_progresso',
  `data_resgate_recompensa` timestamp NULL DEFAULT NULL,
  `recompensa_detalhes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Detalhes da recompensa concedida',
  `dados_conquista` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Dados especÃ­ficos quando conquistou',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cartao_conquista` (`cartao_id`,`conquista_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data_conquista` (`data_conquista`),
  KEY `idx_sync_status` (`sync_status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cliente_conquistas`
--

LOCK TABLES `fidelidade_cliente_conquistas` WRITE;
/*!40000 ALTER TABLE `fidelidade_cliente_conquistas` DISABLE KEYS */;
INSERT INTO `fidelidade_cliente_conquistas` VALUES (1,7,1,'2025-08-02 02:45:25',1.00,1.00,'conquistado','2025-08-02 02:45:25','{\"bonus\":\"10\"}','{\"pedido_id\":100}','cc7','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(2,8,1,'2025-08-02 02:45:25',1.00,1.00,'conquistado','2025-08-02 02:45:25','{\"bonus\":\"10\"}','{\"pedido_id\":100}','cc8','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(3,9,1,'2025-08-02 02:45:25',1.00,1.00,'conquistado','2025-08-02 02:45:25','{\"bonus\":\"10\"}','{\"pedido_id\":100}','cc9','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25');
/*!40000 ALTER TABLE `fidelidade_cliente_conquistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_conquistas`
--

DROP TABLE IF EXISTS `fidelidade_conquistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_conquistas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `icone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pontos_recompensa` int NOT NULL DEFAULT '0',
  `credito_recompensa` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo_requisito` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_requisito` int DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_ativo` (`ativo`),
  KEY `idx_tipo_requisito` (`tipo_requisito`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_conquistas`
--

LOCK TABLES `fidelidade_conquistas` WRITE;
/*!40000 ALTER TABLE `fidelidade_conquistas` DISABLE KEYS */;
INSERT INTO `fidelidade_conquistas` VALUES (1,1,'Primeira Compra','Cliente fez a primeira compra','trophy',100,10.00,'primeira_compra',1,1,'cq1','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(2,1,'Compras VIP','Cliente fez 10 compras','star',250,25.00,'total_compras',10,1,'cq2','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25');
/*!40000 ALTER TABLE `fidelidade_conquistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_creditos`
--

DROP TABLE IF EXISTS `fidelidade_creditos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_creditos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `carteira_id` bigint unsigned NOT NULL,
  `tipo` enum('bonus','promocao','ajuste','compra','resgate','presente') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origem` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sistema, Admin, API, etc',
  `referencia_externa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID externo de referÃªncia',
  `data_vencimento` date DEFAULT NULL,
  `status` enum('ativo','usado','expirado','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `data_utilizacao` timestamp NULL DEFAULT NULL,
  `utilizado_em` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Onde foi usado o crÃ©dito',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_carteira_id` (`carteira_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_status` (`status`),
  KEY `idx_data_vencimento` (`data_vencimento`),
  KEY `idx_sync_status` (`sync_status`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_creditos`
--

LOCK TABLES `fidelidade_creditos` WRITE;
/*!40000 ALTER TABLE `fidelidade_creditos` DISABLE KEYS */;
INSERT INTO `fidelidade_creditos` VALUES (1,4,'bonus',20.00,'BÃ´nus de promoÃ§Ã£o','sistema',NULL,'2025-12-31','ativo',NULL,NULL,'cr4','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(2,5,'bonus',20.00,'BÃ´nus de promoÃ§Ã£o','sistema',NULL,'2025-12-31','ativo',NULL,NULL,'cr5','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(3,6,'bonus',20.00,'BÃ´nus de promoÃ§Ã£o','sistema',NULL,'2025-12-31','ativo',NULL,NULL,'cr6','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(4,7,'bonus',20.00,'BÃ´nus de promoÃ§Ã£o','sistema',NULL,'2025-12-31','ativo',NULL,NULL,'cr7','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(5,1,'bonus',20.00,'BÃ´nus de promoÃ§Ã£o','sistema',NULL,'2025-12-31','ativo',NULL,NULL,'cr1','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25');
/*!40000 ALTER TABLE `fidelidade_creditos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cupons`
--

DROP TABLE IF EXISTS `fidelidade_cupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desconto_sacola',
  `valor_desconto` decimal(10,2) DEFAULT NULL,
  `percentual_desconto` decimal(5,2) DEFAULT NULL,
  `valor_minimo_pedido` decimal(10,2) DEFAULT NULL,
  `quantidade_maxima_uso` int DEFAULT NULL,
  `quantidade_usada` int NOT NULL DEFAULT '0',
  `uso_por_cliente` int NOT NULL DEFAULT '1',
  `data_inicio` datetime DEFAULT NULL,
  `data_fim` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_codigo` (`codigo`),
  KEY `idx_status` (`status`),
  KEY `idx_data_inicio_data_fim` (`data_inicio`,`data_fim`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cupons`
--

LOCK TABLES `fidelidade_cupons` WRITE;
/*!40000 ALTER TABLE `fidelidade_cupons` DISABLE KEYS */;
INSERT INTO `fidelidade_cupons` VALUES (1,1,'BEMVINDO10','Bem-vindo','Desconto de 10% na primeira compra','desconto_sacola',NULL,10.00,50.00,100,0,1,'2025-08-01 00:00:00','2025-09-01 00:00:00','ativo','cup1','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25'),(2,1,'VIP20','Cliente VIP','Desconto de 20% para clientes VIP','desconto_sacola',NULL,20.00,100.00,50,0,1,'2025-08-01 00:00:00','2025-09-30 00:00:00','ativo','cup2','synced','','2025-08-02 02:45:25','2025-08-02 02:45:25');
/*!40000 ALTER TABLE `fidelidade_cupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_cupons_uso`
--

DROP TABLE IF EXISTS `fidelidade_cupons_uso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_cupons_uso` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cupom_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `empresa_id` bigint unsigned NOT NULL,
  `pedido_id` bigint unsigned DEFAULT NULL,
  `valor_desconto_aplicado` decimal(10,2) DEFAULT NULL,
  `data_uso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cupom_id` (`cupom_id`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_pedido_id` (`pedido_id`),
  KEY `idx_data_uso` (`data_uso`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_cupons_uso`
--

LOCK TABLES `fidelidade_cupons_uso` WRITE;
/*!40000 ALTER TABLE `fidelidade_cupons_uso` DISABLE KEYS */;
INSERT INTO `fidelidade_cupons_uso` VALUES (1,1,39,1,3900,10.00,'2025-08-01 22:45:26','cuu39','synced','','2025-08-02 02:45:26','2025-08-02 02:45:26'),(2,1,49,1,4900,10.00,'2025-08-01 22:45:26','cuu49','synced','','2025-08-02 02:45:26','2025-08-02 02:45:26'),(3,1,50,1,5000,10.00,'2025-08-01 22:45:26','cuu50','synced','','2025-08-02 02:45:26','2025-08-02 02:45:26');
/*!40000 ALTER TABLE `fidelidade_cupons_uso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fidelidade_programas`
--

DROP TABLE IF EXISTS `fidelidade_programas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fidelidade_programas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `data_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_fim` datetime DEFAULT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pending','synced','error') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_id` (`empresa_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fidelidade_programas`
--

LOCK TABLES `fidelidade_programas` WRITE;
/*!40000 ALTER TABLE `fidelidade_programas` DISABLE KEYS */;
/*!40000 ALTER TABLE `fidelidade_programas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forma_pag_bandeiras`
--

DROP TABLE IF EXISTS `forma_pag_bandeiras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forma_pag_bandeiras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `dias_para_receber` int NOT NULL,
  `taxa` decimal(5,2) NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forma_pag_bandeiras`
--

LOCK TABLES `forma_pag_bandeiras` WRITE;
/*!40000 ALTER TABLE `forma_pag_bandeiras` DISABLE KEYS */;
INSERT INTO `forma_pag_bandeiras` VALUES (1,'CartÃ£o Visa / Master',30,1.70,1,'2025-04-18 10:14:45','2025-07-23 21:27:39',1,'2025-07-23 09:10:00',NULL,'pendente'),(2,'teste',0,0.00,0,'2025-04-18 10:15:03','2025-07-23 11:59:04',1,'2025-07-23 09:10:00',NULL,'pendente'),(3,'MasterCard',30,1.50,0,'2025-04-18 10:18:08','2025-07-23 11:59:04',1,'2025-07-23 09:10:00',NULL,'pendente'),(4,'Pix',0,1.00,1,'2025-04-18 10:18:22','2025-07-23 11:59:04',1,'2025-07-23 09:10:00',NULL,'pendente'),(5,'Dinheiro',0,0.00,1,'2025-04-21 11:58:17','2025-07-23 11:59:05',1,'2025-07-23 09:10:00',NULL,'pendente'),(6,'PIX ONLINE',1,1.00,1,'2025-04-22 07:38:49','2025-07-23 11:59:05',1,'2025-07-23 09:10:00',NULL,'pendente'),(7,'PAGAMENTO ONLINE ',1,1.50,1,'2025-04-22 07:39:14','2025-07-23 11:59:05',1,'2025-07-23 09:10:00',NULL,'pendente'),(8,'cartÃ£o online mais delivery',0,0.00,0,'2025-04-23 07:28:50','2025-07-23 11:59:06',1,'2025-07-23 09:10:00',NULL,'pendente'),(9,'cartao online alloy',0,0.00,0,'2025-04-23 07:29:01','2025-07-23 11:59:06',1,'2025-07-23 09:10:00',NULL,'pendente'),(10,'Fiado',30,0.00,1,'2025-06-04 19:31:38','2025-07-23 11:59:06',1,'2025-07-23 09:10:00',NULL,'pendente'),(11,'PIX Safe2Pay',0,0.99,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(12,'Visa Online',1,3.49,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(13,'Mastercard Online',1,3.49,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(14,'Elo Online',1,3.49,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(15,'Hipercard Online',1,3.79,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(16,'Boleto Safe2Pay',1,2.50,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(17,'CartÃ£o MÃ¡quina Entregador',0,0.00,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente'),(18,'PIX Entregador',0,0.00,1,'2025-08-02 03:37:15','2025-08-02 03:37:15',1,'2025-08-02 03:37:15',NULL,'pendente');
/*!40000 ALTER TABLE `forma_pag_bandeiras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forma_pagamento_bandeiras`
--

DROP TABLE IF EXISTS `forma_pagamento_bandeiras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forma_pagamento_bandeiras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `forma_pagamento_id` int NOT NULL,
  `forma_pag_bandeira_id` int NOT NULL,
  `empresa_id` int DEFAULT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forma_pagamento_bandeiras`
--

LOCK TABLES `forma_pagamento_bandeiras` WRITE;
/*!40000 ALTER TABLE `forma_pagamento_bandeiras` DISABLE KEYS */;
INSERT INTO `forma_pagamento_bandeiras` VALUES (4,1,1,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(6,1,4,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(7,4,4,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(8,3,5,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(11,5,7,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(12,6,6,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(13,6,7,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(14,5,1,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(15,5,4,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(16,6,1,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(18,7,10,1,NULL,'pendente','2025-07-23 09:10:00','2025-07-23 08:58:08','2025-07-23 08:58:08'),(19,8,11,1,NULL,'pendente','2025-08-02 03:37:21','2025-08-02 03:37:21','2025-08-02 03:37:21'),(20,9,12,1,NULL,'pendente','2025-08-02 03:37:21','2025-08-02 03:37:21','2025-08-02 03:37:21'),(21,9,13,1,NULL,'pendente','2025-08-02 03:37:21','2025-08-02 03:37:21','2025-08-02 03:37:21'),(22,11,16,1,NULL,'pendente','2025-08-02 03:37:21','2025-08-02 03:37:21','2025-08-02 03:37:21'),(23,12,17,1,NULL,'pendente','2025-08-02 03:37:21','2025-08-02 03:37:21','2025-08-02 03:37:21');
/*!40000 ALTER TABLE `forma_pagamento_bandeiras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formas_pagamento`
--

DROP TABLE IF EXISTS `formas_pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formas_pagamento` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `origem` enum('pdv','sistema','delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sistema',
  `tipo` enum('pagamento','recebimento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagamento',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_gateway` tinyint(1) DEFAULT '0' COMMENT 'Indica se usa gateway de pagamento',
  `gateway_provider` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Provedor do gateway (safe2pay, etc)',
  `gateway_method` enum('pix','credit_card','debit_card','bank_slip') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MÃ©todo no gateway',
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formas_pagamento`
--

LOCK TABLES `formas_pagamento` WRITE;
/*!40000 ALTER TABLE `formas_pagamento` DISABLE KEYS */;
INSERT INTO `formas_pagamento` VALUES (1,'Cartao de credito',1,'2025-04-18 10:53:11','2025-07-23 21:27:40',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(2,'teste',0,'2025-04-18 10:53:20','2025-07-23 11:59:07',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(3,'DINHEIRO',1,'2025-04-18 11:29:32','2025-07-23 11:59:08',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(4,'PIX MAQUINA ',1,'2025-04-18 11:29:38','2025-07-23 11:59:08',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(5,'PAGAMENTOS ALLOY',1,'2025-04-22 07:34:03','2025-07-23 11:59:08',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(6,'PAGAMENTOS MAIS DELIVERY',1,'2025-04-22 07:34:27','2025-07-23 11:59:08',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(7,'Fiado',1,'2025-06-04 19:32:08','2025-07-23 11:59:09',1,'sistema','pagamento',NULL,'pendente','2025-07-23 09:10:00',0,NULL,NULL),(8,'PIX Online',1,'2025-08-02 03:37:10','2025-08-02 03:37:32',1,'sistema','recebimento',NULL,'pendente','2025-08-02 03:37:10',1,'safe2pay','pix'),(9,'CartÃ£o de CrÃ©dito Online',1,'2025-08-02 03:37:10','2025-08-02 03:37:32',1,'sistema','recebimento',NULL,'pendente','2025-08-02 03:37:10',1,'safe2pay','credit_card'),(10,'CartÃ£o de DÃ©bito Online',1,'2025-08-02 03:37:10','2025-08-02 03:37:32',1,'sistema','recebimento',NULL,'pendente','2025-08-02 03:37:10',1,'safe2pay','credit_card'),(11,'Boleto Online',1,'2025-08-02 03:37:10','2025-08-02 03:37:32',1,'sistema','recebimento',NULL,'pendente','2025-08-02 03:37:10',1,'safe2pay','bank_slip'),(12,'CartÃ£o na MÃ¡quina (Entregador)',1,'2025-08-02 03:37:10','2025-08-02 03:37:10',1,'delivery','recebimento',NULL,'pendente','2025-08-02 03:37:10',0,NULL,NULL),(13,'Dinheiro (Entregador)',1,'2025-08-02 03:37:10','2025-08-02 03:37:10',1,'delivery','recebimento',NULL,'pendente','2025-08-02 03:37:10',0,NULL,NULL),(14,'PIX na Hora (Entregador)',1,'2025-08-02 03:37:10','2025-08-02 03:37:10',1,'delivery','recebimento',NULL,'pendente','2025-08-02 03:37:10',0,NULL,NULL);
/*!40000 ALTER TABLE `formas_pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `func_recisao`
--

DROP TABLE IF EXISTS `func_recisao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `func_recisao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `funcionario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `data_demissao` date NOT NULL,
  `motivo_demissao` text COLLATE utf8mb3_unicode_ci,
  `saldo_salario` decimal(10,2) DEFAULT NULL,
  `decimo_terceiro` decimal(10,2) DEFAULT NULL,
  `ferias_vencidas` decimal(10,2) DEFAULT NULL,
  `ferias_proporcionais` decimal(10,2) DEFAULT NULL,
  `aviso_previo` decimal(10,2) DEFAULT NULL,
  `fgts` decimal(10,2) DEFAULT NULL,
  `fgts_multa` decimal(10,2) DEFAULT NULL,
  `total_rescisao` decimal(10,2) DEFAULT NULL,
  `periodo_trabalho` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `periodo_anos` int DEFAULT NULL,
  `periodo_meses` int DEFAULT NULL,
  `periodo_dias` int DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `func_recisao`
--

LOCK TABLES `func_recisao` WRITE;
/*!40000 ALTER TABLE `func_recisao` DISABLE KEYS */;
/*!40000 ALTER TABLE `func_recisao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario_cargo`
--

DROP TABLE IF EXISTS `funcionario_cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario_cargo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `departamento_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario_cargo`
--

LOCK TABLES `funcionario_cargo` WRITE;
/*!40000 ALTER TABLE `funcionario_cargo` DISABLE KEYS */;
INSERT INTO `funcionario_cargo` VALUES (1,'Chapeiro','ResponsÃ¡vel pela chapa e preparo de alimentos','2025-04-15 09:08:59','2025-07-23 21:27:40',1,1,NULL,'pendente','2025-07-23 09:10:00'),(2,'Pizzaiola','ResponsÃ¡vel pela cozinha e preparo de pratos','2025-04-15 09:08:59','2025-07-23 11:13:08',1,1,NULL,'pendente','2025-07-23 09:10:00'),(3,'Caixa','ResponsÃ¡vel pelo atendimento ao cliente','2025-04-15 09:08:59','2025-07-23 11:13:08',12,1,NULL,'pendente','2025-07-23 09:10:00'),(4,'garÃ§ons','ResponsÃ¡vel pelo bar e preparo de bebidas','2025-04-15 09:08:59','2025-07-23 11:13:09',2,1,NULL,'pendente','2025-07-23 09:10:00'),(5,'Limpeza','ResponsÃ¡vel pela limpeza do ambiente','2025-04-15 09:08:59','2025-07-23 11:13:09',4,1,NULL,'pendente','2025-07-23 09:10:00'),(6,'Copeiro','ResponsÃ¡vel pela Ã¡rea de boliche','2025-04-15 09:08:59','2025-07-23 11:13:09',3,1,NULL,'pendente','2025-07-23 09:10:00'),(7,'ENTREGAODR',NULL,'2025-05-02 07:27:58','2025-07-23 11:13:10',13,1,NULL,'pendente','2025-07-23 09:10:00');
/*!40000 ALTER TABLE `funcionario_cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionario_depart`
--

DROP TABLE IF EXISTS `funcionario_depart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario_depart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `relacionado_producao` tinyint(1) DEFAULT '0',
  `sync_hash` varchar(64) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario_depart`
--

LOCK TABLES `funcionario_depart` WRITE;
/*!40000 ALTER TABLE `funcionario_depart` DISABLE KEYS */;
INSERT INTO `funcionario_depart` VALUES (1,'Cozinha','Departamento responsÃ¡vel pela preparaÃ§Ã£o de alimentos','2025-04-15 09:53:32','2025-07-23 21:27:41',1,1,NULL,'pendente','2025-07-23 09:10:00'),(2,'Atendimento','Departamento responsÃ¡vel pelo atendimento ao cliente','2025-04-15 09:53:32','2025-07-23 11:13:11',1,0,NULL,'pendente','2025-07-23 09:10:00'),(3,'Bar','Departamento responsÃ¡vel pelo preparo de bebidas','2025-04-15 09:53:32','2025-07-23 11:13:11',1,0,NULL,'pendente','2025-07-23 09:10:00'),(4,'Limpeza','Departamento responsÃ¡vel pela limpeza e organizaÃ§Ã£o','2025-04-15 09:53:32','2025-07-23 11:13:11',1,0,NULL,'pendente','2025-07-23 09:10:00'),(5,'Boliche','Departamento responsÃ¡vel pela Ã¡rea de boliche','2025-04-15 09:53:32','2025-07-23 11:13:11',1,0,NULL,'pendente','2025-07-23 09:10:00'),(6,'Ãrea Infantil','Departamento responsÃ¡vel pela recreaÃ§Ã£o infantil','2025-04-15 09:53:32','2025-07-23 11:13:12',1,0,NULL,'pendente','2025-07-23 09:10:00'),(11,'Chapa','','2025-04-20 10:18:03','2025-07-23 11:13:12',1,1,NULL,'pendente','2025-07-23 09:10:00'),(12,'Caixa','','2025-04-20 10:18:52','2025-07-23 11:13:12',1,0,NULL,'pendente','2025-07-23 09:10:00'),(13,'ENTREGAS','','2025-05-02 07:27:46','2025-07-23 11:13:13',1,0,NULL,'pendente','2025-07-23 09:10:00');
/*!40000 ALTER TABLE `funcionario_depart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli`
--

DROP TABLE IF EXISTS `funforcli`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int DEFAULT NULL,
  `departamento_id` int DEFAULT NULL,
  `cargo_id` int DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `sobrenome` varchar(255) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cpf_cnpj` varchar(25) DEFAULT NULL,
  `rg` varchar(25) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `observacoes` text,
  `dia_vencimento` int DEFAULT NULL,
  `tipo_contratacao` enum('CLT','PJ','Diarista','Terceirizado','EstagiÃ¡rio','Entregador') DEFAULT NULL COMMENT 'CLT,PJ,Diarista,Terceirizado,EstagiÃ¡rio,Entregador',
  `data_demissao` date DEFAULT NULL,
  `motivo_demissao` text,
  `conta_bancaria_principal_id` int DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `tipo` enum('funcionario','fornecedor','cliente','entregador') NOT NULL DEFAULT 'funcionario',
  `plano_atual_id` bigint DEFAULT NULL,
  `plano_status` enum('trial','ativo','suspenso','cancelado') DEFAULT NULL,
  `plano_inicio` timestamp NULL DEFAULT NULL,
  `plano_vencimento` timestamp NULL DEFAULT NULL,
  `plano_trial_expira` timestamp NULL DEFAULT NULL,
  `chave_licenca` varchar(100) DEFAULT NULL,
  `chave_api` varchar(100) DEFAULT NULL,
  `afiliado_codigo` varchar(20) DEFAULT NULL,
  `afiliado_nivel` enum('afiliado','bronze','prata','ouro') DEFAULT NULL,
  `afiliado_taxa_comissao` decimal(5,2) DEFAULT NULL,
  `afiliado_chave_pix` varchar(200) DEFAULT NULL,
  `afiliado_tipo_chave_pix` enum('cpf','cnpj','email','telefone','aleatoria') DEFAULT NULL,
  `afiliado_dados_bancarios` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `afiliado_total_vendas` decimal(15,2) DEFAULT '0.00',
  `afiliado_total_comissoes` decimal(15,2) DEFAULT '0.00',
  `afiliado_total_pago` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endereco_principal_id` int DEFAULT NULL,
  `fiado_limite` decimal(10,2) DEFAULT NULL,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `funforcli_ibfk_conta` (`conta_bancaria_principal_id`),
  KEY `funforcli_ibfk_endereco` (`endereco_principal_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  KEY `idx_plano_atual` (`plano_atual_id`),
  KEY `idx_afiliado_codigo` (`afiliado_codigo`),
  KEY `idx_chave_licenca` (`chave_licenca`),
  KEY `idx_chave_api` (`chave_api`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli`
--

LOCK TABLES `funforcli` WRITE;
/*!40000 ALTER TABLE `funforcli` DISABLE KEYS */;
INSERT INTO `funforcli` VALUES (1,1,3,6,'JosÃ© ','Aldo santos Bernadino junior',NULL,'11','22','','aldo@gmail.com','2025-04-24',1700.00,'ativo','',24,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-04-20 10:23:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(4,1,12,3,'Camila ','eduarda',NULL,'111','222','','camila@gmail.com','2025-04-11',1800.00,'ativo','',11,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-04-20 10:33:33','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(8,1,1,2,'Jordeane','Fenelon do nascimento',NULL,'34','33','','jordeane@gmail.com','2025-04-20',1700.00,'ativo','',20,'CLT',NULL,NULL,NULL,0,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-04-20 10:41:29','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(10,1,1,2,'Marisete','stuch','2025-04-20','5455','545455','','Mari@gmail.com','2025-04-20',1500.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-04-20 10:44:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(11,1,1,2,'Jamily','Vitoria belusso',NULL,'1122','1222','','jamily@gmail.com','2025-04-20',1700.00,'ativo','',2,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-04-20 10:46:24','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(12,1,12,3,'Tailine ','aparecida','2025-04-10','2366','6577','','tailine@gmail.com','2025-04-10',1518.00,'ativo','',10,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 07:32:19','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(13,1,1,2,'Gleidiane ','de paula barbosa','2025-04-20','344','445666','','gleidiane@gmail.com','2025-04-01',1700.00,'ativo','',1,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 07:35:50','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(14,1,11,1,'Eddie','Lima da silva','2025-04-20','676777','67655454','','eddie@gmail.com','2025-04-08',1700.00,'ativo','',8,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 07:41:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(15,1,11,1,'Adhonias','de moura barros','2025-04-20','545433','545454545','','adhonias@gmail.com','2025-04-20',1600.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 08:50:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(16,1,2,4,'Roaldo ','Dinho','2025-04-20','4888','7766','','dinho@gmail.com','2025-04-13',2000.00,'ativo','',13,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 09:01:03','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(17,1,2,4,'Pablo','heinrique de souza martins','2025-04-20','5488','5666','','pablo@gmail.com','2025-04-20',1800.00,'ativo','',20,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 09:02:12','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(18,1,13,7,'Jhony ','Gama',NULL,'1212121','656565','','jhony@gmail.com','2025-05-01',0.00,'ativo','',1,'Entregador',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-05-02 09:06:00','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(20,1,13,7,'Ricardo','Oliveira da silva',NULL,'21212255','21','','ricardo@gmail.com','2025-05-01',0.00,'ativo','',1,'Terceirizado',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(21,1,1,2,'Lucioano Alexandre','Ferreira',NULL,'669998989','5548855','','alexandre@gmail.com','2025-05-01',1600.00,'ativo','',1,'CLT',NULL,NULL,NULL,0,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(22,1,12,3,'Anna ','Claudia',NULL,'588661','6565468468','','anna@gmail.com','2025-05-01',0.00,'desativado','',1,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(23,1,2,4,'Carlos','Eduardo pereira ferreira ',NULL,'54646131','3146','','carlos@gmail.com','2025-05-01',0.00,'ativo','',1,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(24,1,13,7,'Omar','stuch',NULL,'5445411','5454212','','omar@gmail.com','2025-05-02',0.00,'ativo','',2,'Terceirizado',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(25,1,2,4,'Maria ','stuch',NULL,'4522233','2115454','','maria@gmail.com','2025-05-02',0.00,'desativado','',2,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(26,1,2,4,'Sabrina','sampaio',NULL,'5454548787','65646446','','Sabrina@gmail.com','2025-05-02',0.00,'ativo','',2,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(28,1,1,2,'Daniel ','Coutrin',NULL,'000000000','008800','','daniel@gmail.com','2025-05-28',1700.00,'ativo','',28,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(29,1,11,1,'Mikael','souza aguiar',NULL,'000000005','000555','','mikael@gmail.com','2025-06-03',0.00,'ativo','',3,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(33,1,1,2,'Elizangela','de lima',NULL,'0080000553','545454','','elizangela@gmail.com','2025-06-07',0.00,'ativo','',7,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(34,1,2,4,'Kauan ','lemes da silva',NULL,'54545484','5454858','','kauan@gmail.com','2025-05-06',1518.00,'ativo','',6,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(35,1,1,2,'Joana','w',NULL,'454888884','5454454522','','joana@gmail.com','2025-06-09',1700.00,'ativo','',9,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(36,1,4,5,'Jardeane ','Lima e silva',NULL,'6558564','656565533','','Jardeane@gmail.com','2025-06-05',1600.00,'ativo','',5,'CLT',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(37,1,11,4,'Jailson ','w',NULL,'212122','2121225','','jailson@gmail.com','2025-06-12',0.00,'ativo','',12,'Diarista',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(38,1,NULL,NULL,'Joao mussarela',NULL,NULL,'2121555',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-17 11:07:20','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(39,1,NULL,NULL,'Mazinho',NULL,NULL,'02041101148',NULL,'6566565656','12@gmail.com',NULL,NULL,'ativo','wswsws',NULL,NULL,NULL,NULL,NULL,1,'cliente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-17 11:07:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(40,1,NULL,NULL,'Mercado ( GERAL )',NULL,NULL,'549996',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-17 11:08:39','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(41,1,NULL,NULL,'unigas gas e agua',NULL,NULL,'558787542',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-17 23:49:44','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(42,1,NULL,NULL,'Polpa maisa foods',NULL,NULL,'21665656',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-18 00:21:56','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(43,1,NULL,NULL,'DISTRIBUIDORA CRUZEIRO Ambev',NULL,NULL,'3232666',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-18 01:27:18','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(44,1,NULL,NULL,'coca ',NULL,NULL,'12122',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-19 22:59:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(45,1,NULL,NULL,'IMPRERADOR EMBALAGENS ',NULL,NULL,'5656599',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-19 23:14:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(46,1,NULL,NULL,'CASA DA EMBALAGEM ',NULL,NULL,'87845454',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-19 23:15:00','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(48,1,13,7,'ROGERIO','MARQUES',NULL,'665656','32323','','rogerio@gmail.com','2025-06-23',0.00,'ativo','',23,'Entregador',NULL,NULL,NULL,1,'funcionario',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-06-23 11:39:11','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(49,1,NULL,NULL,'Ana','Silva',NULL,'12345678901',NULL,NULL,'ana@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(50,1,NULL,NULL,'JoÃ£o','Santos',NULL,'98765432109',NULL,NULL,'joao@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(51,1,NULL,NULL,'Maria','Costa',NULL,'11122233344',NULL,NULL,'maria@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,'2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10');
/*!40000 ALTER TABLE `funforcli` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli_backup`
--

DROP TABLE IF EXISTS `funforcli_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli_backup` (
  `id` int NOT NULL DEFAULT '0',
  `empresa_id` int DEFAULT NULL,
  `departamento_id` int DEFAULT NULL,
  `cargo_id` int DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `sobrenome` varchar(255) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cpf_cnpj` varchar(25) DEFAULT NULL,
  `rg` varchar(25) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `observacoes` text,
  `dia_vencimento` int DEFAULT NULL,
  `tipo_contratacao` enum('CLT','PJ','Diarista','Terceirizado','EstagiÃ¡rio','Entregador') DEFAULT NULL COMMENT 'CLT,PJ,Diarista,Terceirizado,EstagiÃ¡rio,Entregador',
  `data_demissao` date DEFAULT NULL,
  `motivo_demissao` text,
  `conta_bancaria_principal_id` int DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `tipo` enum('funcionario','fornecedor','cliente','entregador') NOT NULL DEFAULT 'funcionario',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endereco_principal_id` int DEFAULT NULL,
  `fiado_limite` decimal(10,2) DEFAULT NULL,
  `sync_hash` varchar(64) DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli_backup`
--

LOCK TABLES `funforcli_backup` WRITE;
/*!40000 ALTER TABLE `funforcli_backup` DISABLE KEYS */;
INSERT INTO `funforcli_backup` VALUES (1,1,3,6,'JosÃ© ','Aldo santos Bernadino junior',NULL,'11','22','','aldo@gmail.com','2025-04-24',1700.00,'ativo','',24,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:23:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(4,1,12,3,'Camila ','eduarda',NULL,'111','222','','camila@gmail.com','2025-04-11',1800.00,'ativo','',11,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:33:33','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(8,1,1,2,'Jordeane','Fenelon do nascimento',NULL,'34','33','','jordeane@gmail.com','2025-04-20',1700.00,'ativo','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-04-20 10:41:29','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(10,1,1,2,'Marisete','stuch','2025-04-20','5455','545455','','Mari@gmail.com','2025-04-20',1500.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-04-20 10:44:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(11,1,1,2,'Jamily','Vitoria belusso',NULL,'1122','1222','','jamily@gmail.com','2025-04-20',1700.00,'ativo','',2,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:46:24','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(12,1,12,3,'Tailine ','aparecida','2025-04-10','2366','6577','','tailine@gmail.com','2025-04-10',1518.00,'ativo','',10,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:32:19','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(13,1,1,2,'Gleidiane ','de paula barbosa','2025-04-20','344','445666','','gleidiane@gmail.com','2025-04-01',1700.00,'ativo','',1,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:35:50','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(14,1,11,1,'Eddie','Lima da silva','2025-04-20','676777','67655454','','eddie@gmail.com','2025-04-08',1700.00,'ativo','',8,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:41:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(15,1,11,1,'Adhonias','de moura barros','2025-04-20','545433','545454545','','adhonias@gmail.com','2025-04-20',1600.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-05-02 08:50:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(16,1,2,4,'Roaldo ','Dinho','2025-04-20','4888','7766','','dinho@gmail.com','2025-04-13',2000.00,'ativo','',13,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:01:03','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(17,1,2,4,'Pablo','heinrique de souza martins','2025-04-20','5488','5666','','pablo@gmail.com','2025-04-20',1800.00,'ativo','',20,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:02:12','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(18,1,13,7,'Jhony ','Gama',NULL,'1212121','656565','','jhony@gmail.com','2025-05-01',0.00,'ativo','',1,'Entregador',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:06:00','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(20,1,13,7,'Ricardo','Oliveira da silva',NULL,'21212255','21','','ricardo@gmail.com','2025-05-01',0.00,'ativo','',1,'Terceirizado',NULL,NULL,NULL,1,'funcionario','2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(21,1,1,2,'Lucioano Alexandre','Ferreira',NULL,'669998989','5548855','','alexandre@gmail.com','2025-05-01',1600.00,'ativo','',1,'CLT',NULL,NULL,NULL,0,'funcionario','2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(22,1,12,3,'Anna ','Claudia',NULL,'588661','6565468468','','anna@gmail.com','2025-05-01',0.00,'desativado','',1,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(23,1,2,4,'Carlos','Eduardo pereira ferreira ',NULL,'54646131','3146','','carlos@gmail.com','2025-05-01',0.00,'ativo','',1,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(24,1,13,7,'Omar','stuch',NULL,'5445411','5454212','','omar@gmail.com','2025-05-02',0.00,'ativo','',2,'Terceirizado',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(25,1,2,4,'Maria ','stuch',NULL,'4522233','2115454','','maria@gmail.com','2025-05-02',0.00,'desativado','',2,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(26,1,2,4,'Sabrina','sampaio',NULL,'5454548787','65646446','','Sabrina@gmail.com','2025-05-02',0.00,'ativo','',2,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(28,1,1,2,'Daniel ','Coutrin',NULL,'000000000','008800','','daniel@gmail.com','2025-05-28',1700.00,'ativo','',28,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(29,1,11,1,'Mikael','souza aguiar',NULL,'000000005','000555','','mikael@gmail.com','2025-06-03',0.00,'ativo','',3,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(33,1,1,2,'Elizangela','de lima',NULL,'0080000553','545454','','elizangela@gmail.com','2025-06-07',0.00,'ativo','',7,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(34,1,2,4,'Kauan ','lemes da silva',NULL,'54545484','5454858','','kauan@gmail.com','2025-05-06',1518.00,'ativo','',6,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(35,1,1,2,'Joana','w',NULL,'454888884','5454454522','','joana@gmail.com','2025-06-09',1700.00,'ativo','',9,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(36,1,4,5,'Jardeane ','Lima e silva',NULL,'6558564','656565533','','Jardeane@gmail.com','2025-06-05',1600.00,'ativo','',5,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(37,1,11,4,'Jailson ','w',NULL,'212122','2121225','','jailson@gmail.com','2025-06-12',0.00,'ativo','',12,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(38,1,NULL,NULL,'Joao mussarela',NULL,NULL,'2121555',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 11:07:20','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(39,1,NULL,NULL,'Mazinho',NULL,NULL,'02041101148',NULL,'6566565656','12@gmail.com',NULL,NULL,'ativo','wswsws',NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-06-17 11:07:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(40,1,NULL,NULL,'Mercado ( GERAL )',NULL,NULL,'549996',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 11:08:39','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(41,1,NULL,NULL,'unigas gas e agua',NULL,NULL,'558787542',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 23:49:44','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(42,1,NULL,NULL,'Polpa maisa foods',NULL,NULL,'21665656',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-18 00:21:56','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(43,1,NULL,NULL,'DISTRIBUIDORA CRUZEIRO Ambev',NULL,NULL,'3232666',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-18 01:27:18','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(44,1,NULL,NULL,'coca ',NULL,NULL,'12122',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 22:59:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(45,1,NULL,NULL,'IMPRERADOR EMBALAGENS ',NULL,NULL,'5656599',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 23:14:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(46,1,NULL,NULL,'CASA DA EMBALAGEM ',NULL,NULL,'87845454',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 23:15:00','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(48,1,13,7,'ROGERIO','MARQUES',NULL,'665656','32323','','rogerio@gmail.com','2025-06-23',0.00,'ativo','',23,'Entregador',NULL,NULL,NULL,1,'funcionario','2025-06-23 11:39:11','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(49,1,NULL,NULL,'Ana','Silva',NULL,'12345678901',NULL,NULL,'ana@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(50,1,NULL,NULL,'JoÃ£o','Santos',NULL,'98765432109',NULL,NULL,'joao@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(51,1,NULL,NULL,'Maria','Costa',NULL,'11122233344',NULL,NULL,'maria@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(1,1,3,6,'José ','Aldo santos Bernadino junior',NULL,'11','22','','aldo@gmail.com','2025-04-24',1700.00,'ativo','',24,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:23:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(4,1,12,3,'Camila ','eduarda',NULL,'111','222','','camila@gmail.com','2025-04-11',1800.00,'ativo','',11,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:33:33','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(8,1,1,2,'Jordeane','Fenelon do nascimento',NULL,'34','33','','jordeane@gmail.com','2025-04-20',1700.00,'ativo','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-04-20 10:41:29','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(10,1,1,2,'Marisete','stuch','2025-04-20','5455','545455','','Mari@gmail.com','2025-04-20',1500.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-04-20 10:44:11','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(11,1,1,2,'Jamily','Vitoria belusso',NULL,'1122','1222','','jamily@gmail.com','2025-04-20',1700.00,'ativo','',2,'CLT',NULL,NULL,NULL,1,'funcionario','2025-04-20 10:46:24','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(12,1,12,3,'Tailine ','aparecida','2025-04-10','2366','6577','','tailine@gmail.com','2025-04-10',1518.00,'ativo','',10,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:32:19','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(13,1,1,2,'Gleidiane ','de paula barbosa','2025-04-20','344','445666','','gleidiane@gmail.com','2025-04-01',1700.00,'ativo','',1,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:35:50','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(14,1,11,1,'Eddie','Lima da silva','2025-04-20','676777','67655454','','eddie@gmail.com','2025-04-08',1700.00,'ativo','',8,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 07:41:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(15,1,11,1,'Adhonias','de moura barros','2025-04-20','545433','545454545','','adhonias@gmail.com','2025-04-20',1600.00,'desativado','',20,'CLT',NULL,NULL,NULL,0,'funcionario','2025-05-02 08:50:36','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(16,1,2,4,'Roaldo ','Dinho','2025-04-20','4888','7766','','dinho@gmail.com','2025-04-13',2000.00,'ativo','',13,'CLT',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:01:03','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(17,1,2,4,'Pablo','heinrique de souza martins','2025-04-20','5488','5666','','pablo@gmail.com','2025-04-20',1800.00,'ativo','',20,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:02:12','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(18,1,13,7,'Jhony ','Gama',NULL,'1212121','656565','','jhony@gmail.com','2025-05-01',0.00,'ativo','',1,'Entregador',NULL,NULL,NULL,1,'funcionario','2025-05-02 09:06:00','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(20,1,13,7,'Ricardo','Oliveira da silva',NULL,'21212255','21','','ricardo@gmail.com','2025-05-01',0.00,'ativo','',1,'Terceirizado',NULL,NULL,NULL,1,'funcionario','2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(21,1,1,2,'Lucioano Alexandre','Ferreira',NULL,'669998989','5548855','','alexandre@gmail.com','2025-05-01',1600.00,'ativo','',1,'CLT',NULL,NULL,NULL,0,'funcionario','2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(22,1,12,3,'Anna ','Claudia',NULL,'588661','6565468468','','anna@gmail.com','2025-05-01',0.00,'desativado','',1,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(23,1,2,4,'Carlos','Eduardo pereira ferreira ',NULL,'54646131','3146','','carlos@gmail.com','2025-05-01',0.00,'ativo','',1,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(24,1,13,7,'Omar','stuch',NULL,'5445411','5454212','','omar@gmail.com','2025-05-02',0.00,'ativo','',2,'Terceirizado',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(25,1,2,4,'Maria ','stuch',NULL,'4522233','2115454','','maria@gmail.com','2025-05-02',0.00,'desativado','',2,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(26,1,2,4,'Sabrina','sampaio',NULL,'5454548787','65646446','','Sabrina@gmail.com','2025-05-02',0.00,'ativo','',2,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(28,1,1,2,'Daniel ','Coutrin',NULL,'000000000','008800','','daniel@gmail.com','2025-05-28',1700.00,'ativo','',28,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-02 06:41:15','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(29,1,11,1,'Mikael','souza aguiar',NULL,'000000005','000555','','mikael@gmail.com','2025-06-03',0.00,'ativo','',3,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-03 10:00:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(33,1,1,2,'Elizangela','de lima',NULL,'0080000553','545454','','elizangela@gmail.com','2025-06-07',0.00,'ativo','',7,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-07 10:13:54','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(34,1,2,4,'Kauan ','lemes da silva',NULL,'54545484','5454858','','kauan@gmail.com','2025-05-06',1518.00,'ativo','',6,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-10 18:51:55','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(35,1,1,2,'Joana','w',NULL,'454888884','5454454522','','joana@gmail.com','2025-06-09',1700.00,'ativo','',9,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:06:06','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(36,1,4,5,'Jardeane ','Lima e silva',NULL,'6558564','656565533','','Jardeane@gmail.com','2025-06-05',1600.00,'ativo','',5,'CLT',NULL,NULL,NULL,1,'funcionario','2025-06-11 09:08:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(37,1,11,4,'Jailson ','w',NULL,'212122','2121225','','jailson@gmail.com','2025-06-12',0.00,'ativo','',12,'Diarista',NULL,NULL,NULL,1,'funcionario','2025-06-12 09:21:32','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(38,1,NULL,NULL,'Joao mussarela',NULL,NULL,'2121555',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 11:07:20','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(39,1,NULL,NULL,'Mazinho',NULL,NULL,'02041101148',NULL,'6566565656','12@gmail.com',NULL,NULL,'ativo','wswsws',NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-06-17 11:07:56','2025-08-01 19:34:13',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(40,1,NULL,NULL,'Mercado ( GERAL )',NULL,NULL,'549996',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 11:08:39','2025-07-23 11:59:19',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(41,1,NULL,NULL,'unigas gas e agua',NULL,NULL,'558787542',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-17 23:49:44','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(42,1,NULL,NULL,'Polpa maisa foods',NULL,NULL,'21665656',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-18 00:21:56','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(43,1,NULL,NULL,'DISTRIBUIDORA CRUZEIRO Ambev',NULL,NULL,'3232666',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-18 01:27:18','2025-07-23 11:59:20',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(44,1,NULL,NULL,'coca ',NULL,NULL,'12122',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 22:59:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(45,1,NULL,NULL,'IMPRERADOR EMBALAGENS ',NULL,NULL,'5656599',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 23:14:44','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(46,1,NULL,NULL,'CASA DA EMBALAGEM ',NULL,NULL,'87845454',NULL,'','',NULL,NULL,'ativo','',NULL,NULL,NULL,NULL,NULL,1,'fornecedor','2025-06-19 23:15:00','2025-07-23 11:59:21',NULL,NULL,NULL,'pendente','2025-07-23 09:10:00'),(48,1,13,7,'ROGERIO','MARQUES',NULL,'665656','32323','','rogerio@gmail.com','2025-06-23',0.00,'ativo','',23,'Entregador',NULL,NULL,NULL,1,'funcionario','2025-06-23 11:39:11','2025-08-01 19:34:13',NULL,300.00,NULL,'pendente','2025-07-23 09:10:00'),(49,1,NULL,NULL,'Ana','Silva',NULL,'12345678901',NULL,NULL,'ana@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(50,1,NULL,NULL,'João','Santos',NULL,'98765432109',NULL,NULL,'joao@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10'),(51,1,NULL,NULL,'Maria','Costa',NULL,'11122233344',NULL,NULL,'maria@teste.com',NULL,NULL,'ativo',NULL,NULL,NULL,NULL,NULL,NULL,1,'cliente','2025-08-02 03:12:10','2025-08-02 03:12:10',NULL,NULL,NULL,'pendente','2025-08-01 23:12:10');
/*!40000 ALTER TABLE `funforcli_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli_contas_bancarias`
--

DROP TABLE IF EXISTS `funforcli_contas_bancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli_contas_bancarias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL COMMENT 'ReferÃªncia ao cliente na tabela funforcli',
  `banco` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_banco` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agencia` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_conta` enum('corrente','poupanca','salario','investimento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'corrente',
  `operacao` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Para contas que exigem campo de operaÃ§Ã£o',
  `titular` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf_cnpj_titular` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `principal` tinyint(1) DEFAULT '0' COMMENT 'Indica se Ã© a conta principal',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL COMMENT 'Empresa a que pertence este registro',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_contas_bancarias_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `funforcli` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli_contas_bancarias`
--

LOCK TABLES `funforcli_contas_bancarias` WRITE;
/*!40000 ALTER TABLE `funforcli_contas_bancarias` DISABLE KEYS */;
/*!40000 ALTER TABLE `funforcli_contas_bancarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli_enderecos`
--

DROP TABLE IF EXISTS `funforcli_enderecos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli_enderecos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `funforcli_id` int NOT NULL DEFAULT '0' COMMENT 'ReferÃªncia ao cliente na tabela funforcli',
  `tipo` enum('residencial','comercial','cobranca','entrega','outro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'residencial',
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Brasil',
  `principal` tinyint(1) DEFAULT '0' COMMENT 'Indica se Ã© o endereÃ§o principal',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL COMMENT 'Empresa a que pertence este registro',
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`funforcli_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_enderecos_funforcli` FOREIGN KEY (`funforcli_id`) REFERENCES `funforcli` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli_enderecos`
--

LOCK TABLES `funforcli_enderecos` WRITE;
/*!40000 ALTER TABLE `funforcli_enderecos` DISABLE KEYS */;
INSERT INTO `funforcli_enderecos` VALUES (1,39,'residencial','78878787','av bandeirantes','1247','21212','comcordia','paranatinga','MT','Brasil',0,'','2025-07-06 22:38:20','2025-07-23 21:27:43',1,NULL,'pendente','2025-07-23 09:10:00'),(2,39,'residencial','78870000','wsws','121','21','2121','21','MM','Brasil',0,'','2025-07-09 09:52:05','2025-07-23 11:59:23',1,NULL,'pendente','2025-07-23 09:10:00');
/*!40000 ALTER TABLE `funforcli_enderecos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli_pagamentos_planos`
--

DROP TABLE IF EXISTS `funforcli_pagamentos_planos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli_pagamentos_planos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `plano_tipo` varchar(20) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status_pagamento` enum('pendente','aprovado','cancelado','estornado') DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `gateway_response` longtext,
  `data_pagamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_vencimento` date DEFAULT NULL,
  `observacoes` text,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `idx_status_pagamento` (`status_pagamento`),
  KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli_pagamentos_planos`
--

LOCK TABLES `funforcli_pagamentos_planos` WRITE;
/*!40000 ALTER TABLE `funforcli_pagamentos_planos` DISABLE KEYS */;
/*!40000 ALTER TABLE `funforcli_pagamentos_planos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funforcli_planos_historico`
--

DROP TABLE IF EXISTS `funforcli_planos_historico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funforcli_planos_historico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `plano_anterior` varchar(20) DEFAULT NULL,
  `plano_novo` varchar(20) NOT NULL,
  `valor_pago` decimal(10,2) DEFAULT '0.00',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `data_mudanca` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` text,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_data_mudanca` (`data_mudanca`),
  KEY `idx_usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funforcli_planos_historico`
--

LOCK TABLES `funforcli_planos_historico` WRITE;
/*!40000 ALTER TABLE `funforcli_planos_historico` DISABLE KEYS */;
/*!40000 ALTER TABLE `funforcli_planos_historico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressao_logs`
--

DROP TABLE IF EXISTS `impressao_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressao_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `impressao_id` int NOT NULL,
  `acao` enum('impressao','reimpressao','cancelamento','erro','status') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci,
  `detalhes` text COLLATE utf8mb4_unicode_ci COMMENT 'Detalhes tÃ©cnicos em JSON',
  `usuario_id` int DEFAULT NULL COMMENT 'UsuÃ¡rio que realizou a aÃ§Ã£o',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  KEY `impressao_id` (`impressao_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressao_logs`
--

LOCK TABLES `impressao_logs` WRITE;
/*!40000 ALTER TABLE `impressao_logs` DISABLE KEYS */;
INSERT INTO `impressao_logs` VALUES (1,11,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":2,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:15:36\"}',NULL,'2025-07-13 09:15:36',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 21:27:44'),(2,11,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":2,\"setor_id\":6,\"nome\":\"CHAPA\",\"identificacao\":\"IMP_CHAPA\",\"marca\":\"EPSON\",\"modelo\":\"T20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:57:06\",\"updated_at\":\"2025-07-13 01:15:14\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:15:32Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:15:36\"}',NULL,'2025-07-13 09:15:36',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:14'),(3,12,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"2\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:15:36\"}',NULL,'2025-07-13 09:15:36',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:14'),(4,13,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":2,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:15:37\"}',NULL,'2025-07-13 09:15:37',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:14'),(5,13,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":2,\"setor_id\":6,\"nome\":\"CHAPA\",\"identificacao\":\"IMP_CHAPA\",\"marca\":\"EPSON\",\"modelo\":\"T20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:57:06\",\"updated_at\":\"2025-07-13 01:15:14\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:15:32Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:15:37\"}',NULL,'2025-07-13 09:15:37',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:14'),(6,14,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"2\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:15:37\"}',NULL,'2025-07-13 09:15:37',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:15'),(7,15,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:16:00\"}',NULL,'2025-07-13 09:16:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:15'),(8,15,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.51\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:13:22\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:15:54Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:16:00\"}',NULL,'2025-07-13 09:16:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:15'),(9,16,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:16:00\"}',NULL,'2025-07-13 09:16:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:16'),(10,17,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:16:52\"}',NULL,'2025-07-13 09:16:52',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:16'),(11,17,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.51\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:16:47\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:16:47Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:16:52\"}',NULL,'2025-07-13 09:16:52',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:16'),(12,18,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:16:52\"}',NULL,'2025-07-13 09:16:52',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:17'),(13,19,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:22:00\"}',NULL,'2025-07-13 09:22:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:17'),(14,19,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:21:45\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:21:55Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:22:00\"}',NULL,'2025-07-13 09:22:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:17'),(15,20,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:22:00\"}',NULL,'2025-07-13 09:22:00',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:17'),(16,21,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:23:11\"}',NULL,'2025-07-13 09:23:11',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:18'),(17,21,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:21:45\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:21:55Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:23:11\"}',NULL,'2025-07-13 09:23:11',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:18'),(18,22,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:23:11\"}',NULL,'2025-07-13 09:23:11',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:18'),(19,23,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:23:18\"}',NULL,'2025-07-13 09:23:18',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:19'),(20,23,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:21:45\",\"empresa_id\":1},\"TESTE DE IMPRESS\\u00c3O\\r\\n===================\\r\\nData: 13\\/07\\/2025 01:23:15Impressora: [NOME]\\r\\n===================\\r\\nEste \\u00e9 um teste de impress\\u00e3o.\\r\\nSe voc\\u00ea conseguir ver esta mensagem,\\r\\na impressora est\\u00e1 funcionando corretamente.\\r\\n===================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:23:18\"}',NULL,'2025-07-13 09:23:18',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:19'),(21,24,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:23:18\"}',NULL,'2025-07-13 09:23:18',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:19'),(22,25,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:24:20\"}',NULL,'2025-07-13 09:24:20',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:20'),(23,25,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:21:45\",\"empresa_id\":1},\"TESTE R\\u00c1PIDO\\r\\n=================\\r\\nData: 13\\/07\\/2025 01:24:00Sistema: Meu Financeiro\\r\\n=================\\r\\nImpressora funcionando!\\r\\n=================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:24:20\"}',NULL,'2025-07-13 09:24:20',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:20'),(24,26,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:24:20\"}',NULL,'2025-07-13 09:24:20',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:20'),(25,27,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:24:41\"}',NULL,'2025-07-13 09:24:41',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:20'),(26,27,'erro','Tipo de conexÃ£o nÃ£o suportado: ','{\"tipo_conexao\":\"\",\"erro_detalhado\":[{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Controllers\\\\PrinterManager\\\\ImpressorasController.php\",\"line\":361,\"function\":\"testarImpressao\",\"class\":\"App\\\\Models\\\\PrinterManager\\\\Impressora\",\"type\":\"::\",\"args\":[{\"id\":1,\"setor_id\":5,\"nome\":\"COZ.PIZZA\",\"identificacao\":\"IMP_COZPIZZA\",\"marca\":\"epson\",\"modelo\":\"t20\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"tipo_conexao\":\"\",\"driver\":\"raw\",\"largura_papel\":80,\"ultima_impressao\":null,\"preferencial\":0,\"ativo\":1,\"configuracao\":null,\"created_at\":\"2025-07-13 00:53:51\",\"updated_at\":\"2025-07-13 01:21:45\",\"empresa_id\":1},\"TESTE R\\u00c1PIDO\\r\\n=================\\r\\nData: 13\\/07\\/2025 01:24:36Sistema: Meu Financeiro\\r\\n=================\\r\\nImpressora funcionando!\\r\\n=================\"]},{\"function\":\"enviarTesteImpressora\",\"class\":\"App\\\\Controllers\\\\PrinterManager\\\\ImpressorasController\",\"type\":\"->\",\"args\":[]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\app\\\\Router.php\",\"line\":75,\"function\":\"call_user_func_array\",\"args\":[[{},\"enviarTesteImpressora\"],[]]},{\"file\":\"C:\\\\xampp\\\\htdocs\\\\meufinanceiro\\\\sistema\\\\public\\\\index.php\",\"line\":29,\"function\":\"dispatch\",\"class\":\"App\\\\Router\",\"type\":\"->\",\"args\":[]}],\"status_anterior\":\"falha\",\"novo_status\":\"falha\",\"timestamp\":\"2025-07-13 01:24:41\"}',NULL,'2025-07-13 09:24:41',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:21'),(27,28,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":false,\"mensagem_original\":\"Erro ao testar impress\\u00e3o: Tipo de conex\\u00e3o n\\u00e3o suportado: \",\"timestamp\":\"2025-07-13 01:24:41\"}',NULL,'2025-07-13 09:24:41',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:21'),(28,29,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":1,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:26:06\"}',NULL,'2025-07-13 09:26:06',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:21'),(29,29,'impressao','Teste de impressÃ£o realizado com sucesso','{\"tipo_conexao\":\"ip\",\"ip\":\"192.168.1.53\",\"porta\":9100,\"status_anterior\":\"impresso\",\"novo_status\":\"impresso\",\"timestamp\":\"2025-07-13 01:26:08\"}',NULL,'2025-07-13 09:26:08',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:22'),(30,30,'impressao','Teste de impressÃ£o realizado com sucesso','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"1\",\"sucesso\":true,\"mensagem_original\":\"Teste realizado com sucesso\",\"timestamp\":\"2025-07-13 01:26:08\"}',NULL,'2025-07-13 09:26:08',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:22'),(31,31,'impressao','Falha no teste de impressÃ£o','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":2,\"sucesso\":false,\"mensagem_original\":\"Iniciando teste de impress\\u00e3o\",\"timestamp\":\"2025-07-13 01:26:12\"}',NULL,'2025-07-13 09:26:12',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:22'),(32,31,'impressao','Teste de impressÃ£o realizado com sucesso','{\"tipo_conexao\":\"ip\",\"ip\":\"192.168.1.51\",\"porta\":9100,\"status_anterior\":\"impresso\",\"novo_status\":\"impresso\",\"timestamp\":\"2025-07-13 01:26:14\"}',NULL,'2025-07-13 09:26:14',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:23'),(33,32,'impressao','Teste de impressÃ£o realizado com sucesso','{\"tipo_teste\":\"teste_rapido\",\"impressora_id\":\"2\",\"sucesso\":true,\"mensagem_original\":\"Teste realizado com sucesso\",\"timestamp\":\"2025-07-13 01:26:14\"}',NULL,'2025-07-13 09:26:14',NULL,'pendente','2025-07-23 09:10:00',NULL,'2025-07-23 11:13:23');
/*!40000 ALTER TABLE `impressao_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressao_produto_setor`
--

DROP TABLE IF EXISTS `impressao_produto_setor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressao_produto_setor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int unsigned NOT NULL,
  `setor_id` int NOT NULL,
  `prioridade` int DEFAULT '1',
  `imprimir_observacoes` tinyint(1) DEFAULT '1',
  `imprimir_opcoes` tinyint(1) DEFAULT '1',
  `template_personalizado` text COLLATE utf8mb4_unicode_ci COMMENT 'Template especÃ­fico para este produto neste setor',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `impressora_id` int DEFAULT NULL,
  `ativo` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `produto_setor_unique` (`produto_id`,`setor_id`),
  KEY `setor_id` (`setor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressao_produto_setor`
--

LOCK TABLES `impressao_produto_setor` WRITE;
/*!40000 ALTER TABLE `impressao_produto_setor` DISABLE KEYS */;
/*!40000 ALTER TABLE `impressao_produto_setor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressao_setores`
--

DROP TABLE IF EXISTS `impressao_setores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressao_setores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_sistema` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `prioridade` int DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_sistema` (`codigo_sistema`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressao_setores`
--

LOCK TABLES `impressao_setores` WRITE;
/*!40000 ALTER TABLE `impressao_setores` DISABLE KEYS */;
INSERT INTO `impressao_setores` VALUES (1,'Cozinha Principal','COZINHA','Setor principal de produÃ§Ã£o',1,1,'2025-07-13 08:41:47','2025-07-23 21:27:44',NULL,'2025-07-23 09:10:00',NULL,'pendente'),(2,'Bar','BAR','Setor de bebidas e drinks',1,2,'2025-07-13 08:41:47','2025-07-23 11:13:24',NULL,'2025-07-23 09:10:00',NULL,'pendente'),(3,'Sobremesas','SOBREMESAS','Setor de preparo de sobremesas',1,3,'2025-07-13 08:41:47','2025-07-23 11:13:24',NULL,'2025-07-23 09:10:00',NULL,'pendente'),(4,'Saladas','SALADAS','Setor de preparo de saladas',1,4,'2025-07-13 08:41:47','2025-07-23 11:13:24',NULL,'2025-07-23 09:10:00',NULL,'pendente'),(5,'Cozinha','54','',1,3,'2025-07-13 08:52:12','2025-07-23 11:13:25',1,'2025-07-23 09:10:00',NULL,'pendente'),(6,'Chapa','556','',1,3,'2025-07-13 08:52:23','2025-07-23 11:13:25',1,'2025-07-23 09:10:00',NULL,'pendente');
/*!40000 ALTER TABLE `impressao_setores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressoes`
--

DROP TABLE IF EXISTS `impressoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lancamento_id` int NOT NULL,
  `impressora_id` int NOT NULL,
  `setor_id` int NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pendente','impresso','falha','reimpresso') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `tentativas` int DEFAULT '0',
  `ultimo_erro` text COLLATE utf8mb4_unicode_ci,
  `data_impressao` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  KEY `impressora_id` (`impressora_id`),
  KEY `setor_id` (`setor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressoes`
--

LOCK TABLES `impressoes` WRITE;
/*!40000 ALTER TABLE `impressoes` DISABLE KEYS */;
INSERT INTO `impressoes` VALUES (11,0,2,6,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:15:36','2025-07-13 09:15:36','2025-07-23 21:27:45',1,'pendente','2025-07-23 09:10:01',NULL),(12,0,2,6,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:15:36','2025-07-13 09:15:36','2025-07-23 11:13:26',1,'pendente','2025-07-23 09:10:01',NULL),(13,0,2,6,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:15:37','2025-07-13 09:15:37','2025-07-23 11:13:26',1,'pendente','2025-07-23 09:10:01',NULL),(14,0,2,6,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:15:37','2025-07-13 09:15:37','2025-07-23 11:13:26',1,'pendente','2025-07-23 09:10:01',NULL),(15,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:16:00','2025-07-13 09:16:00','2025-07-23 11:13:27',1,'pendente','2025-07-23 09:10:01',NULL),(16,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:16:00','2025-07-13 09:16:00','2025-07-23 11:13:27',1,'pendente','2025-07-23 09:10:01',NULL),(17,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:16:52','2025-07-13 09:16:52','2025-07-23 11:13:27',1,'pendente','2025-07-23 09:10:01',NULL),(18,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:16:52','2025-07-13 09:16:52','2025-07-23 11:13:28',1,'pendente','2025-07-23 09:10:01',NULL),(19,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:22:00','2025-07-13 09:22:00','2025-07-23 11:13:28',1,'pendente','2025-07-23 09:10:01',NULL),(20,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:22:00','2025-07-13 09:22:00','2025-07-23 11:13:28',1,'pendente','2025-07-23 09:10:01',NULL),(21,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:23:11','2025-07-13 09:23:11','2025-07-23 11:13:29',1,'pendente','2025-07-23 09:10:01',NULL),(22,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:23:11','2025-07-13 09:23:11','2025-07-23 11:13:29',1,'pendente','2025-07-23 09:10:01',NULL),(23,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:23:18','2025-07-13 09:23:18','2025-07-23 11:13:29',1,'pendente','2025-07-23 09:10:01',NULL),(24,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:23:18','2025-07-13 09:23:18','2025-07-23 11:13:29',1,'pendente','2025-07-23 09:10:01',NULL),(25,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:24:20','2025-07-13 09:24:20','2025-07-23 11:13:30',1,'pendente','2025-07-23 09:10:01',NULL),(26,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:24:20','2025-07-13 09:24:20','2025-07-23 11:13:30',1,'pendente','2025-07-23 09:10:01',NULL),(27,0,1,5,'Iniciando teste de impressÃ£o','falha',0,'Tipo de conexÃ£o nÃ£o suportado: ','2025-07-13 01:24:41','2025-07-13 09:24:41','2025-07-23 11:13:30',1,'pendente','2025-07-23 09:10:01',NULL),(28,0,1,5,'Erro ao testar impressÃ£o: Tipo de conexÃ£o nÃ£o suportado: ','falha',0,NULL,'2025-07-13 01:24:41','2025-07-13 09:24:41','2025-07-23 11:13:31',1,'pendente','2025-07-23 09:10:01',NULL),(29,0,1,5,'Iniciando teste de impressÃ£o','impresso',0,NULL,'2025-07-13 01:26:08','2025-07-13 09:26:06','2025-07-23 11:13:31',1,'pendente','2025-07-23 09:10:01',NULL),(30,0,1,5,'Teste realizado com sucesso','impresso',0,NULL,'2025-07-13 01:26:08','2025-07-13 09:26:08','2025-07-23 11:13:31',1,'pendente','2025-07-23 09:10:01',NULL),(31,0,2,6,'Iniciando teste de impressÃ£o','impresso',0,NULL,'2025-07-13 01:26:14','2025-07-13 09:26:12','2025-07-23 11:13:32',1,'pendente','2025-07-23 09:10:01',NULL),(32,0,2,6,'Teste realizado com sucesso','impresso',0,NULL,'2025-07-13 01:26:14','2025-07-13 09:26:14','2025-07-23 11:13:32',1,'pendente','2025-07-23 09:10:01',NULL),(36,204,1,5,'================================================================================\n                               COMANDA - SETOR 1                                \n--------------------------------------------------------------------------------\nPedido: #204\nData: 21/07/2025 07:16:59\nMesa: 2\n--------------------------------------------------------------------------------\nITEM                                                               QTD     VALOR\n--------------------------------------------------------------------------------\nx bacon                                                           1.00     10.00\nAÃ‡AI 300ML                                                       1.00     16.00\n--------------------------------------------------------------------------------\nTOTAL:                                                                   R$ 0.00\n================================================================================\n                                  === FIM ===                                   \n','impresso',0,NULL,'2025-07-21 01:16:59','2025-07-21 09:16:59','2025-07-23 11:13:32',1,'pendente','2025-07-23 11:13:32',NULL),(37,204,1,5,'================================================================================\n                               COMANDA - SETOR 1                                \n--------------------------------------------------------------------------------\nPedido: #204\nData: 21/07/2025 07:17:16\nMesa: 2\n--------------------------------------------------------------------------------\nITEM                                                               QTD     VALOR\n--------------------------------------------------------------------------------\nx bacon                                                           1.00     10.00\nAÃ‡AI 300ML                                                       1.00     16.00\n--------------------------------------------------------------------------------\nTOTAL:                                                                   R$ 0.00\n================================================================================\n                                  === FIM ===                                   \n','impresso',0,NULL,'2025-07-21 01:17:16','2025-07-21 09:17:16','2025-07-23 11:13:32',1,'pendente','2025-07-23 11:13:32',NULL),(46,372,1,5,'================================================================================\n                               COMANDA - SETOR 1                                \n--------------------------------------------------------------------------------\nPedido: #372\nData: 24/07/2025 06:13:21\nMesa: 5\n--------------------------------------------------------------------------------\nITEM                                                               QTD     VALOR\n--------------------------------------------------------------------------------\nAÃ‡AI 300ML                                                       1.00     16.00\n--------------------------------------------------------------------------------\nTOTAL:                                                                   R$ 0.00\n================================================================================\n                                  === FIM ===                                   \n','impresso',0,NULL,'2025-07-24 00:13:21','2025-07-24 08:13:21','2025-07-24 08:13:21',1,'pendente','2025-07-24 09:15:02',NULL);
/*!40000 ALTER TABLE `impressoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressoras`
--

DROP TABLE IF EXISTS `impressoras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressoras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setor_id` int NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identificacao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome Ãºnico para identificar a impressora',
  `marca` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modelo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `porta` int DEFAULT '9100',
  `tipo_conexao` enum('ip','usb','bluetooth','serial') COLLATE utf8mb4_unicode_ci DEFAULT 'ip',
  `driver` enum('escpos','raw','zebra','epl','windows') COLLATE utf8mb4_unicode_ci NOT NULL,
  `largura_papel` int DEFAULT '80',
  `ultima_impressao` datetime DEFAULT NULL,
  `preferencial` tinyint(1) DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `configuracao` text COLLATE utf8mb4_unicode_ci COMMENT 'ConfiguraÃ§Ãµes especÃ­ficas em JSON',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificacao` (`identificacao`),
  KEY `setor_id` (`setor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressoras`
--

LOCK TABLES `impressoras` WRITE;
/*!40000 ALTER TABLE `impressoras` DISABLE KEYS */;
INSERT INTO `impressoras` VALUES (1,5,'COZ.PIZZA','IMP_COZPIZZA','epson','t20','192.168.1.53',9100,'ip','raw',80,'2025-07-13 01:26:08',0,1,NULL,'2025-07-13 08:53:51','2025-07-23 21:27:46',1,'2025-07-23 09:10:01',NULL,'pendente'),(2,6,'CHAPA','IMP_CHAPA','EPSON','T20','192.168.1.51',9100,'ip','raw',80,'2025-07-13 01:26:14',0,1,NULL,'2025-07-13 08:57:06','2025-07-23 11:13:33',1,'2025-07-23 09:10:01',NULL,'pendente'),(3,6,'IMPRESSORA_COZINHA','IMP_COZ_01','','','192.168.1.52',9100,'ip','raw',80,NULL,0,1,NULL,'2025-07-13 09:13:22','2025-07-26 06:49:46',1,'2025-07-23 09:10:01',NULL,'pendente');
/*!40000 ALTER TABLE `impressoras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impressoras_configuracoes`
--

DROP TABLE IF EXISTS `impressoras_configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impressoras_configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `chave` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `valor` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `codigo_sistema` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'CÃ³digo Ãºnico do sistema',
  `sync_hash` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Hash MD5 dos dados',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_general_ci DEFAULT 'pendente' COMMENT 'Status da sincronizaÃ§Ã£o',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_config` (`empresa_id`,`categoria`,`chave`),
  KEY `idx_empresa_categoria` (`empresa_id`,`categoria`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impressoras_configuracoes`
--

LOCK TABLES `impressoras_configuracoes` WRITE;
/*!40000 ALTER TABLE `impressoras_configuracoes` DISABLE KEYS */;
INSERT INTO `impressoras_configuracoes` VALUES (1,1,'geral','timeout_conexao','10','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000001_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(2,1,'geral','max_tentativas','3','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000002_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(3,1,'geral','intervalo_tentativas','1000','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000003_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(4,1,'formatacao','encoding_padrao','UTF-8','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000004_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(5,1,'formatacao','largura_padrao','48','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000005_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(6,1,'formatacao','cortar_papel','1','2025-07-14 00:56:39','2025-07-25 11:46:11','IMP_000006_1752440199',NULL,'pendente','2025-07-25 11:46:11'),(7,1,'template','cabecalho','=== MEU RESTAURANTE ===\n{data_hora}\n------------------','2025-07-14 00:56:39','2025-07-24 08:57:13','IMP_000007_1752440199',NULL,'pendente','2025-07-24 08:57:13'),(8,1,'template','corpo','Mesa: {mesa}\nProduto: {produto}\nQtd: {quantidade}\nObs: {observacoes}','2025-07-14 00:56:39','2025-07-24 08:57:13','IMP_000008_1752440199',NULL,'pendente','2025-07-24 08:57:13'),(9,1,'template','rodape','------------------\nObrigado!\n{hora_impressao}','2025-07-14 00:56:39','2025-07-24 08:57:13','IMP_000009_1752440199',NULL,'pendente','2025-07-24 08:57:13'),(10,1,'controlid_impressora_3','tamanho_fonte','1','2025-07-25 10:55:33','2025-07-25 20:42:49',NULL,NULL,'pendente','2025-07-25 20:42:49'),(11,1,'controlid_impressora_3','densidade_extra','1','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(12,1,'controlid_impressora_3','contraste_melhorado','1','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(13,1,'controlid_impressora_3','temperatura_impressao','80','2025-07-25 10:55:33','2025-07-25 20:20:01',NULL,NULL,'pendente','2025-07-25 20:20:01'),(14,1,'controlid_impressora_3','velocidade_impressao','1','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(15,1,'controlid_impressora_3','negrito_permanente','1','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(16,1,'controlid_impressora_3','dupla_densidade','0','2025-07-25 10:55:33','2025-07-25 11:49:38',NULL,NULL,'pendente','2025-07-25 11:49:38'),(17,1,'controlid_global','escpos_densidade_maxima','15','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(18,1,'controlid_global','escpos_temperatura','80','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(19,1,'controlid_global','comando_reset_controlid','0','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(20,1,'controlid_global','corte_papel_controlid','1','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11'),(21,1,'controlid_global','encoding_controlid','UTF-8','2025-07-25 10:55:33','2025-07-25 11:46:11',NULL,NULL,'pendente','2025-07-25 11:46:11');
/*!40000 ALTER TABLE `impressoras_configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lancamento_itens`
--

DROP TABLE IF EXISTS `lancamento_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lancamento_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lancamento_id` int NOT NULL,
  `produto_id` int unsigned DEFAULT NULL,
  `produto_variacao_id` int unsigned DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `observacoes` text COLLATE utf8mb3_unicode_ci,
  `empresa_id` int NOT NULL,
  `hora_transferencia` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_id` int DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lancamento_itens`
--

LOCK TABLES `lancamento_itens` WRITE;
/*!40000 ALTER TABLE `lancamento_itens` DISABLE KEYS */;
INSERT INTO `lancamento_itens` VALUES (7,74,2,NULL,19.00,10.00,190.00,NULL,1,NULL,'2025-07-23 09:00:48','2025-07-23 21:27:48',NULL,'pendente','2025-07-23 09:10:01',NULL),(8,73,9,NULL,1.00,10.00,10.00,NULL,1,NULL,'2025-07-23 09:00:48','2025-07-23 11:59:30',NULL,'pendente','2025-07-23 09:10:01',NULL),(9,73,9,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-10 21:11:50','2025-07-23 11:59:30',NULL,'pendente','2025-07-23 09:10:01',NULL),(10,73,9,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-10 21:31:44','2025-07-23 11:59:30',NULL,'pendente','2025-07-23 09:10:01',NULL),(11,73,9,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-10 21:39:14','2025-07-23 11:59:31',NULL,'pendente','2025-07-23 09:10:01',NULL),(12,73,9,NULL,1.00,0.00,0.00,'',1,NULL,'2025-05-10 21:57:06','2025-07-23 11:59:31',NULL,'pendente','2025-07-23 09:10:01',NULL),(13,73,9,NULL,1.00,0.00,0.00,'',1,NULL,'2025-05-11 00:12:06','2025-07-23 11:59:31',NULL,'pendente','2025-07-23 09:10:01',NULL),(14,73,9,NULL,1.00,0.00,0.00,'',1,NULL,'2025-05-11 00:12:14','2025-07-23 11:59:32',NULL,'pendente','2025-07-23 09:10:01',NULL),(15,73,9,NULL,1.00,0.00,0.00,'',1,NULL,'2025-05-13 12:02:44','2025-07-23 11:59:32',NULL,'pendente','2025-07-23 09:10:01',NULL),(16,73,9,NULL,1.00,20.00,20.00,'',1,NULL,'2025-05-13 12:03:26','2025-07-23 11:59:32',NULL,'pendente','2025-07-23 09:10:01',NULL),(17,74,2,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-15 11:55:55','2025-07-23 11:59:33',3,'pendente','2025-07-23 09:10:01',NULL),(18,76,14,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-15 12:23:36','2025-07-23 11:59:33',3,'pendente','2025-07-23 09:10:01',NULL),(19,77,9,NULL,1.00,20.00,20.00,'',1,NULL,'2025-05-31 09:38:12','2025-07-23 11:59:33',1,'pendente','2025-07-23 09:10:01',NULL),(20,80,14,NULL,1.00,10.00,10.00,'',1,NULL,'2025-05-31 10:46:00','2025-07-23 11:59:33',1,'pendente','2025-07-23 09:10:01',NULL),(21,158,9,NULL,1.00,20.00,20.00,'',1,NULL,'2025-06-05 20:12:41','2025-07-23 11:59:34',5,'pendente','2025-07-23 09:10:01',NULL),(22,204,2,NULL,1.00,10.00,10.00,'',1,NULL,'2025-06-10 19:04:22','2025-07-23 11:59:34',3,'pendente','2025-07-23 09:10:01',NULL),(23,352,40,NULL,1.00,16.00,16.00,'',1,NULL,'2025-07-05 06:49:29','2025-07-23 11:59:34',3,'pendente','2025-07-23 09:10:01',NULL),(24,354,40,NULL,1.00,16.00,16.00,'',1,NULL,'2025-07-06 22:37:00','2025-07-23 11:59:35',3,'pendente','2025-07-23 09:10:01',NULL),(25,355,40,NULL,1.00,16.00,16.00,'',1,NULL,'2025-07-09 10:39:16','2025-07-23 11:59:35',3,'pendente','2025-07-23 09:10:01',NULL),(26,355,294,NULL,1.00,25.00,25.00,'',1,NULL,'2025-07-09 10:39:19','2025-07-23 11:59:35',3,'pendente','2025-07-23 09:10:01',NULL),(27,353,43,NULL,1.00,13.00,13.00,'',1,NULL,'2025-07-12 09:05:52','2025-07-23 11:59:36',3,'pendente','2025-07-23 09:10:01',NULL),(30,372,40,NULL,1.00,16.00,16.00,'',1,NULL,'2025-07-24 08:13:07','2025-07-24 08:13:07',3,'pendente','2025-07-24 09:15:03',NULL);
/*!40000 ALTER TABLE `lancamento_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lancamento_itens_opcoes`
--

DROP TABLE IF EXISTS `lancamento_itens_opcoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lancamento_itens_opcoes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lancamento_item_id` int unsigned NOT NULL COMMENT 'ReferÃªncia ao item do lanÃ§amento',
  `produto_configuracao_id` int unsigned NOT NULL COMMENT 'ReferÃªncia Ã  configuraÃ§Ã£o do produto',
  `produto_configuracao_opcao_id` int unsigned NOT NULL COMMENT 'ReferÃªncia Ã  opÃ§Ã£o selecionada',
  `valor_adicional` decimal(10,2) DEFAULT NULL COMMENT 'Valor adicional da opÃ§Ã£o',
  `empresa_id` int unsigned NOT NULL COMMENT 'ReferÃªncia Ã  empresa',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usuario_id` int DEFAULT NULL,
  `sync_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nome_produto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantidade_adicional` int DEFAULT NULL,
  `lancamento_id` int NOT NULL DEFAULT '0',
  `nome_configuracao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='Tabela para armazenar as opÃ§Ãµes selecionadas nos itens do lanÃ§amento';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lancamento_itens_opcoes`
--

LOCK TABLES `lancamento_itens_opcoes` WRITE;
/*!40000 ALTER TABLE `lancamento_itens_opcoes` DISABLE KEYS */;
INSERT INTO `lancamento_itens_opcoes` VALUES (1,1,7,18,10.00,1,'2025-05-13 08:52:40','2025-07-23 21:27:48',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(2,1,8,3,10.00,1,'2025-05-13 08:52:40','2025-07-23 11:59:37',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(3,1,8,4,10.00,1,'2025-05-13 08:52:40','2025-07-23 11:59:37',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(4,1,7,18,10.00,1,'2025-05-13 08:54:15','2025-07-23 11:59:37',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(5,1,8,3,10.00,1,'2025-05-13 08:54:15','2025-07-23 11:59:38',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(6,1,8,4,10.00,1,'2025-05-13 08:54:15','2025-07-23 11:59:38',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(7,1,7,16,10.00,1,'2025-05-13 09:04:47','2025-07-23 11:59:38',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(8,1,7,17,10.00,1,'2025-05-13 09:04:47','2025-07-23 11:59:38',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(9,1,8,3,10.00,1,'2025-05-13 09:04:47','2025-07-23 11:59:39',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(10,31,7,16,10.00,1,'2025-05-13 09:32:55','2025-07-23 11:59:39',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(11,31,7,17,10.00,1,'2025-05-13 09:32:55','2025-07-23 11:59:39',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(12,31,8,5,10.00,1,'2025-05-13 09:32:55','2025-07-23 11:59:40',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(13,32,7,16,10.00,1,'2025-05-13 09:42:55','2025-07-23 11:59:40',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(14,32,7,17,10.00,1,'2025-05-13 09:42:55','2025-07-23 11:59:40',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(15,32,8,3,10.00,1,'2025-05-13 09:42:55','2025-07-23 11:59:41',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(16,32,8,4,10.00,1,'2025-05-13 09:42:55','2025-07-23 11:59:41',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(17,33,7,16,10.00,1,'2025-05-13 09:44:31','2025-07-23 11:59:41',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(18,33,7,17,10.00,1,'2025-05-13 09:44:31','2025-07-23 11:59:41',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(19,33,8,3,10.00,1,'2025-05-13 09:44:31','2025-07-23 11:59:42',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(20,33,8,4,10.00,1,'2025-05-13 09:44:31','2025-07-23 11:59:42',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(21,34,7,16,10.00,1,'2025-05-13 09:49:28','2025-07-23 11:59:42',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(22,34,7,17,10.00,1,'2025-05-13 09:49:28','2025-07-23 11:59:43',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(23,34,8,4,10.00,1,'2025-05-13 09:49:28','2025-07-23 11:59:43',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(24,37,11,19,10.00,1,'2025-05-13 10:10:53','2025-07-23 11:59:43',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(25,40,11,19,10.00,1,'2025-05-13 10:23:09','2025-07-23 11:59:44',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(26,42,11,19,10.00,1,'2025-05-13 10:28:53','2025-07-23 11:59:44',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(27,42,11,20,15.00,1,'2025-05-13 10:28:53','2025-07-23 11:59:44',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(28,43,11,19,10.00,1,'2025-05-13 10:39:09','2025-07-23 11:59:44',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(29,43,11,20,15.00,1,'2025-05-13 10:39:09','2025-07-23 11:59:45',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(30,44,11,19,10.00,1,'2025-05-13 10:41:42','2025-07-23 11:59:45',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(31,44,11,20,15.00,1,'2025-05-13 10:41:42','2025-07-23 11:59:45',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(32,45,11,19,10.00,1,'2025-05-13 10:42:14','2025-07-23 11:59:46',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(33,45,11,20,15.00,1,'2025-05-13 10:42:14','2025-07-23 11:59:46',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(34,46,11,19,10.00,1,'2025-05-13 10:46:06','2025-07-23 11:59:46',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(35,47,11,19,10.00,1,'2025-05-13 10:46:43','2025-07-23 11:59:46',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(36,47,11,20,15.00,1,'2025-05-13 10:46:43','2025-07-23 11:59:47',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(37,47,11,21,10.00,1,'2025-05-13 10:46:43','2025-07-23 11:59:47',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(38,48,11,19,10.00,1,'2025-05-13 10:48:30','2025-07-23 11:59:47',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(39,48,11,20,15.00,1,'2025-05-13 10:48:30','2025-07-23 11:59:48',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(40,15,7,16,10.00,1,'2025-05-13 12:02:44','2025-07-23 11:59:48',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(41,15,7,17,10.00,1,'2025-05-13 12:02:44','2025-07-23 11:59:48',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(42,15,8,3,10.00,1,'2025-05-13 12:02:44','2025-07-23 11:59:49',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(43,15,8,4,10.00,1,'2025-05-13 12:02:44','2025-07-23 11:59:49',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(44,16,7,16,10.00,1,'2025-05-13 12:03:26','2025-07-23 11:59:49',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(45,16,7,17,10.00,1,'2025-05-13 12:03:26','2025-07-23 11:59:49',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(46,16,8,3,10.00,1,'2025-05-13 12:03:26','2025-07-23 11:59:50',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(47,16,8,4,10.00,1,'2025-05-13 12:03:26','2025-07-23 11:59:50',NULL,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(48,17,11,19,12.50,1,'2025-05-15 11:55:55','2025-07-23 11:59:50',3,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(49,17,11,20,12.50,1,'2025-05-15 11:55:55','2025-07-23 11:59:51',3,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(50,19,7,16,10.00,1,'2025-05-31 09:38:12','2025-07-23 11:59:51',1,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(51,19,8,3,10.00,1,'2025-05-31 09:38:12','2025-07-23 11:59:51',1,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(52,21,7,16,10.00,1,'2025-06-05 20:12:41','2025-07-23 11:59:52',5,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(53,21,7,18,10.00,1,'2025-06-05 20:12:41','2025-07-23 11:59:52',5,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(54,21,8,3,10.00,1,'2025-06-05 20:12:41','2025-07-23 11:59:52',5,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(55,21,8,4,10.00,1,'2025-06-05 20:12:41','2025-07-23 11:59:52',5,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(56,22,11,19,12.50,1,'2025-06-10 19:04:22','2025-07-23 11:59:53',3,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL),(57,22,11,20,12.50,1,'2025-06-10 19:04:22','2025-07-23 11:59:53',3,NULL,'pendente','2025-07-23 09:10:01',NULL,NULL,0,NULL);
/*!40000 ALTER TABLE `lancamento_itens_opcoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lancamento_status`
--

DROP TABLE IF EXISTS `lancamento_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lancamento_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `origem` enum('pdv','lancamentos','delivery','site','app_garcom','geral') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'geral',
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `cor` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#007bff',
  `icone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  `permite_edicao` tinyint(1) NOT NULL DEFAULT '1',
  `permite_exclusao` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_origem_codigo` (`empresa_id`,`origem`,`codigo`),
  KEY `idx_ativo` (`ativo`),
  KEY `idx_empresa_origem` (`empresa_id`,`origem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lancamento_status`
--

LOCK TABLES `lancamento_status` WRITE;
/*!40000 ALTER TABLE `lancamento_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `lancamento_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lancamentos`
--

DROP TABLE IF EXISTS `lancamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lancamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL DEFAULT '0',
  `mesa_id` int NOT NULL DEFAULT '0',
  `tipo_id` int DEFAULT NULL,
  `funcionario_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `caixa_id` int DEFAULT NULL,
  `conta_gerencial_id` int DEFAULT NULL,
  `tipo_lancamento_id` int DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `parcela_referencia` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data_modificacao` datetime DEFAULT NULL,
  `origem` enum('pdv','lancamento','delivery') COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT '''pdv'',''lancamento'',''delivery''',
  `tipo_venda` enum('venda','orcamento','devolucao') COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status_pdv` enum('livre','ocupado','fechado','cancelado','excluido','finalizado','iniciado','producao','pronto_retirar','saiu_entrega','fila_preparo') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'livre' COMMENT '''livre'',''ocupado'',''fechado'',''cancelado'',''excluido'',''finalizado'',''iniciado'',''producao'',''pronto_retirar'',''saiu_entrega'',''fila_preparo''',
  `desconto` decimal(10,2) DEFAULT '0.00',
  `entregador_id` int DEFAULT NULL,
  `endereco_id` int DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lancamentos`
--

LOCK TABLES `lancamentos` WRITE;
/*!40000 ALTER TABLE `lancamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `lancamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `ultimo_login` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_login` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_login_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (2,1,'2025-03-25 00:48:00','111.111.111.1','2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:50'),(3,1,'2025-03-25 02:09:00','::1','2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:35');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `ativo` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'COCA','2025-03-27 22:33:37','2025-07-23 21:27:51',1,NULL,'2025-07-23 09:10:01',NULL,'pendente'),(2,'AMBEV','2025-03-27 22:33:37','2025-07-23 11:13:36',1,NULL,'2025-07-23 09:10:01',NULL,'pendente');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `melhorias`
--

DROP TABLE IF EXISTS `melhorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `melhorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `versao` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` date NOT NULL,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `melhorias`
--

LOCK TABLES `melhorias` WRITE;
/*!40000 ALTER TABLE `melhorias` DISABLE KEYS */;
INSERT INTO `melhorias` VALUES (1,'atualizaÃ§Ã£o das melhorias ','1.0.2','Hoje implementamos uma melhoria para exibir automaticamente a Ãºltima versÃ£o da aplicaÃ§Ã£o no rodapÃ© do site. A seguir, segue um resumo das alteraÃ§Ãµes:\r\n\r\nAtualizaÃ§Ã£o do Modelo (Melhoria.php):\r\n\r\nFoi adicionado o mÃ©todo estÃ¡tico getLatestVersion().\r\nEsse mÃ©todo utiliza o objeto PDO para buscar, na tabela melhorias, o campo versao ordenado pela data mais recente e retorna o valor encontrado.\r\nCaso nÃ£o haja nenhum registro, Ã© retornado o valor da constante APP_VERSION.\r\nAtualizaÃ§Ã£o do RodapÃ© (footer.php):\r\n\r\nNo arquivo do rodapÃ©, chamamos o mÃ©todo Melhoria::getLatestVersion() para recuperar a Ãºltima versÃ£o cadastrada.\r\nA mensagem \"Bem-vindo ao MeuFinanceiro - VersÃ£o X.X.X\" Ã© exibida automaticamente, garantindo que o site esteja sempre mostrando a versÃ£o atual da aplicaÃ§Ã£o.\r\nEssas alteraÃ§Ãµes permitem que o site exiba de forma dinÃ¢mica a versÃ£o da aplicaÃ§Ã£o conforme as melhorias cadastradas no banco de dados, facilitando o controle e a comunicaÃ§Ã£o das atualizaÃ§Ãµes realizadas para os usuÃ¡rios.','2025-04-21',1,'2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:52'),(2,'Resumo das melhorias implementadas na versÃ£o 1.0.3','1.0.3','**Resumo das melhorias implementadas na versÃ£o 1.0.3**\r\n\r\nNesta atualizaÃ§Ã£o, foram realizadas diversas melhorias para tornar o sistema mais prÃ¡tico, moderno e eficiente para o usuÃ¡rio:\r\n\r\n- **Filtro de pesquisa aprimorado:** Agora, ao acessar a tela de lanÃ§amentos, o filtro jÃ¡ traz por padrÃ£o os lanÃ§amentos do dia atual, facilitando a visualizaÃ§Ã£o dos dados mais recentes.\r\n- **ManutenÃ§Ã£o dos filtros e ordenaÃ§Ã£o:** Ao utilizar a ordenaÃ§Ã£o das colunas ou navegar entre as pÃ¡ginas, todos os filtros de pesquisa e o critÃ©rio de ordenaÃ§Ã£o sÃ£o mantidos, proporcionando uma experiÃªncia mais fluida e intuitiva.\r\n- **Rolagem automÃ¡tica para resultados:** Sempre que o usuÃ¡rio realiza uma pesquisa, ordena ou muda de pÃ¡gina, a tela rola automaticamente para a tabela de resultados, agilizando o acesso Ã  informaÃ§Ã£o.\r\n- **Aprimoramento visual e responsividade:** Pequenos ajustes de layout e responsividade foram feitos para garantir melhor visualizaÃ§Ã£o em diferentes dispositivos.\r\n\r\nEssas melhorias tornam o sistema mais amigÃ¡vel, rÃ¡pido e eficiente para o controle financeiro do usuÃ¡rio.\r\n\r\n---\r\n\r\nVocÃª pode registrar essas mudanÃ§as como release notes da versÃ£o 1.0.3!','2025-04-21',1,'2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:37'),(3,'Pagamentos agora vinculados Ã  conta bancÃ¡ria:','1.4','Resumo das Melhorias\r\nPagamentos agora vinculados Ã  conta bancÃ¡ria:\r\nAdicionado o campo conta_bancaria_id na tabela de pagamentos e no formulÃ¡rio, garantindo que cada pagamento registre de qual conta saiu ou entrou o valor.\r\n\r\nValidaÃ§Ã£o aprimorada:\r\nO controller agora valida se a conta bancÃ¡ria foi selecionada antes de registrar o pagamento, evitando erros de integridade no banco.\r\n\r\nExibiÃ§Ã£o do saldo por conta:\r\nImplementada consulta SQL e funÃ§Ã£o PHP para exibir o saldo de cada conta bancÃ¡ria com base nos pagamentos realizados, alÃ©m do saldo total.\r\n\r\nRedirecionamento inteligente:\r\nApÃ³s registrar pagamento de salÃ¡rio, o sistema redireciona automaticamente para a tela de ediÃ§Ã£o do Ãºltimo lanÃ§amento, facilitando o acompanhamento.\r\n\r\nAjustes de interface:\r\nO formulÃ¡rio de pagamento ficou mais claro, com seleÃ§Ã£o obrigatÃ³ria de conta, valor mÃ¡ximo limitado ao valor restante e informaÃ§Ãµes detalhadas de cada conta (nome, banco, saldo).\r\n\r\nCorreÃ§Ã£o de bugs:\r\nCorrigidos erros de parÃ¢metros e de acesso a arrays/objetos, garantindo que os dados sejam manipulados corretamente em todas as telas.','2025-04-25',1,'2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:37'),(4,'Melhorias na Listagem de LanÃ§amentos','1.0.5','\r\n**Notas da VersÃ£o 1.0.5 - Melhorias na Listagem de LanÃ§amentos**\r\n\r\n*   **Filtro de Data PadrÃ£o:** O filtro de data agora exibe por padrÃ£o o mÃªs atual completo (do dia 1 ao Ãºltimo dia) ao carregar a pÃ¡gina.\r\n*   **Coluna \"Tipo\":** Adicionada uma nova coluna Ã  tabela que indica se o lanÃ§amento Ã© \"Receita\" ou \"Despesa\", utilizando badges coloridos (verde para receita, vermelho para despesa).\r\n*   **ColoraÃ§Ã£o de Linhas:** As linhas da tabela agora possuem uma cor de fundo sutil (verde claro para receitas, vermelho claro para despesas) para facilitar a identificaÃ§Ã£o visual.\r\n*   **Truncamento de DescriÃ§Ã£o:** A coluna \"DescriÃ§Ã£o\" agora exibe apenas os primeiros 15 caracteres. A descriÃ§Ã£o completa pode ser visualizada passando o mouse sobre a cÃ©lula (tooltip).\r\n*   **Totais da PÃ¡gina:** Adicionado um rodapÃ© (`<tfoot>`) Ã  tabela que exibe a soma total das receitas e despesas *listadas na pÃ¡gina atual*, alÃ©m do saldo resultante para essa pÃ¡gina.\r\n*   **Filtro \"Registros por PÃ¡gina\":** IncluÃ­do um novo campo `<select>` no formulÃ¡rio de filtro, permitindo ao usuÃ¡rio escolher quantos lanÃ§amentos deseja exibir por pÃ¡gina (10, 25, 50 ou 100).\r\n*   **RefatoraÃ§Ã£o e Estilo:** O cÃ³digo PHP dentro do loop da tabela foi reorganizado para melhor clareza. Pequenos ajustes de estilo (espaÃ§amento, classes Bootstrap) foram aplicados ao formulÃ¡rio e Ã  tabela.','2025-04-28',1,'2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:38'),(5,'### 1. **Parcelamento de LanÃ§amentos**','1.0.8','Claro! Aqui estÃ¡ um resumo do que foi feito hoje no seu sistema de lanÃ§amentos:\r\n\r\n---\r\n\r\n### 1. **Parcelamento de LanÃ§amentos**\r\n- ImplementaÃ§Ã£o do parcelamento criando mÃºltiplos registros na tabela `lancamentos` (um para cada parcela).\r\n- Cada parcela recebe um valor, data de vencimento e descriÃ§Ã£o prÃ³pria.\r\n- Foi criado o campo `parcela_referencia` para identificar e agrupar todas as parcelas de um mesmo parcelamento.\r\n\r\n### 2. **FormulÃ¡rio e Fluxo**\r\n- O formulÃ¡rio de lanÃ§amento agora aceita a quantidade de parcelas.\r\n- Se for 1 parcela, o lanÃ§amento Ã© criado normalmente.\r\n- Se for mais de 1, sÃ£o criados vÃ¡rios lanÃ§amentos, cada um com vencimento e valor ajustados.\r\n- ApÃ³s criar, o usuÃ¡rio Ã© redirecionado para uma tela de revisÃ£o das parcelas.\r\n\r\n### 3. **RevisÃ£o e EdiÃ§Ã£o das Parcelas**\r\n- Tela para revisar todas as parcelas de um parcelamento, permitindo editar valor e data de vencimento de cada uma.\r\n- Soma automÃ¡tica do valor total das parcelas exibida na tela.\r\n- Link para editar individualmente cada parcela.\r\n- O botÃ£o \"Editar\" fica desabilitado para a parcela atualmente selecionada.\r\n\r\n### 4. **VisualizaÃ§Ã£o no Edit**\r\n- Ao editar um lanÃ§amento parcelado, Ã© exibida uma lista de todas as parcelas relacionadas na parte inferior da pÃ¡gina.\r\n- Cada parcela tem link para ediÃ§Ã£o individual.\r\n\r\n### 5. **Melhorias Gerais**\r\n- CorreÃ§Ã£o de mÃ©todos no Model para buscar parcelas por referÃªncia.\r\n- Ajuste do identificador de parcelamento para ser numÃ©rico e curto.\r\n- AtualizaÃ§Ã£o do controller para processar corretamente valores e datas ao atualizar parcelas.\r\n\r\n---\r\n\r\nSe precisar de algum detalhe de cÃ³digo ou quiser revisar algum ponto especÃ­fico, Ã© sÃ³ pedir!','2025-05-01',1,'2025-07-23 09:10:01',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:38');
/*!40000 ALTER TABLE `melhorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesa_status_log`
--

DROP TABLE IF EXISTS `mesa_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mesa_status_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `mesa_id` int NOT NULL,
  `lancamento_id` int DEFAULT NULL,
  `status_anterior` varchar(50) DEFAULT NULL,
  `status_novo` varchar(50) NOT NULL,
  `usuario_id` int NOT NULL,
  `usuario_nome` varchar(100) NOT NULL,
  `acao` varchar(50) NOT NULL,
  `observacoes` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `data_mudanca` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_data_mudanca` (`data_mudanca`),
  KEY `idx_empresa_mesa` (`empresa_id`,`mesa_id`),
  KEY `idx_lancamento` (`lancamento_id`),
  KEY `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesa_status_log`
--

LOCK TABLES `mesa_status_log` WRITE;
/*!40000 ALTER TABLE `mesa_status_log` DISABLE KEYS */;
INSERT INTO `mesa_status_log` VALUES (4,4,0,0,'iniciar_mesa','371',3,'mazinho','Mesa iniciada no PDV',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-23 09:47:36','2025-07-23 12:01:38',NULL,'pendente','2025-07-23 09:47:36','2025-07-23 21:27:52'),(5,5,0,0,'iniciar_mesa','372',3,'mazinho','Mesa iniciada no PDV',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','2025-07-24 08:13:04','2025-07-24 09:15:05',NULL,'pendente','2025-07-24 08:13:04','2025-07-24 08:13:04');
/*!40000 ALTER TABLE `mesa_status_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (2,'2024_01_01_000001_create_payment_gateways_table',1),(7,'2024_01_20_fix_database_structure',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lancamento_id` int NOT NULL,
  `tipo_id` int NOT NULL,
  `forma_pagamento_id` int NOT NULL,
  `bandeira_id` int DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `observacao` text COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `conta_bancaria_id` int NOT NULL,
  `taxa` decimal(5,2) DEFAULT NULL,
  `empresa_id` int NOT NULL,
  `caixa_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `valor_taxa` decimal(10,2) DEFAULT NULL,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL COMMENT 'Hash MD5 dos dados',
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente' COMMENT 'Status da sincronizaÃ§Ã£o',
  `sync_status_copy` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=385 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos`
--

LOCK TABLES `pagamentos` WRITE;
/*!40000 ALTER TABLE `pagamentos` DISABLE KEYS */;
INSERT INTO `pagamentos` VALUES (1,3,0,3,5,165.00,'2025-04-21','','2025-04-21 11:58:44','2025-05-05 12:12:56',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(3,5,0,4,4,1500.00,'2025-04-22','','2025-04-22 09:53:25','2025-05-05 12:12:57',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(4,6,0,4,4,608.00,'2025-04-22','','2025-04-22 09:55:58','2025-05-05 12:13:22',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(6,4,0,4,4,300.00,'2025-04-25','','2025-04-25 23:05:49','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(8,8,1,4,4,100.00,'2025-04-26','','2025-04-26 23:54:37','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(9,9,2,3,5,200.00,'2025-04-27','','2025-04-27 11:22:25','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(10,11,1,5,4,335.00,'2025-04-27','','2025-04-27 23:13:54','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(11,12,1,4,4,46.00,'2025-04-27','','2025-04-27 23:15:05','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(12,13,2,3,5,80.00,'2025-04-27','','2025-04-27 23:17:59','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(13,14,2,4,4,110.00,'2025-04-27','','2025-04-27 23:19:40','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(14,15,1,6,6,156.00,'2025-04-27','','2025-04-27 23:21:23','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(15,16,2,3,5,9.00,'2025-04-27','','2025-04-27 23:28:45','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(16,17,1,5,6,101.00,'2025-04-27','','2025-04-28 01:53:03','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(17,18,1,6,7,137.00,'2025-04-27','','2025-04-28 01:57:33','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(18,19,1,6,7,75.00,'2025-04-27','','2025-04-28 01:59:03','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(19,20,2,3,5,20.00,'2025-04-27','','2025-04-28 02:00:46','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(21,22,2,4,4,100.00,'2025-04-27','','2025-04-28 02:03:47','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(22,21,2,4,4,100.00,'2025-04-27','','2025-04-28 02:04:18','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(23,23,2,4,4,110.00,'2025-04-27','','2025-04-28 02:09:58','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(24,24,1,6,6,787.00,'2025-04-28','','2025-04-28 09:00:47','2025-05-05 12:13:22',3,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(25,25,2,4,4,322.00,'2025-04-28','','2025-04-28 09:12:33','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(26,26,2,4,4,300.00,'2025-04-28','','2025-04-28 09:13:50','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(27,26,2,3,5,300.00,'2025-04-28','','2025-04-28 09:14:02','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(28,27,2,3,5,84.00,'2025-04-28','','2025-04-28 09:17:27','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(29,28,1,6,1,374.00,'2025-04-28','','2025-04-28 09:23:54','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(30,28,1,1,1,489.00,'2025-04-28','','2025-04-28 09:24:05','2025-05-05 12:13:22',3,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(31,29,1,3,5,6.00,'2025-04-28','','2025-04-28 09:25:11','2025-05-05 12:13:22',4,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(32,29,1,4,4,75.00,'2025-04-28','','2025-04-28 09:25:25','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(33,29,1,1,1,1160.00,'2025-04-28','','2025-04-28 09:25:36','2025-05-05 12:13:22',3,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(34,30,1,5,7,471.00,'2025-04-28','','2025-04-28 09:27:17','2025-05-05 12:13:22',1,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(35,30,1,1,1,1113.00,'2025-04-28','','2025-04-28 09:27:59','2025-05-05 12:13:22',3,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(38,33,2,4,4,200.00,'2025-04-29','','2025-04-29 11:59:59','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(39,32,2,4,4,3000.00,'2025-04-30','','2025-04-30 11:16:34','2025-05-05 12:13:22',3,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(42,34,1,5,1,743.00,'2025-04-30','','2025-04-30 11:43:40','2025-05-05 12:13:22',3,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(44,34,1,5,4,173.00,'2025-04-30','','2025-04-30 11:45:10','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(48,34,1,3,5,12.00,'2025-04-30','','2025-04-30 11:54:53','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(49,34,1,5,1,87.00,'2025-04-30','','2025-04-30 11:55:05','2025-05-05 12:13:22',3,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(50,35,1,6,1,151.00,'2025-04-30','','2025-04-30 11:56:48','2025-05-05 12:13:22',3,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(51,36,1,1,1,1184.00,'2025-04-30','','2025-04-30 12:00:31','2025-05-05 12:13:22',3,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(54,36,1,3,5,220.00,'2025-04-30','','2025-04-30 12:03:02','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(55,37,2,4,4,1417.00,'2025-04-30','','2025-04-30 22:38:06','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(56,31,2,4,4,790.00,'2025-04-30','','2025-04-30 22:39:08','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(57,39,2,4,4,987.00,'2025-04-30','','2025-05-01 00:25:46','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(58,7,2,4,4,1472.00,'2025-04-30','','2025-05-01 00:29:23','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(59,40,2,3,5,100.00,'2025-04-30','','2025-05-01 00:39:13','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(60,42,1,6,6,89.00,'2025-04-30','','2025-05-01 08:57:32','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(61,42,1,6,1,85.00,'2025-04-30','','2025-05-01 08:57:51','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(62,43,1,5,1,742.20,'2025-05-01','','2025-05-01 09:07:46','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(63,43,1,5,1,301.00,'2025-05-01','','2025-05-01 09:08:17','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(64,43,1,5,7,338.00,'2025-05-01','','2025-05-01 09:08:34','2025-05-05 12:13:22',1,1.50,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(65,44,1,1,1,1810.00,'2025-05-01','','2025-05-01 09:12:18','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(66,44,1,4,4,942.00,'2025-05-01','','2025-05-01 09:12:30','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(67,44,1,3,5,411.00,'2025-05-01','','2025-05-01 09:13:37','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(68,45,2,3,5,353.00,'2025-05-01','','2025-05-01 09:15:41','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(69,45,2,4,4,185.00,'2025-05-01','','2025-05-01 09:15:52','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(70,46,1,5,4,490.00,'2025-05-01','','2025-05-01 12:07:56','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(71,46,1,5,7,139.00,'2025-05-01','','2025-05-01 12:09:09','2025-05-05 12:13:22',1,1.50,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(72,46,1,5,1,296.00,'2025-05-01','','2025-05-01 12:09:25','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(73,47,1,4,4,9.00,'2025-05-01','','2025-05-01 12:10:23','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(74,47,1,3,5,1208.00,'2025-05-01','','2025-05-01 12:10:50','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(75,48,1,6,7,125.00,'2025-05-01','','2025-05-01 12:11:45','2025-05-05 12:13:22',1,1.50,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(76,48,1,6,1,36.00,'2025-05-01','','2025-05-01 12:12:21','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(77,49,2,3,5,40.00,'2025-05-01','','2025-05-01 12:13:30','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(78,50,2,3,5,80.00,'2025-05-01','','2025-05-01 12:14:23','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(79,51,2,3,5,140.00,'2025-05-01','','2025-05-01 12:14:58','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(80,62,2,4,4,80.00,'2025-05-02','','2025-05-02 09:04:12','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(81,61,2,4,4,153.00,'2025-05-02','','2025-05-02 09:05:04','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(82,60,2,4,4,80.00,'2025-05-02','','2025-05-02 09:06:24','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(83,59,2,4,4,80.00,'2025-05-02','','2025-05-02 09:22:46','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(84,64,2,3,5,120.00,'2025-05-02','','2025-05-02 09:52:05','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(85,64,2,4,4,480.00,'2025-05-02','','2025-05-02 09:52:12','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(88,65,1,4,4,382.00,'2025-05-02','','2025-05-02 10:15:05','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(89,65,1,1,1,783.00,'2025-05-02','','2025-05-02 10:16:42','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(90,66,1,6,1,208.00,'2025-05-02','','2025-05-02 10:18:01','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(91,66,1,6,7,181.00,'2025-05-02','','2025-05-02 10:18:24','2025-05-05 12:13:22',1,1.50,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(92,66,1,3,5,61.00,'2025-05-02','','2025-05-02 10:18:34','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(94,67,1,5,4,406.00,'2025-05-02','','2025-05-02 10:19:56','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(95,67,1,5,7,196.00,'2025-05-02','','2025-05-02 10:20:28','2025-05-05 12:13:22',1,1.50,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(96,67,1,5,1,808.00,'2025-05-02','','2025-05-02 10:20:56','2025-05-05 12:13:22',1,1.70,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(97,67,1,3,5,131.00,'2025-05-02','','2025-05-02 10:21:06','2025-05-05 12:13:22',1,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(98,68,2,4,4,164.00,'2025-05-02','','2025-05-03 01:20:36','2025-05-05 12:13:22',1,1.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(100,70,2,3,5,100.00,'2025-05-02','','2025-05-03 01:52:13','2025-05-05 12:13:22',4,0.00,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(101,73,0,1,0,130.00,'2025-05-14','','2025-05-14 23:59:46','2025-05-14 23:59:46',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(102,74,0,3,5,225.00,'2025-05-15','teste','2025-05-15 11:56:19','2025-05-15 11:56:19',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(103,76,0,1,1,10.00,'2025-05-15','','2025-05-15 12:23:51','2025-05-15 12:23:51',0,NULL,1,NULL,NULL,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(104,77,0,1,1,40.00,'2025-05-31','','2025-05-31 09:38:20','2025-05-31 09:38:20',0,NULL,1,19,1,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(105,80,0,3,5,10.00,'2025-05-31','','2025-05-31 10:46:08','2025-05-31 10:46:08',0,NULL,1,19,1,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(106,82,2,3,5,227.00,'2025-06-01',NULL,'2025-06-01 21:53:58','2025-06-01 23:03:31',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(107,83,2,4,4,1787.00,'2025-06-01',NULL,'2025-06-01 21:52:17','2025-06-01 23:03:46',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(108,84,2,4,4,100.00,'2025-06-01',NULL,'2025-06-01 21:50:18','2025-06-01 23:04:01',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(109,85,2,3,5,100.00,'2025-06-01',NULL,'2025-06-01 21:49:46','2025-06-01 23:04:06',4,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(110,86,2,3,5,100.00,'2025-06-01',NULL,'2025-06-01 21:48:37','2025-06-01 23:06:18',4,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(111,86,2,4,4,50.00,'2025-06-01',NULL,'2025-06-01 21:48:28','2025-06-01 23:06:21',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(112,87,2,4,4,100.00,'2025-06-01',NULL,'2025-06-01 21:47:21','2025-06-01 23:06:02',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(113,88,1,4,4,500.00,'2025-06-01','','2025-06-01 23:29:37','2025-06-01 23:29:37',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(114,89,1,4,4,396.00,'2025-06-01','','2025-06-01 23:37:21','2025-06-01 23:37:21',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(115,90,1,4,4,5074.00,'2025-06-01','','2025-06-01 23:40:03','2025-06-01 23:40:03',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(116,91,1,4,4,857.00,'2025-06-01','','2025-06-01 23:49:30','2025-06-01 23:49:30',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(117,92,2,4,4,100.00,'2025-06-01','','2025-06-02 06:42:32','2025-06-02 06:42:32',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(118,93,2,4,4,200.00,'2025-06-01','','2025-06-02 08:21:24','2025-06-02 08:21:24',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(120,94,2,3,5,200.00,'2025-06-01','','2025-06-02 08:22:42','2025-06-02 08:22:42',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(121,95,2,4,4,100.00,'2025-06-01','','2025-06-02 08:23:18','2025-06-02 08:23:18',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(122,96,2,4,4,1687.86,'2025-06-01','','2025-06-02 08:32:48','2025-06-02 08:32:48',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(124,98,1,5,4,2491.00,'2025-06-01','','2025-06-02 08:40:19','2025-06-02 08:40:19',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(126,100,1,6,6,64.00,'2025-06-02','','2025-06-02 09:07:28','2025-06-02 09:07:28',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(127,99,1,4,4,3408.00,'2025-06-02','','2025-06-02 09:08:17','2025-06-02 09:08:17',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(128,101,1,4,4,1580.00,'2025-06-02','','2025-06-03 08:45:52','2025-06-03 08:45:52',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(129,102,1,5,7,824.00,'2025-06-02','','2025-06-03 08:54:55','2025-06-03 08:54:55',1,1.50,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(130,103,1,6,6,242.00,'2025-06-02','','2025-06-03 08:57:27','2025-06-03 08:57:27',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(131,104,2,4,4,898.48,'2025-06-03','','2025-06-03 09:19:50','2025-06-03 09:19:50',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(132,105,2,4,4,1645.00,'2025-06-03','','2025-06-03 09:22:00','2025-06-03 09:22:00',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(133,106,2,4,4,678.00,'2025-06-03','','2025-06-03 09:23:32','2025-06-03 09:23:32',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(134,107,2,4,4,1500.00,'2025-06-03','','2025-06-03 09:25:19','2025-06-03 09:25:19',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(135,108,2,4,4,628.00,'2025-06-03','','2025-06-03 09:28:59','2025-06-03 09:28:59',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(136,112,2,4,4,180.00,'2025-06-03','','2025-06-03 10:02:22','2025-06-03 10:02:22',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(137,113,1,4,4,436.00,'2025-06-03','','2025-06-03 10:14:38','2025-06-03 10:14:38',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(138,114,2,4,4,200.00,'2025-06-03','','2025-06-03 10:15:15','2025-06-03 10:15:15',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(139,115,2,4,4,60.00,'2025-06-03','','2025-06-03 10:16:00','2025-06-03 10:16:00',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(140,116,2,4,4,559.00,'2025-06-03','','2025-06-03 10:21:03','2025-06-03 10:21:03',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(141,117,2,4,4,42.00,'2025-06-03','','2025-06-03 10:22:41','2025-06-03 10:22:41',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(142,118,2,4,4,54.00,'2025-06-03','','2025-06-03 10:23:14','2025-06-03 10:23:14',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(143,119,2,4,4,557.00,'2025-06-03','','2025-06-03 10:25:24','2025-06-03 10:25:24',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(144,120,2,4,4,121.00,'2025-06-03','','2025-06-03 10:26:00','2025-06-03 10:26:00',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(145,121,2,4,4,200.00,'2025-06-03','','2025-06-03 10:26:42','2025-06-03 10:26:42',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(146,122,2,4,4,80.00,'2025-06-03','','2025-06-03 10:27:54','2025-06-03 10:27:54',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(147,123,2,4,4,350.00,'2025-06-03','','2025-06-03 10:28:25','2025-06-03 10:28:25',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(149,126,2,4,4,937.00,'2025-06-03','','2025-06-03 10:45:38','2025-06-03 10:45:38',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(150,124,2,4,4,1000.00,'2025-06-03','','2025-06-03 10:51:00','2025-06-03 10:51:00',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(151,130,2,4,4,80.00,'2025-06-03','','2025-06-03 11:19:26','2025-06-03 11:19:26',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(152,131,2,3,5,97.00,'2025-06-03','','2025-06-03 11:20:34','2025-06-03 11:20:34',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(153,132,2,3,5,20.00,'2025-06-03','','2025-06-03 11:21:16','2025-06-03 11:21:16',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(154,133,2,4,4,1430.00,'2025-06-03','','2025-06-03 22:12:11','2025-06-03 22:12:11',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(155,134,1,4,4,853.00,'2025-06-03','','2025-06-03 22:12:43','2025-06-03 22:12:43',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(156,135,1,6,1,72.00,'2025-06-04','','2025-06-04 19:07:45','2025-06-04 19:07:45',1,1.70,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(157,136,1,3,5,110.00,'2025-06-04','','2025-06-04 19:10:17','2025-06-04 19:10:17',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(158,136,1,4,4,155.00,'2025-06-04','','2025-06-04 19:10:34','2025-06-04 19:10:34',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(159,136,1,5,1,210.00,'2025-06-04','','2025-06-04 19:11:07','2025-06-04 19:11:07',1,1.70,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(160,136,1,5,4,50.00,'2025-06-04','','2025-06-04 19:11:41','2025-06-04 19:11:41',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(161,136,1,7,10,97.00,'2025-06-04','','2025-06-04 19:33:05','2025-06-04 19:33:05',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(162,137,1,4,4,891.00,'2025-06-04','','2025-06-04 19:37:00','2025-06-04 19:37:00',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(163,138,2,4,4,350.00,'2025-06-04','','2025-06-04 23:32:50','2025-06-04 23:32:50',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(164,139,2,4,4,30.00,'2025-06-04','','2025-06-04 23:47:15','2025-06-04 23:47:15',3,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(165,140,2,4,4,524.00,'2025-06-04','','2025-06-04 23:49:36','2025-06-04 23:49:36',3,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(166,141,2,4,4,2095.00,'2025-06-04','','2025-06-04 23:56:10','2025-06-04 23:56:10',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(167,142,2,4,4,200.00,'2025-06-04','','2025-06-04 23:57:38','2025-06-04 23:57:38',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(168,143,2,4,4,600.00,'2025-06-04','','2025-06-04 23:58:14','2025-06-04 23:58:14',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(169,144,2,4,4,490.67,'2025-06-04','','2025-06-04 23:59:32','2025-06-04 23:59:32',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(170,145,2,4,4,101.00,'2025-06-04','','2025-06-05 00:00:20','2025-06-05 00:00:20',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(171,146,2,4,4,70.00,'2025-06-04','','2025-06-05 00:01:56','2025-06-05 00:01:56',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(172,147,2,4,4,90.00,'2025-06-04','','2025-06-05 00:02:41','2025-06-05 00:02:41',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(173,148,2,4,4,101.00,'2025-06-05','','2025-06-05 09:20:59','2025-06-05 09:20:59',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(174,149,2,4,4,10.00,'2025-06-05','','2025-06-05 09:21:54','2025-06-05 09:21:54',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(176,151,1,4,4,1258.00,'2025-06-05','','2025-06-05 09:30:13','2025-06-05 09:30:13',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(177,152,1,4,4,113.00,'2025-06-05','','2025-06-05 09:38:19','2025-06-05 09:38:19',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(178,153,2,4,4,369.97,'2025-06-05','','2025-06-05 10:06:29','2025-06-05 10:06:29',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(179,154,2,4,4,60.00,'2025-06-05','','2025-06-05 10:06:52','2025-06-05 10:06:52',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(180,155,2,4,4,45.60,'2025-06-05','','2025-06-05 10:09:02','2025-06-05 10:09:02',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(181,156,1,4,4,2500.00,'2025-06-05','','2025-06-05 10:20:27','2025-06-05 10:20:27',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(183,158,0,1,4,60.00,'2025-06-05','','2025-06-05 20:13:21','2025-06-05 20:13:21',0,NULL,1,20,5,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(184,157,1,4,4,1000.00,'2025-06-05','','2025-06-05 20:15:32','2025-06-05 20:15:32',1,1.00,1,NULL,5,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(185,159,2,4,4,3309.00,'2025-06-05','','2025-06-05 21:27:46','2025-06-05 21:27:46',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(186,161,2,4,4,600.00,'2025-06-05','','2025-06-05 21:51:51','2025-06-05 21:51:51',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(187,150,1,4,4,1649.00,'2025-06-05','','2025-06-05 22:59:58','2025-06-05 22:59:58',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(188,162,2,4,4,115.75,'2025-06-06','','2025-06-06 09:42:48','2025-06-06 09:42:48',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(189,163,2,4,4,700.00,'2025-06-06','','2025-06-06 09:44:55','2025-06-06 09:44:55',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(190,164,1,4,4,924.00,'2025-06-06','','2025-06-06 09:46:47','2025-06-06 09:46:47',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(191,165,1,5,4,1193.00,'2025-06-06','','2025-06-06 09:53:24','2025-06-06 09:53:24',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(194,166,1,6,1,474.00,'2025-06-06','','2025-06-06 09:55:12','2025-06-06 09:55:12',1,1.70,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(195,167,1,4,4,4012.00,'2025-06-07','','2025-06-07 09:55:06','2025-06-07 09:55:06',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(196,168,1,5,4,1645.00,'2025-06-07','','2025-06-07 09:59:39','2025-06-07 09:59:39',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(197,169,2,4,4,200.00,'2025-06-07','','2025-06-07 10:06:42','2025-06-07 10:06:42',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(198,170,2,3,5,150.00,'2025-06-07','','2025-06-07 10:07:21','2025-06-07 10:07:21',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(199,171,2,3,5,200.00,'2025-06-07','','2025-06-07 10:16:59','2025-06-07 10:16:59',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(200,172,2,3,5,127.00,'2025-06-07','','2025-06-07 10:17:34','2025-06-07 10:17:34',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(202,173,2,3,5,666.00,'2025-06-07','','2025-06-07 10:25:16','2025-06-07 10:25:16',1,0.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(203,173,2,4,4,881.00,'2025-06-07','','2025-06-07 10:25:25','2025-06-07 10:25:25',1,1.00,1,NULL,3,NULL,NULL,'pendente','pendente','2025-07-23 09:10:32'),(204,174,2,3,5,1267.00,'2025-06-07','','2025-06-08 00:48:36','2025-06-08 00:48:36',4,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(205,175,2,4,4,402.00,'2025-06-07','','2025-06-08 01:56:08','2025-06-08 01:56:08',1,1.00,1,NULL,3,4.02,NULL,'pendente','pendente','2025-07-23 09:10:32'),(206,176,2,4,4,58.00,'2025-06-07','','2025-06-08 02:16:18','2025-06-08 02:16:18',1,1.00,1,NULL,3,0.58,NULL,'pendente','pendente','2025-07-23 09:10:32'),(207,177,2,4,4,55.00,'2025-06-07','','2025-06-08 02:36:20','2025-06-08 02:36:20',1,1.00,1,NULL,3,0.55,NULL,'pendente','pendente','2025-07-23 09:10:32'),(208,178,2,3,5,145.00,'2025-06-07','','2025-06-08 02:38:06','2025-06-08 02:38:06',4,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(209,179,2,3,5,800.00,'2025-06-08','','2025-06-08 20:43:13','2025-06-08 20:43:13',4,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(210,179,2,4,4,103.00,'2025-06-08','','2025-06-08 20:43:29','2025-06-08 20:43:29',1,1.00,1,NULL,3,1.03,NULL,'pendente','pendente','2025-07-23 09:10:32'),(211,181,1,4,4,287.00,'2025-06-09','','2025-06-09 23:10:19','2025-06-09 23:10:19',1,1.00,1,NULL,3,2.87,NULL,'pendente','pendente','2025-07-23 09:10:32'),(212,182,2,4,4,2575.15,'2025-06-09','','2025-06-09 23:23:15','2025-06-09 23:23:15',3,1.00,1,NULL,3,25.75,NULL,'pendente','pendente','2025-07-23 09:10:32'),(213,183,1,4,4,5548.00,'2025-06-09','','2025-06-09 23:24:06','2025-06-09 23:24:06',1,1.00,1,NULL,3,55.48,NULL,'pendente','pendente','2025-07-23 09:10:32'),(214,184,2,4,4,1000.00,'2025-06-09','','2025-06-09 23:45:38','2025-06-09 23:45:38',1,1.00,1,NULL,3,10.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(215,185,1,4,4,2628.00,'2025-06-09','','2025-06-10 01:06:07','2025-06-10 01:06:07',1,1.00,1,NULL,3,26.28,NULL,'pendente','pendente','2025-07-23 09:10:32'),(216,186,2,4,4,5000.00,'2025-06-09','','2025-06-10 01:08:59','2025-06-10 01:08:59',1,1.00,1,NULL,3,50.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(217,187,1,4,4,763.00,'2025-06-10','','2025-06-10 09:08:33','2025-06-10 09:08:33',1,1.00,1,NULL,3,7.63,NULL,'pendente','pendente','2025-07-23 09:10:32'),(219,189,1,5,4,1130.00,'2025-06-10','','2025-06-10 09:16:52','2025-06-10 09:16:52',1,1.00,1,NULL,3,11.30,NULL,'pendente','pendente','2025-07-23 09:10:32'),(220,188,1,6,1,33.00,'2025-06-10','','2025-06-10 09:17:12','2025-06-10 09:17:12',1,1.70,1,NULL,3,0.56,NULL,'pendente','pendente','2025-07-23 09:10:32'),(221,190,2,3,5,132.00,'2025-06-10','','2025-06-10 09:19:16','2025-06-10 09:19:16',1,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(223,192,2,4,4,200.00,'2025-06-10','','2025-06-10 09:21:14','2025-06-10 09:21:14',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(224,193,2,4,4,157.62,'2025-06-10','','2025-06-10 09:22:18','2025-06-10 09:22:18',1,1.00,1,NULL,3,1.58,NULL,'pendente','pendente','2025-07-23 09:10:32'),(225,194,2,4,4,100.00,'2025-06-10','','2025-06-10 09:24:18','2025-06-10 09:24:18',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(226,191,2,4,4,333.00,'2025-06-10','','2025-06-10 09:26:47','2025-06-10 09:26:47',1,1.00,1,NULL,3,3.33,NULL,'pendente','pendente','2025-07-23 09:10:32'),(227,195,2,4,4,751.76,'2025-06-10','','2025-06-10 09:33:14','2025-06-10 09:33:14',1,1.00,1,NULL,3,7.52,NULL,'pendente','pendente','2025-07-23 09:10:32'),(228,196,2,4,4,100.00,'2025-06-10','','2025-06-10 09:33:50','2025-06-10 09:33:50',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(229,197,2,4,4,178.02,'2025-06-10','','2025-06-10 09:36:17','2025-06-10 09:36:17',1,1.00,1,NULL,3,1.78,NULL,'pendente','pendente','2025-07-23 09:10:32'),(230,198,2,4,4,150.00,'2025-06-10','','2025-06-10 09:38:30','2025-06-10 09:38:30',1,1.00,1,NULL,3,1.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(231,199,2,4,4,1598.37,'2025-06-10','','2025-06-10 09:39:59','2025-06-10 09:39:59',1,1.00,1,NULL,3,15.98,NULL,'pendente','pendente','2025-07-23 09:10:32'),(232,200,2,4,4,265.00,'2025-06-10','','2025-06-10 09:41:31','2025-06-10 09:41:31',1,1.00,1,NULL,3,2.65,NULL,'pendente','pendente','2025-07-23 09:10:32'),(233,201,2,4,4,650.00,'2025-06-10','','2025-06-10 09:47:27','2025-06-10 09:47:27',1,1.00,1,NULL,3,6.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(234,202,2,4,4,400.00,'2025-06-10','','2025-06-10 09:48:35','2025-06-10 09:48:35',1,1.00,1,NULL,3,4.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(235,203,2,4,4,1518.00,'2025-06-10','','2025-06-10 18:56:33','2025-06-10 18:56:33',5,1.00,1,NULL,3,15.18,NULL,'pendente','pendente','2025-07-23 09:10:32'),(237,205,1,5,4,934.00,'2025-06-11','','2025-06-11 09:35:04','2025-06-11 09:35:04',1,1.00,1,NULL,1,9.34,NULL,'pendente','pendente','2025-07-23 09:10:32'),(238,206,1,4,4,1251.00,'2025-06-11','','2025-06-11 09:40:32','2025-06-11 09:40:32',1,1.00,1,NULL,1,12.51,NULL,'pendente','pendente','2025-07-23 09:10:32'),(239,207,2,4,4,100.00,'2025-06-11','','2025-06-11 09:42:27','2025-06-11 09:42:27',1,1.00,1,NULL,1,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(240,208,2,4,4,30.00,'2025-06-11','','2025-06-11 09:43:35','2025-06-11 09:43:35',1,1.00,1,NULL,1,0.30,NULL,'pendente','pendente','2025-07-23 09:10:32'),(241,209,2,4,4,500.00,'2025-06-11','','2025-06-11 09:44:21','2025-06-11 09:44:21',1,1.00,1,NULL,1,5.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(242,210,2,4,4,511.37,'2025-06-11','','2025-06-11 09:51:46','2025-06-11 09:51:46',1,1.00,1,NULL,1,5.11,NULL,'pendente','pendente','2025-07-23 09:10:32'),(243,211,2,4,4,1000.00,'2025-06-11','','2025-06-11 09:52:52','2025-06-11 09:52:52',1,1.00,1,NULL,1,10.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(244,212,2,4,4,100.00,'2025-06-11','','2025-06-11 09:53:22','2025-06-11 09:53:22',1,1.00,1,NULL,1,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(245,213,2,4,4,1929.67,'2025-06-11','','2025-06-11 23:09:07','2025-06-11 23:09:07',1,1.00,1,NULL,3,19.30,NULL,'pendente','pendente','2025-07-23 09:10:32'),(246,214,2,4,4,427.60,'2025-06-12','','2025-06-12 09:18:39','2025-06-12 09:18:39',1,1.00,1,NULL,3,4.28,NULL,'pendente','pendente','2025-07-23 09:10:32'),(248,215,2,4,4,80.00,'2025-06-12','','2025-06-12 09:23:57','2025-06-12 09:23:57',5,1.00,1,NULL,3,0.80,NULL,'pendente','pendente','2025-07-23 09:10:32'),(249,216,2,4,4,470.00,'2025-06-12','','2025-06-12 09:28:28','2025-06-12 09:28:28',1,1.00,1,NULL,3,4.70,NULL,'pendente','pendente','2025-07-23 09:10:32'),(250,217,2,3,5,500.00,'2025-06-12','','2025-06-12 09:29:24','2025-06-12 09:29:24',1,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(252,218,1,4,4,1734.00,'2025-06-12','','2025-06-12 09:35:01','2025-06-12 09:35:01',1,1.00,1,NULL,3,17.34,NULL,'pendente','pendente','2025-07-23 09:10:32'),(253,219,1,5,4,320.00,'2025-06-12','','2025-06-12 09:37:42','2025-06-12 09:37:42',1,1.00,1,NULL,3,3.20,NULL,'pendente','pendente','2025-07-23 09:10:32'),(255,220,1,5,4,391.00,'2025-06-12','','2025-06-12 09:42:58','2025-06-12 09:42:58',1,1.00,1,NULL,3,3.91,NULL,'pendente','pendente','2025-07-23 09:10:32'),(256,221,2,4,4,66.00,'2025-06-12','','2025-06-12 09:48:19','2025-06-12 09:48:19',1,1.00,1,NULL,3,0.66,NULL,'pendente','pendente','2025-07-23 09:10:32'),(257,222,2,4,4,399.40,'2025-06-12','','2025-06-12 22:41:53','2025-06-12 22:41:53',5,1.00,1,NULL,3,3.99,NULL,'pendente','pendente','2025-07-23 09:10:32'),(258,223,1,4,4,3481.00,'2025-06-13','','2025-06-13 09:31:58','2025-06-13 09:31:58',1,1.00,1,NULL,3,34.81,NULL,'pendente','pendente','2025-07-23 09:10:32'),(259,224,1,5,4,1052.00,'2025-06-13','','2025-06-13 09:34:03','2025-06-13 09:34:03',1,1.00,1,NULL,3,10.52,NULL,'pendente','pendente','2025-07-23 09:10:32'),(261,225,1,6,1,380.00,'2025-06-13','','2025-06-13 09:35:37','2025-06-13 09:35:37',1,1.70,1,NULL,3,6.46,NULL,'pendente','pendente','2025-07-23 09:10:32'),(262,226,2,4,4,137.60,'2025-06-13','','2025-06-13 09:43:15','2025-06-13 09:43:15',1,1.00,1,NULL,3,1.38,NULL,'pendente','pendente','2025-07-23 09:10:32'),(263,227,2,4,4,200.00,'2025-06-13','','2025-06-13 09:44:21','2025-06-13 09:44:21',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(264,227,2,3,5,30.00,'2025-06-13','','2025-06-13 09:46:21','2025-06-13 09:46:21',4,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(265,228,2,4,4,266.00,'2025-06-13','','2025-06-13 09:48:41','2025-06-13 09:48:41',1,1.00,1,NULL,3,2.66,NULL,'pendente','pendente','2025-07-23 09:10:32'),(266,228,2,4,4,133.00,'2025-06-13','','2025-06-13 09:50:03','2025-06-13 09:50:03',1,1.00,1,NULL,3,1.33,NULL,'pendente','pendente','2025-07-23 09:10:32'),(267,229,2,4,4,1080.85,'2025-06-13','','2025-06-13 09:51:31','2025-06-13 09:51:31',5,1.00,1,NULL,3,10.81,NULL,'pendente','pendente','2025-07-23 09:10:32'),(268,230,2,4,4,80.00,'2025-06-13','','2025-06-14 08:24:13','2025-06-14 08:24:13',1,1.00,1,NULL,3,0.80,NULL,'pendente','pendente','2025-07-23 09:10:32'),(269,231,2,4,4,160.00,'2025-06-13','','2025-06-14 08:25:06','2025-06-14 08:25:06',1,1.00,1,NULL,3,1.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(270,232,2,4,4,20.00,'2025-06-13','','2025-06-14 08:29:30','2025-06-14 08:29:30',1,1.00,1,NULL,3,0.20,NULL,'pendente','pendente','2025-07-23 09:10:32'),(271,233,2,4,4,60.00,'2025-06-13','','2025-06-14 08:41:46','2025-06-14 08:41:46',1,1.00,1,NULL,3,0.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(272,234,1,4,4,1215.00,'2025-06-14','','2025-06-14 09:13:05','2025-06-14 09:13:05',1,1.00,1,NULL,3,12.15,NULL,'pendente','pendente','2025-07-23 09:10:32'),(273,235,1,5,4,1361.00,'2025-06-14','','2025-06-14 09:14:46','2025-06-14 09:14:46',1,1.00,1,NULL,3,13.61,NULL,'pendente','pendente','2025-07-23 09:10:32'),(274,236,1,6,1,209.00,'2025-06-14','','2025-06-14 09:18:31','2025-06-14 09:18:31',1,1.70,1,NULL,3,3.55,NULL,'pendente','pendente','2025-07-23 09:10:32'),(275,237,2,4,4,10.00,'2025-06-15','','2025-06-15 09:02:06','2025-06-15 09:02:06',1,1.00,1,NULL,3,0.10,NULL,'pendente','pendente','2025-07-23 09:10:32'),(277,237,2,4,4,30.00,'2025-06-15','','2025-06-15 09:02:49','2025-06-15 09:02:49',1,1.00,1,NULL,3,0.30,NULL,'pendente','pendente','2025-07-23 09:10:32'),(278,237,2,4,4,60.00,'2025-06-15','','2025-06-15 09:03:19','2025-06-15 09:03:19',1,1.00,1,NULL,3,0.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(279,238,2,4,4,101.00,'2025-06-15','','2025-06-15 09:05:51','2025-06-15 09:05:51',1,1.00,1,NULL,3,1.01,NULL,'pendente','pendente','2025-07-23 09:10:32'),(280,239,1,4,4,2825.00,'2025-06-15','','2025-06-15 11:58:27','2025-06-15 11:58:27',1,1.00,1,NULL,3,28.25,NULL,'pendente','pendente','2025-07-23 09:10:32'),(281,240,1,6,1,349.00,'2025-06-15','','2025-06-15 11:59:18','2025-06-15 11:59:18',1,1.70,1,NULL,3,5.93,NULL,'pendente','pendente','2025-07-23 09:10:32'),(282,241,1,5,4,1399.00,'2025-06-15','','2025-06-15 12:00:20','2025-06-15 12:00:20',1,1.00,1,NULL,3,13.99,NULL,'pendente','pendente','2025-07-23 09:10:32'),(283,242,2,4,4,2422.78,'2025-06-15','','2025-06-16 01:16:05','2025-06-16 01:16:05',1,1.00,1,NULL,3,24.23,NULL,'pendente','pendente','2025-07-23 09:10:32'),(284,243,2,4,4,80.00,'2025-06-15','','2025-06-16 01:17:20','2025-06-16 01:17:20',1,1.00,1,NULL,3,0.80,NULL,'pendente','pendente','2025-07-23 09:10:32'),(285,244,2,4,4,200.00,'2025-06-15','','2025-06-16 01:17:51','2025-06-16 01:17:51',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(286,245,2,4,4,468.31,'2025-06-15','','2025-06-16 01:37:18','2025-06-16 01:37:18',1,1.00,1,NULL,3,4.68,NULL,'pendente','pendente','2025-07-23 09:10:32'),(287,246,2,4,4,100.00,'2025-06-15','','2025-06-16 01:37:50','2025-06-16 01:37:50',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(288,247,2,4,4,200.00,'2025-06-15','','2025-06-16 01:39:37','2025-06-16 01:39:37',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(289,248,2,4,4,369.00,'2025-06-15','','2025-06-16 01:42:02','2025-06-16 01:42:02',1,1.00,1,NULL,3,3.69,NULL,'pendente','pendente','2025-07-23 09:10:32'),(290,249,2,4,4,89.00,'2025-06-15','','2025-06-16 01:45:02','2025-06-16 01:45:02',1,1.00,1,NULL,3,0.89,NULL,'pendente','pendente','2025-07-23 09:10:32'),(291,250,2,4,4,100.00,'2025-06-15','','2025-06-16 01:59:21','2025-06-16 01:59:21',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(292,251,2,4,4,20.00,'2025-06-15','','2025-06-16 02:00:33','2025-06-16 02:00:33',1,1.00,1,NULL,3,0.20,NULL,'pendente','pendente','2025-07-23 09:10:32'),(293,252,2,4,4,180.00,'2025-06-15','','2025-06-16 02:01:04','2025-06-16 02:01:04',1,1.00,1,NULL,3,1.80,NULL,'pendente','pendente','2025-07-23 09:10:32'),(294,253,2,4,4,60.00,'2025-06-15','','2025-06-16 02:01:36','2025-06-16 02:01:36',1,1.00,1,NULL,3,0.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(295,254,2,3,5,1150.00,'2025-06-15','','2025-06-16 02:12:22','2025-06-16 02:12:22',1,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(296,255,1,4,4,3526.00,'2025-06-16','','2025-06-16 09:05:09','2025-06-16 09:05:09',1,1.00,1,NULL,3,35.26,NULL,'pendente','pendente','2025-07-23 09:10:32'),(298,257,2,4,4,60.00,'2025-06-16','','2025-06-16 09:08:40','2025-06-16 09:08:40',1,1.00,1,NULL,3,0.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(299,258,1,5,4,1608.00,'2025-06-16','','2025-06-16 09:09:59','2025-06-16 09:09:59',1,1.00,1,NULL,3,16.08,NULL,'pendente','pendente','2025-07-23 09:10:32'),(300,256,1,6,1,113.00,'2025-06-16','','2025-06-16 09:10:33','2025-06-16 09:10:33',1,1.70,1,NULL,3,1.92,NULL,'pendente','pendente','2025-07-23 09:10:32'),(301,259,2,4,4,87.75,'2025-06-16','','2025-06-16 09:17:39','2025-06-16 09:17:39',1,1.00,1,NULL,3,0.88,NULL,'pendente','pendente','2025-07-23 09:10:32'),(302,295,1,4,4,3364.00,'2025-06-17','','2025-06-17 09:17:16','2025-06-17 09:17:16',1,1.00,1,NULL,3,33.64,NULL,'pendente','pendente','2025-07-23 09:10:32'),(304,296,1,6,1,285.00,'2025-06-17','','2025-06-17 09:18:20','2025-06-17 09:18:20',1,1.70,1,NULL,3,4.85,NULL,'pendente','pendente','2025-07-23 09:10:32'),(305,297,1,4,4,3906.00,'2025-06-17','','2025-06-17 09:22:13','2025-06-17 09:22:13',1,1.00,1,NULL,3,39.06,NULL,'pendente','pendente','2025-07-23 09:10:32'),(307,299,2,4,4,50.00,'2025-06-17','','2025-06-17 11:09:32','2025-06-17 11:09:32',1,1.00,1,NULL,3,0.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(308,300,2,4,4,700.00,'2025-06-17','','2025-06-17 22:45:33','2025-06-17 22:45:33',1,1.00,1,NULL,3,7.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(309,301,2,4,4,170.00,'2025-06-17','\r\n','2025-06-17 23:44:29','2025-06-17 23:44:29',1,1.00,1,NULL,3,1.70,NULL,'pendente','pendente','2025-07-23 09:10:32'),(310,302,2,4,4,361.20,'2025-06-17','','2025-06-17 23:51:14','2025-06-17 23:51:14',1,1.00,1,NULL,3,3.61,NULL,'pendente','pendente','2025-07-23 09:10:32'),(311,303,2,4,4,1002.13,'2025-06-17','','2025-06-17 23:53:48','2025-06-17 23:53:48',1,1.00,1,NULL,3,10.02,NULL,'pendente','pendente','2025-07-23 09:10:32'),(312,304,2,4,4,50.00,'2025-06-17','','2025-06-18 00:10:24','2025-06-18 00:10:24',1,1.00,1,NULL,3,0.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(313,305,2,4,4,76.00,'2025-06-17','','2025-06-18 00:10:53','2025-06-18 00:10:53',1,1.00,1,NULL,3,0.76,NULL,'pendente','pendente','2025-07-23 09:10:32'),(314,306,2,4,4,843.00,'2025-06-17','','2025-06-18 00:12:12','2025-06-18 00:12:12',1,1.00,1,NULL,3,8.43,NULL,'pendente','pendente','2025-07-23 09:10:32'),(315,307,2,4,4,440.00,'2025-06-17','','2025-06-18 00:15:01','2025-06-18 00:15:01',1,1.00,1,NULL,3,4.40,NULL,'pendente','pendente','2025-07-23 09:10:32'),(316,308,2,4,4,2500.00,'2025-06-17','','2025-06-18 00:18:47','2025-06-18 00:18:47',1,1.00,1,NULL,3,25.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(317,309,2,4,4,1200.00,'2025-06-17','','2025-06-18 00:19:50','2025-06-18 00:19:50',1,1.00,1,NULL,3,12.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(318,310,2,4,4,1100.00,'2025-06-17','','2025-06-18 00:22:27','2025-06-18 00:22:27',1,1.00,1,NULL,3,11.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(319,311,2,4,4,291.00,'2025-06-17','','2025-06-18 00:25:11','2025-06-18 00:25:11',1,1.00,1,NULL,3,2.91,NULL,'pendente','pendente','2025-07-23 09:10:32'),(320,312,2,4,4,366.72,'2025-06-17','','2025-06-18 00:25:48','2025-06-18 00:25:48',1,1.00,1,NULL,3,3.67,NULL,'pendente','pendente','2025-07-23 09:10:32'),(321,313,2,4,4,3127.00,'2025-06-17','','2025-06-18 01:27:42','2025-06-18 01:27:42',1,1.00,1,NULL,3,31.27,NULL,'pendente','pendente','2025-07-23 09:10:32'),(322,314,1,4,4,1049.00,'2025-06-18','','2025-06-18 09:34:42','2025-06-18 09:34:42',1,1.00,1,NULL,3,10.49,NULL,'pendente','pendente','2025-07-23 09:10:32'),(323,315,1,4,4,1024.00,'2025-06-18','','2025-06-18 09:37:25','2025-06-18 09:37:25',1,1.00,1,NULL,3,10.24,NULL,'pendente','pendente','2025-07-23 09:10:32'),(324,316,1,6,1,110.00,'2025-06-18','','2025-06-18 09:38:44','2025-06-18 09:38:44',1,1.70,1,NULL,3,1.87,NULL,'pendente','pendente','2025-07-23 09:10:32'),(325,317,1,4,4,2688.00,'2025-06-18','','2025-06-18 09:40:46','2025-06-18 09:40:46',1,1.00,1,NULL,3,26.88,NULL,'pendente','pendente','2025-07-23 09:10:32'),(326,318,2,4,4,500.00,'2025-06-18','','2025-06-18 21:39:36','2025-06-18 21:39:36',1,1.00,1,NULL,3,5.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(327,319,2,4,4,100.00,'2025-06-19','','2025-06-19 22:56:51','2025-06-19 22:56:51',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(328,320,2,4,4,1967.67,'2025-06-19','','2025-06-19 23:00:12','2025-06-19 23:00:12',1,1.00,1,NULL,3,19.68,NULL,'pendente','pendente','2025-07-23 09:10:32'),(329,321,2,4,4,450.00,'2025-06-19','','2025-06-19 23:02:15','2025-06-19 23:02:15',1,1.00,1,NULL,3,4.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(330,322,2,4,4,250.00,'2025-06-19','','2025-06-19 23:02:51','2025-06-19 23:02:51',1,1.00,1,NULL,3,2.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(331,323,2,4,4,216.27,'2025-06-19','','2025-06-19 23:03:54','2025-06-19 23:03:54',1,1.00,1,NULL,3,2.16,NULL,'pendente','pendente','2025-07-23 09:10:32'),(332,324,2,4,4,409.50,'2025-06-19','','2025-06-19 23:12:34','2025-06-19 23:12:34',1,1.00,1,NULL,3,4.10,NULL,'pendente','pendente','2025-07-23 09:10:32'),(333,325,2,4,4,43.25,'2025-06-19','','2025-06-19 23:13:17','2025-06-19 23:13:17',1,1.00,1,NULL,3,0.43,NULL,'pendente','pendente','2025-07-23 09:10:32'),(334,326,2,4,4,550.00,'2025-06-19','','2025-06-19 23:13:47','2025-06-19 23:13:47',1,1.00,1,NULL,3,5.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(335,327,1,4,4,1523.00,'2025-06-19','','2025-06-19 23:18:07','2025-06-19 23:18:07',1,1.00,1,NULL,3,15.23,NULL,'pendente','pendente','2025-07-23 09:10:32'),(336,328,1,4,4,212.00,'2025-06-19','','2025-06-19 23:18:54','2025-06-19 23:18:54',1,1.00,1,NULL,3,2.12,NULL,'pendente','pendente','2025-07-23 09:10:32'),(337,329,1,5,4,1098.00,'2025-06-19','','2025-06-19 23:21:47','2025-06-19 23:21:47',1,1.00,1,NULL,3,10.98,NULL,'pendente','pendente','2025-07-23 09:10:32'),(338,330,2,4,4,700.00,'2025-06-19','','2025-06-19 23:23:17','2025-06-19 23:23:17',1,1.00,1,NULL,3,7.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(339,331,2,4,4,200.00,'2025-06-23','','2025-06-23 11:34:01','2025-06-23 11:34:01',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(340,332,2,4,4,150.00,'2025-06-23','','2025-06-23 11:34:32','2025-06-23 11:34:32',1,1.00,1,NULL,3,1.50,NULL,'pendente','pendente','2025-07-23 09:10:32'),(341,333,2,4,4,231.00,'2025-06-23','','2025-06-23 11:37:31','2025-06-23 11:37:31',1,1.00,1,NULL,3,2.31,NULL,'pendente','pendente','2025-07-23 09:10:32'),(342,334,2,4,4,61.00,'2025-06-23','','2025-06-23 11:39:50','2025-06-23 11:39:50',1,1.00,1,NULL,3,0.61,NULL,'pendente','pendente','2025-07-23 09:10:32'),(343,335,2,4,4,1052.37,'2025-06-23','','2025-06-23 11:41:46','2025-06-23 11:41:46',1,1.00,1,NULL,3,10.52,NULL,'pendente','pendente','2025-07-23 09:10:32'),(344,336,2,4,4,300.00,'2025-06-23','','2025-06-23 11:42:26','2025-06-23 11:42:26',1,1.00,1,NULL,3,3.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(345,337,2,4,4,110.00,'2025-06-23','','2025-06-23 11:43:01','2025-06-23 11:43:01',1,1.00,1,NULL,3,1.10,NULL,'pendente','pendente','2025-07-23 09:10:32'),(346,338,2,4,4,100.00,'2025-06-23','','2025-06-23 11:43:42','2025-06-23 11:43:42',1,1.00,1,NULL,3,1.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(347,339,2,4,4,260.00,'2025-06-23','','2025-06-23 11:44:28','2025-06-23 11:44:28',1,1.00,1,NULL,3,2.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(348,340,2,4,4,500.00,'2025-06-23','','2025-06-23 11:47:37','2025-06-23 11:47:37',1,1.00,1,NULL,3,5.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(349,341,2,4,4,1658.49,'2025-06-23','','2025-06-23 11:49:01','2025-06-23 11:49:01',1,1.00,1,NULL,3,16.58,NULL,'pendente','pendente','2025-07-23 09:10:32'),(350,111,2,4,4,1400.00,'2025-06-23','','2025-06-23 11:49:42','2025-06-23 11:49:42',1,1.00,1,NULL,3,14.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(351,342,2,4,4,500.00,'2025-06-23','','2025-06-23 11:50:24','2025-06-23 11:50:24',1,1.00,1,NULL,3,5.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(352,343,2,4,4,200.00,'2025-06-23','','2025-06-23 11:50:59','2025-06-23 11:50:59',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(353,344,2,4,4,1282.11,'2025-06-23','','2025-06-23 11:52:20','2025-06-23 11:52:20',1,1.00,1,NULL,3,12.82,NULL,'pendente','pendente','2025-07-23 09:10:32'),(354,345,1,4,4,8438.00,'2025-06-23','','2025-06-23 11:57:31','2025-06-23 11:57:31',1,1.00,1,NULL,3,84.38,NULL,'pendente','pendente','2025-07-23 09:10:32'),(355,346,1,6,1,2977.00,'2025-06-23','','2025-06-23 11:58:30','2025-06-23 11:58:30',1,1.70,1,NULL,3,50.61,NULL,'pendente','pendente','2025-07-23 09:10:32'),(356,347,1,5,1,6079.00,'2025-06-23','','2025-06-23 11:59:45','2025-06-23 11:59:45',1,1.70,1,NULL,3,103.34,NULL,'pendente','pendente','2025-07-23 09:10:32'),(357,348,2,3,5,864.00,'2025-06-26','','2025-06-26 23:07:32','2025-06-26 23:07:32',5,0.00,1,NULL,5,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(358,349,2,3,5,406.00,'2025-06-26','','2025-06-26 23:08:11','2025-06-26 23:08:11',5,0.00,1,NULL,5,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(360,351,2,3,5,415.00,'2025-06-30','','2025-07-04 00:48:30','2025-07-04 00:48:30',5,0.00,1,NULL,5,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(361,352,0,1,1,16.00,'2025-07-06','','2025-07-06 22:36:42','2025-07-06 22:36:42',0,NULL,1,18,3,0.27,NULL,'pendente','pendente','2025-07-23 09:10:32'),(362,354,0,1,1,16.00,'2025-07-06','','2025-07-06 22:37:18','2025-07-06 22:37:18',0,NULL,1,18,3,0.27,NULL,'pendente','pendente','2025-07-23 09:10:32'),(363,355,1,3,5,41.00,'2025-07-09','','2025-07-09 10:40:04','2025-07-09 10:40:04',0,NULL,1,18,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(365,356,2,4,4,200.00,'2025-06-30','','2025-07-09 22:12:53','2025-07-09 22:12:53',1,1.00,1,NULL,3,2.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(367,357,2,4,4,2483.31,'2025-06-30','','2025-07-09 22:46:19','2025-07-09 22:46:19',1,1.00,1,NULL,3,24.83,NULL,'pendente','pendente','2025-07-23 09:10:32'),(368,358,2,4,4,180.00,'2025-06-30','','2025-07-09 22:54:15','2025-07-09 22:54:15',1,1.00,1,NULL,3,1.80,NULL,'pendente','pendente','2025-07-23 09:10:32'),(369,359,2,4,4,60.00,'2025-07-09','','2025-07-09 22:55:09','2025-07-09 22:55:09',1,1.00,1,NULL,3,0.60,NULL,'pendente','pendente','2025-07-23 09:10:32'),(371,360,2,4,4,1500.00,'2025-06-30','','2025-07-09 22:57:20','2025-07-09 22:57:20',1,1.00,1,NULL,3,15.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(372,361,2,4,4,400.00,'2025-06-30','','2025-07-09 23:06:27','2025-07-09 23:06:27',1,1.00,1,NULL,3,4.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(373,362,2,4,4,1500.00,'2025-06-30','','2025-07-09 23:07:04','2025-07-09 23:07:04',1,1.00,1,NULL,3,15.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(375,363,2,4,4,258.00,'2025-06-30','','2025-07-09 23:08:38','2025-07-09 23:08:38',1,1.00,1,NULL,3,2.58,NULL,'pendente','pendente','2025-07-23 09:10:32'),(376,364,2,4,4,240.00,'2025-06-30','','2025-07-09 23:50:14','2025-07-09 23:50:14',1,1.00,1,NULL,3,2.40,NULL,'pendente','pendente','2025-07-23 09:10:32'),(377,365,2,4,4,310.00,'2025-06-30','','2025-07-09 23:52:50','2025-07-09 23:52:50',1,1.00,1,NULL,3,3.10,NULL,'pendente','pendente','2025-07-23 09:10:32'),(378,366,2,3,5,20.00,'2025-07-22','','2025-07-22 11:22:42','2025-07-22 11:22:42',1,0.00,1,NULL,3,0.00,NULL,'pendente','pendente','2025-07-23 09:10:32'),(383,367,2,4,4,50.00,'2025-07-22','','2025-07-22 09:27:52','2025-07-23 21:27:53',1,1.00,1,NULL,1,0.50,NULL,'pendente','pendente','2025-07-23 12:01:39'),(384,372,1,1,1,16.00,'2025-07-24','','2025-07-24 08:13:21','2025-07-24 08:13:21',0,NULL,1,18,3,0.27,NULL,'pendente','pendente','2025-07-24 09:15:06');
/*!40000 ALTER TABLE `pagamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_webhooks`
--

DROP TABLE IF EXISTS `payment_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_webhooks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_transaction_id` bigint unsigned DEFAULT NULL,
  `gateway_provider` enum('safe2pay','mercadopago','stripe','paypal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `signature` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `processed_at` timestamp NULL DEFAULT NULL,
  `processing_attempts` int NOT NULL DEFAULT '0',
  `processing_error` text COLLATE utf8mb4_unicode_ci,
  `processing_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `payment_webhooks_chk_1` CHECK (json_valid(`payload`)),
  CONSTRAINT `payment_webhooks_chk_2` CHECK (json_valid(`processing_response`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_webhooks`
--

LOCK TABLES `payment_webhooks` WRITE;
/*!40000 ALTER TABLE `payment_webhooks` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_webhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_mesas`
--

DROP TABLE IF EXISTS `pdv_mesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_mesas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `numero_mesa` int NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text,
  `capacidade` int DEFAULT '4',
  `status_pdv` enum('livre','ocupado','fechado','cancelado','excluido','finalizado','delivery','reservado') NOT NULL DEFAULT 'livre',
  `posicao_x` int DEFAULT '0',
  `posicao_y` int DEFAULT '0',
  `cor` varchar(7) DEFAULT '#007bff',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_status` enum('pendente','sincronizado','erro') DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_mesa_empresa` (`empresa_id`,`numero_mesa`),
  KEY `idx_empresa_ativo` (`empresa_id`,`ativo`),
  KEY `idx_numero_mesa` (`numero_mesa`),
  KEY `idx_status_pdv` (`status_pdv`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_mesas`
--

LOCK TABLES `pdv_mesas` WRITE;
/*!40000 ALTER TABLE `pdv_mesas` DISABLE KEYS */;
INSERT INTO `pdv_mesas` VALUES (1,1,1,'',NULL,4,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 21:27:54','pendente','2025-07-23 09:10:35',NULL),(2,1,2,'',NULL,2,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:40','pendente','2025-07-23 09:10:35',NULL),(3,1,3,'',NULL,6,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:40','pendente','2025-07-23 09:10:35',NULL),(4,1,4,'',NULL,4,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:41','pendente','2025-07-23 09:10:35',NULL),(5,1,5,'',NULL,8,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:41','pendente','2025-07-23 09:10:35',NULL),(6,1,6,'',NULL,2,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:41','pendente','2025-07-23 09:10:35',NULL),(7,1,7,'',NULL,4,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:42','pendente','2025-07-23 09:10:35',NULL),(8,1,8,'',NULL,6,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:42','pendente','2025-07-23 09:10:35',NULL),(9,1,9,'',NULL,4,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:42','pendente','2025-07-23 09:10:35',NULL),(10,1,10,'',NULL,2,'livre',0,0,'#007bff',1,'2025-07-20 09:06:52','2025-07-23 12:01:43','pendente','2025-07-23 09:10:35',NULL);
/*!40000 ALTER TABLE `pdv_mesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_sales`
--

DROP TABLE IF EXISTS `pdv_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_sales`
--

LOCK TABLES `pdv_sales` WRITE;
/*!40000 ALTER TABLE `pdv_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_categorias`
--

DROP TABLE IF EXISTS `produto_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_categorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `tipo` enum('produto','insumo','complemento') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'produto, insumo, complemento',
  `ativo` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_categorias`
--

LOCK TABLES `produto_categorias` WRITE;
/*!40000 ALTER TABLE `produto_categorias` DISABLE KEYS */;
INSERT INTO `produto_categorias` VALUES (1,'LANCHES','2025-03-27 22:33:09','2025-07-23 21:27:56',1,'produto','1','2025-07-23 09:10:36',NULL,'pendente'),(2,'PIZZAS','2025-03-27 22:33:09','2025-07-23 12:01:45',1,'produto','1','2025-07-23 09:10:36',NULL,'pendente'),(3,'GERAL','2025-06-27 06:33:08','2025-07-23 12:01:46',1,'produto','1','2025-07-23 09:10:36',NULL,'pendente'),(4,'INSUMOS','2025-06-27 08:05:13','2025-07-23 12:01:46',1,'insumo','1','2025-07-23 09:10:36',NULL,'pendente'),(5,'COMPLEMENTO','2025-06-27 08:19:12','2025-07-23 12:01:46',1,'complemento','1','2025-07-23 09:10:36',NULL,'pendente');
/*!40000 ALTER TABLE `produto_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_configuracao_itens`
--

DROP TABLE IF EXISTS `produto_configuracao_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_configuracao_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `produto_configuracao_id` int NOT NULL,
  `ordem` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_configuracao_itens`
--

LOCK TABLES `produto_configuracao_itens` WRITE;
/*!40000 ALTER TABLE `produto_configuracao_itens` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto_configuracao_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_configuracoes`
--

DROP TABLE IF EXISTS `produto_configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int DEFAULT NULL,
  `nome` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `qtd_minima` int DEFAULT NULL,
  `qtd_maxima` int DEFAULT NULL,
  `tipo_calculo` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `codigo_sistema` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `obrigatorio` tinyint(1) DEFAULT NULL,
  `campo_busca` tinyint(1) DEFAULT NULL,
  `ativo` int DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_configuracoes`
--

LOCK TABLES `produto_configuracoes` WRITE;
/*!40000 ALTER TABLE `produto_configuracoes` DISABLE KEYS */;
INSERT INTO `produto_configuracoes` VALUES (7,9,'Pizza G w',1,4,'soma','10545',1,1,1,1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 09:01:53','2025-07-23 21:27:57'),(8,9,'complemento',1,5,'soma','',1,0,1,1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 09:01:53','2025-07-23 11:23:31'),(9,9,'20',20,20,'media','20',1,1,0,1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 09:01:53','2025-07-23 11:23:32'),(10,9,'teste',54,54,'media','54',1,1,0,1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 09:01:53','2025-07-23 11:23:32'),(11,2,'Complemento',1,2,'media','',1,1,1,1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 09:01:53','2025-07-23 11:23:32');
/*!40000 ALTER TABLE `produto_configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_ficha_tecnica_produto`
--

DROP TABLE IF EXISTS `produto_ficha_tecnica_produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_ficha_tecnica_produto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `insumo_produto_id` int NOT NULL DEFAULT '0' COMMENT 'id do insumo na tabela produto',
  `empresa_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `quantidade` decimal(10,3) NOT NULL DEFAULT '1.000',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_ficha_tecnica_produto`
--

LOCK TABLES `produto_ficha_tecnica_produto` WRITE;
/*!40000 ALTER TABLE `produto_ficha_tecnica_produto` DISABLE KEYS */;
INSERT INTO `produto_ficha_tecnica_produto` VALUES (7,2,24,1,'2025-06-21 21:12:17','2025-07-23 21:27:58',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(8,2,25,1,'2025-06-21 21:12:17','2025-07-23 12:01:48',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(9,15,23,1,'2025-06-21 21:14:58','2025-07-23 12:01:48',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(10,15,24,1,'2025-06-21 21:14:58','2025-07-23 12:01:49',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(11,19,20,1,'2025-06-21 21:48:12','2025-07-23 12:01:49',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(12,19,22,1,'2025-06-21 21:48:12','2025-07-23 12:01:49',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(18,27,20,1,'2025-06-22 10:41:26','2025-07-23 12:01:49',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(19,27,22,1,'2025-06-22 10:41:26','2025-07-23 12:01:50',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(20,27,23,1,'2025-06-22 10:41:26','2025-07-23 12:01:50',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(21,28,20,1,'2025-06-22 22:35:57','2025-07-23 12:01:50',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(22,28,22,1,'2025-06-22 22:35:57','2025-07-23 12:01:51',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(23,28,23,1,'2025-06-22 22:35:57','2025-07-23 12:01:51',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(24,16,20,1,'2025-06-26 23:40:32','2025-07-23 12:01:51',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(25,16,22,1,'2025-06-26 23:40:32','2025-07-23 12:01:52',1.000,'2025-07-23 09:10:36',NULL,'pendente'),(26,16,23,1,'2025-06-26 23:40:32','2025-07-23 12:01:52',1.000,'2025-07-23 09:10:36',NULL,'pendente');
/*!40000 ALTER TABLE `produto_ficha_tecnica_produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_historico_precos`
--

DROP TABLE IF EXISTS `produto_historico_precos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_historico_precos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` int unsigned NOT NULL,
  `preco_anterior` decimal(10,2) NOT NULL,
  `preco_novo` decimal(10,2) NOT NULL,
  `tipo` enum('custo','venda','promocional') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_historico_precos`
--

LOCK TABLES `produto_historico_precos` WRITE;
/*!40000 ALTER TABLE `produto_historico_precos` DISABLE KEYS */;
INSERT INTO `produto_historico_precos` VALUES (6,407,20.00,19.00,'custo',NULL,3,'2025-06-28 06:10:35',1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 21:27:59'),(7,407,19.00,18.00,'custo',NULL,3,'2025-06-28 06:10:46',1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:53'),(8,40,15.00,16.00,'venda',NULL,3,'2025-06-28 06:10:56',1,'2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:53');
/*!40000 ALTER TABLE `produto_historico_precos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_imagens`
--

DROP TABLE IF EXISTS `produto_imagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_imagens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` int unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` int DEFAULT '0',
  `principal` tinyint(1) DEFAULT '0',
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_imagens`
--

LOCK TABLES `produto_imagens` WRITE;
/*!40000 ALTER TABLE `produto_imagens` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto_imagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_importar`
--

DROP TABLE IF EXISTS `produto_importar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_importar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `arquivo_nome` varchar(255) NOT NULL,
  `total_registros` int DEFAULT '0',
  `total_importados` int DEFAULT '0',
  `data_importacao` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(30) DEFAULT 'concluida',
  `observacao` text,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_produto_importar_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `empresa_usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_importar`
--

LOCK TABLES `produto_importar` WRITE;
/*!40000 ALTER TABLE `produto_importar` DISABLE KEYS */;
INSERT INTO `produto_importar` VALUES (1,3,1,'PRODUTOS-_377_.csv',377,377,'2025-06-27 00:54:48','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 21:27:59'),(2,3,1,'INSUMOS-_109_.csv',111,109,'2025-06-27 00:54:55','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:44'),(3,3,1,'COMPLEMENTOS-_81_.csv',82,81,'2025-06-27 00:55:01','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:45'),(4,3,1,'PRODUTOS-_377_.csv',377,377,'2025-06-27 13:26:25','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:45'),(5,3,1,'INSUMOS-_109_.csv',111,109,'2025-06-27 13:26:34','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:45'),(6,3,1,'COMPLEMENTOS-_81_.csv',82,81,'2025-06-27 13:26:42','concluida','','2025-07-23 09:10:36',NULL,'pendente','2025-07-22 23:00:07','2025-07-23 11:13:46');
/*!40000 ALTER TABLE `produto_importar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_inventario_itens`
--

DROP TABLE IF EXISTS `produto_inventario_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_inventario_itens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `inventario_id` int unsigned NOT NULL,
  `produto_id` int unsigned NOT NULL,
  `quantidade_sistema` decimal(10,2) NOT NULL,
  `quantidade_contada` decimal(10,2) NOT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `usuario_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventario_id` (`inventario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_inventario_itens`
--

LOCK TABLES `produto_inventario_itens` WRITE;
/*!40000 ALTER TABLE `produto_inventario_itens` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto_inventario_itens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_inventarios`
--

DROP TABLE IF EXISTS `produto_inventarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_inventarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_fim` timestamp NULL DEFAULT NULL,
  `status` enum('aberto','finalizado','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'aberto',
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_inventarios`
--

LOCK TABLES `produto_inventarios` WRITE;
/*!40000 ALTER TABLE `produto_inventarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto_inventarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_movimentacoes`
--

DROP TABLE IF EXISTS `produto_movimentacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_movimentacoes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `produto_id` int unsigned NOT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `tipo` enum('entrada','saida','ajuste') COLLATE utf8mb3_unicode_ci NOT NULL,
  `origem` enum('compra','venda','transferencia','inventario','devolucao','perda','outros') COLLATE utf8mb3_unicode_ci NOT NULL,
  `documento` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8mb3_unicode_ci,
  `usuario_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unidade_medida` varchar(50) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_movimentacoes`
--

LOCK TABLES `produto_movimentacoes` WRITE;
/*!40000 ALTER TABLE `produto_movimentacoes` DISABLE KEYS */;
INSERT INTO `produto_movimentacoes` VALUES (15,2,15.00,'entrada','compra','',NULL,3,1,'2025-06-19 11:08:06','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 21:28:00'),(16,1,6.00,'entrada','compra','',NULL,3,1,'2025-06-19 11:08:15','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:55'),(17,2,10.00,'entrada','compra','',NULL,3,1,'2025-06-20 07:29:04','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:55'),(18,2,20.00,'saida','compra','',NULL,3,1,'2025-06-20 07:29:15','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:56'),(19,25,0.60,'entrada','compra','',NULL,3,1,'2025-06-21 22:18:59','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:56'),(20,25,0.60,'entrada','compra','',NULL,3,1,'2025-06-21 22:20:20','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:56'),(21,22,0.60,'entrada','compra','',NULL,3,1,'2025-06-21 22:20:30','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:57'),(22,22,0.60,'entrada','compra','hg','hg',3,1,'2025-06-21 22:22:27','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:57'),(33,23,0.60,'entrada','compra','','',3,1,'2025-06-21 22:47:45','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:57'),(34,22,0.60,'entrada','compra','','',3,1,'2025-06-21 22:48:04','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:58'),(35,22,0.60,'entrada','compra','','',3,1,'2025-06-22 08:31:40','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:58'),(36,22,0.60,'entrada','compra','','',3,1,'2025-06-22 08:31:49','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:58'),(38,22,0.60,'entrada','compra','','',3,1,'2025-06-22 08:36:40','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:58'),(39,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:36:54','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:59'),(40,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:39:52','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:59'),(41,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:41:29','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:01:59'),(42,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:42:08','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:00'),(43,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:42:50','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:00'),(55,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:47:54','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:00'),(56,16,10.00,'entrada','compra','','',3,1,'2025-06-22 08:49:19','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:00'),(57,22,10.00,'entrada','compra','','',3,1,'2025-06-22 08:57:12','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:01'),(58,22,0.25,'entrada','compra','','',3,1,'2025-06-22 09:09:40','KG','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:01'),(59,24,10.00,'entrada','compra','','',3,1,'2025-06-22 10:23:48','','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:01'),(60,26,10.00,'entrada','compra','','',3,1,'2025-06-22 10:25:08','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:02'),(61,29,10.00,'entrada','compra','','',3,1,'2025-06-22 10:32:56','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:02'),(62,26,10.00,'entrada','compra','','',3,1,'2025-06-22 10:33:13','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:02'),(63,26,30.00,'entrada','compra','','',3,1,'2025-06-22 10:33:28','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:03'),(64,23,10.00,'entrada','compra','','',3,1,'2025-06-22 22:40:32','UN','2025-07-23 09:10:36',NULL,'pendente','2025-07-23 12:02:03');
/*!40000 ALTER TABLE `produto_movimentacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto_receitas`
--

DROP TABLE IF EXISTS `produto_receitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto_receitas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `produto_final_id` int unsigned NOT NULL COMMENT 'ID do produto vendÃ¡vel',
  `insumo_id` int unsigned NOT NULL COMMENT 'ID do insumo usado',
  `quantidade` decimal(10,2) NOT NULL COMMENT 'Qtd do insumo por unidade do produto',
  `observacoes` text,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_receitas`
--

LOCK TABLES `produto_receitas` WRITE;
/*!40000 ALTER TABLE `produto_receitas` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto_receitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_configuracao_id` int DEFAULT NULL,
  `categoria_id` int unsigned NOT NULL DEFAULT '0',
  `tipo` enum('produto','insumo','complemento') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'produto'', ''insumo'', ''complemento',
  `codigo_sistema` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('disponivel','indisponivel','pausado','esgotado','novidade') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'disponivel,indisponivel,pausado,esgotado,novidade',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `codigo_barras` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cest` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidade_medida` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UN',
  `peso_liquido` decimal(10,3) DEFAULT NULL,
  `peso_bruto` decimal(10,3) DEFAULT NULL,
  `marca_id` int unsigned DEFAULT NULL,
  `preco_custo_base` decimal(10,2) DEFAULT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `em_promocao` tinyint(1) DEFAULT '0',
  `controla_estoque` tinyint(1) DEFAULT '1',
  `estoque_atual` decimal(10,2) DEFAULT '0.00',
  `estoque_minimo` decimal(10,2) DEFAULT '0.00',
  `estoque_maximo` decimal(10,2) DEFAULT NULL,
  `situacao_estoque` enum('disponivel','estoque_baixo','esgotado') COLLATE utf8mb4_unicode_ci DEFAULT 'esgotado',
  `tempo_preparo` int DEFAULT NULL,
  `ingredientes` text COLLATE utf8mb4_unicode_ci,
  `informacoes_nutricionais` text COLLATE utf8mb4_unicode_ci,
  `localizacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `profundidade` decimal(10,2) DEFAULT NULL,
  `aliquota_icms` decimal(5,2) DEFAULT NULL,
  `situacao_tributaria_pis` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `situacao_tributaria_cofins` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tempo_validade` int DEFAULT NULL COMMENT 'Dias de validade após abertura',
  `perecivel` tinyint(1) DEFAULT '0',
  `controlado` tinyint(1) DEFAULT '0' COMMENT 'Insumo de alto custo ou crítico',
  `compoe_produto_final` tinyint(1) DEFAULT '0' COMMENT 'Para insumos que entram na produção',
  `ordem` int DEFAULT NULL,
  `preco_custo_antigo` decimal(10,2) NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_sistema` (`codigo_sistema`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `produto_categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=597 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_configuracoes`
--

DROP TABLE IF EXISTS `sis_configuracoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sis_configuracoes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '1',
  `chave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descricao` text,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_chave` (`empresa_id`,`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_configuracoes`
--

LOCK TABLES `sis_configuracoes` WRITE;
/*!40000 ALTER TABLE `sis_configuracoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sis_configuracoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_gateways`
--

DROP TABLE IF EXISTS `sis_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sis_gateways` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '1',
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `provedor` enum('safe2pay','mercadopago','pagseguro','stripe') NOT NULL,
  `ambiente` enum('sandbox','producao') DEFAULT 'sandbox',
  `ativo` tinyint(1) DEFAULT '1',
  `credenciais` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `configuracoes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `url_webhook` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresa_codigo` (`empresa_id`,`codigo`),
  CONSTRAINT `sis_gateways_chk_1` CHECK (json_valid(`credenciais`)),
  CONSTRAINT `sis_gateways_chk_2` CHECK (json_valid(`configuracoes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_gateways`
--

LOCK TABLES `sis_gateways` WRITE;
/*!40000 ALTER TABLE `sis_gateways` DISABLE KEYS */;
/*!40000 ALTER TABLE `sis_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sis_transacoes`
--

DROP TABLE IF EXISTS `sis_transacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sis_transacoes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa_id` int NOT NULL DEFAULT '1',
  `gateway_id` int DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','processando','aprovado','cancelado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `tipo_transacao` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_externa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadados` json DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` datetime DEFAULT NULL,
  `sync_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uuid` (`uuid`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_status` (`status`),
  KEY `idx_gateway` (`gateway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sis_transacoes`
--

LOCK TABLES `sis_transacoes` WRITE;
/*!40000 ALTER TABLE `sis_transacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sis_transacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_control`
--

DROP TABLE IF EXISTS `sync_control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sync_control` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `tabela` varchar(50) NOT NULL,
  `ultima_sync` timestamp NULL DEFAULT NULL,
  `proxima_sync` timestamp NULL DEFAULT NULL,
  `status` enum('ativo','pausado','erro') DEFAULT 'ativo',
  `registros_pendentes` int DEFAULT '0',
  `configuracoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_status` varchar(20) DEFAULT 'pendente',
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_empresa_tabela` (`empresa_id`,`tabela`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_control`
--

LOCK TABLES `sync_control` WRITE;
/*!40000 ALTER TABLE `sync_control` DISABLE KEYS */;
INSERT INTO `sync_control` VALUES (5,1,'produtos',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 10, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:53','sincronizado','2025-07-23 09:10:36'),(6,1,'funforcli',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 15, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:53','sincronizado','2025-07-23 09:10:36'),(7,1,'lancamentos',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 5, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:53','sincronizado','2025-07-23 09:10:36'),(8,1,'pdv_mesas',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 2, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:53','sincronizado','2025-07-23 09:10:36'),(9,1,'produto_categorias',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:53','sincronizado','2025-07-23 09:10:36'),(10,1,'produto_configuracoes',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(11,1,'produto_imagens',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(12,1,'produto_movimentacoes',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 15, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(13,1,'produto_receitas',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(14,1,'produto_historico_precos',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(15,1,'lancamento_itens',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 5, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(16,1,'lancamento_itens_opcoes',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 10, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:54','sincronizado','2025-07-23 09:10:36'),(17,1,'pagamentos',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 5, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(18,1,'caixas',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 5, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(19,1,'caixa_movimentos',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 5, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(20,1,'caixa_fechamento',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 10, \"priority\": \"high\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(21,1,'caixa_fechamento_formas',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 10, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(22,1,'config',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:55','sincronizado','2025-07-23 09:10:36'),(23,1,'formas_pagamento',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(24,1,'forma_pagamento_bandeiras',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(25,1,'tipo',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(26,1,'tipo_lancamento',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(27,1,'empresa_usuarios',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(28,1,'usuarios_perfis',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 120, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(29,1,'usuarios_permissoes',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 120, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:56','sincronizado','2025-07-23 09:10:36'),(30,1,'conta_bancaria',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:57','sincronizado','2025-07-23 09:10:36'),(31,1,'funforcli_enderecos',NULL,NULL,'ativo',0,'{\"auto_sync\": true, \"interval_minutes\": 30, \"priority\": \"medium\"}','2025-07-22 00:12:59','2025-07-24 09:21:57','sincronizado','2025-07-23 09:10:36'),(32,1,'mesa_status_log',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 60, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:57','sincronizado','2025-07-23 09:10:36'),(33,1,'impressoras_configuracoes',NULL,NULL,'ativo',0,'{\"auto_sync\": false, \"interval_minutes\": 120, \"priority\": \"low\"}','2025-07-22 00:12:59','2025-07-24 09:21:57','sincronizado','2025-07-23 09:10:36');
/*!40000 ALTER TABLE `sync_control` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_logs`
--

DROP TABLE IF EXISTS `sync_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sync_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL,
  `tabela` varchar(50) NOT NULL,
  `tipo_sync` enum('completa','incremental','manual') NOT NULL,
  `direcao` enum('import','export','bidirectional') NOT NULL,
  `inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fim` timestamp NULL DEFAULT NULL,
  `registros_processados` int DEFAULT '0',
  `registros_inseridos` int DEFAULT '0',
  `registros_atualizados` int DEFAULT '0',
  `registros_erro` int DEFAULT '0',
  `status` enum('executando','sucesso','erro','cancelado') DEFAULT 'executando',
  `detalhes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `erro_mensagem` text,
  `sync_status` varchar(20) DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_logs`
--

LOCK TABLES `sync_logs` WRITE;
/*!40000 ALTER TABLE `sync_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo`
--

DROP TABLE IF EXISTS `tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `empresa_id` int NOT NULL,
  `value` varchar(25) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo`
--

LOCK TABLES `tipo` WRITE;
/*!40000 ALTER TABLE `tipo` DISABLE KEYS */;
INSERT INTO `tipo` VALUES (1,'receita',1,'receita','2025-07-23 09:10:36',NULL,'sincronizado','2025-07-23 09:03:32','2025-07-24 09:21:58'),(2,'despesa',1,'despesa','2025-07-23 09:10:36',NULL,'sincronizado','2025-07-23 09:03:32','2025-07-24 09:21:58');
/*!40000 ALTER TABLE `tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_lancamento`
--

DROP TABLE IF EXISTS `tipo_lancamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_lancamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo_id` int NOT NULL,
  `nome_value` char(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `empresa_id` int NOT NULL,
  `sync_data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb3_unicode_ci DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_lancamento`
--

LOCK TABLES `tipo_lancamento` WRITE;
/*!40000 ALTER TABLE `tipo_lancamento` DISABLE KEYS */;
INSERT INTO `tipo_lancamento` VALUES (1,'PDV',2,'pdv',1,'2025-07-23 09:10:36',NULL,'sincronizado','2025-07-23 09:03:38','2025-07-24 09:21:59'),(2,'CONTA PAGAR',2,'conta_pagar',1,'2025-07-23 09:10:36',NULL,'sincronizado','2025-07-23 09:03:38','2025-07-24 09:21:59'),(3,'CONTA RECEBER',1,'conta_receber',1,'2025-07-23 09:10:36',NULL,'sincronizado','2025-07-23 09:03:38','2025-07-24 09:21:59');
/*!40000 ALTER TABLE `tipo_lancamento` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-04  5:48:54
