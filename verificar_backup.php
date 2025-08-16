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

echo "=== VERIFICANDO TABELAS DE BACKUP ===\n\n";

try {
    $tables = DB::select("SHOW TABLES LIKE '%backup%'");
    
    if (empty($tables)) {
        echo "❌ Nenhuma tabela de backup encontrada\n";
        
        // Verificar todas as tabelas
        echo "\n=== TABELAS EXISTENTES ===\n";
        $allTables = DB::select("SHOW TABLES");
        foreach ($allTables as $table) {
            $tableName = array_values((array) $table)[0];
            echo "- $tableName\n";
        }
        
    } else {
        echo "✓ Tabelas de backup encontradas:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            echo "- $tableName\n";
            
            // Contar registros
            $count = DB::table($tableName)->count();
            echo "  ($count registros)\n";
        }
        
        echo "\n=== RECUPERAR BACKUP ===\n";
        echo "Execute os comandos:\n";
        echo "1. DROP TABLE IF EXISTS lancamentos;\n";
        echo "2. CREATE TABLE lancamentos AS SELECT * FROM lancamentos_backup;\n";
        echo "3. DROP TABLE IF EXISTS lancamento_itens;\n";
        echo "4. CREATE TABLE lancamento_itens AS SELECT * FROM lancamento_itens_backup;\n";
        echo "5. DROP TABLE IF EXISTS lancamento_movimentacoes;\n";
        echo "6. CREATE TABLE lancamento_movimentacoes AS SELECT * FROM lancamento_movimentacoes_backup;\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
