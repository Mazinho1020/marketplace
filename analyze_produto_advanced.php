<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ESTRUTURAS DAS TABELAS DE PRODUTOS AVANÇADOS ===\n\n";

$tabelasParaAnalisar = [
    'produto_variacoes_combinacoes',
    'produto_kits',
    'produto_relacionados',
    'produto_precos_quantidade'
];

foreach ($tabelasParaAnalisar as $tabela) {
    try {
        echo "=== TABELA: $tabela ===\n";
        $columns = DB::select("DESCRIBE $tabela");
        foreach ($columns as $column) {
            $null = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
            $key = $column->Key ? " ($column->Key)" : '';
            echo "  {$column->Field}: {$column->Type} $null$key\n";
        }

        // Mostrar dados de exemplo
        $count = DB::select("SELECT COUNT(*) as total FROM $tabela")[0]->total;
        echo "  Registros: $count\n";

        if ($count > 0) {
            $samples = DB::select("SELECT * FROM $tabela LIMIT 3");
            echo "  Exemplos:\n";
            foreach ($samples as $sample) {
                $data = json_encode($sample, JSON_UNESCAPED_UNICODE);
                echo "    $data\n";
            }
        }
        echo "\n";
    } catch (Exception $e) {
        echo "  ❌ Erro: " . $e->getMessage() . "\n\n";
    }
}

echo "=== ANÁLISE DE FUNCIONALIDADES ===\n\n";

// Verificar se existem Models para essas tabelas
$modelsPath = 'app/Models/';
$models = [
    'ProdutoVariacao.php',
    'ProdutoVariacaoCombinacao.php',
    'ProdutoKit.php',
    'ProdutoRelacionado.php',
    'ProdutoPrecoQuantidade.php'
];

foreach ($models as $model) {
    $path = $modelsPath . $model;
    if (file_exists($path)) {
        echo "✅ Model existe: $model\n";
    } else {
        echo "❌ Model faltante: $model\n";
    }
}

echo "\n=== ANÁLISE DE CONTROLADORES ===\n\n";

$controllersPath = 'app/Http/Controllers/Comerciante/';
$controllers = [
    'ProdutoVariacaoController.php',
    'ProdutoKitController.php',
    'ProdutoRelacionadoController.php',
    'ProdutoPrecoQuantidadeController.php'
];

foreach ($controllers as $controller) {
    $path = $controllersPath . $controller;
    if (file_exists($path)) {
        echo "✅ Controller existe: $controller\n";
    } else {
        echo "❌ Controller faltante: $controller\n";
    }
}

echo "\n=== PRÓXIMAS IMPLEMENTAÇÕES PRIORITÁRIAS ===\n\n";
echo "1. 🔴 VARIAÇÕES DE PRODUTOS - Maior impacto comercial\n";
echo "2. 🟡 KITS/COMBOS - Estratégia de vendas\n";
echo "3. 🟡 PRODUTOS RELACIONADOS - Cross-selling\n";
echo "4. 🟢 PREÇOS POR QUANTIDADE - Vendas B2B\n";
