-- --------------------------------------------------------
-- Servidor:                     localhost
-- Versão do servidor:           8.0.43 - MySQL Community Server - GPL
-- OS do Servidor:               Linux
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando estrutura para tabela meufinanceiro.produtos
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `categoria_id` int unsigned DEFAULT NULL,
  `subcategoria_id` int unsigned DEFAULT NULL,
  `marca_id` int unsigned DEFAULT NULL,
  `tipo` enum('produto','insumo','complemento','servico','combo','kit') COLLATE utf8mb4_unicode_ci DEFAULT 'produto',
  `possui_variacoes` tinyint(1) DEFAULT '0',
  `codigo_sistema` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_reduzido` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_fabricante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('disponivel','indisponivel','pausado','esgotado','novidade') COLLATE utf8mb4_unicode_ci DEFAULT 'disponivel',
  `status_venda` enum('disponivel','indisponivel','pausado','esgotado','novidade','promocao') COLLATE utf8mb4_unicode_ci DEFAULT 'disponivel',
  `ativo` tinyint(1) DEFAULT '1',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `descricao_curta` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `especificacoes_tecnicas` text COLLATE utf8mb4_unicode_ci,
  `ingredientes` text COLLATE utf8mb4_unicode_ci,
  `informacoes_nutricionais` text COLLATE utf8mb4_unicode_ci,
  `modo_uso` text COLLATE utf8mb4_unicode_ci,
  `cuidados` text COLLATE utf8mb4_unicode_ci,
  `codigo_barras` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gtin` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cest` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `cfop` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preco_compra` decimal(10,2) DEFAULT '0.00',
  `preco_venda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `margem_lucro` decimal(5,2) DEFAULT NULL,
  `controla_estoque` tinyint(1) DEFAULT '1',
  `estoque_atual` decimal(10,3) DEFAULT '0.000',
  `estoque_minimo` decimal(10,3) DEFAULT '0.000',
  `estoque_maximo` decimal(10,3) DEFAULT NULL,
  `unidade_medida` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UN',
  `unidade_compra` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UN',
  `fator_conversao` decimal(10,4) DEFAULT '1.0000',
  `peso_liquido` decimal(10,3) DEFAULT NULL,
  `peso_bruto` decimal(10,3) DEFAULT NULL,
  `altura` decimal(10,2) DEFAULT NULL,
  `largura` decimal(10,2) DEFAULT NULL,
  `profundidade` decimal(10,2) DEFAULT NULL,
  `volume` decimal(10,3) DEFAULT NULL,
  `cst` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aliquota_icms` decimal(5,2) DEFAULT NULL,
  `aliquota_ipi` decimal(5,2) DEFAULT NULL,
  `aliquota_pis` decimal(5,2) DEFAULT NULL,
  `aliquota_cofins` decimal(5,2) DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `palavras_chave` text COLLATE utf8mb4_unicode_ci,
  `ordem_exibicao` int DEFAULT '0',
  `destaque` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa_status` (`empresa_id`,`status`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_codigo_barras` (`codigo_barras`),
  KEY `idx_sku` (`sku`),
  KEY `idx_nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produtos: ~8 rows (aproximadamente)
DELETE FROM `produtos`;
INSERT INTO `produtos` (`id`, `empresa_id`, `categoria_id`, `subcategoria_id`, `marca_id`, `tipo`, `possui_variacoes`, `codigo_sistema`, `nome`, `nome_reduzido`, `slug`, `sku`, `codigo_fabricante`, `status`, `status_venda`, `ativo`, `descricao`, `descricao_curta`, `especificacoes_tecnicas`, `ingredientes`, `informacoes_nutricionais`, `modo_uso`, `cuidados`, `codigo_barras`, `gtin`, `ncm`, `cest`, `origem`, `cfop`, `preco_compra`, `preco_venda`, `preco_promocional`, `margem_lucro`, `controla_estoque`, `estoque_atual`, `estoque_minimo`, `estoque_maximo`, `unidade_medida`, `unidade_compra`, `fator_conversao`, `peso_liquido`, `peso_bruto`, `altura`, `largura`, `profundidade`, `volume`, `cst`, `aliquota_icms`, `aliquota_ipi`, `aliquota_pis`, `aliquota_cofins`, `observacoes`, `palavras_chave`, `ordem_exibicao`, `destaque`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 1, NULL, 1, 'produto', 0, NULL, 'Pizza Margherita', NULL, NULL, NULL, NULL, 'disponivel', 'disponivel', 1, 'Pizza tradicional com molho de tomate, mussarela e manjericão', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 25.90, NULL, NULL, 0, NULL, NULL, NULL, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(2, 1, 2, NULL, 1, 'produto', 0, NULL, 'Refrigerante Cola 350ml', NULL, NULL, NULL, NULL, 'disponivel', 'disponivel', 1, 'Refrigerante sabor cola 350ml', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 4.50, NULL, NULL, 1, 50.000, 10.000, NULL, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(3, 1, 3, NULL, 1, 'servico', 0, NULL, 'Consulta Técnica', NULL, NULL, NULL, NULL, 'disponivel', 'disponivel', 1, 'Consulta técnica especializada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 100.00, NULL, NULL, 0, 25.000, NULL, NULL, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 07:51:51', '2025-08-09 03:42:22', NULL, 'pendente', '2025-08-09 03:42:22', NULL),
	(4, 1, 1, NULL, NULL, 'produto', 0, NULL, 'Produto Teste - Estoque Baixo', NULL, NULL, 'TESTE-BAIXO-001', NULL, 'disponivel', 'disponivel', 1, 'Produto para testar notificações de estoque baixo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 25.90, NULL, NULL, 1, 2.000, 5.000, 50.000, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 12:56:49', '2025-08-08 12:56:49', NULL, 'pendente', '2025-08-08 16:56:49', NULL),
	(5, 1, 1, NULL, NULL, 'produto', 0, NULL, 'Produto Teste - Esgotado', NULL, NULL, 'TESTE-ESGOTADO-001', NULL, 'esgotado', 'disponivel', 1, 'Produto para testar notificações de estoque esgotado', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 35.50, NULL, NULL, 1, 0.000, 3.000, 30.000, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 12:56:49', '2025-08-08 12:56:49', NULL, 'pendente', '2025-08-08 16:56:49', NULL),
	(6, 2, 1, NULL, NULL, 'produto', 0, NULL, 'Pizza Especial - Teste', NULL, NULL, 'PIZZA-TESTE-002', NULL, 'disponivel', 'disponivel', 1, 'Pizza com ingredientes quase acabando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 45.90, NULL, NULL, 1, 1.000, 8.000, 20.000, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-08 12:56:49', '2025-08-08 12:56:49', NULL, 'pendente', '2025-08-08 16:56:49', NULL),
	(7, 1, 1, NULL, 2, 'kit', 0, NULL, 'teste', NULL, 'teste', NULL, NULL, 'disponivel', 'disponivel', 1, 'jhjhjhjh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0.00, 100.00, NULL, NULL, 0, 0.000, 0.000, NULL, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-09 14:19:50', '2025-08-09 14:26:48', NULL, 'pendente', '2025-08-09 18:26:48', NULL),
	(8, 1, 1, NULL, 2, 'produto', 0, NULL, 'coca ks', NULL, 'coca-ks', 'COC0001', NULL, 'disponivel', 'disponivel', 1, '54545454', '54545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 2.00, 5.00, 4.00, 150.00, 0, 0.000, 0.000, NULL, 'UN', 'UN', 1.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2025-08-09 14:31:50', '2025-08-09 14:31:50', NULL, 'pendente', '2025-08-09 18:31:50', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_categorias
CREATE TABLE IF NOT EXISTS `produto_categorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `categoria_pai_id` int unsigned DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cor` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_categoria_pai` (`categoria_pai_id`),
  KEY `idx_nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_categorias_pai` FOREIGN KEY (`categoria_pai_id`) REFERENCES `produto_categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_categorias: ~4 rows (aproximadamente)
DELETE FROM `produto_categorias`;
INSERT INTO `produto_categorias` (`id`, `empresa_id`, `categoria_pai_id`, `nome`, `descricao`, `slug`, `icone`, `cor`, `imagem`, `ordem`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, NULL, 'Alimentação', 'Produtos alimentícios', 'alimentacao', 'fas fa-utensils', '#28a745', NULL, 1, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(2, 1, NULL, 'Bebidas', 'Bebidas em geral', 'bebidas', 'fas fa-glass-cheers', '#007bff', NULL, 2, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(3, 1, NULL, 'Serviços', 'Serviços oferecidos', 'servicos', 'fas fa-tools', '#ffc107', NULL, 3, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(4, 1, NULL, 'Produtos', 'Produtos diversos', 'produtos', 'fas fa-box', '#6f42c1', NULL, 4, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_codigos_barras
CREATE TABLE IF NOT EXISTS `produto_codigos_barras` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `variacao_id` int unsigned DEFAULT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('ean13','ean8','upca','upce','code128','code39','qrcode','datamatrix') COLLATE utf8mb4_unicode_ci DEFAULT 'ean13',
  `principal` tinyint(1) DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_codigo` (`codigo`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_variacao` (`variacao_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_codigos_barras_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_codigos_barras_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_codigos_barras: ~4 rows (aproximadamente)
DELETE FROM `produto_codigos_barras`;
INSERT INTO `produto_codigos_barras` (`id`, `empresa_id`, `produto_id`, `variacao_id`, `codigo`, `tipo`, `principal`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 1, NULL, '7891234567890', 'ean13', 1, 1, '2025-08-09 00:47:26', '2025-08-09 00:47:26', NULL, 'pendente', '2025-08-09 04:47:26', NULL),
	(2, 1, 2, NULL, '7891234567891', 'ean13', 1, 1, '2025-08-09 00:47:26', '2025-08-09 00:47:26', NULL, 'pendente', '2025-08-09 04:47:26', NULL),
	(3, 1, 1, NULL, '7891234567899', 'ean13', 0, 1, '2025-08-09 01:04:03', '2025-08-09 01:04:03', NULL, 'pendente', '2025-08-09 05:04:03', NULL),
	(4, 1, 1, NULL, '1111111111111', 'ean13', 0, 1, '2025-08-09 01:05:35', '2025-08-09 01:17:05', NULL, 'pendente', '2025-08-09 05:17:05', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_configuracao_itens
CREATE TABLE IF NOT EXISTS `produto_configuracao_itens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_configuracao_id` int NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `valor_adicional` decimal(10,2) DEFAULT '0.00',
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `disponivel` tinyint(1) DEFAULT '1',
  `padrao` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_configuracao` (`produto_configuracao_id`),
  KEY `idx_disponivel` (`disponivel`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_itens_configuracao` FOREIGN KEY (`produto_configuracao_id`) REFERENCES `produto_configuracoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_configuracao_itens: ~38 rows (aproximadamente)
DELETE FROM `produto_configuracao_itens`;
INSERT INTO `produto_configuracao_itens` (`id`, `empresa_id`, `produto_configuracao_id`, `nome`, `descricao`, `valor_adicional`, `imagem`, `ordem`, `disponivel`, `padrao`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 1, 'Pequena (25cm)', 'Serve 1 pessoa', -5.00, NULL, 1, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(2, 1, 1, 'Média (30cm)', 'Serve 2 pessoas', 0.00, NULL, 2, 1, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(3, 1, 1, 'Grande (35cm)', 'Serve 3-4 pessoas', 8.00, NULL, 3, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(4, 1, 1, 'Família (40cm)', 'Serve 4-6 pessoas', 15.00, NULL, 4, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(5, 1, 2, 'Borda Tradicional', 'Borda simples da massa', 0.00, NULL, 1, 1, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(6, 1, 2, 'Borda Recheada - Catupiry', 'Borda recheada com catupiry', 6.00, NULL, 2, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(7, 1, 2, 'Borda Recheada - Cheddar', 'Borda recheada com cheddar', 7.00, NULL, 3, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(8, 1, 3, 'Bacon', NULL, 4.00, NULL, 1, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(9, 1, 3, 'Calabresa', NULL, 3.50, NULL, 2, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(10, 1, 3, 'Frango Desfiado', NULL, 3.50, NULL, 3, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(11, 1, 3, 'Catupiry Extra', NULL, 3.00, NULL, 4, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(12, 1, 3, 'Azeitona', NULL, 2.00, NULL, 5, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(13, 1, 3, 'Milho', NULL, 1.50, NULL, 6, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(14, 1, 3, 'Champignon', NULL, 3.00, NULL, 7, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(15, 1, 3, 'Tomate Seco', NULL, 2.50, NULL, 8, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(16, 1, 4, 'Gelado', 'Servido bem gelado', 0.00, NULL, 1, 1, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(17, 1, 4, 'Natural', 'Temperatura ambiente', 0.00, NULL, 2, 1, 0, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(18, 1, 5, 'Pequena (25cm)', 'Pizza pequena de 25cm', 0.00, NULL, 1, 1, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(19, 1, 5, 'Média (30cm)', 'Pizza média de 30cm', 5.00, NULL, 2, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(20, 1, 5, 'Grande (35cm)', 'Pizza grande de 35cm', 12.00, NULL, 3, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(21, 1, 6, 'Borda Tradicional', 'Borda simples sem recheio', 0.00, NULL, 1, 1, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(22, 1, 6, 'Borda Recheada com Catupiry', 'Borda recheada com catupiry', 8.00, NULL, 2, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(23, 1, 6, 'Borda Recheada com Cheddar', 'Borda recheada com cheddar', 10.00, NULL, 3, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(24, 1, 7, 'Cola', 'Refrigerante sabor cola', 0.00, NULL, 1, 1, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(25, 1, 7, 'Laranja', 'Refrigerante sabor laranja', 0.00, NULL, 2, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(26, 1, 7, 'Guaraná', 'Refrigerante sabor guaraná', 0.00, NULL, 3, 1, 0, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(27, 1, 8, 'Pequena (25cm)', 'Pizza pequena de 25cm', 0.00, NULL, 1, 1, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(28, 1, 8, 'Média (30cm)', 'Pizza média de 30cm', 5.00, NULL, 2, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(29, 1, 8, 'Grande (35cm)', 'Pizza grande de 35cm', 12.00, NULL, 3, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(30, 1, 9, 'Borda Tradicional', 'Borda simples sem recheio', 0.00, NULL, 1, 1, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(31, 1, 9, 'Borda Recheada com Catupiry', 'Borda recheada com catupiry', 8.00, NULL, 2, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(32, 1, 9, 'Borda Recheada com Cheddar', 'Borda recheada com cheddar', 10.00, NULL, 3, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(33, 1, 10, 'Cola', 'Refrigerante sabor cola', 0.00, NULL, 1, 1, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(34, 1, 10, 'Laranja', 'Refrigerante sabor laranja', 0.00, NULL, 2, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(35, 1, 10, 'Guaraná', 'Refrigerante sabor guaraná', 0.00, NULL, 3, 1, 0, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(36, 1, 3, 'teste', 'swsw', 0.00, NULL, 9, 1, 0, '2025-08-09 02:07:58', '2025-08-09 02:07:58', NULL, 'pendente', '2025-08-09 06:07:58', NULL),
	(37, 1, 3, 'teste2', 'edededed', 300.00, NULL, 10, 1, 0, '2025-08-09 02:08:33', '2025-08-09 02:08:33', NULL, 'pendente', '2025-08-09 06:08:33', NULL),
	(38, 1, 3, 'tesrrrr333333', 'hhhhh', 300.00, NULL, 11, 0, 0, '2025-08-09 02:13:23', '2025-08-09 02:16:23', '2025-08-09 02:16:23', 'pendente', '2025-08-09 06:16:23', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_configuracoes
CREATE TABLE IF NOT EXISTS `produto_configuracoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `tipo_configuracao` enum('tamanho','sabor','ingrediente','complemento','personalizado') COLLATE utf8mb4_unicode_ci DEFAULT 'personalizado',
  `obrigatorio` tinyint(1) DEFAULT '0',
  `permite_multiplos` tinyint(1) DEFAULT '0',
  `qtd_minima` int DEFAULT NULL,
  `qtd_maxima` int DEFAULT NULL,
  `tipo_calculo` enum('soma','media','maximo','substituicao') COLLATE utf8mb4_unicode_ci DEFAULT 'soma',
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_tipo` (`tipo_configuracao`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_configuracoes_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_configuracoes: ~10 rows (aproximadamente)
DELETE FROM `produto_configuracoes`;
INSERT INTO `produto_configuracoes` (`id`, `empresa_id`, `produto_id`, `nome`, `descricao`, `tipo_configuracao`, `obrigatorio`, `permite_multiplos`, `qtd_minima`, `qtd_maxima`, `tipo_calculo`, `ordem`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 1, 'Tamanho da Pizza', 'Escolha o tamanho desejado', 'tamanho', 1, 0, NULL, NULL, 'substituicao', 1, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(2, 1, 1, 'Tipo de Borda', 'Escolha o tipo de borda', 'complemento', 1, 0, NULL, NULL, 'soma', 2, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(3, 1, 1, 'Ingredientes Adicionais', 'Adicione mais ingredientes', 'ingrediente', 0, 1, NULL, 5, 'soma', 3, 1, '2025-08-09 01:25:51', '2025-08-09 01:56:49', NULL, 'pendente', '2025-08-09 05:56:49', NULL),
	(4, 1, 2, 'Temperatura', 'Como prefere o refrigerante?', 'personalizado', 1, 0, NULL, NULL, 'substituicao', 1, 1, '2025-08-09 01:25:51', '2025-08-09 01:25:51', NULL, 'pendente', '2025-08-09 05:25:51', NULL),
	(5, 1, 1, 'Tamanho da Pizza', 'Escolha o tamanho da sua pizza', 'tamanho', 1, 0, 1, 1, 'substituicao', 1, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(6, 1, 1, 'Tipo de Borda', 'Escolha o tipo de borda da sua pizza', 'complemento', 0, 0, 0, 1, 'soma', 2, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(7, 1, 2, 'Sabor do Refrigerante', 'Escolha o sabor do refrigerante', 'sabor', 1, 0, 1, 1, 'substituicao', 1, 1, '2025-08-09 01:57:58', '2025-08-09 01:57:58', NULL, 'pendente', '2025-08-09 05:57:58', NULL),
	(8, 1, 1, 'Tamanho da Pizza', 'Escolha o tamanho da sua pizza', 'tamanho', 1, 0, 1, 1, 'substituicao', 1, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(9, 1, 1, 'Tipo de Borda', 'Escolha o tipo de borda da sua pizza', 'complemento', 0, 0, 0, 1, 'soma', 2, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL),
	(10, 1, 2, 'Sabor do Refrigerante', 'Escolha o sabor do refrigerante', 'sabor', 1, 0, 1, 1, 'substituicao', 1, 1, '2025-08-09 01:58:18', '2025-08-09 01:58:18', NULL, 'pendente', '2025-08-09 05:58:18', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_historico_precos
CREATE TABLE IF NOT EXISTS `produto_historico_precos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `variacao_id` int unsigned DEFAULT NULL,
  `preco_compra_anterior` decimal(10,2) DEFAULT NULL,
  `preco_compra_novo` decimal(10,2) DEFAULT NULL,
  `preco_venda_anterior` decimal(10,2) DEFAULT NULL,
  `preco_venda_novo` decimal(10,2) DEFAULT NULL,
  `margem_anterior` decimal(5,2) DEFAULT NULL,
  `margem_nova` decimal(5,2) DEFAULT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `data_alteracao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_variacao` (`variacao_id`),
  KEY `idx_data` (`data_alteracao`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_historico_precos_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_historico_precos_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_historico_precos: ~3 rows (aproximadamente)
DELETE FROM `produto_historico_precos`;
INSERT INTO `produto_historico_precos` (`id`, `empresa_id`, `produto_id`, `variacao_id`, `preco_compra_anterior`, `preco_compra_novo`, `preco_venda_anterior`, `preco_venda_novo`, `margem_anterior`, `margem_nova`, `motivo`, `usuario_id`, `data_alteracao`, `created_at`, `updated_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 3, NULL, 60.00, 100.00, 100.00, 120.00, 66.67, 20.00, NULL, NULL, '2025-08-09 00:45:00', '2025-08-09 00:45:52', '2025-08-09 00:45:52', 'pendente', '2025-08-09 04:45:52', NULL),
	(2, 1, 1, NULL, NULL, NULL, 20.00, 25.90, NULL, NULL, 'ajuste_comercial', 1, '2025-08-04 00:47:37', '2025-08-09 00:47:37', '2025-08-09 00:47:37', 'pendente', '2025-08-09 04:47:37', NULL),
	(3, 1, 2, NULL, NULL, NULL, 3.50, 4.50, NULL, NULL, 'aumento_fornecedor', 1, '2025-08-06 00:47:37', '2025-08-09 00:47:37', '2025-08-09 00:47:37', 'pendente', '2025-08-09 04:47:37', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_imagens
CREATE TABLE IF NOT EXISTS `produto_imagens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `variacao_id` int unsigned DEFAULT NULL,
  `tipo` enum('principal','galeria','miniatura','zoom') COLLATE utf8mb4_unicode_ci DEFAULT 'galeria',
  `arquivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `tamanho_arquivo` int DEFAULT NULL,
  `dimensoes` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_variacao` (`variacao_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_imagens_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_imagens_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_imagens: ~4 rows (aproximadamente)
DELETE FROM `produto_imagens`;
INSERT INTO `produto_imagens` (`id`, `empresa_id`, `produto_id`, `variacao_id`, `tipo`, `arquivo`, `titulo`, `alt_text`, `ordem`, `tamanho_arquivo`, `dimensoes`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 3, NULL, 'principal', 'produto_principal_3.jpg', 'Consulta Técnica - Imagem Principal', 'Consulta Técnica - Imagem Principal', 1, 123456, '800x600', 1, '2025-08-09 03:42:22', '2025-08-09 03:42:22', NULL, 'pendente', '2025-08-09 03:42:22', NULL),
	(2, 1, 3, NULL, 'galeria', 'produto_galeria_1_3.jpg', 'Consulta Técnica - Vista Lateral', 'Consulta Técnica - Vista Lateral', 2, 123456, '800x600', 1, '2025-08-09 03:42:22', '2025-08-09 03:42:22', NULL, 'pendente', '2025-08-09 03:42:22', NULL),
	(3, 1, 3, NULL, 'galeria', 'produto_galeria_2_3.jpg', 'Consulta Técnica - Detalhe', 'Consulta Técnica - Detalhe', 3, 123456, '800x600', 1, '2025-08-09 03:42:22', '2025-08-09 03:42:22', NULL, 'pendente', '2025-08-09 03:42:22', NULL),
	(4, 1, 8, NULL, 'principal', '1754764310_12033882.jpg', 'coca ks', 'coca ks', 1, 247440, NULL, 1, '2025-08-09 14:31:50', '2025-08-09 14:31:50', NULL, 'pendente', '2025-08-09 18:31:50', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_kits
CREATE TABLE IF NOT EXISTS `produto_kits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_principal_id` int unsigned NOT NULL,
  `produto_item_id` int unsigned NOT NULL,
  `variacao_item_id` int unsigned DEFAULT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT '1.000',
  `preco_item` decimal(10,2) DEFAULT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL,
  `obrigatorio` tinyint(1) DEFAULT '1',
  `substituivel` tinyint(1) DEFAULT '0',
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto_principal` (`produto_principal_id`),
  KEY `idx_produto_item` (`produto_item_id`),
  KEY `idx_variacao_item` (`variacao_item_id`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_kits_produto_item` FOREIGN KEY (`produto_item_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kits_produto_principal` FOREIGN KEY (`produto_principal_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kits_variacao_item` FOREIGN KEY (`variacao_item_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_kits: ~10 rows (aproximadamente)
DELETE FROM `produto_kits`;
INSERT INTO `produto_kits` (`id`, `empresa_id`, `produto_principal_id`, `produto_item_id`, `variacao_item_id`, `quantidade`, `preco_item`, `desconto_percentual`, `obrigatorio`, `substituivel`, `ordem`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:19:50', '2025-08-09 14:26:48', '2025-08-09 14:26:48', 'pendente', '2025-08-09 18:26:48', NULL),
	(2, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:19:50', '2025-08-09 14:26:48', '2025-08-09 14:26:48', 'pendente', '2025-08-09 18:26:48', NULL),
	(3, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:26:49', '2025-08-09 14:32:28', '2025-08-09 14:32:28', 'pendente', '2025-08-09 18:32:29', NULL),
	(4, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:26:49', '2025-08-09 14:32:28', '2025-08-09 14:32:28', 'pendente', '2025-08-09 18:32:29', NULL),
	(5, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:32:29', '2025-08-09 14:41:23', '2025-08-09 14:41:23', 'pendente', '2025-08-09 18:41:23', NULL),
	(6, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:32:29', '2025-08-09 14:41:23', '2025-08-09 14:41:23', 'pendente', '2025-08-09 18:41:23', NULL),
	(7, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:41:23', '2025-08-09 14:44:47', '2025-08-09 14:44:47', 'pendente', '2025-08-09 18:44:47', NULL),
	(8, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:41:23', '2025-08-09 14:44:47', '2025-08-09 14:44:47', 'pendente', '2025-08-09 18:44:47', NULL),
	(9, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:44:47', '2025-08-09 14:51:41', '2025-08-09 14:51:41', 'pendente', '2025-08-09 18:51:41', NULL),
	(10, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:44:47', '2025-08-09 14:51:41', '2025-08-09 14:51:41', 'pendente', '2025-08-09 18:51:41', NULL),
	(11, 1, 7, 1, NULL, 1.000, 25.90, 0.00, 1, 0, 1, 1, '2025-08-09 14:51:41', '2025-08-09 14:51:41', NULL, 'pendente', '2025-08-09 14:51:41', NULL),
	(12, 1, 7, 2, NULL, 1.000, 4.50, 0.00, 1, 0, 2, 1, '2025-08-09 14:51:41', '2025-08-09 14:51:41', NULL, 'pendente', '2025-08-09 14:51:41', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_marcas
CREATE TABLE IF NOT EXISTS `produto_marcas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_marcas: ~2 rows (aproximadamente)
DELETE FROM `produto_marcas`;
INSERT INTO `produto_marcas` (`id`, `empresa_id`, `nome`, `descricao`, `logo`, `site`, `telefone`, `email`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 'Marca Própria', 'Produtos da casa', NULL, NULL, NULL, NULL, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL),
	(2, 1, 'Fornecedor Padrão', 'Produtos de fornecedores diversos', NULL, NULL, NULL, NULL, 1, '2025-08-08 07:51:51', '2025-08-08 07:51:51', NULL, 'pendente', '2025-08-08 07:51:51', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_movimentacoes
CREATE TABLE IF NOT EXISTS `produto_movimentacoes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `variacao_id` int unsigned DEFAULT NULL,
  `tipo` enum('entrada','saida','ajuste','venda','compra','devolucao','perda','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `valor_unitario` decimal(10,2) DEFAULT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `estoque_anterior` decimal(10,3) NOT NULL,
  `estoque_posterior` decimal(10,3) NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `documento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fornecedor_id` bigint unsigned DEFAULT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `data_movimento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_variacao` (`variacao_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_data` (`data_movimento`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  KEY `fk_movimentacoes_fornecedor` (`fornecedor_id`),
  KEY `fk_movimentacoes_cliente` (`cliente_id`),
  CONSTRAINT `fk_movimentacoes_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `pessoas` (`id`),
  CONSTRAINT `fk_movimentacoes_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `pessoas` (`id`),
  CONSTRAINT `fk_movimentacoes_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_movimentacoes_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_movimentacoes: ~4 rows (aproximadamente)
DELETE FROM `produto_movimentacoes`;
INSERT INTO `produto_movimentacoes` (`id`, `empresa_id`, `produto_id`, `variacao_id`, `tipo`, `quantidade`, `valor_unitario`, `valor_total`, `estoque_anterior`, `estoque_posterior`, `motivo`, `observacoes`, `documento`, `fornecedor_id`, `cliente_id`, `usuario_id`, `data_movimento`, `created_at`, `updated_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 3, NULL, 'entrada', 10.000, 25.50, 255.00, 15.000, 25.000, 'Compra inicial de estoque', NULL, NULL, NULL, NULL, NULL, '2025-08-09 03:42:22', '2025-08-09 03:42:22', '2025-08-09 03:42:22', 'pendente', '2025-08-09 03:42:22', NULL),
	(2, 1, 3, NULL, 'saida', 3.000, 25.50, 76.50, 25.000, 22.000, 'Venda para cliente', NULL, NULL, NULL, NULL, NULL, '2025-08-09 03:42:22', '2025-08-09 03:42:22', '2025-08-09 03:42:22', 'pendente', '2025-08-09 03:42:22', NULL),
	(3, 1, 3, NULL, 'entrada', 5.000, 25.50, 127.50, 22.000, 27.000, 'Reposição de estoque', NULL, NULL, NULL, NULL, NULL, '2025-08-09 03:42:22', '2025-08-09 03:42:22', '2025-08-09 03:42:22', 'pendente', '2025-08-09 03:42:22', NULL),
	(4, 1, 3, NULL, 'saida', 2.000, 25.50, 51.00, 27.000, 25.000, 'Venda online', NULL, NULL, NULL, NULL, NULL, '2025-08-09 03:42:22', '2025-08-09 03:42:22', '2025-08-09 03:42:22', 'pendente', '2025-08-09 03:42:22', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_precos_quantidade
CREATE TABLE IF NOT EXISTS `produto_precos_quantidade` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `variacao_id` int unsigned DEFAULT NULL,
  `quantidade_minima` decimal(10,3) NOT NULL,
  `quantidade_maxima` decimal(10,3) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_variacao` (`variacao_id`),
  KEY `idx_quantidade` (`quantidade_minima`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_precos_quantidade_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_precos_quantidade_variacao` FOREIGN KEY (`variacao_id`) REFERENCES `produto_variacoes_combinacoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_precos_quantidade: ~0 rows (aproximadamente)
DELETE FROM `produto_precos_quantidade`;

-- Copiando estrutura para tabela meufinanceiro.produto_relacionados
CREATE TABLE IF NOT EXISTS `produto_relacionados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `produto_relacionado_id` int unsigned NOT NULL,
  `tipo_relacao` enum('similar','complementar','acessorio','substituto','kit','cross-sell','up-sell') COLLATE utf8mb4_unicode_ci DEFAULT 'similar',
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_produto_relacionado` (`produto_id`,`produto_relacionado_id`,`tipo_relacao`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_relacionado` (`produto_relacionado_id`),
  KEY `idx_tipo` (`tipo_relacao`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_relacionados_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_relacionados_produto_rel` FOREIGN KEY (`produto_relacionado_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_relacionados: ~0 rows (aproximadamente)
DELETE FROM `produto_relacionados`;

-- Copiando estrutura para tabela meufinanceiro.produto_subcategorias
CREATE TABLE IF NOT EXISTS `produto_subcategorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `categoria_id` int unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_nome` (`nome`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_subcategorias_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `produto_categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_subcategorias: ~3 rows (aproximadamente)
DELETE FROM `produto_subcategorias`;
INSERT INTO `produto_subcategorias` (`id`, `empresa_id`, `categoria_id`, `parent_id`, `nome`, `descricao`, `slug`, `icone`, `ordem`, `ativo`, `created_at`, `updated_at`, `deleted_at`, `sync_status`, `sync_data`, `sync_hash`) VALUES
	(1, 1, 1, NULL, 'Pizzas Doces', NULL, 'pizzas-doces', NULL, 1, 1, '2025-08-09 00:47:17', '2025-08-09 00:47:17', NULL, 'pendente', '2025-08-09 04:47:17', NULL),
	(2, 1, 1, NULL, 'Pizzas Salgadas', NULL, 'pizzas-salgadas', NULL, 2, 1, '2025-08-09 00:47:17', '2025-08-09 00:47:17', NULL, 'pendente', '2025-08-09 04:47:17', NULL),
	(3, 1, 2, NULL, 'Refrigerantes', NULL, 'refrigerantes', NULL, 1, 1, '2025-08-09 00:47:17', '2025-08-09 00:47:17', NULL, 'pendente', '2025-08-09 04:47:17', NULL);

-- Copiando estrutura para tabela meufinanceiro.produto_variacoes_combinacoes
CREATE TABLE IF NOT EXISTS `produto_variacoes_combinacoes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int NOT NULL DEFAULT '0',
  `produto_id` int unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_barras` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `configuracoes` json NOT NULL,
  `preco_adicional` decimal(10,2) DEFAULT '0.00',
  `preco_final` decimal(10,2) NOT NULL,
  `estoque_proprio` decimal(10,3) DEFAULT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pendente','sincronizado','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sync_data` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sync_hash` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_sku` (`sku`),
  KEY `idx_codigo_barras` (`codigo_barras`),
  KEY `idx_sync` (`sync_status`,`sync_data`),
  CONSTRAINT `fk_variacoes_combinacoes_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela meufinanceiro.produto_variacoes_combinacoes: ~0 rows (aproximadamente)
DELETE FROM `produto_variacoes_combinacoes`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
