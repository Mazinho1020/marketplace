<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Config\ConfigDefinition;

try {
    echo "=== TESTE DO MÉTODO formatarValor ===\n\n";

    // Criar uma instância do modelo para testar
    $config = new ConfigDefinition();
    $config->tipo = ConfigDefinition::TYPE_STRING;

    // Testar se o método existe
    if (method_exists($config, 'formatarValor')) {
        echo "✓ Método formatarValor() existe\n";

        // Testar diferentes tipos
        $tests = [
            ['tipo' => ConfigDefinition::TYPE_STRING, 'valor' => 'teste', 'esperado' => 'teste'],
            ['tipo' => ConfigDefinition::TYPE_INTEGER, 'valor' => '123', 'esperado' => 123],
            ['tipo' => ConfigDefinition::TYPE_FLOAT, 'valor' => '12.34', 'esperado' => 12.34],
            ['tipo' => ConfigDefinition::TYPE_BOOLEAN, 'valor' => '1', 'esperado' => true],
            ['tipo' => ConfigDefinition::TYPE_ARRAY, 'valor' => 'a,b,c', 'esperado' => ['a', 'b', 'c']],
        ];

        foreach ($tests as $test) {
            $config->tipo = $test['tipo'];
            $resultado = $config->formatarValor($test['valor']);
            $success = ($resultado === $test['esperado']) ? '✓' : '✗';
            echo "{$success} Tipo {$test['tipo']}: '{$test['valor']}' -> " . var_export($resultado, true) . "\n";
        }
    } else {
        echo "✗ Método formatarValor() NÃO existe\n";
    }

    echo "\n=== TESTE CONCLUÍDO ===\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
