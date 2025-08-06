-- Script para criar e popular a tabela empresa_user_vinculos

-- 1. Criar a tabela pivot se não existir
CREATE TABLE IF NOT EXISTS `empresa_user_vinculos` (
    `id` int NOT NULL AUTO_INCREMENT,
    `empresa_id` int NOT NULL,
    `user_id` int NOT NULL,
    `perfil` enum(
        'proprietario',
        'administrador',
        'gerente',
        'colaborador'
    ) NOT NULL DEFAULT 'colaborador',
    `status` enum(
        'ativo',
        'inativo',
        'suspenso'
    ) NOT NULL DEFAULT 'ativo',
    `permissoes` json DEFAULT NULL,
    `data_vinculo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_empresa_user` (`empresa_id`, `user_id`),
    KEY `idx_empresa_id` (`empresa_id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 2. Limpar dados existentes (para teste)
DELETE FROM empresa_user_vinculos;

-- 3. Inserir vínculos de teste baseados nos usuários existentes
-- Assumindo que a empresa ID 1 existe
INSERT INTO
    empresa_user_vinculos (
        empresa_id,
        user_id,
        perfil,
        status,
        permissoes,
        data_vinculo
    )
VALUES (
        1,
        1,
        'colaborador',
        'ativo',
        '["produtos.view", "vendas.view"]',
        NOW()
    ),
    (
        1,
        2,
        'administrador',
        'ativo',
        '["produtos.view", "produtos.create", "vendas.view", "relatorios.view", "usuarios.manage"]',
        NOW()
    ),
    (
        1,
        3,
        'proprietario',
        'ativo',
        '["*"]',
        NOW()
    ),
    (
        1,
        5,
        'gerente',
        'ativo',
        '["produtos.view", "produtos.create", "vendas.view", "relatorios.view"]',
        NOW()
    ),
    (
        1,
        6,
        'colaborador',
        'ativo',
        '["produtos.view"]',
        NOW()
    );

-- 4. Verificar se os dados foram inseridos
SELECT ev.id, ev.empresa_id, ev.user_id, eu.nome, eu.email, ev.perfil, ev.status, ev.data_vinculo
FROM
    empresa_user_vinculos ev
    JOIN empresa_usuarios eu ON ev.user_id = eu.id
ORDER BY ev.id;