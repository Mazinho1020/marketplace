<?php

echo "🔧 DIAGNÓSTICO - MÓDULO FIDELIDADE 🔧\n";
echo "═══════════════════════════════════════\n\n";

echo "1. 📁 VERIFICANDO ARQUIVOS CSS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$cssFiles = [
    'public/Theme1/css/app.min.css',
    'public/Theme1/css/icons.min.css',
    'public/Theme1/css/bootstrap.min.css',
    'public/Theme1/css/custom.min.css'
];

foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        $size = round(filesize($file) / 1024, 2);
        echo "✅ {$file} ({$size} KB)\n";
    } else {
        echo "❌ {$file} - NÃO ENCONTRADO\n";
    }
}

echo "\n2. 🛣️ VERIFICANDO ROTAS:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$routeFiles = [
    'routes/web.php',
    'routes/fidelidade/web.php'
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";

        if ($file === 'routes/web.php') {
            $content = file_get_contents($file);
            if (strpos($content, "require __DIR__.'/fidelidade/web.php'") !== false) {
                echo "   ✅ Inclui rotas de fidelidade\n";
            } else {
                echo "   ❌ NÃO inclui rotas de fidelidade\n";
            }
        }
    } else {
        echo "❌ {$file} - NÃO ENCONTRADO\n";
    }
}

echo "\n3. 🎨 VERIFICANDO LAYOUT:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$layoutFile = 'resources/views/layouts/app.blade.php';
if (file_exists($layoutFile)) {
    echo "✅ {$layoutFile}\n";

    $content = file_get_contents($layoutFile);

    // Verificar se está usando os arquivos corretos
    if (strpos($content, 'app.min.css') !== false) {
        echo "   ✅ Carrega app.min.css\n";
    }
    if (strpos($content, 'icons.min.css') !== false) {
        echo "   ✅ Carrega icons.min.css\n";
    }
    if (strpos($content, 'bootstrap.min.css') !== false) {
        echo "   ⚠️ Referência a bootstrap.min.css (pode estar desatualizado)\n";
    }
    if (strpos($content, 'custom.min.css') !== false) {
        echo "   ⚠️ Referência a custom.min.css (pode estar desatualizado)\n";
    }
} else {
    echo "❌ {$layoutFile} - NÃO ENCONTRADO\n";
}

echo "\n4. 🎯 VERIFICANDO CONTROLLER:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$controllerFile = 'app/Http/Controllers/Fidelidade/FidelidadeController.php';
if (file_exists($controllerFile)) {
    echo "✅ {$controllerFile}\n";
} else {
    echo "❌ {$controllerFile} - NÃO ENCONTRADO\n";
}

echo "\n5. 📄 VERIFICANDO VIEW:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$viewFile = 'resources/views/fidelidade/dashboard.blade.php';
if (file_exists($viewFile)) {
    echo "✅ {$viewFile}\n";

    $content = file_get_contents($viewFile);
    if (strpos($content, "@extends('layouts.app')") !== false) {
        echo "   ✅ Estende layouts.app\n";
    } else {
        echo "   ❌ NÃO estende layouts.app\n";
    }
} else {
    echo "❌ {$viewFile} - NÃO ENCONTRADO\n";
}

echo "\n" . str_repeat("═", 60) . "\n";
echo "🔍 DIAGNÓSTICO COMPLETO\n";
echo str_repeat("═", 60) . "\n\n";

echo "📋 SOLUÇÕES RECOMENDADAS:\n";
echo "1. Limpar cache: php artisan view:clear\n";
echo "2. Testar rota: http://localhost/marketplace/public/fidelidade\n";
echo "3. Verificar logs: storage/logs/laravel.log\n";
echo "4. Verificar permissões dos arquivos CSS\n\n";

echo "🌐 URLs para teste:\n";
echo "• Fidelidade: http://localhost/marketplace/public/fidelidade\n";
echo "• CSS Bootstrap: http://localhost/marketplace/public/Theme1/css/bootstrap.min.css\n";
echo "• CSS Custom: http://localhost/marketplace/public/Theme1/css/custom.min.css\n";

echo "\n🏁 Diagnóstico concluído: " . date('Y-m-d H:i:s') . "\n";
