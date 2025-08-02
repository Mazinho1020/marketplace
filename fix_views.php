<?php
// Criar views corrigidas sem colunas inexistentes
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Corrigindo Views Problemáticas</h2>\n";

    // 1. View Merchants corrigida
    $merchants_view = "CREATE OR REPLACE VIEW merchants AS
    SELECT
        f.id,
        UUID() as uuid,
        CONCAT(f.nome, COALESCE(CONCAT(' ', f.sobrenome), '')) as name,
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
            WHEN f.ativo = 1 THEN 'active'
            ELSE 'inactive'
        END as status,
        NULL as email_verified_at,
        f.created_at,
        f.updated_at,
        f.id as funforcli_id,
        f.empresa_id
    FROM funforcli f
        LEFT JOIN empresas e ON e.id = f.empresa_id
    WHERE f.tipo IN ('cliente', 'funcionario')
        OR f.plano_atual_id IS NOT NULL";

    $pdo->exec($merchants_view);
    echo "✅ View 'merchants' corrigida<br>\n";

    // 2. View Affiliates corrigida
    $affiliates_view = "CREATE OR REPLACE VIEW affiliates AS
    SELECT
        f.id,
        UUID() as uuid,
        COALESCE(f.afiliado_codigo, CONCAT('AF', f.id)) as code,
        CONCAT(f.nome, COALESCE(CONCAT(' ', f.sobrenome), '')) as name,
        f.email,
        f.telefone as phone,
        f.cpf_cnpj as document,
        JSON_OBJECT(
            'chave_pix', f.afiliado_chave_pix,
            'tipo_chave_pix', f.afiliado_tipo_chave_pix,
            'dados_bancarios', f.afiliado_dados_bancarios
        ) as bank_account,
        COALESCE(f.afiliado_taxa_comissao, 10.00) as commission_rate,
        CASE
            WHEN f.ativo = 1 AND f.afiliado_codigo IS NOT NULL THEN 'approved'
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
        f.id as funforcli_id,
        f.empresa_id
    FROM funforcli f
    WHERE f.afiliado_codigo IS NOT NULL
        OR f.afiliado_taxa_comissao IS NOT NULL";

    $pdo->exec($affiliates_view);
    echo "✅ View 'affiliates' corrigida<br>\n";

    // 3. Dropar e recriar payment_gateways e payment_transactions
    $pdo->exec("DROP VIEW IF EXISTS payment_gateways");
    $pdo->exec("DROP VIEW IF EXISTS payment_transactions");

    // 4. View Payment Gateways
    $gateways_view = "CREATE OR REPLACE VIEW payment_gateways AS
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
        g.id as gateway_id,
        g.empresa_id
    FROM afi_plan_gateways g";

    $pdo->exec($gateways_view);
    echo "✅ View 'payment_gateways' criada<br>\n";

    // 5. View Payment Transactions
    $transactions_view = "CREATE OR REPLACE VIEW payment_transactions AS
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
        NULL as customer_document,
        t.descricao as description,
        t.metadados as metadata,
        t.expira_em as expires_at,
        t.processado_em as processed_at,
        t.aprovado_em as completed_at,
        t.cancelado_em as cancelled_at,
        t.created_at,
        t.updated_at,
        t.id as transacao_id,
        t.empresa_id
    FROM afi_plan_transacoes t";

    $pdo->exec($transactions_view);
    echo "✅ View 'payment_transactions' criada<br>\n";

    // 6. Agora recriar as views que dependem das anteriores
    $dashboard_stats_view = "CREATE OR REPLACE VIEW admin_dashboard_stats AS
    SELECT 
        (SELECT COUNT(*) FROM merchants WHERE status = 'active') as total_merchants,
        (SELECT COUNT(*) FROM merchants WHERE status = 'active' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_merchants_month,
        (SELECT COUNT(*) FROM merchant_subscriptions WHERE status = 'ativo') as active_subscriptions,
        (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'ativo') as monthly_revenue,
        (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'ativo' AND billing_cycle = 'monthly') as mrr,
        (SELECT COUNT(*) FROM affiliates WHERE status = 'approved') as total_affiliates,
        (SELECT COALESCE(SUM(total_sales), 0) FROM affiliates WHERE status = 'approved') as total_affiliate_sales,
        (SELECT COUNT(*) FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as transactions_last_30_days,
        (SELECT COALESCE(SUM(final_amount), 0) FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as revenue_last_30_days";

    $pdo->exec($dashboard_stats_view);
    echo "✅ View 'admin_dashboard_stats' corrigida<br>\n";

    echo "<br><h3>🧪 Testando Views Corrigidas:</h3>\n";

    $test_views = ['merchants', 'affiliates', 'payment_transactions', 'admin_dashboard_stats'];

    foreach ($test_views as $view) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $view");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ $view ({$result['count']} registros)<br>\n";
        } catch (Exception $e) {
            echo "❌ $view - Erro: " . $e->getMessage() . "<br>\n";
        }
    }

    echo "<br><h3>🎉 Views Corrigidas com Sucesso!</h3>\n";
    echo "<p><a href='http://127.0.0.1:8000/admin/payments' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🧪 TESTAR PÁGINA DE PAGAMENTOS</a></p>\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
