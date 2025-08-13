<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testando scope ativo()...\n";

    // Testar o scope ativo
    $produtosAtivos = App\Models\Produto::ativo()->limit(3)->get();
    echo "✓ Scope ativo() funcionando - Produtos ativos: " . $produtosAtivos->count() . "\n";

    // Testar com porEmpresa e ativo
    $produtosEmpresa = App\Models\Produto::porEmpresa(1)->ativo()->limit(3)->get();
    echo "✓ Scope porEmpresa()->ativo() funcionando - Produtos: " . $produtosEmpresa->count() . "\n";

    // Testar controller
    echo "\nTestando controller de histórico de preços...\n";
    $controller = new App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController();
    echo "✓ Controller criado com sucesso\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTeste concluído!\n";
