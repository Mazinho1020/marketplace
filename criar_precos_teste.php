<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProdutoPrecoQuantidade;
use App\Models\Produto;

// Criar preços por quantidade para o produto 7 (Kit Pizza + Refrigerante)
$produto7 = Produto::find(7);
if ($produto7) {
    ProdutoPrecoQuantidade::create([
        'empresa_id' => 1,
        'produto_id' => 7,
        'variacao_id' => null,
        'quantidade_minima' => 1,
        'quantidade_maxima' => 4,
        'preco' => 30.40,
        'desconto_percentual' => 0,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    ProdutoPrecoQuantidade::create([
        'empresa_id' => 1,
        'produto_id' => 7,
        'variacao_id' => null,
        'quantidade_minima' => 5,
        'quantidade_maxima' => 9,
        'preco' => 28.90,
        'desconto_percentual' => 5,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    ProdutoPrecoQuantidade::create([
        'empresa_id' => 1,
        'produto_id' => 7,
        'variacao_id' => null,
        'quantidade_minima' => 10,
        'quantidade_maxima' => null,
        'preco' => 26.50,
        'desconto_percentual' => 13,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    echo "Criados 3 preços por quantidade para o produto Kit Pizza + Refrigerante\n";
} else {
    echo "Produto 7 não encontrado\n";
}

// Verificar se foram criadas
$total = ProdutoPrecoQuantidade::where('produto_id', 7)->count();
echo "Total de preços para produto 7: $total\n";
