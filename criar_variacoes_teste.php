<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProdutoVariacaoCombinacao;
use App\Models\Produto;

// Criar variações para o produto 1 (Pizza)
$produto1 = Produto::find(1);
if ($produto1) {
    ProdutoVariacaoCombinacao::create([
        'empresa_id' => 1,
        'produto_id' => 1,
        'nome' => 'Pequena',
        'sku' => 'PIZZA-P',
        'configuracoes' => ['tamanho' => 'pequena'],
        'preco_adicional' => -5.00,
        'preco_final' => 20.90,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    ProdutoVariacaoCombinacao::create([
        'empresa_id' => 1,
        'produto_id' => 1,
        'nome' => 'Média',
        'sku' => 'PIZZA-M',
        'configuracoes' => ['tamanho' => 'media'],
        'preco_adicional' => 0.00,
        'preco_final' => 25.90,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    ProdutoVariacaoCombinacao::create([
        'empresa_id' => 1,
        'produto_id' => 1,
        'nome' => 'Grande',
        'sku' => 'PIZZA-G',
        'configuracoes' => ['tamanho' => 'grande'],
        'preco_adicional' => 10.00,
        'preco_final' => 35.90,
        'ativo' => true,
        'sync_status' => 'pendente'
    ]);

    echo "Criadas 3 variações para o produto Pizza\n";
} else {
    echo "Produto 1 não encontrado\n";
}

// Verificar se foram criadas
$total = ProdutoVariacaoCombinacao::where('produto_id', 1)->count();
echo "Total de variações para produto 1: $total\n";
