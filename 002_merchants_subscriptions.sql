-- =================================================================
-- SISTEMA DE COMERCIANTES E ASSINATURAS
-- =================================================================

-- ===== MERCHANTS (COMERCIANTES) =====
CREATE TABLE merchants ( id BIGINT PRIMARY KEY AUTO_INCREMENT,

-- Dados básicos
company_name VARCHAR(200) NOT NULL,
trading_name VARCHAR(200) NULL,
document VARCHAR(20) NOT NULL UNIQUE, -- CNPJ
email VARCHAR(200) NOT NULL UNIQUE,
phone VARCHAR(20) NULL,

-- Endereço
address_street VARCHAR(300) NULL,
address_number VARCHAR(20) NULL,
address_complement VARCHAR(100) NULL,
address_neighborhood VARCHAR(100) NULL,
address_city VARCHAR(100) NULL,
address_state CHAR(2) NULL,
address_zipcode VARCHAR(10) NULL,

-- Status
status ENUM(
    'active',
    'inactive',
    'suspended',
    'trial'
) DEFAULT 'trial',

-- Dados técnicos
license_key VARCHAR(100) UNIQUE NULL,
api_key VARCHAR(100) UNIQUE NULL,

-- Controle
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_document (document),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_license_key (license_key)
);

-- ===== SUBSCRIPTION PLANS =====
CREATE TABLE subscription_plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

-- Identificação
code VARCHAR(50) NOT NULL UNIQUE,
name VARCHAR(100) NOT NULL,
description TEXT NULL,

-- Preços
price_monthly DECIMAL(10, 2) NOT NULL DEFAULT 0,
price_yearly DECIMAL(10, 2) NOT NULL DEFAULT 0,
price_lifetime DECIMAL(10, 2) NOT NULL DEFAULT 0,

-- Features e limites
features JSON NOT NULL, -- ["financeiro", "pdv", "delivery"]
limits JSON NOT NULL, -- {"users": 10, "companies": 1}

-- Configurações
trial_days INTEGER DEFAULT 7,
is_active BOOLEAN DEFAULT TRUE,
sort_order INTEGER DEFAULT 0,

-- Metadados
metadata JSON NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
);

-- ===== MERCHANT SUBSCRIPTIONS =====
CREATE TABLE merchant_subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    merchant_id BIGINT NOT NULL,
    plan_id BIGINT NOT NULL,

-- Dados do plano (snapshot)
plan_code VARCHAR(50) NOT NULL,
plan_name VARCHAR(100) NOT NULL,
features JSON NOT NULL,
limits JSON NOT NULL,

-- Cobrança
billing_cycle ENUM(
    'monthly',
    'yearly',
    'lifetime'
) NOT NULL,
amount DECIMAL(10, 2) NOT NULL,
currency CHAR(3) DEFAULT 'BRL',

-- Status e datas
status ENUM(
    'active',
    'suspended',
    'expired',
    'cancelled',
    'trial'
) DEFAULT 'trial',
started_at TIMESTAMP NOT NULL,
expires_at TIMESTAMP NULL,
cancelled_at TIMESTAMP NULL,

-- Trial
trial_ends_at TIMESTAMP NULL,

-- Controle de renovação
auto_renewal BOOLEAN DEFAULT TRUE,
last_payment_at TIMESTAMP NULL,
next_payment_at TIMESTAMP NULL,

-- Dados do gateway
gateway_customer_id VARCHAR(200) NULL,
    gateway_subscription_id VARCHAR(200) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_merchant_status (merchant_id, status),
    INDEX idx_plan (plan_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_next_payment (next_payment_at),
    
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);