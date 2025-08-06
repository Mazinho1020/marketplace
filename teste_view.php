<?php
// Teste de Renderização da View usuarios.blade.php

try {
    echo "<h2>🧪 Teste de Renderização - usuarios.blade.php</h2>";

    // Definir path do Laravel
    require_once 'vendor/autoload.php';

    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

    // Criar dados mock para a view
    $empresa = (object) [
        'id' => 1,
        'nome_fantasia' => 'Empresa Teste',
        'razao_social' => 'Empresa Teste LTDA',
        'status' => 'ativa',
        'usuariosVinculados' => collect([]),
        'proprietario' => null,
        'marca' => null
    ];

    // Tentar renderizar a view
    $view = view('comerciantes.empresas.usuarios', compact('empresa'));
    $html = $view->render();

    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ VIEW RENDERIZADA COM SUCESSO!</h4>";
    echo "<p>A view usuarios.blade.php foi carregada sem erros de sintaxe.</p>";
    echo "<p><strong>Tamanho do HTML gerado:</strong> " . number_format(strlen($html)) . " caracteres</p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>❌ ERRO AO RENDERIZAR VIEW</h4>";
    echo "<p><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . " (linha " . $e->getLine() . ")</p>";
    echo "</div>";
}

echo "<br><p><em>Teste realizado em " . date('Y-m-d H:i:s') . "</em></p>";
