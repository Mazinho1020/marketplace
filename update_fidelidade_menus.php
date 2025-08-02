<?php
require 'vendor/autoload.php';

echo "Atualizando menus em todas as views de fidelidade...\n\n";

$fidelidadeViews = [
    'cashback.blade.php',
    'cartoes.blade.php',
    'transacoes.blade.php',
    'relatorios.blade.php',
    'index.blade.php',
    'dashboard.blade.php'
];

foreach ($fidelidadeViews as $view) {
    $filePath = "resources/views/admin/fidelidade/{$view}";

    if (file_exists($filePath)) {
        echo "Processando: {$view}\n";

        $content = file_get_contents($filePath);

        // Remover CSS do navbar-custom
        $content = preg_replace('/\.navbar-custom\s*\{[^}]*\}/s', '', $content);
        $content = preg_replace('/\.navbar-custom\s+[^{]*\{[^}]*\}/s', '', $content);

        // Substituir o menu antigo pelos includes
        $oldMenu = '/<!-- Navbar Superior do Admin Fidelidade -->.*?<div class="container[^>]*>/s';
        $newMenu = '</head>
<body>
    {{-- Include do Menu Principal --}}
    @include(\'admin.partials.menuConfig\')
    
    {{-- Include do Menu Secundário Fidelidade --}}
    @include(\'admin.partials.menuFidelidade\')

    <div class="container-fluid mt-4">';

        $content = preg_replace($oldMenu, $newMenu, $content);

        // Salvar arquivo atualizado
        file_put_contents($filePath, $content);
        echo "✓ {$view} atualizado\n";
    } else {
        echo "✗ {$view} não encontrado\n";
    }
}

echo "\nAtualização concluída!\n";
