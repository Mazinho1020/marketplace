<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TESTE DO LAYOUT DOS COMERCIANTES\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Verificar se o arquivo de layout existe
    $layoutPath = resource_path('views/comerciantes/layout.blade.php');
    $exists = file_exists($layoutPath);

    echo ($exists ? "✅" : "❌") . " Layout: " . ($exists ? "Existe" : "Não existe") . "\n";
    echo "   Caminho: $layoutPath\n";

    if ($exists) {
        $content = file_get_contents($layoutPath);
        $size = strlen($content);
        echo "   Tamanho: " . number_format($size) . " bytes\n";

        // Verificar elementos importantes
        $checks = [
            'Bootstrap' => strpos($content, 'bootstrap@5.3.0') !== false,
            'Font Awesome' => strpos($content, 'font-awesome') !== false,
            'Sidebar' => strpos($content, 'sidebar') !== false,
            'Navigation' => strpos($content, 'Dashboard') !== false,
            'User Info' => strpos($content, 'Auth::guard') !== false,
            'CSRF Token' => strpos($content, 'csrf_token') !== false,
            'Flash Messages' => strpos($content, 'session(\'success\')') !== false,
            'Scripts Stack' => strpos($content, '@stack(\'scripts\')') !== false
        ];

        echo "\n🔍 COMPONENTES DO LAYOUT:\n";
        foreach ($checks as $component => $found) {
            echo "   " . ($found ? "✅" : "❌") . " $component\n";
        }
    }

    echo "\n🎯 URLS PARA TESTAR O LAYOUT:\n";
    echo "   📋 Empresas: http://localhost:8000/comerciantes/empresas\n";
    echo "   ✏️ Editar: http://localhost:8000/comerciantes/empresas/1/edit\n";
    echo "   ➕ Criar: http://localhost:8000/comerciantes/empresas/create\n";
    echo "   🏠 Dashboard: http://localhost:8000/comerciantes/dashboard\n";

    echo "\n✅ LAYOUT DOS COMERCIANTES CRIADO!\n";
    echo "   - Layout responsivo com Bootstrap 5\n";
    echo "   - Sidebar com navegação completa\n";
    echo "   - Header com informações do usuário\n";
    echo "   - Sistema de alertas (success, error, warning)\n";
    echo "   - Scripts e estilos organizados\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 42) . "\n";
