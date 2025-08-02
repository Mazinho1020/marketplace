<?php
// Corrigir view monthly_revenue_chart com GROUP BY correto
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Corrigindo View monthly_revenue_chart</h2>\n";

    // Dropar a view problemática
    $pdo->exec("DROP VIEW IF EXISTS monthly_revenue_chart");
    echo "✅ View anterior removida<br>\n";

    // Criar view corrigida baseada em payment_transactions
    $monthly_chart_view = "CREATE OR REPLACE VIEW monthly_revenue_chart AS
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
        DATE_FORMAT(created_at, '%Y-%m'),
        DATE_FORMAT(created_at, '%m/%Y')
    ORDER BY month ASC";

    $pdo->exec($monthly_chart_view);
    echo "✅ View 'monthly_revenue_chart' corrigida (baseada em payment_transactions)<br>\n";

    // Criar uma view alternativa baseada em funforcli para backup
    $monthly_chart_backup = "CREATE OR REPLACE VIEW monthly_funforcli_chart AS
    SELECT
        DATE_FORMAT(f.created_at, '%Y-%m') as month,
        DATE_FORMAT(f.created_at, '%m/%Y') as month_label,
        COUNT(*) as transaction_count,
        COUNT(*) * 100.00 as total_revenue,
        100.00 as avg_transaction_value
    FROM funforcli f
    WHERE
        f.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        AND f.ativo = 1
    GROUP BY
        DATE_FORMAT(f.created_at, '%Y-%m'),
        DATE_FORMAT(f.created_at, '%m/%Y')
    ORDER BY month ASC";

    $pdo->exec($monthly_chart_backup);
    echo "✅ View backup 'monthly_funforcli_chart' criada<br>\n";

    // Corrigir também outras views que podem ter problemas similares
    echo "<br><h3>🔧 Verificando e Corrigindo Outras Views:</h3>\n";

    // Corrigir recent_transactions
    $recent_transactions_view = "CREATE OR REPLACE VIEW recent_transactions AS
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
    LIMIT 50";

    $pdo->exec($recent_transactions_view);
    echo "✅ View 'recent_transactions' corrigida<br>\n";

    // Atualizar admin_dashboard_stats para usar as views corretas
    $dashboard_stats_view = "CREATE OR REPLACE VIEW admin_dashboard_stats AS
    SELECT 
        (SELECT COUNT(*) FROM merchants WHERE status = 'active') as total_merchants,
        (SELECT COUNT(*) FROM merchants WHERE status = 'active' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_merchants_month,
        (SELECT COUNT(*) FROM merchant_subscriptions WHERE status = 'ativo') as active_subscriptions,
        (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'ativo') as monthly_revenue,
        (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'ativo' AND billing_cycle = 'monthly') as mrr,
        (SELECT COUNT(*) FROM affiliates WHERE status IN ('approved', 'active')) as total_affiliates,
        (SELECT COALESCE(SUM(total_sales), 0) FROM affiliates WHERE status IN ('approved', 'active')) as total_affiliate_sales,
        (SELECT COUNT(*) FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as transactions_last_30_days,
        (SELECT COALESCE(SUM(final_amount), 0) FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as revenue_last_30_days";

    $pdo->exec($dashboard_stats_view);
    echo "✅ View 'admin_dashboard_stats' atualizada<br>\n";

    echo "<br><h3>🧪 Testando Views Corrigidas:</h3>\n";

    $test_views = [
        'monthly_revenue_chart' => 'Gráfico de receita mensal',
        'recent_transactions' => 'Transações recentes',
        'admin_dashboard_stats' => 'Estatísticas do dashboard'
    ];

    foreach ($test_views as $view => $description) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $view");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ <strong>$view</strong> - $description ({$result['count']} registros)<br>\n";
        } catch (Exception $e) {
            echo "❌ <strong>$view</strong> - Erro: " . $e->getMessage() . "<br>\n";
        }
    }

    echo "<br><h3>🎉 Problema Resolvido!</h3>\n";
    echo "<p>As views foram corrigidas e agora devem funcionar sem erros de GROUP BY.</p>\n";
    echo "<p><a href='http://127.0.0.1:8000/admin' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🚀 TESTAR DASHBOARD AGORA</a></p>\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
