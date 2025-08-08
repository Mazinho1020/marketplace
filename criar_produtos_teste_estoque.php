<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Inserir produtos de teste com estoque baixo para demonstração
try {
    echo "Criando produtos de teste com problemas de estoque...\n";

    // Produto com estoque baixo
    DB::table('produtos')->insert([
        'empresa_id' => 1,
        'nome' => 'Produto Teste - Estoque Baixo',
        'descricao' => 'Produto para testar notificações de estoque baixo',
        'sku' => 'TESTE-BAIXO-001',
        'preco_venda' => 25.90,
        'categoria_id' => 1,
        'controla_estoque' => true,
        'estoque_atual' => 2.0,
        'estoque_minimo' => 5.0,
        'estoque_maximo' => 50.0,
        'ativo' => true,
        'status' => 'disponivel',
        'tipo' => 'produto',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Produto com estoque zerado
    DB::table('produtos')->insert([
        'empresa_id' => 1,
        'nome' => 'Produto Teste - Esgotado',
        'descricao' => 'Produto para testar notificações de estoque esgotado',
        'sku' => 'TESTE-ESGOTADO-001',
        'preco_venda' => 35.50,
        'categoria_id' => 1,
        'controla_estoque' => true,
        'estoque_atual' => 0.0,
        'estoque_minimo' => 3.0,
        'estoque_maximo' => 30.0,
        'ativo' => true,
        'status' => 'esgotado',
        'tipo' => 'produto',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Produto da empresa 2 com estoque baixo
    DB::table('produtos')->insert([
        'empresa_id' => 2,
        'nome' => 'Pizza Especial - Teste',
        'descricao' => 'Pizza com ingredientes quase acabando',
        'sku' => 'PIZZA-TESTE-002',
        'preco_venda' => 45.90,
        'categoria_id' => 1,
        'controla_estoque' => true,
        'estoque_atual' => 1.0,
        'estoque_minimo' => 8.0,
        'estoque_maximo' => 20.0,
        'ativo' => true,
        'status' => 'disponivel',
        'tipo' => 'produto',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "✅ Produtos de teste criados com sucesso!\n";
    echo "- Produto com estoque baixo (2/5)\n";
    echo "- Produto esgotado (0/3)\n";
    echo "- Pizza com estoque crítico (1/8)\n";
} catch (Exception $e) {
    echo '❌ Erro: ' . $e->getMessage();
}
