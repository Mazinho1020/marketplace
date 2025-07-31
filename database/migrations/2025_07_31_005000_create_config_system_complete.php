<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Executar o SQL do sistema de configurações baseado no instrucoes.php
        DB::unprepared("
            SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
            SET time_zone = '+00:00';
            SET FOREIGN_KEY_CHECKS=0;

            -- Tabela config_environments
            CREATE TABLE IF NOT EXISTS `config_environments` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `codigo` VARCHAR(50) NOT NULL COMMENT 'Código único do ambiente (ex: online, offline)',
              `nome` VARCHAR(100) NOT NULL COMMENT 'Nome de exibição do ambiente',
              `descricao` TEXT NULL COMMENT 'Descrição detalhada do ambiente',
              `is_producao` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica se é ambiente de produção',
              `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status do ambiente',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              INDEX `config_environments_empresa_id_index` (`empresa_id`),
              UNIQUE INDEX `config_environments_codigo_unique` (`empresa_id`, `codigo`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ambientes de execução do sistema';

            -- Tabela config_sites
            CREATE TABLE IF NOT EXISTS `config_sites` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `codigo` VARCHAR(50) NOT NULL COMMENT 'Código único do site (ex: sistema, pdv, fidelidade)',
              `nome` VARCHAR(100) NOT NULL COMMENT 'Nome de exibição do site',
              `descricao` TEXT NULL COMMENT 'Descrição do site',
              `base_url_padrao` VARCHAR(255) NULL COMMENT 'URL base padrão do site',
              `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status do site',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              INDEX `config_sites_empresa_id_index` (`empresa_id`),
              UNIQUE INDEX `config_sites_codigo_unique` (`empresa_id`, `codigo`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sites que compõem o marketplace';

            -- Tabela config_groups
            CREATE TABLE IF NOT EXISTS `config_groups` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `codigo` VARCHAR(50) NOT NULL COMMENT 'Código único do grupo',
              `nome` VARCHAR(100) NOT NULL COMMENT 'Nome de exibição do grupo',
              `descricao` TEXT NULL COMMENT 'Descrição do grupo',
              `grupo_pai_id` INT UNSIGNED NULL COMMENT 'ID do grupo pai para hierarquia',
              `icone_class` VARCHAR(50) NULL COMMENT 'Classe de ícone para interface',
              `ordem` INT NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição do grupo',
              `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status do grupo',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              INDEX `config_groups_empresa_id_index` (`empresa_id`),
              UNIQUE INDEX `config_groups_codigo_unique` (`empresa_id`, `codigo`),
              INDEX `config_groups_grupo_pai_id_foreign` (`grupo_pai_id`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
              CONSTRAINT `config_groups_grupo_pai_id_foreign`
                FOREIGN KEY (`grupo_pai_id`)
                REFERENCES `config_groups` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Grupos para organizar as configurações';

            -- Tabela config_definitions
            CREATE TABLE IF NOT EXISTS `config_definitions` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `chave` VARCHAR(100) NOT NULL COMMENT 'Nome da chave de configuração',
              `nome` VARCHAR(100) NOT NULL COMMENT 'Nome amigável da configuração',
              `descricao` TEXT NULL COMMENT 'Descrição da configuração',
              `tipo` ENUM('string', 'integer', 'float', 'boolean', 'array', 'json', 'url', 'email', 'password') NOT NULL DEFAULT 'string' COMMENT 'Tipo de dado da configuração',
              `grupo_id` INT UNSIGNED NULL COMMENT 'Grupo ao qual pertence',
              `valor_padrao` TEXT NULL COMMENT 'Valor padrão quando não definido',
              `obrigatorio` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Se a configuração é obrigatória',
              `min_length` INT NULL COMMENT 'Tamanho mínimo',
              `max_length` INT NULL COMMENT 'Tamanho máximo',
              `regex_validacao` VARCHAR(255) NULL COMMENT 'Regex para validação',
              `opcoes` TEXT NULL COMMENT 'Opções possíveis para seleção (JSON)',
              `editavel` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Se pode ser editado via interface',
              `avancado` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Se é uma configuração avançada',
              `ordem` INT NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição',
              `dica` TEXT NULL COMMENT 'Dica de ajuda na interface',
              `ajuda` TEXT NULL COMMENT 'Texto de ajuda detalhado',
              `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status da definição',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              INDEX `config_definitions_empresa_id_index` (`empresa_id`),
              INDEX `config_definitions_grupo_id_foreign` (`grupo_id`),
              UNIQUE INDEX `config_definitions_chave_unique` (`empresa_id`, `chave`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
              CONSTRAINT `config_definitions_grupo_id_foreign`
                FOREIGN KEY (`grupo_id`)
                REFERENCES `config_groups` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Define as configurações disponíveis no sistema';

            -- Tabela config_values
            CREATE TABLE IF NOT EXISTS `config_values` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `config_id` INT UNSIGNED NOT NULL COMMENT 'ID da definição da configuração',
              `site_id` INT UNSIGNED NULL COMMENT 'ID do site específico ou NULL para global',
              `ambiente_id` INT UNSIGNED NULL COMMENT 'ID do ambiente específico ou NULL para todos',
              `valor` TEXT NULL COMMENT 'Valor da configuração',
              `usuario_id` INT UNSIGNED NULL COMMENT 'ID do usuário que fez a última alteração',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `config_values_unique` (`empresa_id`, `config_id`, `site_id`, `ambiente_id`),
              INDEX `config_values_config_id_foreign` (`config_id`),
              INDEX `config_values_site_id_foreign` (`site_id`),
              INDEX `config_values_ambiente_id_foreign` (`ambiente_id`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
              CONSTRAINT `config_values_config_id_foreign`
                FOREIGN KEY (`config_id`)
                REFERENCES `config_definitions` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `config_values_site_id_foreign`
                FOREIGN KEY (`site_id`)
                REFERENCES `config_sites` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `config_values_ambiente_id_foreign`
                FOREIGN KEY (`ambiente_id`)
                REFERENCES `config_environments` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Valores das configurações por site e ambiente';

            -- Tabela config_history
            CREATE TABLE IF NOT EXISTS `config_history` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa',
              `config_id` INT UNSIGNED NOT NULL COMMENT 'ID da configuração',
              `site_id` INT UNSIGNED NULL COMMENT 'ID do site',
              `ambiente_id` INT UNSIGNED NULL COMMENT 'ID do ambiente',
              `acao` ENUM('create', 'update', 'delete') NOT NULL COMMENT 'Ação realizada',
              `valor_anterior` TEXT NULL COMMENT 'Valor anterior',
              `valor_novo` TEXT NULL COMMENT 'Novo valor',
              `usuario_id` INT UNSIGNED NULL COMMENT 'ID do usuário que fez a alteração',
              `usuario_nome` VARCHAR(100) NULL COMMENT 'Nome do usuário',
              `ip` VARCHAR(45) NULL COMMENT 'IP do usuário',
              `user_agent` TEXT NULL COMMENT 'User-Agent do navegador',
              `contexto_info` TEXT NULL COMMENT 'Informações de contexto',
              `sync_hash` VARCHAR(64) NULL COMMENT 'Hash para sincronização',
              `sync_status` ENUM('pending', 'synced', 'error') NOT NULL DEFAULT 'pending' COMMENT 'Status da sincronização',
              `sync_data` TIMESTAMP NULL COMMENT 'Data da última sincronização',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` TIMESTAMP NULL,
              PRIMARY KEY (`id`),
              INDEX `config_history_empresa_id_index` (`empresa_id`),
              INDEX `config_history_config_id_index` (`config_id`),
              INDEX `config_history_usuario_id_index` (`usuario_id`),
              FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
              FOREIGN KEY (`config_id`) REFERENCES `config_definitions` (`id`) ON DELETE CASCADE,
              FOREIGN KEY (`site_id`) REFERENCES `config_sites` (`id`) ON DELETE SET NULL,
              FOREIGN KEY (`ambiente_id`) REFERENCES `config_environments` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de alterações nas configurações';

            SET FOREIGN_KEY_CHECKS=1;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            SET FOREIGN_KEY_CHECKS=0;
            DROP TABLE IF EXISTS config_history;
            DROP TABLE IF EXISTS config_values;
            DROP TABLE IF EXISTS config_definitions;
            DROP TABLE IF EXISTS config_groups;
            DROP TABLE IF EXISTS config_sites;
            DROP TABLE IF EXISTS config_environments;
            SET FOREIGN_KEY_CHECKS=1;
        ");
    }
};
