<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== TESTE DE CRIAÇÃO DE CÓDIGO DE BARRAS ===\n\n";

    // Simular dados do formulário
    $dados = [
        'produto_id' => 1, // Pizza Margherita
        'codigo' => '7891234567899', // Novo código de teste
        'tipo' => 'EAN13',
        'principal' => false
    ];

    echo "1. Dados para criação:\n";
    foreach ($dados as $campo => $valor) {
        echo "   - {$campo}: {$valor}\n";
    }

    // Verificar se o produto existe
    echo "\n2. Verificando produto...\n";
    $produto = App\Models\Produto::find($dados['produto_id']);
    if ($produto) {
        echo "   ✓ Produto encontrado: {$produto->nome}\n";
        echo "   ✓ Empresa ID: {$produto->empresa_id}\n";
    } else {
        echo "   ❌ Produto não encontrado\n";
        exit;
    }

    // Verificar duplicação
    echo "\n3. Verificando duplicação...\n";
    $duplicado = App\Models\ProdutoCodigoBarras::verificarDuplicacao($produto->empresa_id, $dados['codigo']);
    echo "   " . ($duplicado ? "❌ Código já existe" : "✓ Código disponível") . "\n";

    if (!$duplicado) {
        // Tentar criar o código de barras
        echo "\n4. Criando código de barras...\n";

        $codigoBarras = new App\Models\ProdutoCodigoBarras();
        $codigoBarras->fill([
            'empresa_id' => $produto->empresa_id,
            'produto_id' => $dados['produto_id'],
            'codigo' => $dados['codigo'],
            'tipo' => $dados['tipo'],
            'principal' => $dados['principal'],
            'ativo' => true,
        ]);

        if ($codigoBarras->save()) {
            echo "   ✓ Código de barras criado com sucesso!\n";
            echo "   ✓ ID: {$codigoBarras->id}\n";
            echo "   ✓ Código: {$codigoBarras->codigo}\n";
        } else {
            echo "   ❌ Erro ao salvar código de barras\n";
        }
    }

    echo "\n5. Listando códigos do produto...\n";
    $codigos = App\Models\ProdutoCodigoBarras::where('produto_id', $dados['produto_id'])->ativo()->get();
    foreach ($codigos as $codigo) {
        echo "   - ID: {$codigo->id} | Código: {$codigo->codigo} | Tipo: {$codigo->tipo} | Principal: " . ($codigo->principal ? 'Sim' : 'Não') . "\n";
    }
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
