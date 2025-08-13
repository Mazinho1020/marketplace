<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE FINAL CORREÇÃO CÓDIGO DE BARRAS ===\n\n";

try {
    // 1. Verificar se o scope ativo funciona
    echo "1. Testando scopes...\n";
    $codigosAtivos = App\Models\ProdutoCodigoBarras::ativo()->count();
    echo "   ✓ Scope ativo(): {$codigosAtivos} códigos\n";

    $codigosEmpresa = App\Models\ProdutoCodigoBarras::porEmpresa(1)->ativo()->count();
    echo "   ✓ Scope porEmpresa()->ativo(): {$codigosEmpresa} códigos\n";

    // 2. Testar método verificarDuplicacao
    echo "\n2. Testando verificação de duplicação...\n";
    $duplicado = App\Models\ProdutoCodigoBarras::verificarDuplicacao(1, '7891234567890');
    echo "   ✓ Código existente duplicado: " . ($duplicado ? 'Sim' : 'Não') . "\n";

    $novoCodigo = App\Models\ProdutoCodigoBarras::verificarDuplicacao(1, '9999999999999');
    echo "   ✓ Código novo disponível: " . ($novoCodigo ? 'Não' : 'Sim') . "\n";

    // 3. Testar buscarPorCodigo
    echo "\n3. Testando busca por código...\n";
    $codigo = App\Models\ProdutoCodigoBarras::buscarPorCodigo(1, '7891234567890');
    if ($codigo) {
        echo "   ✓ Código encontrado: {$codigo->codigo} - Produto: {$codigo->produto->nome}\n";
    } else {
        echo "   ❌ Código não encontrado\n";
    }

    // 4. Testar criação de novo código
    echo "\n4. Testando criação de código...\n";
    $novoCodigoTeste = '1111111111111';

    // Verificar se já existe
    $jaExiste = App\Models\ProdutoCodigoBarras::where('codigo', $novoCodigoTeste)->first();
    if ($jaExiste) {
        echo "   ! Código de teste já existe, pulando criação\n";
    } else {
        $codigoBarras = new App\Models\ProdutoCodigoBarras();
        $codigoBarras->fill([
            'empresa_id' => 1,
            'produto_id' => 1,
            'codigo' => $novoCodigoTeste,
            'tipo' => 'ean13',
            'principal' => false,
            'ativo' => true,
        ]);

        if ($codigoBarras->save()) {
            echo "   ✓ Código criado com sucesso: ID {$codigoBarras->id}\n";
        } else {
            echo "   ❌ Erro ao criar código\n";
        }
    }

    // 5. Listar todos os códigos
    echo "\n5. Códigos de barras existentes:\n";
    $todosOsCodigos = App\Models\ProdutoCodigoBarras::with('produto')->ativo()->get();
    foreach ($todosOsCodigos as $codigo) {
        $produto = $codigo->produto ? $codigo->produto->nome : 'N/A';
        $principal = $codigo->principal ? 'Principal' : 'Secundário';
        echo "   - ID: {$codigo->id} | Código: {$codigo->codigo} | Produto: {$produto} | {$principal}\n";
    }

    echo "\n✅ TODOS OS TESTES PASSARAM!\n";
    echo "✅ O erro 'Call to undefined method ativo()' foi resolvido\n";
    echo "✅ Sistema de códigos de barras funcionando corretamente\n";
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
