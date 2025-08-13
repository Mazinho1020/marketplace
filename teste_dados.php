<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DADOS DE TESTE CRIADOS ===\n";

echo "\n--- SUBCATEGORIAS ---\n";
$subcategorias = App\Models\ProdutoSubcategoria::all();
foreach ($subcategorias as $sub) {
    echo "ID: {$sub->id} - Nome: {$sub->nome} - Pai: " . ($sub->categoria_pai_id ?: 'Raiz') . "\n";
}

echo "\n--- CÓDIGOS DE BARRAS ---\n";
$codigos = App\Models\ProdutoCodigoBarras::with('produto')->get();
foreach ($codigos as $codigo) {
    $produto_nome = $codigo->produto ? $codigo->produto->nome : 'N/A';
    echo "ID: {$codigo->id} - Código: {$codigo->codigo} - Produto: {$produto_nome}\n";
}

echo "\n--- HISTÓRICO DE PREÇOS ---\n";
$historicos = App\Models\ProdutoHistoricoPreco::with('produto')->get();
foreach ($historicos as $hist) {
    $produto_nome = $hist->produto ? $hist->produto->nome : 'N/A';
    echo "ID: {$hist->id} - Produto: {$produto_nome} - De: R\${$hist->preco_venda_anterior} Para: R\${$hist->preco_venda_novo}\n";
}

echo "\n--- PRODUTOS DISPONÍVEIS ---\n";
$produtos = App\Models\Produto::limit(5)->get();
foreach ($produtos as $produto) {
    echo "ID: {$produto->id} - Nome: {$produto->nome} - Preço: R\${$produto->preco_venda}\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
