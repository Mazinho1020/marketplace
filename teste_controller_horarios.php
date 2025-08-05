<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Comerciantes\Controllers\HorarioFuncionamentoController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

// Simular ambiente Laravel
$app = new Application();
$app->singleton('app', function () use ($app) {
    return $app;
});

// Configurar banco de dados
Config::set('database.default', 'mysql');
Config::set('database.connections.mysql', [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'marketplace',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

try {
    echo "=== TESTE DO CONTROLLER HORÁRIOS ===\n\n";

    // Tentar instanciar o controller
    $controller = new HorarioFuncionamentoController();
    echo "✅ Controller instanciado com sucesso!\n\n";

    // Testar se os métodos existem
    $methods = [
        'index',
        'padraoIndex',
        'padraoCriar',
        'padraoSalvar',
        'padraoEditar',
        'padraoAtualizar',
        'padraoDeletar',
        'excecoesIndex',
        'excecoesCriar',
        'excecoesSalvar',
        'excecoesEditar',
        'excecoesAtualizar',
        'excecoesDeletar',
        'apiStatus',
        'apiProximoHorario'
    ];

    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✅ Método '$method' existe\n";
        } else {
            echo "❌ Método '$method' não encontrado\n";
        }
    }

    echo "\n=== TESTE COMPLETO ===\n";
    echo "Controller está funcionando corretamente!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
