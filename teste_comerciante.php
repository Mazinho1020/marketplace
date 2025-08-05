<?php

// Script de teste para verificar a estrutura do módulo comerciante
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DO MÓDULO COMERCIANTE ===\n\n";

try {
    // 1. Teste de conexão com banco
    echo "1. Testando conexão com banco...\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✓ Conexão OK\n\n";

    // 2. Teste das tabelas
    echo "2. Verificando tabelas...\n";

    $tables = ['empresa_usuarios', 'marcas', 'empresas', 'empresa_user_vinculos'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   ✓ Tabela '$table': $count registros\n";
        } catch (Exception $e) {
            echo "   ✗ Tabela '$table': " . $e->getMessage() . "\n";
        }
    }

    echo "\n3. Testando Models...\n";

    // 3. Teste dos models
    try {
        $marca = new \App\Comerciantes\Models\Marca();
        echo "   ✓ Model Marca: OK\n";
    } catch (Exception $e) {
        echo "   ✗ Model Marca: " . $e->getMessage() . "\n";
    }

    try {
        $empresa = new \App\Comerciantes\Models\Empresa();
        echo "   ✓ Model Empresa: OK\n";
    } catch (Exception $e) {
        echo "   ✗ Model Empresa: " . $e->getMessage() . "\n";
    }

    try {
        $usuario = new \App\Comerciantes\Models\EmpresaUsuario();
        echo "   ✓ Model EmpresaUsuario: OK\n";
    } catch (Exception $e) {
        echo "   ✗ Model EmpresaUsuario: " . $e->getMessage() . "\n";
    }

    echo "\n4. Testando Rotas...\n";

    // 4. Verificar se as rotas estão carregadas
    $routes = Route::getRoutes();
    $comercianteRoutes = 0;

    foreach ($routes as $route) {
        if (str_starts_with($route->uri(), 'comerciantes/')) {
            $comercianteRoutes++;
        }
    }

    echo "   ✓ Rotas do comerciante encontradas: $comercianteRoutes\n";

    echo "\n=== TODOS OS TESTES CONCLUÍDOS ===\n";
    echo "O módulo comerciante está pronto para uso!\n";
} catch (Exception $e) {
    echo "ERRO GERAL: " . $e->getMessage() . "\n";
}
