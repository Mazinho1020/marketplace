# 🛠️ GUIA PARA CRIAR TABELAS DO MARKETPLACE

## 📋 Situação Atual

- ✅ Conexão com banco funcionando
- ✅ Database: `meufinanceiro`
- ❌ Faltam 3 tabelas: `marcas`, `empresas_marketplace`, `empresa_user_vinculos`

## 🎯 SOLUÇÃO RÁPIDA

### Opção 1: HeidiSQL / phpMyAdmin

1. Abra seu HeidiSQL ou phpMyAdmin
2. Conecte ao banco `meufinanceiro`
3. Execute cada script abaixo separadamente:

### 🏷️ Script 1: Criar tabela MARCAS

```sql
CREATE TABLE `marcas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identidade_visual` json DEFAULT NULL,
  `pessoa_fisica_id` bigint(20) unsigned NOT NULL,
  `status` enum('ativa','inativa','suspensa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `configuracoes` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marcas_slug_unique` (`slug`),
  KEY `marcas_pessoa_fisica_status_idx` (`pessoa_fisica_id`, `status`),
  CONSTRAINT `marcas_pessoa_fisica_id_foreign` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 🏢 Script 2: Criar tabela EMPRESAS_MARKETPLACE

```sql
CREATE TABLE `empresas_marketplace` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_fantasia` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca_id` bigint(20) unsigned DEFAULT NULL,
  `proprietario_id` bigint(20) unsigned NOT NULL,
  `endereco_cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_logradouro` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco_estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ativa','inativa','suspensa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `configuracoes` json DEFAULT NULL,
  `horario_funcionamento` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_marketplace_cnpj_unique` (`cnpj`),
  UNIQUE KEY `empresas_marketplace_slug_unique` (`slug`),
  KEY `empresas_marketplace_marca_status_idx` (`marca_id`, `status`),
  KEY `empresas_marketplace_proprietario_status_idx` (`proprietario_id`, `status`),
  CONSTRAINT `empresas_marketplace_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `empresas_marketplace_proprietario_id_foreign` FOREIGN KEY (`proprietario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 👥 Script 3: Criar tabela EMPRESA_USER_VINCULOS

```sql
CREATE TABLE `empresa_user_vinculos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `perfil` enum('proprietario','administrador','gerente','colaborador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colaborador',
  `status` enum('ativo','inativo','suspenso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `permissoes` json DEFAULT NULL,
  `data_vinculo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_user_vinculo_unique` (`empresa_id`, `user_id`),
  KEY `empresa_user_vinculos_user_status_idx` (`user_id`, `status`),
  CONSTRAINT `empresa_user_vinculos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_marketplace` (`id`) ON DELETE CASCADE,
  CONSTRAINT `empresa_user_vinculos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 📊 Script 4: Inserir dados de exemplo (OPCIONAL)

```sql
-- Marca de exemplo (ajuste o persona_fisica_id conforme necessário)
INSERT INTO `marcas` (`nome`, `slug`, `descricao`, `pessoa_fisica_id`, `status`, `identidade_visual`)
VALUES ('Pizzaria Tradição', 'pizzaria-tradicao', 'Rede de pizzarias tradicionais', 3, 'ativa', '{"cor_primaria": "#2ECC71", "cor_secundaria": "#27AE60"}');

-- Empresas de exemplo
INSERT INTO `empresas_marketplace` (`nome`, `slug`, `marca_id`, `proprietario_id`, `endereco_cidade`, `endereco_estado`, `telefone`, `status`)
VALUES
('Pizzaria Tradição Concórdia', 'pizzaria-tradicao-concordia', 1, 3, 'Concórdia', 'SC', '(47) 3442-1234', 'ativa'),
('Pizzaria Tradição Praça Central', 'pizzaria-tradicao-praca-central', 1, 3, 'Concórdia', 'SC', '(47) 3442-5678', 'ativa');
```

## ✅ APÓS CRIAR AS TABELAS

Execute este comando para testar:

```bash
php testar_tabelas.php
```

E depois acesse o painel:

```
http://localhost:8000/comerciantes/login
```

## 🔧 VERIFICAÇÃO FINAL

Se tudo der certo, você deve ver:

- ✅ marcas: JÁ EXISTE
- ✅ empresas_marketplace: JÁ EXISTE
- ✅ empresa_user_vinculos: JÁ EXISTE

**📝 NOTA:** Ajuste o `pessoa_fisica_id` no script de dados de exemplo para um ID válido da sua tabela `empresa_usuarios`.
