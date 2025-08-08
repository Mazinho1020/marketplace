<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== VERIFICANDO STATUS DOS ARQUIVOS ===\n";

// 1. Verificar se o arquivo de login correto está sendo usado
echo "\n📄 VIEWS DE LOGIN DISPONÍVEIS:\n";
$loginViews = [
    'resources/views/comerciantes/auth/login-simples.blade.php',
    'resources/views/comerciantes/auth/login.blade.php',
    'resources/views/auth/login-simplified.blade.php',
    'resources/views/admin/login-simplified.blade.php'
];

foreach ($loginViews as $view) {
    $fullPath = "c:\\xampp\\htdocs\\marketplace\\$view";
    if (file_exists($fullPath)) {
        echo "✅ $view (existe)\n";
    } else {
        echo "❌ $view (não existe)\n";
    }
}

// 2. Verificar arquivos JavaScript/CSS que podem estar causando problema
echo "\n📁 VERIFICANDO DIRETÓRIO Theme1:\n";
$theme1Path = "c:\\xampp\\htdocs\\marketplace\\public\\Theme1";
if (is_dir($theme1Path)) {
    echo "✅ Diretório Theme1 existe\n";

    $jsPath = "$theme1Path\\js\\bootstrap.bundle.min.js";
    if (file_exists($jsPath)) {
        echo "✅ bootstrap.bundle.min.js existe\n";
    } else {
        echo "❌ bootstrap.bundle.min.js NÃO existe (problema encontrado!)\n";
    }
} else {
    echo "❌ Diretório Theme1 NÃO existe\n";
}

// 3. Verificar cache de views
echo "\n🗃️ VERIFICANDO CACHE DE VIEWS:\n";
$cacheDir = "c:\\xampp\\htdocs\\marketplace\\storage\\framework\\views";
$files = glob("$cacheDir/*.php");
echo "Arquivos em cache: " . count($files) . "\n";

if (count($files) > 0) {
    echo "Alguns arquivos de cache:\n";
    foreach (array_slice($files, 0, 3) as $file) {
        echo "- " . basename($file) . "\n";
    }
}

echo "\n✅ VERIFICAÇÃO CONCLUÍDA!\n";
