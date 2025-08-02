-- =================================================================
-- SISTEMA DE AFILIADOS
-- =================================================================

-- ===== AFFILIATES =====
CREATE TABLE affiliates ( id BIGINT PRIMARY KEY AUTO_INCREMENT,

-- Dados pessoais
name VARCHAR(200) NOT NULL,
email VARCHAR(200) NOT NULL UNIQUE,
phone VARCHAR(20) NULL,
document VARCHAR(20) NOT NULL UNIQUE, -- CPF/CNPJ

-- Endereço
address_street VARCHAR(300) NULL,
address_number VARCHAR(20) NULL,
address_complement VARCHAR(100) NULL,
address_neighborhood VARCHAR(100) NULL,
address_city VARCHAR(100) NULL,
address_state CHAR(2) NULL,
address_zipcode VARCHAR(10) NULL,

-- Afiliação
affiliate_code VARCHAR(20) NOT NULL UNIQUE,
tier ENUM(
    'affiliate',
    'bronze',
    'silver',
    'gold'
) DEFAULT 'affiliate',
commission_rate DECIMAL(5, 2) DEFAULT 20.00, -- Percentual

-- Dados bancários
bank_code VARCHAR(10) NULL,
bank_name VARCHAR(100) NULL,
account_type ENUM('checking', 'savings') NULL,
account_number VARCHAR(20) NULL,
account_digit VARCHAR(5) NULL,
agency_number VARCHAR(10) NULL,
agency_digit VARCHAR(2) NULL,
pix_key VARCHAR(200) NULL,
pix_key_type ENUM(
    'cpf',
    'cnpj',
    'email',
    'phone',
    'random'
) NULL,

-- Estatísticas
total_sales DECIMAL(15, 2) DEFAULT 0,
total_commissions DECIMAL(15, 2) DEFAULT 0,
total_paid DECIMAL(15, 2) DEFAULT 0,

-- Status
status ENUM(
    'active',
    'inactive',
    'suspended',
    'pending_approval'
) DEFAULT 'pending_approval',
approved_at TIMESTAMP NULL,
approved_by INT NULL,

-- Controle
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_document (document),
    INDEX idx_affiliate_code (affiliate_code),
    INDEX idx_status (status),
    INDEX idx_tier (tier)
);

-- ===== AFFILIATE SALES =====
CREATE TABLE affiliate_sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    affiliate_id BIGINT NOT NULL,
    merchant_id BIGINT NOT NULL,
    subscription_id BIGINT NOT NULL,

-- Dados da venda
plan_code VARCHAR(50) NOT NULL,
billing_cycle ENUM(
    'monthly',
    'yearly',
    'lifetime'
) NOT NULL,
sale_amount DECIMAL(10, 2) NOT NULL,

-- Comissão
commission_rate DECIMAL(5, 2) NOT NULL,
commission_amount DECIMAL(10, 2) NOT NULL,

-- Status
status ENUM(
    'pending',
    'confirmed',
    'cancelled',
    'refunded'
) DEFAULT 'pending',
confirmed_at TIMESTAMP NULL,

-- Dados do cliente
customer_name VARCHAR(200) NOT NULL,
    customer_email VARCHAR(200) NOT NULL,
    customer_phone VARCHAR(20) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_affiliate (affiliate_id),
    INDEX idx_merchant (merchant_id),
    INDEX idx_subscription (subscription_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (affiliate_id) REFERENCES affiliates(id) ON DELETE CASCADE,
    FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES merchant_subscriptions(id) ON DELETE CASCADE
);

-- ===== AFFILIATE COMMISSIONS =====
CREATE TABLE affiliate_commissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    affiliate_id BIGINT NOT NULL,
    sale_id BIGINT NOT NULL,

-- Dados da comissão
amount DECIMAL(10, 2) NOT NULL,
rate_applied DECIMAL(5, 2) NOT NULL,
reference_period VARCHAR(7) NOT NULL, -- YYYY-MM

-- Status de pagamento
status ENUM(
    'pending',
    'approved',
    'paid',
    'cancelled'
) DEFAULT 'pending',
approved_at TIMESTAMP NULL,
approved_by INT NULL,
paid_at TIMESTAMP NULL,

-- Dados do pagamento
payment_method ENUM('pix', 'ted', 'bank_transfer') NULL,
    payment_transaction_id BIGINT NULL,
    payment_reference VARCHAR(100) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_affiliate (affiliate_id),
    INDEX idx_sale (sale_id),
    INDEX idx_status (status),
    INDEX idx_reference_period (reference_period),
    INDEX idx_payment_transaction (payment_transaction_id),
    
    FOREIGN KEY (affiliate_id) REFERENCES affiliates(id) ON DELETE CASCADE,
    FOREIGN KEY (sale_id) REFERENCES affiliate_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_transaction_id) REFERENCES payment_transactions(id) ON DELETE SET NULL
);