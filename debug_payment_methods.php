<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>🔍 Debug Payment Methods Query</h2>";

try {
    $results = DB::select("
        SELECT 
            payment_method as method,
            COUNT(*) as total_transactions,
            COALESCE(SUM(final_amount), 0) as total_amount,
            COALESCE(AVG(final_amount), 0) as avg_amount,
            ROUND(
                COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / COUNT(*), 2
            ) as success_rate
        FROM payment_transactions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY payment_method
        ORDER BY total_transactions DESC
    ");

    echo "<h3>✅ Resultados encontrados: " . count($results) . "</h3>";

    if (!empty($results)) {
        echo "<pre>";
        foreach ($results as $result) {
            echo "Method: " . ($result->method ?? 'NULL') . "\n";
            echo "Total Transactions: " . ($result->total_transactions ?? 'NULL') . "\n";
            echo "Total Amount: " . ($result->total_amount ?? 'NULL') . "\n";
            echo "Avg Amount: " . ($result->avg_amount ?? 'NULL') . "\n";
            echo "Success Rate: " . ($result->success_rate ?? 'NULL') . "\n";
            echo "---\n";
        }
        echo "</pre>";
    } else {
        echo "<p>❌ Nenhum resultado encontrado</p>";

        // Verificar se há dados na tabela
        $count = DB::selectOne("SELECT COUNT(*) as total FROM payment_transactions");
        echo "<p>Total de transações na tabela: " . $count->total . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
