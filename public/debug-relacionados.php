<?php
// Debug para produtos relacionados
try {
    echo "<h2>Debug - Produtos Relacionados</h2>";

    // Verificar se é um marketplace Laravel
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }

    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    // Simular requisição
    $produtoId = 8;

    echo "<h3>1. Verificando Produto ID: {$produtoId}</h3>";

    $produto = \App\Models\Produto::find($produtoId);
    if ($produto) {
        echo "✅ Produto encontrado: {$produto->nome}<br>";
        echo "📦 Empresa ID: {$produto->empresa_id}<br>";
        echo "💰 Preço: R$ " . number_format($produto->preco_venda, 2, ',', '.') . "<br>";
    } else {
        echo "❌ Produto não encontrado!<br>";
    }

    echo "<h3>2. Verificando Relacionados Existentes</h3>";

    $relacionados = \App\Models\ProdutoRelacionado::where('produto_id', $produtoId)->get();
    echo "📊 Total de relacionados: " . $relacionados->count() . "<br>";

    foreach ($relacionados as $rel) {
        echo "- Tipo: {$rel->tipo_relacao}, Produto: {$rel->produto_relacionado_id}<br>";
    }

    echo "<h3>3. Verificando Controller</h3>";

    if (class_exists('App\Http\Controllers\Comerciante\ProdutoRelacionadoController')) {
        echo "✅ Controller existe<br>";

        $controller = new \App\Http\Controllers\Comerciante\ProdutoRelacionadoController();

        if (method_exists($controller, 'index')) {
            echo "✅ Método index existe<br>";
        } else {
            echo "❌ Método index não existe<br>";
        }
    } else {
        echo "❌ Controller não existe<br>";
    }

    echo "<h3>4. Verificando View</h3>";

    $viewPath = resource_path('views/comerciantes/produtos/relacionados/index.blade.php');

    if (file_exists($viewPath)) {
        echo "✅ View existe: {$viewPath}<br>";
        echo "📏 Tamanho: " . number_format(filesize($viewPath)) . " bytes<br>";
    } else {
        echo "❌ View não existe: {$viewPath}<br>";
    }

    echo "<h3>5. Links de Teste</h3>";
    echo '<a href="/marketplace/public/comerciantes/produtos/8/relacionados">🔗 Ir para Relacionados</a><br>';
    echo '<a href="/marketplace/public/comerciantes/produtos">🔗 Voltar para Produtos</a><br>';
} catch (Exception $e) {
    echo "<h3 style='color: red'>❌ ERRO:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
