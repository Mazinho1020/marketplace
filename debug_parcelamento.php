<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICANDO PROBLEMAS DE PARCELAMENTO ===\n";

// 1. Verificar registro 379
echo "1. Verificando registro ID 379:\n";
$reg379 = \App\Models\Financial\LancamentoFinanceiro::find(379);
if ($reg379) {
    echo "   - Parcela atual: " . ($reg379->parcela_atual ?? 'null') . "\n";
    echo "   - Total parcelas: " . ($reg379->total_parcelas ?? 'null') . "\n";
    echo "   - Grupo parcelas: " . ($reg379->grupo_parcelas ?? 'null') . "\n";
    echo "   - Descrição: " . $reg379->descricao . "\n";
} else {
    echo "   - Registro não encontrado\n";
}

// 2. Verificar últimos lançamentos parcelados
echo "\n2. Últimos lançamentos com parcelamento:\n";
$parcelados = \App\Models\Financial\LancamentoFinanceiro::whereNotNull('grupo_parcelas')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

if ($parcelados->count() > 0) {
    foreach ($parcelados as $p) {
        echo "   - ID: {$p->id} | Parcela: {$p->parcela_atual}/{$p->total_parcelas} | Grupo: " . substr($p->grupo_parcelas, 0, 10) . "...\n";
    }
} else {
    echo "   - Nenhum lançamento parcelado encontrado\n";
}

// 3. Verificar campo cliente_id não usado
echo "\n3. Registros com cliente_id preenchido (para verificar se pode remover):\n";
$comClienteId = \App\Models\Financial\LancamentoFinanceiro::whereNotNull('cliente_id')
    ->where('cliente_id', '>', 0)
    ->count();
echo "   - Total de registros com cliente_id: {$comClienteId}\n";

// 4. Testar criação de parcelamento
echo "\n4. Testando lógica de parcelamento no controller...\n";
echo "   - Verificando se método criarLancamentosParcelados está sendo chamado\n";
