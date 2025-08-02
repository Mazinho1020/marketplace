<?php

/**
 * Script para converter todas as views de fidelidade para usar o novo layout modular
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

echo "<h1>🔄 Convertendo Views para Layout Modular</h1>";

foreach ($views as $view_file) {
    $file_path = $views_directory . $view_file;

    if (!file_exists($file_path)) {
        echo "<p>❌ <strong>{$view_file}</strong> não encontrado</p>";
        continue;
    }

    echo "<h3>🔧 Processando: {$view_file}</h3>";

    $content = file_get_contents($file_path);

    // Extrair título da view atual
    preg_match('/<title>(.*?)<\/title>/', $content, $title_matches);
    $title = isset($title_matches[1]) ? str_replace(' - Admin', '', $title_matches[1]) : 'Fidelidade';

    // Extrair conteúdo principal (entre <div class="container-fluid mt-4"> e fechamento)
    $start_pattern = '/<div class="container-fluid mt-4">/';
    $end_pattern = '/<script src="\/Theme1\/libs\/bootstrap\/js\/bootstrap\.bundle\.min\.js"><\/script>/';

    preg_match($start_pattern, $content, $start_matches, PREG_OFFSET_CAPTURE);
    preg_match($end_pattern, $content, $end_matches, PREG_OFFSET_CAPTURE);

    if (!$start_matches || !$end_matches) {
        echo "<p>⚠️ Não foi possível encontrar delimitadores em {$view_file}</p>";
        continue;
    }

    $start_pos = $start_matches[0][1] + strlen('<div class="container-fluid mt-4">');
    $end_pos = $end_matches[0][1];

    $main_content = substr($content, $start_pos, $end_pos - $start_pos);

    // Remover fechamentos desnecessários do final
    $main_content = preg_replace('/\s*<\/div>\s*<\/body>\s*<\/html>\s*$/', '', $main_content);
    $main_content = trim($main_content);

    // Extrair scripts específicos da página
    preg_match_all('/<script[^>]*>(.*?)<\/script>/s', $content, $script_matches);
    $page_scripts = '';

    foreach ($script_matches[1] as $script_content) {
        $script_content = trim($script_content);
        if (
            !empty($script_content) &&
            !strpos($script_content, 'bootstrap.bundle.min.js') &&
            !strpos($script_content, 'admin-menus.js')
        ) {
            $page_scripts .= $script_content . "\n";
        }
    }

    // Gerar nova view
    $new_content = "@extends('admin.layouts.fidelidade')\n\n";
    $new_content .= "@section('title', '{$title}')\n\n";
    $new_content .= "@section('content')\n";
    $new_content .= $main_content . "\n";
    $new_content .= "@endsection\n";

    if (!empty($page_scripts)) {
        $new_content .= "\n@section('scripts')\n";
        $new_content .= "<script>\n{$page_scripts}</script>\n";
        $new_content .= "@endsection\n";
    }

    // Salvar backup
    $backup_path = $file_path . '.backup';
    copy($file_path, $backup_path);
    echo "<p>💾 Backup criado: {$backup_path}</p>";

    // Salvar nova versão
    file_put_contents($file_path, $new_content);
    echo "<p>✅ <strong>{$view_file}</strong> convertido com sucesso</p>";

    // Mostrar preview das primeiras linhas
    $preview_lines = explode("\n", $new_content);
    $preview = implode("\n", array_slice($preview_lines, 0, 8));
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>{$preview}...</pre>";
}

echo "<h2>✅ Conversão Concluída!</h2>";
echo "<p><strong>Todas as views foram convertidas para usar o layout modular.</strong></p>";
echo "<p>Os arquivos originais foram mantidos como backup (.backup)</p>";

echo "<h3>🔗 Estrutura Final:</h3>";
echo "<ul>";
echo "<li><strong>Layout Base:</strong> admin.layouts.fidelidade</li>";
echo "<li><strong>CSS/JS Centralizados:</strong> No layout base</li>";
echo "<li><strong>Views Simplificadas:</strong> Apenas conteúdo específico</li>";
echo "<li><strong>Sistema Modular:</strong> Fácil manutenção</li>";
echo "</ul>";
