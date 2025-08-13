<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testando scope ativo() no ProdutoCodigoBarras...\n";

    // Testar o scope ativo
    $codigosAtivos = App\Models\ProdutoCodigoBarras::ativo()->count();
    echo "✓ Scope ativo() funcionando - Códigos ativos: " . $codigosAtivos . "\n";

    // Testar com porEmpresa e ativo
    $codigosEmpresa = App\Models\ProdutoCodigoBarras::porEmpresa(1)->ativo()->count();
    echo "✓ Scope porEmpresa()->ativo() funcionando - Códigos: " . $codigosEmpresa . "\n";

    // Testar método buscarPorCodigo
    $codigo = App\Models\ProdutoCodigoBarras::buscarPorCodigo(1, '7891234567890');
    if ($codigo) {
        echo "✓ Método buscarPorCodigo funcionando - Produto: " . $codigo->produto->nome . "\n";
    } else {
        echo "! Nenhum código encontrado para teste\n";
    }

    // Testar método verificarDuplicacao
    $duplicado = App\Models\ProdutoCodigoBarras::verificarDuplicacao(1, '7891234567890');
    echo "✓ Método verificarDuplicacao funcionando - Duplicado: " . ($duplicado ? 'Sim' : 'Não') . "\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTeste do scope concluído!\n";
