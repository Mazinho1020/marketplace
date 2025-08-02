-- =================================================================
-- VIEWS PARA RELATÓRIOS E DASHBOARD
-- =================================================================

-- View: Dashboard Resumo
CREATE VIEW dashboard_summary AS
SELECT
    -- MRR Total
    (
        SELECT SUM(amount)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
            AND billing_cycle = 'monthly'
    ) + (
        SELECT SUM(amount) / 12
        FROM merchant_subscriptions
        WHERE
            status = 'active'
            AND billing_cycle = 'yearly'
    ) AS mrr_total,

-- Total de Assinantes
(
    SELECT COUNT(*)
    FROM merchant_subscriptions
    WHERE
        status = 'active'
) AS total_subscribers,

-- Revenue Hoje
(
    SELECT COALESCE(SUM(amount_final), 0)
    FROM payment_transactions
    WHERE
        DATE(created_at) = CURDATE()
        AND status = 'approved'
) AS revenue_today,

-- Total de Afiliados Ativos
(
    SELECT COUNT(*)
    FROM affiliates
    WHERE
        status = 'active'
) AS active_affiliates,

-- Churn Rate (últimos 30 dias)
(
    SELECT ROUND(
            (COUNT(*) * 100.0) / (
                SELECT COUNT(*)
                FROM merchant_subscriptions
                WHERE
                    status IN ('active', 'cancelled')
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ), 2
        )
    FROM merchant_subscriptions
    WHERE
        status = 'cancelled'
        AND cancelled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
) AS churn_rate,

-- Inadimplência
(
    SELECT ROUND(
            (COUNT(*) * 100.0) / (
                SELECT COUNT(*)
                FROM merchant_subscriptions
                WHERE
                    status IN ('active', 'suspended')
            ), 2
        )
    FROM merchant_subscriptions
    WHERE
        status = 'suspended'
) AS default_rate;

-- View: Planos Performance
CREATE VIEW plans_performance AS
SELECT
    sp.code,
    sp.name,
    sp.price_monthly,
    COUNT(ms.id) as total_subscriptions,
    COUNT(
        CASE
            WHEN ms.status = 'active' THEN 1
        END
    ) as active_subscriptions,
    COUNT(
        CASE
            WHEN ms.status = 'trial' THEN 1
        END
    ) as trial_subscriptions,
    SUM(
        CASE
            WHEN ms.status = 'active'
            AND ms.billing_cycle = 'monthly' THEN ms.amount
            ELSE 0
        END
    ) as monthly_revenue,
    SUM(
        CASE
            WHEN ms.status = 'active'
            AND ms.billing_cycle = 'yearly' THEN ms.amount
            ELSE 0
        END
    ) as yearly_revenue,
    ROUND(
        AVG(
            CASE
                WHEN ms.status = 'active' THEN ms.amount
            END
        ),
        2
    ) as avg_revenue_per_user
FROM
    subscription_plans sp
    LEFT JOIN merchant_subscriptions ms ON sp.id = ms.plan_id
GROUP BY
    sp.id,
    sp.code,
    sp.name,
    sp.price_monthly
ORDER BY active_subscriptions DESC;

-- View: Afiliados Performance
CREATE VIEW affiliates_performance AS
SELECT
    a.id,
    a.name,
    a.email,
    a.affiliate_code,
    a.tier,
    a.commission_rate,
    COUNT(asales.id) as total_sales,
    COUNT(
        CASE
            WHEN asales.status = 'confirmed' THEN 1
        END
    ) as confirmed_sales,
    SUM(
        CASE
            WHEN asales.status = 'confirmed' THEN asales.sale_amount
            ELSE 0
        END
    ) as total_revenue,
    SUM(
        CASE
            WHEN asales.status = 'confirmed' THEN asales.commission_amount
            ELSE 0
        END
    ) as total_commissions,
    SUM(
        CASE
            WHEN ac.status = 'paid' THEN ac.amount
            ELSE 0
        END
    ) as paid_commissions,
    SUM(
        CASE
            WHEN ac.status = 'approved' THEN ac.amount
            ELSE 0
        END
    ) as pending_commissions,
    ROUND(
        CASE
            WHEN COUNT(asales.id) > 0 THEN (
                COUNT(
                    CASE
                        WHEN asales.status = 'confirmed' THEN 1
                    END
                ) * 100.0
            ) / COUNT(asales.id)
            ELSE 0
        END,
        2
    ) as conversion_rate
FROM
    affiliates a
    LEFT JOIN affiliate_sales asales ON a.id = asales.affiliate_id
    LEFT JOIN affiliate_commissions ac ON asales.id = ac.sale_id
WHERE
    a.status = 'active'
GROUP BY
    a.id,
    a.name,
    a.email,
    a.affiliate_code,
    a.tier,
    a.commission_rate
ORDER BY total_revenue DESC;

-- View: Transações Recentes
CREATE VIEW recent_transactions AS
SELECT
    pt.id,
    pt.transaction_code,
    pt.source_type,
    pt.amount_final,
    pt.payment_method,
    pt.status,
    pt.customer_name,
    pt.customer_email,
    pt.gateway_provider,
    pt.created_at,
    pt.approved_at,
    CASE
        WHEN pt.source_type IN (
            'subscription_new',
            'subscription_renewal'
        ) THEN (
            SELECT CONCAT(
                    sp.name, ' - ', ms.billing_cycle
                )
            FROM
                merchant_subscriptions ms
                JOIN subscription_plans sp ON ms.plan_id = sp.id
            WHERE
                ms.id = pt.source_id
        )
        ELSE pt.description
    END as description
FROM payment_transactions pt
WHERE
    pt.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY pt.created_at DESC;

-- View: Métricas Mensais
CREATE VIEW monthly_metrics AS
SELECT
    DATE_FORMAT(created_at, '%Y-%m') as month_year,
    COUNT(*) as total_transactions,
    COUNT(
        CASE
            WHEN status = 'approved' THEN 1
        END
    ) as approved_transactions,
    COUNT(
        CASE
            WHEN status = 'declined' THEN 1
        END
    ) as declined_transactions,
    SUM(
        CASE
            WHEN status = 'approved' THEN amount_final
            ELSE 0
        END
    ) as total_revenue,
    SUM(
        CASE
            WHEN status = 'approved'
            AND source_type = 'subscription_new' THEN amount_final
            ELSE 0
        END
    ) as new_subscriptions_revenue,
    SUM(
        CASE
            WHEN status = 'approved'
            AND source_type = 'subscription_renewal' THEN amount_final
            ELSE 0
        END
    ) as renewal_revenue,
    ROUND(
        (
            COUNT(
                CASE
                    WHEN status = 'approved' THEN 1
                END
            ) * 100.0
        ) / COUNT(*),
        2
    ) as approval_rate
FROM payment_transactions
WHERE
    created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY
    DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month_year DESC;

-- View: Vencimentos Próximos
CREATE VIEW upcoming_renewals AS
SELECT
    ms.id,
    m.company_name,
    m.trading_name,
    m.email,
    sp.name as plan_name,
    ms.amount,
    ms.billing_cycle,
    ms.expires_at,
    ms.auto_renewal,
    DATEDIFF(ms.expires_at, NOW()) as days_to_expire,
    CASE
        WHEN DATEDIFF(ms.expires_at, NOW()) <= 0 THEN 'expired'
        WHEN DATEDIFF(ms.expires_at, NOW()) <= 3 THEN 'critical'
        WHEN DATEDIFF(ms.expires_at, NOW()) <= 7 THEN 'warning'
        ELSE 'normal'
    END as urgency_level
FROM
    merchant_subscriptions ms
    JOIN merchants m ON ms.merchant_id = m.id
    JOIN subscription_plans sp ON ms.plan_id = sp.id
WHERE
    ms.status = 'active'
    AND ms.expires_at IS NOT NULL
    AND DATEDIFF(ms.expires_at, NOW()) <= 30
ORDER BY ms.expires_at ASC;

-- View: Comissões por Período
CREATE VIEW commissions_by_period AS
SELECT
    ac.reference_period,
    COUNT(*) as total_commissions,
    COUNT(
        CASE
            WHEN ac.status = 'pending' THEN 1
        END
    ) as pending_commissions,
    COUNT(
        CASE
            WHEN ac.status = 'approved' THEN 1
        END
    ) as approved_commissions,
    COUNT(
        CASE
            WHEN ac.status = 'paid' THEN 1
        END
    ) as paid_commissions,
    SUM(ac.amount) as total_amount,
    SUM(
        CASE
            WHEN ac.status = 'pending' THEN ac.amount
            ELSE 0
        END
    ) as pending_amount,
    SUM(
        CASE
            WHEN ac.status = 'approved' THEN ac.amount
            ELSE 0
        END
    ) as approved_amount,
    SUM(
        CASE
            WHEN ac.status = 'paid' THEN ac.amount
            ELSE 0
        END
    ) as paid_amount
FROM affiliate_commissions ac
GROUP BY
    ac.reference_period
ORDER BY ac.reference_period DESC;