<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando modelos do sistema financeiro:\n";
echo "======================================\n\n";

try {
    // Testar ContaGerencial
    echo "1. Testando ContaGerencial...\n";
    $contas = \App\Models\Financial\ContaGerencial::where('empresa_id', 1)->limit(3)->get();
    echo "   ✅ {$contas->count()} contas encontradas\n";

    // Testar CategoriaContaGerencial  
    echo "2. Testando CategoriaContaGerencial...\n";
    $categorias = \App\Models\Financial\CategoriaContaGerencial::where('empresa_id', 1)->limit(3)->get();
    echo "   ✅ {$categorias->count()} categorias encontradas\n";

    // Testar Tipo
    echo "3. Testando Tipo...\n";
    $tipos = \App\Models\Financial\Tipo::ativos()->limit(3)->get();
    echo "   ✅ {$tipos->count()} tipos ativos encontrados\n";

    // Testar ClassificacaoDre
    echo "4. Testando ClassificacaoDre...\n";
    $classificacoes = \App\Models\Financial\ClassificacaoDre::ativos()->limit(3)->get();
    echo "   ✅ {$classificacoes->count()} classificações ativas encontradas\n";

    echo "\n🎉 Todos os modelos estão funcionando corretamente!\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
}
