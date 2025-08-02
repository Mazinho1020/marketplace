-- =================================================================
-- SCRIPT DE INTEGRAÇÃO - SISTEMA ATUAL PARA MARKETPLACE ADMIN
-- =================================================================
-- Data de Criação: 2025-08-02
-- Autor: Sistema Marketplace
-- Descrição: Integra tabelas existentes com sistema admin
-- =================================================================

-- Desabilitar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 0;

-- =================================================================
-- 1. CRIAR VIEWS PARA COMPATIBILIDADE COM SISTEMA ADMIN
-- =================================================================

-- View Merchants (baseada na tabela funforcli + empresas)
CREATE OR REPLACE VIEW merchants AS
SELECT
    f.id,
    COALESCE(f.uuid, UUID()) as uuid,
    CONCAT(
        f.nome,
        COALESCE(CONCAT(' ', f.sobrenome), '')
    ) as name,
    f.email,
    f.telefone as phone,
    f.cpf_cnpj as document,
    CASE
        WHEN LENGTH(f.cpf_cnpj) <= 11 THEN 'cpf'
        ELSE 'cnpj'
    END as document_type,
    COALESCE(e.razao_social, f.nome) as business_name,
    CONCAT(
        COALESCE(e.logradouro, ''),
        ' ',
        COALESCE(e.numero, ''),
        ' ',
        COALESCE(e.bairro, ''),
        ' ',
        COALESCE(e.cidade, ''),
        ' ',
        COALESCE(e.uf, '')
    ) as business_address,
    f.chave_api as api_key,
    NULL as webhook_url,
    CASE
        WHEN f.ativo = 1
        AND f.status = 'ativo' THEN 'active'
        WHEN f.status = 'desativado' THEN 'inactive'
        ELSE 'pending'
    END as status,
    NULL as email_verified_at,
    f.created_at,
    f.updated_at,
    -- Campos de integração
    f.id as funforcli_id,
    f.empresa_id
FROM funforcli f
    LEFT JOIN empresas e ON e.id = f.empresa_id
WHERE
    f.tipo IN ('cliente', 'funcionario')
    OR f.plano_atual_id IS NOT NULL;

-- View Subscription Plans (baseada na tabela afi_plan_planos)
CREATE OR REPLACE VIEW subscription_plans AS
SELECT
    p.id,
    p.codigo as code,
    p.nome as name,
    p.descricao as description,
    p.preco_mensal as monthly_price,
    p.preco_anual as yearly_price,
    p.preco_vitalicio as lifetime_price,
    p.dias_trial as trial_days,
    p.recursos as features,
    p.limites as limits,
    p.ativo as is_active,
    p.created_at,
    p.updated_at,
    -- Campos de integração
    p.id as plano_id,
    p.empresa_id
FROM afi_plan_planos p
WHERE
    p.ativo = 1;

-- View Merchant Subscriptions (baseada na tabela afi_plan_assinaturas)
CREATE OR REPLACE VIEW merchant_subscriptions AS
SELECT
    a.id,
    a.funforcli_id as merchant_id,
    a.plano_id as plan_id,
    p.codigo as plan_code,
    p.nome as plan_name,
    CASE a.ciclo_cobranca
        WHEN 'mensal' THEN 'monthly'
        WHEN 'anual' THEN 'yearly'
        WHEN 'vitalicio' THEN 'lifetime'
        ELSE a.ciclo_cobranca
    END as billing_cycle,
    a.valor as amount,
    CASE a.status
        WHEN 'ativo' THEN 'active'
        WHEN 'suspenso' THEN 'suspended'
        WHEN 'expirado' THEN 'expired'
        WHEN 'cancelado' THEN 'cancelled'
        WHEN 'trial' THEN 'trial'
        ELSE a.status
    END as status,
    a.trial_expira_em as trial_ends_at,
    a.iniciado_em as starts_at,
    a.expira_em as ends_at,
    a.proxima_cobranca_em as next_billing_at,
    a.ultima_cobranca_em as last_billing_at,
    a.cancelado_em as cancelled_at,
    a.renovacao_automatica as auto_renew,
    a.created_at,
    a.updated_at,
    -- Campos de integração
    a.id as assinatura_id,
    a.empresa_id
FROM
    afi_plan_assinaturas a
    JOIN afi_plan_planos p ON p.id = a.plano_id;

-- View Payment Gateways (baseada na tabela afi_plan_gateways)
CREATE OR REPLACE VIEW payment_gateways AS
SELECT
    g.id,
    g.codigo as code,
    g.nome as name,
    g.provedor as provider,
    CASE g.ambiente
        WHEN 'sandbox' THEN 'sandbox'
        WHEN 'producao' THEN 'production'
        ELSE g.ambiente
    END as environment,
    g.ativo as is_active,
    g.credenciais as credentials,
    g.configuracoes as settings,
    g.url_webhook as webhook_url,
    g.created_at,
    g.updated_at,
    -- Campos de integração
    g.id as gateway_id,
    g.empresa_id
FROM afi_plan_gateways g;

-- View Payment Transactions (baseada na tabela afi_plan_transacoes)
CREATE OR REPLACE VIEW payment_transactions AS
SELECT
    t.id,
    t.uuid,
    t.codigo_transacao as transaction_code,
    t.cliente_id as merchant_id,
    t.gateway_id,
    t.gateway_transacao_id as gateway_transaction_id,
    CASE t.tipo_origem
        WHEN 'nova_assinatura' THEN 'subscription'
        WHEN 'renovacao_assinatura' THEN 'renewal'
        WHEN 'comissao_afiliado' THEN 'commission'
        ELSE 'sale'
    END as type,
    t.id_origem as origin_id,
    t.valor_original as original_amount,
    t.valor_desconto as discount_amount,
    t.valor_taxas as fee_amount,
    t.valor_final as final_amount,
    t.moeda as currency,
    CASE t.forma_pagamento
        WHEN 'cartao_credito' THEN 'credit_card'
        WHEN 'cartao_debito' THEN 'debit_card'
        ELSE t.forma_pagamento
    END as payment_method,
    CASE t.status
        WHEN 'rascunho' THEN 'draft'
        WHEN 'pendente' THEN 'pending'
        WHEN 'processando' THEN 'processing'
        WHEN 'aprovado' THEN 'completed'
        WHEN 'recusado' THEN 'failed'
        WHEN 'cancelado' THEN 'cancelled'
        WHEN 'estornado' THEN 'refunded'
        ELSE t.status
    END as status,
    t.gateway_status,
    t.cliente_nome as customer_name,
    t.cliente_email as customer_email,
    CAST(t.cliente_id AS CHAR) as customer_document,
    t.descricao as description,
    t.metadados as metadata,
    t.expira_em as expires_at,
    t.processado_em as processed_at,
    t.aprovado_em as completed_at,
    t.cancelado_em as cancelled_at,
    t.created_at,
    t.updated_at,
    -- Campos de integração
    t.id as transacao_id,
    t.empresa_id
FROM afi_plan_transacoes t;

-- View Affiliates (baseada na tabela funforcli com campos de afiliado)
CREATE OR REPLACE VIEW affiliates AS
SELECT
    f.id,
    COALESCE(f.uuid, UUID()) as uuid,
    COALESCE(
        f.afiliado_codigo,
        CONCAT('AF', f.id)
    ) as code,
    CONCAT(
        f.nome,
        COALESCE(CONCAT(' ', f.sobrenome), '')
    ) as name,
    f.email,
    f.telefone as phone,
    f.cpf_cnpj as document,
    JSON_OBJECT(
        'chave_pix',
        f.afiliado_chave_pix,
        'tipo_chave_pix',
        f.afiliado_tipo_chave_pix,
        'dados_bancarios',
        f.afiliado_dados_bancarios
    ) as bank_account,
    COALESCE(
        f.afiliado_taxa_comissao,
        10.00
    ) as commission_rate,
    CASE
        WHEN f.ativo = 1
        AND f.afiliado_codigo IS NOT NULL THEN 'approved'
        WHEN f.ativo = 0 THEN 'suspended'
        ELSE 'pending'
    END as status,
    f.created_at as approved_at,
    NULL as approved_by,
    0 as total_referrals,
    COALESCE(f.afiliado_total_vendas, 0) as total_sales,
    COALESCE(f.afiliado_total_comissoes, 0) as total_commissions,
    f.updated_at as last_sale_at,
    f.created_at,
    f.updated_at,
    -- Campos de integração
    f.id as funforcli_id,
    f.empresa_id
FROM funforcli f
WHERE
    f.afiliado_codigo IS NOT NULL
    OR f.afiliado_taxa_comissao IS NOT NULL;

-- View Affiliate Referrals (baseada em dados simulados)
CREATE OR REPLACE VIEW affiliate_referrals AS
SELECT
    CONCAT(a.id, '-', v.id) as id,
    a.id as affiliate_id,
    v.cliente_id as merchant_id,
    a.code as referral_code,
    '127.0.0.1' as ip_address,
    'Sistema Interno' as user_agent,
    'afiliado' as utm_source,
    'referral' as utm_medium,
    a.code as utm_campaign,
    CASE v.status
        WHEN 'confirmado' THEN 'converted'
        WHEN 'cancelado' THEN 'expired'
        ELSE 'pending'
    END as status,
    v.confirmado_em as converted_at,
    DATE_ADD(v.created_at, INTERVAL 30 DAY) as expires_at,
    v.created_at,
    v.updated_at,
    -- Campos de integração
    v.empresa_id
FROM
    affiliates a
    JOIN afi_plan_vendas v ON v.afiliado_id = a.funforcli_id;

-- View Affiliate Commissions (baseada na tabela afi_plan_vendas)
CREATE OR REPLACE VIEW affiliate_commissions AS
SELECT
    v.id,
    v.afiliado_id as affiliate_id,
    v.cliente_id as merchant_id,
    v.assinatura_id as subscription_id,
    NULL as transaction_id,
    v.valor_venda as sale_amount,
    v.taxa_comissao as commission_rate,
    v.valor_comissao as commission_amount,
    DATE_FORMAT(v.created_at, '%Y-%m') as period,
    CASE v.status
        WHEN 'pendente' THEN 'pending'
        WHEN 'confirmado' THEN 'approved'
        WHEN 'cancelado' THEN 'cancelled'
        WHEN 'estornado' THEN 'cancelled'
        ELSE v.status
    END as status,
    v.confirmado_em as approved_at,
    NULL as approved_by,
    NULL as paid_at,
    NULL as payment_transaction_id,
    NULL as payment_method,
    NULL as payment_data,
    v.created_at,
    v.updated_at,
    -- Campos de integração
    v.id as venda_id,
    NULL as comissao_id,
    v.empresa_id
FROM afi_plan_vendas v;

-- =================================================================
-- 2. VIEWS PARA ESTATÍSTICAS DO DASHBOARD ADMIN
-- =================================================================

-- View principal para estatísticas do dashboard
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT (
        SELECT COUNT(*)
        FROM merchants
        WHERE
            status = 'active'
    ) as total_merchants,
    (
        SELECT COUNT(*)
        FROM merchants
        WHERE
            status = 'active'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as new_merchants_month,
    (
        SELECT COUNT(*)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
    ) as active_subscriptions,
    (
        SELECT COALESCE(SUM(amount), 0)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
    ) as monthly_revenue,
    (
        SELECT COALESCE(SUM(amount), 0)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
            AND billing_cycle = 'monthly'
    ) as mrr,
    (
        SELECT COUNT(*)
        FROM affiliates
        WHERE
            status = 'approved'
    ) as total_affiliates,
    (
        SELECT COALESCE(SUM(total_sales), 0)
        FROM affiliates
        WHERE
            status = 'approved'
    ) as total_affiliate_sales,
    (
        SELECT COUNT(*)
        FROM payment_transactions
        WHERE
            status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as transactions_last_30_days,
    (
        SELECT COALESCE(SUM(final_amount), 0)
        FROM payment_transactions
        WHERE
            status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as revenue_last_30_days;

-- View para estatísticas de merchants
CREATE OR REPLACE VIEW merchant_stats AS
SELECT
    m.id,
    m.name,
    m.email,
    m.status,
    m.created_at,
    COALESCE(s.subscription_count, 0) as subscription_count,
    COALESCE(s.total_spent, 0) as total_spent,
    COALESCE(t.transaction_count, 0) as transaction_count,
    COALESCE(t.total_revenue, 0) as total_revenue,
    s.current_plan,
    s.current_status
FROM merchants m
    LEFT JOIN (
        SELECT
            merchant_id, COUNT(*) as subscription_count, SUM(amount) as total_spent, MAX(plan_name) as current_plan, MAX(status) as current_status
        FROM merchant_subscriptions
        GROUP BY
            merchant_id
    ) s ON s.merchant_id = m.id
    LEFT JOIN (
        SELECT
            merchant_id, COUNT(*) as transaction_count, SUM(final_amount) as total_revenue
        FROM payment_transactions
        WHERE
            status = 'completed'
        GROUP BY
            merchant_id
    ) t ON t.merchant_id = m.id;

-- View para estatísticas de afiliados
CREATE OR REPLACE VIEW affiliate_stats AS
SELECT
    a.id,
    a.name,
    a.email,
    a.code,
    a.status,
    a.commission_rate,
    a.total_sales,
    a.total_commissions,
    a.created_at,
    COALESCE(c.pending_commissions, 0) as pending_commissions,
    COALESCE(c.approved_commissions, 0) as approved_commissions,
    COALESCE(c.paid_commissions, 0) as paid_commissions,
    COALESCE(r.total_referrals, 0) as total_referrals,
    COALESCE(r.converted_referrals, 0) as converted_referrals
FROM affiliates a
    LEFT JOIN (
        SELECT
            affiliate_id, SUM(
                CASE
                    WHEN status = 'pending' THEN commission_amount
                    ELSE 0
                END
            ) as pending_commissions, SUM(
                CASE
                    WHEN status = 'approved' THEN commission_amount
                    ELSE 0
                END
            ) as approved_commissions, SUM(
                CASE
                    WHEN status = 'paid' THEN commission_amount
                    ELSE 0
                END
            ) as paid_commissions
        FROM affiliate_commissions
        GROUP BY
            affiliate_id
    ) c ON c.affiliate_id = a.id
    LEFT JOIN (
        SELECT
            affiliate_id, COUNT(*) as total_referrals, SUM(
                CASE
                    WHEN status = 'converted' THEN 1
                    ELSE 0
                END
            ) as converted_referrals
        FROM affiliate_referrals
        GROUP BY
            affiliate_id
    ) r ON r.affiliate_id = a.id;

-- View para transações recentes
CREATE OR REPLACE VIEW recent_transactions AS
SELECT
    t.id,
    t.transaction_code,
    t.type,
    t.final_amount,
    t.status,
    t.payment_method,
    t.customer_name,
    t.customer_email,
    t.created_at,
    m.name as merchant_name,
    g.name as gateway_name
FROM
    payment_transactions t
    LEFT JOIN merchants m ON m.id = t.merchant_id
    LEFT JOIN payment_gateways g ON g.id = t.gateway_id
ORDER BY t.created_at DESC
LIMIT 50;

-- View para receita mensal dos últimos 12 meses
CREATE OR REPLACE VIEW monthly_revenue_chart AS
SELECT
    DATE_FORMAT(created_at, '%Y-%m') as month,
    DATE_FORMAT(created_at, '%m/%Y') as month_label,
    COUNT(*) as transaction_count,
    SUM(final_amount) as total_revenue,
    AVG(final_amount) as avg_transaction_value
FROM payment_transactions
WHERE
    status = 'completed'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY
    DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC;

-- View para distribuição de planos
CREATE OR REPLACE VIEW plan_distribution AS
SELECT
    p.name as plan_name,
    p.code as plan_code,
    COUNT(s.id) as subscription_count,
    SUM(s.amount) as total_revenue,
    AVG(s.amount) as avg_price,
    COUNT(
        CASE
            WHEN s.status = 'active' THEN 1
        END
    ) as active_count,
    COUNT(
        CASE
            WHEN s.status = 'trial' THEN 1
        END
    ) as trial_count
FROM
    subscription_plans p
    LEFT JOIN merchant_subscriptions s ON s.plan_id = p.id
WHERE
    p.is_active = 1
GROUP BY
    p.id,
    p.name,
    p.code
ORDER BY subscription_count DESC;

-- View para top performers (afiliados)
CREATE OR REPLACE VIEW top_affiliate_performers AS
SELECT
    a.id,
    a.name,
    a.email,
    a.code,
    a.total_sales,
    a.total_commissions,
    a.commission_rate,
    COALESCE(c.commission_count, 0) as commission_count,
    COALESCE(c.avg_commission, 0) as avg_commission,
    COALESCE(r.conversion_rate, 0) as conversion_rate
FROM affiliates a
    LEFT JOIN (
        SELECT
            affiliate_id, COUNT(*) as commission_count, AVG(commission_amount) as avg_commission
        FROM affiliate_commissions
        WHERE
            status IN ('approved', 'paid')
        GROUP BY
            affiliate_id
    ) c ON c.affiliate_id = a.id
    LEFT JOIN (
        SELECT
            affiliate_id, CASE
                WHEN COUNT(*) = 0 THEN 0
                ELSE (
                    SUM(
                        CASE
                            WHEN status = 'converted' THEN 1
                            ELSE 0
                        END
                    ) / COUNT(*)
                ) * 100
            END as conversion_rate
        FROM affiliate_referrals
        GROUP BY
            affiliate_id
    ) r ON r.affiliate_id = a.id
WHERE
    a.status = 'approved'
ORDER BY a.total_sales DESC, a.total_commissions DESC
LIMIT 10;

-- Reabilitar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1;

-- =================================================================
-- 3. EXECUTAR VERIFICAÇÕES
-- =================================================================

-- Verificar se as views foram criadas corretamente
SELECT 'Views criadas com sucesso!' as message;

-- Mostrar estatísticas básicas
SELECT * FROM admin_dashboard_stats;

-- =================================================================
-- FIM DO SCRIPT
-- =================================================================

-- =================================================================
-- 3. EXECUTAR SINCRONIZAÇÃO INICIAL
-- =================================================================

-- Executar a sincronização
CALL SyncAllData ();

-- =================================================================
-- 4. CRIAR VIEWS PARA COMPATIBILIDADE
-- =================================================================

-- View para estatísticas do dashboard
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT (
        SELECT COUNT(*)
        FROM merchants
        WHERE
            status = 'active'
    ) as total_merchants,
    (
        SELECT COUNT(*)
        FROM merchants
        WHERE
            status = 'active'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as new_merchants_month,
    (
        SELECT COUNT(*)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
    ) as active_subscriptions,
    (
        SELECT COALESCE(SUM(amount), 0)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
    ) as monthly_revenue,
    (
        SELECT COUNT(*)
        FROM affiliates
        WHERE
            status = 'approved'
    ) as active_affiliates,
    (
        SELECT COALESCE(SUM(final_amount), 0)
        FROM payment_transactions
        WHERE
            status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as monthly_volume,
    (
        SELECT COUNT(*)
        FROM payment_transactions
        WHERE
            status = 'completed'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as monthly_transactions,
    (
        SELECT
            CASE
                WHEN COUNT(*) > 0 THEN (
                    COUNT(
                        CASE
                            WHEN status = 'active' THEN 1
                        END
                    ) / COUNT(*)
                ) * 100
                ELSE 0
            END
        FROM merchant_subscriptions
        WHERE
            created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as subscription_growth,
    (
        SELECT
            CASE
                WHEN COUNT(*) > 0 THEN (
                    COUNT(
                        CASE
                            WHEN status = 'converted' THEN 1
                        END
                    ) / COUNT(*)
                ) * 100
                ELSE 0
            END
        FROM affiliate_referrals
        WHERE
            created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) as conversion_rate,
    (
        SELECT COALESCE(SUM(amount), 0)
        FROM merchant_subscriptions
        WHERE
            status = 'active'
            AND billing_cycle = 'monthly'
    ) as mrr;

-- Reabilitar verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS = 1;

-- =================================================================
-- 5. INSERIR DADOS DE EXEMPLO (se necessário)
-- =================================================================

-- Inserir gateway padrão se não existir
INSERT IGNORE INTO
    payment_gateways (
        code,
        name,
        provider,
        environment,
        is_active,
        credentials,
        settings,
        webhook_url,
        empresa_id
    )
VALUES (
        'pix_default',
        'PIX Padrão',
        'pix',
        'production',
        TRUE,
        '{}',
        '{}',
        '/webhook/pix',
        1
    ),
    (
        'boleto_default',
        'Boleto Padrão',
        'boleto',
        'production',
        TRUE,
        '{}',
        '{}',
        '/webhook/boleto',
        1
    );

-- =================================================================
-- FIM DO SCRIPT
-- =================================================================