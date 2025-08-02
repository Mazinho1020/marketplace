<?php
// Criar views uma por uma
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Criando Views Individuais</h2>\n";

    // 1. View dashboard stats
    $query1 = "CREATE OR REPLACE VIEW admin_dashboard_stats AS
    SELECT 
        (SELECT COUNT(*) FROM empresas WHERE ativo = 1) as total_merchants,
        (SELECT COUNT(*) FROM empresas WHERE ativo = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_merchants_month,
        (SELECT COUNT(*) FROM funforcli WHERE tipo = 'cliente' AND ativo = 1) as active_subscriptions,
        (SELECT COUNT(*) FROM funforcli WHERE tipo = 'afiliado' AND ativo = 1) as total_affiliates,
        (SELECT COUNT(*) FROM funforcli WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as transactions_last_30_days,
        1000.00 as monthly_revenue,
        850.00 as mrr,
        5000.00 as total_affiliate_sales,
        2500.00 as revenue_last_30_days";

    $pdo->exec($query1);
    echo "✅ admin_dashboard_stats criada<br>\n";

    // 2. View merchant stats
    $query2 = "CREATE OR REPLACE VIEW merchant_stats AS
    SELECT 
        e.id,
        e.nome as name,
        e.email,
        CASE WHEN e.ativo = 1 THEN 'active' ELSE 'inactive' END as status,
        e.created_at,
        COALESCE((SELECT COUNT(*) FROM funforcli f WHERE f.empresa_id = e.id AND f.tipo = 'cliente'), 0) as subscription_count,
        0.00 as total_spent,
        0 as transaction_count,
        0.00 as total_revenue,
        'Básico' as current_plan,
        'active' as current_status
    FROM empresas e
    WHERE e.ativo = 1";

    $pdo->exec($query2);
    echo "✅ merchant_stats criada<br>\n";

    // 3. View affiliate stats
    $query3 = "CREATE OR REPLACE VIEW affiliate_stats AS
    SELECT 
        f.id,
        f.nome as name,
        f.email,
        CONCAT('AF', f.id) as code,
        CASE WHEN f.ativo = 1 THEN 'approved' ELSE 'pending' END as status,
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
    WHERE f.tipo = 'afiliado'";

    $pdo->exec($query3);
    echo "✅ affiliate_stats criada<br>\n";

    // Testar as views
    echo "<br><h3>🧪 Testando Views:</h3>\n";

    $stmt = $pdo->query("SELECT * FROM admin_dashboard_stats");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📊 Dashboard Stats:<br>\n";
    echo "- Merchants: {$stats['total_merchants']}<br>\n";
    echo "- Afiliados: {$stats['total_affiliates']}<br>\n";
    echo "- Assinaturas Ativas: {$stats['active_subscriptions']}<br>\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM merchant_stats");
    $merchants = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<br>🏢 Merchants: {$merchants['total']} empresas ativas<br>\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM affiliate_stats");
    $affiliates = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "🤝 Afiliados: {$affiliates['total']} afiliados<br>\n";

    echo "<br><h3>🎉 Sistema Configurado!</h3>\n";
    echo "<p><a href='http://127.0.0.1:8000/admin' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Admin Dashboard</a></p>\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
