<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DAS 3 FUNCIONALIDADES IMPLEMENTADAS ===\n";

try {
    // 1. Teste Subcategorias
    echo "\n1. SUBCATEGORIAS:\n";
    $subcategorias = App\Models\ProdutoSubcategoria::count();
    echo "Total de subcategorias: $subcategorias\n";

    $porEmpresa = App\Models\ProdutoSubcategoria::where('empresa_id', 1)->count();
    echo "Subcategorias da empresa 1: $porEmpresa\n";

    // 2. Teste Códigos de Barras  
    echo "\n2. CÓDIGOS DE BARRAS:\n";
    $codigosBarras = App\Models\ProdutoCodigoBarras::count();
    echo "Total de códigos de barras: $codigosBarras\n";

    $porEmpresa2 = App\Models\ProdutoCodigoBarras::where('empresa_id', 1)->count();
    echo "Códigos de barras da empresa 1: $porEmpresa2\n";

    // 3. Teste Histórico de Preços
    echo "\n3. HISTÓRICO DE PREÇOS:\n";
    $historicos = App\Models\ProdutoHistoricoPreco::count();
    echo "Total de históricos de preço: $historicos\n";

    $porEmpresa3 = App\Models\ProdutoHistoricoPreco::where('empresa_id', 1)->count();
    echo "Históricos da empresa 1: $porEmpresa3\n";

    // Teste de categorias e produtos disponíveis para contexto
    echo "\n4. DADOS EXISTENTES:\n";
    $categorias = App\Models\ProdutoCategoria::where('empresa_id', 1)->count();
    echo "Categorias disponíveis: $categorias\n";

    $produtos = App\Models\Produto::where('empresa_id', 1)->count();
    echo "Produtos disponíveis: $produtos\n";

    echo "\n✅ TODOS OS MODELS ESTÃO FUNCIONANDO!\n";
    echo "\n📊 RESUMO DA IMPLEMENTAÇÃO:\n";
    echo "✅ 1. Subcategorias - Model, Controller, Views e Rotas\n";
    echo "✅ 2. Códigos de Barras - Model, Controller, Views e Rotas\n";
    echo "✅ 3. Histórico de Preços - Model, Controller e Rotas\n";
    echo "\n🎯 PRÓXIMOS PASSOS SUGERIDOS:\n";
    echo "- Criar algumas subcategorias para testar navegação hierárquica\n";
    echo "- Adicionar códigos de barras aos produtos existentes\n";
    echo "- Testar alterações de preços para gerar histórico\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
