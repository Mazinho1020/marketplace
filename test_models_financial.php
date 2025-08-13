<?php
require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\ContaGerencial;
use App\Models\Financial\ClassificacaoDre;
use App\Models\Financial\Tipo;

try {
    echo "=== TESTE DOS MODELOS FINANCEIROS ===\n\n";

    // Teste ContaGerencial
    echo "1. Testando ContaGerencial...\n";
    $contas = ContaGerencial::where('empresa_id', 1)->limit(3)->get();
    echo "✅ ContaGerencial: " . $contas->count() . " registros encontrados\n";

    // Teste ClassificacaoDre
    echo "2. Testando ClassificacaoDre...\n";
    $classificacoes = ClassificacaoDre::limit(3)->get();
    echo "✅ ClassificacaoDre: " . $classificacoes->count() . " registros encontrados\n";

    // Teste Tipo
    echo "3. Testando Tipo...\n";
    $tipos = Tipo::limit(3)->get();
    echo "✅ Tipo: " . $tipos->count() . " registros encontrados\n";

    // Teste relacionamentos
    echo "4. Testando relacionamentos...\n";
    if ($contas->count() > 0) {
        $conta = $contas->first();
        echo "✅ Primeira conta: " . $conta->nome . "\n";

        if ($conta->classificacao_dre_id) {
            $classificacao = $conta->classificacaoDre;
            echo "✅ Classificação DRE: " . ($classificacao ? $classificacao->nome : 'Não encontrada') . "\n";
        }

        if ($conta->tipo_id) {
            $tipo = $conta->tipo;
            echo "✅ Tipo: " . ($tipo ? $tipo->nome : 'Não encontrado') . "\n";
        }
    }

    echo "\n=== TODOS OS TESTES PASSARAM! ===\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
