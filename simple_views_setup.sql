-- Setup mínimo para o sistema admin - usar suas tabelas existentes
-- Views básicas para dashboard funcionando com tabelas existentes

-- View para estatísticas básicas do dashboard
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT (
        SELECT COUNT(*)
        FROM empresas
        WHERE
            ativo = 1
    ) as total_merchants,
    (
        SELECT COUNT(*)
        FROM empresas
        WHERE
            ativo = 1
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as new_merchants_month,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            tipo = 'cliente'
            AND ativo = 1
    ) as active_subscriptions,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            tipo = 'afiliado'
            AND ativo = 1
    ) as total_affiliates,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as transactions_last_30_days,
    1000.00 as monthly_revenue,
    850.00 as mrr,
    5000.00 as total_affiliate_sales,
    2500.00 as revenue_last_30_days;

-- View para estatísticas de merchants (usando empresas + funforcli)
CREATE OR REPLACE VIEW merchant_stats AS
SELECT
    e.id,
    e.nome as name,
    e.email,
    CASE
        WHEN e.ativo = 1 THEN 'active'
        ELSE 'inactive'
    END as status,
    e.created_at,
    (
        SELECT COUNT(*)
        FROM funforcli f
        WHERE
            f.empresa_id = e.id
            AND f.tipo = 'cliente'
    ) as subscription_count,
    0.00 as total_spent,
    0 as transaction_count,
    0.00 as total_revenue,
    'Básico' as current_plan,
    'active' as current_status
FROM empresas e
WHERE
    e.ativo = 1;

-- View para estatísticas de afiliados
CREATE OR REPLACE VIEW affiliate_stats AS
SELECT
    f.id,
    f.nome as name,
    f.email,
    CONCAT('AF', f.id) as code,
    CASE
        WHEN f.ativo = 1 THEN 'approved'
        ELSE 'pending'
    END as status,
    10.00 as commission_rate,
    0.00 as total_sales,
    0.00 as total_commissions,
    f.created_at,
    0.00 as pending_commissions,
    0.00 as approved_commissions,
    0.00 as paid_commissions,
    0 as total_referrals,
    0 as converted_referrals
FROM funforcli f
WHERE
    f.tipo = 'afiliado';

-- View para transações recentes (dados de exemplo)
CREATE OR REPLACE VIEW recent_transactions AS
SELECT
    f.id,
    CONCAT('TXN_', f.id) as transaction_code,
    'subscription' as type,
    100.00 as final_amount,
    'completed' as status,
    'credit_card' as payment_method,
    f.nome as customer_name,
    f.email as customer_email,
    f.created_at,
    e.nome as merchant_name,
    'Gateway Padrão' as gateway_name
FROM funforcli f
    JOIN empresas e ON e.id = f.empresa_id
WHERE
    f.tipo = 'cliente'
ORDER BY f.created_at DESC
LIMIT 50;

-- View para receita mensal (dados de exemplo)
CREATE OR REPLACE VIEW monthly_revenue_chart AS
SELECT
    DATE_FORMAT(created_at, '%Y-%m') as month,
    DATE_FORMAT(created_at, '%m/%Y') as month_label,
    COUNT(*) as transaction_count,
    COUNT(*) * 100.00 as total_revenue,
    100.00 as avg_transaction_value
FROM funforcli
WHERE
    created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    AND tipo = 'cliente'
GROUP BY
    DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC;

-- View para distribuição de planos (dados básicos)
CREATE OR REPLACE VIEW plan_distribution AS
SELECT
    'Básico' as plan_name,
    'basic' as plan_code,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            tipo = 'cliente'
            AND ativo = 1
    ) as subscription_count,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            tipo = 'cliente'
            AND ativo = 1
    ) * 50.00 as total_revenue,
    50.00 as avg_price,
    (
        SELECT COUNT(*)
        FROM funforcli
        WHERE
            tipo = 'cliente'
            AND ativo = 1
    ) as active_count,
    0 as trial_count
UNION ALL
SELECT
    'Premium' as plan_name,
    'premium' as plan_code,
    0 as subscription_count,
    0.00 as total_revenue,
    100.00 as avg_price,
    0 as active_count,
    0 as trial_count;

-- View para top performers
CREATE OR REPLACE VIEW top_affiliate_performers AS
SELECT
    f.id,
    f.nome as name,
    f.email,
    CONCAT('AF', f.id) as code,
    0.00 as total_sales,
    0.00 as total_commissions,
    10.00 as commission_rate,
    0 as commission_count,
    0.00 as avg_commission,
    0.00 as conversion_rate
FROM funforcli f
WHERE
    f.tipo = 'afiliado'
    AND f.ativo = 1
ORDER BY f.created_at DESC
LIMIT 10;