<?php
// Teste rápido do Laravel
echo "=== TESTE LARAVEL ===\n";

try {
    require_once 'vendor/autoload.php';

    echo "✅ Autoload carregado\n";

    $app = require_once 'bootstrap/app.php';
    echo "✅ App Bootstrap carregado\n";

    // Testar conexão de banco via Laravel
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "✅ Kernel inicializado\n";

    // Testar conexão direta
    $db = $app->make('db');
    $connection = $db->connection();
    $pdo = $connection->getPdo();

    echo "✅ Conexão PDO estabelecida\n";

    // Listar tabelas
    $tables = $connection->select("SHOW TABLES");
    echo "📊 Tabelas encontradas: " . count($tables) . "\n";

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }

    // Verificar tabelas específicas
    echo "\n=== VERIFICANDO TABELAS CRÍTICAS ===\n";

    $critical_tables = ['empresa_usuarios', 'empresas', 'empresa_usuario_tipos'];

    foreach ($critical_tables as $table) {
        try {
            $count = $connection->table($table)->count();
            echo "✅ $table: $count registros\n";
        } catch (Exception $e) {
            echo "❌ $table: NÃO EXISTE\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
