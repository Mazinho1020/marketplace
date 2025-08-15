<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar o Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Support\Facades\DB;

echo "🧪 TESTE DO ENUM SITUACAO_FINANCEIRA\n";
echo "=====================================\n\n";

// Buscar o lançamento 380
$lancamento = LancamentoFinanceiro::find(380);

if (!$lancamento) {
    echo "❌ Lançamento 380 não encontrado\n";
    exit;
}

echo "📋 Lançamento ID: {$lancamento->id}\n";
echo "📋 Situação atual: " . ($lancamento->situacao_financeira ? $lancamento->situacao_financeira->value : 'null') . "\n\n";

// Testar atribuição do enum
echo "🧪 Testando atribuição de enum:\n";

try {
    echo "1. Testando PARCIALMENTE_PAGO:\n";
    $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PARCIALMENTE_PAGO;
    echo "   ✅ Enum atribuído: {$lancamento->situacao_financeira->value}\n";

    // Verificar o SQL que será executado
    DB::enableQueryLog();

    echo "2. Tentando salvar:\n";
    $lancamento->save();
    echo "   ✅ Salvo com sucesso!\n";

    // Mostrar as queries executadas
    $queries = DB::getQueryLog();
    echo "\n📊 Queries executadas:\n";
    foreach ($queries as $query) {
        echo "   SQL: {$query['sql']}\n";
        echo "   Bindings: " . json_encode($query['bindings']) . "\n";
        echo "   Time: {$query['time']}ms\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro: {$e->getMessage()}\n\n";

    // Mostrar as queries que falharam
    $queries = DB::getQueryLog();
    if (!empty($queries)) {
        echo "📊 Queries que falharam:\n";
        foreach ($queries as $query) {
            echo "   SQL: {$query['sql']}\n";
            echo "   Bindings: " . json_encode($query['bindings']) . "\n\n";
        }
    }
}

// Testar se o enum está configurado corretamente
echo "🔍 Verificando configuração do model:\n";
$casts = $lancamento->getCasts();
echo "   Cast para situacao_financeira: " . ($casts['situacao_financeira'] ?? 'não definido') . "\n";

// Verificar estrutura da tabela
echo "\n🏗️ Estrutura da coluna situacao_financeira:\n";
$columns = DB::select("DESCRIBE lancamentos");
foreach ($columns as $column) {
    if ($column->Field === 'situacao_financeira') {
        echo "   Tipo: {$column->Type}\n";
        echo "   Null: {$column->Null}\n";
        echo "   Default: " . ($column->Default ?? 'NULL') . "\n";
        break;
    }
}

// Verificar valores possíveis do enum na tabela
echo "\n📋 Valores únicos na coluna situacao_financeira:\n";
$valores = DB::select("SELECT DISTINCT situacao_financeira FROM lancamentos WHERE situacao_financeira IS NOT NULL LIMIT 10");
foreach ($valores as $valor) {
    echo "   - '{$valor->situacao_financeira}'\n";
}

echo "\n🎯 POSSÍVEL SOLUÇÃO:\n";
echo "Se o erro persistir, pode ser necessário:\n";
echo "1. Verificar se a coluna é ENUM no banco de dados\n";
echo "2. Alterar para VARCHAR se necessário\n";
echo "3. Ou ajustar a migration para incluir 'parcialmente_pago'\n";
