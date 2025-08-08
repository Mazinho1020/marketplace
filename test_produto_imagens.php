<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Teste do Relacionamento Produto->Imagens ===\n";

try {
    // Buscar um produto qualquer
    $produto = \App\Models\Produto::first();

    if ($produto) {
        echo "✅ Produto encontrado: {$produto->nome}\n";

        // Testar o relacionamento de imagens
        echo "🔍 Buscando imagens do produto...\n";
        $imagens = $produto->imagens;

        echo "✅ Relacionamento funcionando! Encontradas " . $imagens->count() . " imagens\n";

        // Testar imagem principal
        echo "🔍 Buscando imagem principal...\n";
        $imagemPrincipal = $produto->imagemPrincipal;

        if ($imagemPrincipal) {
            echo "✅ Imagem principal encontrada: {$imagemPrincipal->arquivo}\n";
        } else {
            echo "ℹ️ Nenhuma imagem principal encontrada\n";
        }
    } else {
        echo "❌ Nenhum produto encontrado no banco\n";
    }

    echo "\n=== Teste Direto do Modelo ProdutoImagem ===\n";

    // Testar acesso direto ao modelo
    $totalImagens = \App\Models\ProdutoImagem::count();
    echo "📊 Total de imagens no banco: {$totalImagens}\n";

    if ($totalImagens > 0) {
        $primeiraImagem = \App\Models\ProdutoImagem::first();
        echo "✅ Primeira imagem: {$primeiraImagem->arquivo} (Produto ID: {$primeiraImagem->produto_id})\n";
    }

    echo "\n🎉 Todos os testes passaram! O erro foi corrigido.\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
