-- =================================================================
-- SISTEMA CORE DE PAGAMENTOS
-- Data: 2025-08-02 07:07:15
-- Autor: Mazinho1020
-- =================================================================

-- Limpar tabelas existentes (cuidado em produção!)
DROP TABLE IF EXISTS payment_events;

DROP TABLE IF EXISTS payment_webhooks;

DROP TABLE IF EXISTS payment_transactions;

DROP TABLE IF EXISTS payment_gateways;

-- ===== PAYMENT GATEWAYS =====
CREATE TABLE payment_gateways (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    empresa_id INT NOT NULL DEFAULT 1,

-- Identificação
provider ENUM(
    'safe2pay',
    'mercadopago',
    'pagseguro',
    'stripe',
    'outros'
) NOT NULL,
name VARCHAR(100) NOT NULL,

-- Configurações
environment ENUM('sandbox', 'production') DEFAULT 'sandbox',
is_active BOOLEAN DEFAULT TRUE,
priority INTEGER DEFAULT 1,

-- Credenciais (serão criptografadas)
credentials JSON NOT NULL,

-- Métodos suportados
supported_methods JSON NOT NULL,

-- URLs
webhook_url VARCHAR(500) NOT NULL,
success_url VARCHAR(500) NOT NULL,
cancel_url VARCHAR(500) NOT NULL,

-- Limites
min_amount DECIMAL(10, 2) DEFAULT 0.01,
max_amount DECIMAL(10, 2) DEFAULT 999999.99,

-- Taxas
fees_config JSON NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_empresa_provider (empresa_id, provider),
    INDEX idx_active (is_active),
    INDEX idx_priority (priority)
);

-- ===== PAYMENT TRANSACTIONS =====
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,

-- Identificação única
uuid CHAR(36) NOT NULL UNIQUE DEFAULT(UUID()),
transaction_code VARCHAR(50) NOT NULL UNIQUE,

-- Empresa e contexto
empresa_id INT NOT NULL DEFAULT 1, tenant_id VARCHAR(50) NULL,

-- Origem da transação
source_type ENUM(
    'pdv',
    'lancamento',
    'site_cliente',
    'site_planos',
    'subscription_new',
    'subscription_renewal',
    'affiliate_commission',
    'marketplace',
    'api_externa',
    'mobile_app',
    'webhook'
) NOT NULL,
source_id BIGINT NOT NULL,
source_reference VARCHAR(100) NULL,

-- Dados financeiros
amount_original DECIMAL(15, 2) NOT NULL,
amount_discount DECIMAL(15, 2) DEFAULT 0,
amount_fees DECIMAL(15, 2) DEFAULT 0,
amount_final DECIMAL(15, 2) NOT NULL,
currency_code CHAR(3) DEFAULT 'BRL',

-- Método de pagamento
payment_method ENUM(
    'cash',
    'pix',
    'credit_card',
    'debit_card',
    'bank_slip',
    'bank_transfer',
    'crypto',
    'digital_wallet'
) NOT NULL,

-- Parcelamento
installments INTEGER DEFAULT 1,
installment_amount DECIMAL(15, 2) NULL,

-- Gateway
gateway_provider ENUM(
    'safe2pay',
    'mercadopago',
    'pagseguro',
    'stripe',
    'outros'
) NULL,
gateway_transaction_id VARCHAR(200) NULL,
gateway_status VARCHAR(50) NULL,
gateway_raw_response JSON NULL,

-- Status
status ENUM(
    'draft',
    'pending',
    'processing',
    'approved',
    'declined',
    'cancelled',
    'refunded',
    'expired'
) DEFAULT 'draft',

-- Datas
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
expires_at TIMESTAMP NULL,
processed_at TIMESTAMP NULL,
approved_at TIMESTAMP NULL,
cancelled_at TIMESTAMP NULL,

-- Cliente
customer_name VARCHAR(200) NULL,
customer_email VARCHAR(200) NULL,
customer_document VARCHAR(20) NULL,
customer_phone VARCHAR(20) NULL,

-- URLs
success_url VARCHAR(500) NULL,
cancel_url VARCHAR(500) NULL,
notification_url VARCHAR(500) NULL,

-- Dados específicos
payment_data JSON NULL,

-- Metadados
description TEXT NULL,
internal_notes TEXT NULL,
metadata JSON NULL,

-- Controle
created_by_user_id INT NULL, version INTEGER DEFAULT 1,

-- Índices para performance
INDEX idx_empresa_source (empresa_id, source_type, source_id),
    INDEX idx_status_method (status, payment_method),
    INDEX idx_gateway (gateway_provider, gateway_transaction_id),
    INDEX idx_customer (customer_document, customer_email),
    INDEX idx_dates (created_at, expires_at),
    INDEX idx_transaction_code (transaction_code),
    INDEX idx_uuid (uuid)
);

-- ===== PAYMENT EVENTS =====
CREATE TABLE payment_events (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,

-- Tipo do evento
event_type ENUM(
    'created',
    'sent_to_gateway',
    'gateway_response',
    'webhook_received',
    'status_changed',
    'payment_approved',
    'payment_declined',
    'refund_requested',
    'expired',
    'manual_update'
) NOT NULL,

-- Dados do evento
event_data JSON NULL,
previous_status VARCHAR(50) NULL,
new_status VARCHAR(50) NULL,

-- Origem
triggered_by ENUM('system', 'user', 'gateway', 'webhook', 'cron') NOT NULL,
    user_id INT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_transaction (transaction_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (transaction_id) REFERENCES payment_transactions(id) ON DELETE CASCADE
);

-- ===== PAYMENT WEBHOOKS =====
CREATE TABLE payment_webhooks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    transaction_id BIGINT NULL,

-- Dados do webhook
gateway_provider VARCHAR(50) NOT NULL,
event_type VARCHAR(100) NOT NULL,
webhook_id VARCHAR(200) NULL,

-- Payload completo
raw_payload JSON NOT NULL, headers JSON NULL,

-- Processamento
processed BOOLEAN DEFAULT FALSE,
processed_at TIMESTAMP NULL,
processing_attempts INTEGER DEFAULT 0,
last_error TEXT NULL,

-- Verificação de segurança
signature_valid BOOLEAN NULL,
    ip_address VARCHAR(45) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_transaction (transaction_id),
    INDEX idx_gateway_event (gateway_provider, event_type),
    INDEX idx_processed (processed),
    
    FOREIGN KEY (transaction_id) REFERENCES payment_transactions(id) ON DELETE SET NULL
);