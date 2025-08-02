-- Criar tabelas do sistema de pagamento (afi_plan_)
-- Script para criar as tabelas básicas necessárias

SET FOREIGN_KEY_CHECKS = 0;

-- Tabela de configurações
CREATE TABLE IF NOT EXISTS afi_plan_configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    chave VARCHAR(100) NOT NULL,
    valor TEXT,
    tipo ENUM(
        'string',
        'number',
        'boolean',
        'json'
    ) DEFAULT 'string',
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_empresa_chave (empresa_id, chave)
);

-- Tabela de gateways de pagamento
CREATE TABLE IF NOT EXISTS afi_plan_gateways (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    provedor VARCHAR(50) NOT NULL,
    ambiente ENUM('sandbox', 'producao') DEFAULT 'sandbox',
    ativo TINYINT(1) DEFAULT 1,
    credenciais JSON,
    configuracoes JSON,
    url_webhook VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_empresa_codigo (empresa_id, codigo)
);

-- Tabela de planos
CREATE TABLE IF NOT EXISTS afi_plan_planos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    codigo VARCHAR(50) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco_mensal DECIMAL(10, 2) DEFAULT 0,
    preco_anual DECIMAL(10, 2) DEFAULT 0,
    preco_vitalicio DECIMAL(10, 2) DEFAULT 0,
    dias_trial INT DEFAULT 0,
    recursos JSON,
    limites JSON,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_empresa_codigo (empresa_id, codigo)
);

-- Tabela de assinaturas
CREATE TABLE IF NOT EXISTS afi_plan_assinaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    funforcli_id INT NOT NULL,
    plano_id INT NOT NULL,
    ciclo_cobranca ENUM(
        'mensal',
        'anual',
        'vitalicio'
    ) DEFAULT 'mensal',
    valor DECIMAL(10, 2) NOT NULL,
    status ENUM(
        'trial',
        'ativo',
        'suspenso',
        'expirado',
        'cancelado'
    ) DEFAULT 'trial',
    trial_expira_em TIMESTAMP NULL,
    iniciado_em TIMESTAMP NULL,
    expira_em TIMESTAMP NULL,
    proxima_cobranca_em TIMESTAMP NULL,
    ultima_cobranca_em TIMESTAMP NULL,
    cancelado_em TIMESTAMP NULL,
    renovacao_automatica TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_funforcli (funforcli_id),
    INDEX idx_plano (plano_id),
    INDEX idx_status (status)
);

-- Tabela de transações
CREATE TABLE IF NOT EXISTS afi_plan_transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL,
    empresa_id INT NOT NULL,
    codigo_transacao VARCHAR(100) NOT NULL,
    cliente_id INT,
    gateway_id INT,
    gateway_transacao_id VARCHAR(255),
    tipo_origem ENUM(
        'nova_assinatura',
        'renovacao_assinatura',
        'comissao_afiliado',
        'venda_avulsa'
    ) DEFAULT 'venda_avulsa',
    id_origem INT,
    valor_original DECIMAL(10, 2) NOT NULL,
    valor_desconto DECIMAL(10, 2) DEFAULT 0,
    valor_taxas DECIMAL(10, 2) DEFAULT 0,
    valor_final DECIMAL(10, 2) NOT NULL,
    moeda VARCHAR(3) DEFAULT 'BRL',
    forma_pagamento VARCHAR(50),
    status ENUM(
        'rascunho',
        'pendente',
        'processando',
        'aprovado',
        'recusado',
        'cancelado',
        'estornado'
    ) DEFAULT 'rascunho',
    gateway_status VARCHAR(50),
    cliente_nome VARCHAR(255),
    cliente_email VARCHAR(255),
    cliente_id VARCHAR(50),
    descricao TEXT,
    metadados JSON,
    expira_em TIMESTAMP NULL,
    processado_em TIMESTAMP NULL,
    aprovado_em TIMESTAMP NULL,
    cancelado_em TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_codigo_transacao (codigo_transacao),
    INDEX idx_status (status),
    INDEX idx_cliente (cliente_id),
    INDEX idx_gateway (gateway_id)
);

-- Tabela de vendas (para comissões de afiliados)
CREATE TABLE IF NOT EXISTS afi_plan_vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    afiliado_id INT NOT NULL,
    cliente_id INT NOT NULL,
    assinatura_id INT,
    transacao_id INT,
    valor_venda DECIMAL(10, 2) NOT NULL,
    taxa_comissao DECIMAL(5, 2) NOT NULL,
    valor_comissao DECIMAL(10, 2) NOT NULL,
    status ENUM(
        'pendente',
        'confirmado',
        'cancelado',
        'estornado'
    ) DEFAULT 'pendente',
    confirmado_em TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_afiliado (afiliado_id),
    INDEX idx_cliente (cliente_id),
    INDEX idx_status (status)
);

-- Inserir dados básicos de exemplo
INSERT IGNORE INTO
    afi_plan_gateways (
        empresa_id,
        codigo,
        nome,
        provedor,
        ambiente,
        ativo,
        credenciais,
        configuracoes
    )
VALUES (
        1,
        'pix_interno',
        'PIX Interno',
        'pix',
        'producao',
        1,
        '{}',
        '{}'
    ),
    (
        1,
        'boleto_interno',
        'Boleto Interno',
        'boleto',
        'producao',
        1,
        '{}',
        '{}'
    );

INSERT IGNORE INTO
    afi_plan_planos (
        empresa_id,
        codigo,
        nome,
        descricao,
        preco_mensal,
        preco_anual,
        ativo
    )
VALUES (
        1,
        'basico',
        'Plano Básico',
        'Plano básico para iniciantes',
        50.00,
        500.00,
        1
    ),
    (
        1,
        'premium',
        'Plano Premium',
        'Plano completo com todas as funcionalidades',
        100.00,
        1000.00,
        1
    ),
    (
        1,
        'enterprise',
        'Plano Enterprise',
        'Plano para grandes empresas',
        200.00,
        2000.00,
        1
    );

-- Inserir algumas assinaturas de exemplo para clientes existentes
INSERT IGNORE INTO
    afi_plan_assinaturas (
        empresa_id,
        funforcli_id,
        plano_id,
        ciclo_cobranca,
        valor,
        status,
        iniciado_em,
        expira_em
    )
SELECT 1, f.id, 1, -- Plano básico
    'mensal', 50.00, 'ativo', f.created_at, DATE_ADD(
        f.created_at, INTERVAL 1 MONTH
    )
FROM funforcli f
WHERE
    f.tipo = 'cliente'
    AND f.ativo = 1
LIMIT 3;

-- Inserir algumas transações de exemplo
INSERT IGNORE INTO
    afi_plan_transacoes (
        uuid,
        empresa_id,
        codigo_transacao,
        cliente_id,
        gateway_id,
        tipo_origem,
        valor_original,
        valor_final,
        forma_pagamento,
        status,
        cliente_nome,
        cliente_email,
        aprovado_em
    )
SELECT UUID(), 1, CONCAT(
        'TXN_', f.id, '_', DATE_FORMAT(NOW(), '%Y%m%d')
    ), f.id, 1, 'nova_assinatura', 50.00, 50.00, 'pix', 'aprovado', f.nome, f.email, NOW()
FROM funforcli f
WHERE
    f.tipo = 'cliente'
    AND f.ativo = 1
LIMIT 5;

SET FOREIGN_KEY_CHECKS = 1;