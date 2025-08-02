<?php

/**
 * Script para limpar views e remover códigos desnecessários dos menus antigos
 */

$views_directory = 'resources/views/admin/fidelidade/';
$views = [
    'dashboard.blade.php',
    'transacoes.blade.php',
    'cupons.blade.php',
    'cashback.blade.php',
    'relatorios.blade.php',
    'configuracoes.blade.php'
];

echo "<h1>🧹 Limpando Views Convertidas</h1>";

foreach ($views as $view_file) {
    $file_path = $views_directory . $view_file;

    if (!file_exists($file_path)) {
        continue;
    }

    echo "<h3>🔧 Limpando: {$view_file}</h3>";

    $content = file_get_contents($file_path);

    // Remover elementos de navbar antigos que ficaram no conteúdo
    $patterns_to_remove = [
        // Navbar elements
        '/<a class="navbar-brand"[^>]*>.*?<\/a>/s',
        '/<button class="navbar-toggler"[^>]*>.*?<\/button>/s',
        '/<div class="collapse navbar-collapse"[^>]*>.*?<\/div>/s',
        '/<ul class="navbar-nav[^>]*>.*?<\/ul>/s',
        '/<nav class="navbar[^>]*>.*?<\/nav>/s',

        // Menu container elements
        '/<div class="container-navbar[^>]*>.*?<\/div>/s',

        // Elementos órfãos
        '/\s*<\/div>\s*<\/div>\s*<\/nav>/s',
        '/\s*<\/div>\s*<\/nav>/s',
        '/\s*<\/nav>/s',

        // Múltiplas quebras de linha
        '/\n{3,}/',

        // Espaços em branco no início
        '/^\s+/m'
    ];

    $replacements = [
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        "\n\n",
        ''
    ];

    $cleaned_content = preg_replace($patterns_to_remove, $replacements, $content);

    // Limpar linhas vazias no início do @section('content')
    $cleaned_content = preg_replace('/(@section\(\'content\'\)\n)\s*\n+/', '$1', $cleaned_content);

    // Garantir que a primeira linha após @section('content') seja um comentário ou div
    $lines = explode("\n", $cleaned_content);
    $in_content_section = false;
    $cleaned_lines = [];

    foreach ($lines as $line) {
        if (strpos($line, "@section('content')") !== false) {
            $in_content_section = true;
            $cleaned_lines[] = $line;
            continue;
        }

        if ($in_content_section && strpos($line, "@endsection") !== false) {
            $in_content_section = false;
        }

        if ($in_content_section) {
            $trimmed = trim($line);
            // Pular linhas que são elementos de navbar órfãos
            if (
                empty($trimmed) ||
                strpos($trimmed, '<a class="navbar-brand') !== false ||
                strpos($trimmed, '<button class="navbar-toggler') !== false ||
                strpos($trimmed, '<div class="collapse navbar-collapse') !== false ||
                strpos($trimmed, '<ul class="navbar-nav') !== false ||
                strpos($trimmed, '</a>') === 0 ||
                strpos($trimmed, '</button>') === 0 ||
                strpos($trimmed, '</div>') === 0 ||
                strpos($trimmed, '</ul>') === 0 ||
                strpos($trimmed, '</nav>') === 0
            ) {
                continue;
            }
        }

        $cleaned_lines[] = $line;
    }

    $final_content = implode("\n", $cleaned_lines);

    // Verificar se houve mudanças significativas
    if ($final_content !== $content) {
        file_put_contents($file_path, $final_content);
        echo "<p>✅ <strong>{$view_file}</strong> limpo com sucesso</p>";

        // Mostrar preview das primeiras linhas limpas
        $preview_lines = explode("\n", $final_content);
        $preview = implode("\n", array_slice($preview_lines, 0, 10));
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>{$preview}...</pre>";
    } else {
        echo "<p>ℹ️ <strong>{$view_file}</strong> já estava limpo</p>";
    }
}

echo "<h2>✅ Limpeza Concluída!</h2>";
echo "<p><strong>Todas as views foram limpas e organizadas.</strong></p>";
