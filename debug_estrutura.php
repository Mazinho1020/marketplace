<?php
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'meufinanceiro',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== ANALISANDO ESTRUTURA DOS DADOS ===\n\n";

try {
    // Verificar estrutura das tabelas de backup
    echo "1. ESTRUTURA DA TABELA lancamentos_backup:\n";
    $columns = DB::select("DESCRIBE lancamentos_backup");
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
    
    echo "\n2. AMOSTRA DE DADOS lancamentos_backup:\n";
    $sample = DB::table('lancamentos_backup')->first();
    if ($sample) {
        foreach ((array) $sample as $field => $value) {
            echo "   $field: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    }
    
    echo "\n3. VERIFICANDO SQL OTIMIZADO:\n";
    $sqlFile = 'lancamentos_otimizado.sql';
    if (file_exists($sqlFile)) {
        $content = file_get_contents($sqlFile);
        $lines = explode("\n", $content);
        $createTableLines = array_filter($lines, function($line) {
            return str_contains(strtoupper($line), 'CREATE TABLE');
        });
        
        foreach ($createTableLines as $line) {
            echo "   Encontrado: " . trim($line) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
