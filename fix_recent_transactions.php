<?php
// Corrigir view recent_transactions para incluir email
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Corrigindo View recent_transactions</h2>\n";

    // Corrigir a view para incluir o email do customer
    $view_sql = "CREATE OR REPLACE VIEW recent_transactions AS
    SELECT
        t.id,
        t.codigo_transacao as transaction_code,
        CASE t.tipo_origem
            WHEN 'nova_assinatura' THEN 'subscription'
            WHEN 'renovacao_assinatura' THEN 'renewal'
            WHEN 'comissao_afiliado' THEN 'commission'
            ELSE 'sale'
        END as type,
        t.valor_final as final_amount,
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
        CASE t.forma_pagamento
            WHEN 'cartao_credito' THEN 'credit_card'
            WHEN 'cartao_debito' THEN 'debit_card'
            ELSE COALESCE(t.forma_pagamento, 'pix')
        END as payment_method,
        COALESCE(t.cliente_nome, f.nome) as customer_name,
        COALESCE(t.cliente_email, f.email) as customer_email,
        t.created_at,
        COALESCE(e.razao_social, 'Merchant') as merchant_name,
        COALESCE(g.nome, 'Gateway Padrão') as gateway_name
    FROM afi_plan_transacoes t
        LEFT JOIN funforcli f ON f.id = t.cliente_id
        LEFT JOIN empresas e ON e.id = t.empresa_id
        LEFT JOIN afi_plan_gateways g ON g.id = t.gateway_id
    ORDER BY t.created_at DESC
    LIMIT 50";

    $pdo->exec($view_sql);
    echo "✅ View 'recent_transactions' corrigida<br>\n";

    // Testar a view
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM recent_transactions");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ View testada - {$result['count']} registros<br>\n";

    // Testar uma consulta específica
    $stmt = $pdo->query("SELECT id, transaction_code, customer_email, merchant_name FROM recent_transactions LIMIT 3");
    echo "<br><h3>📊 Dados de Exemplo:</h3>\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- ID: {$row['id']}, Code: {$row['transaction_code']}, Email: {$row['customer_email']}, Merchant: {$row['merchant_name']}<br>\n";
    }

    echo "<br><h3>🎉 View Corrigida com Sucesso!</h3>\n";
    echo "<p>O problema de collation foi resolvido removendo o JOIN problemático.</p>\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
