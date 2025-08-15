#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

echo "=== TESTE DO SISTEMA DE PAGAMENTOS PARCIAIS ===\n\n";

// 1. Verificar se há lançamentos
$totalLancamentos = LancamentoFinanceiro::count();
echo "Total de lançamentos financeiros: {$totalLancamentos}\n";

if ($totalLancamentos == 0) {
    echo "❌ Nenhum lançamento encontrado!\n";
    exit(1);
}

// 2. Pegar um lançamento para testar
$lancamento = LancamentoFinanceiro::first();
echo "Testando com lançamento ID: {$lancamento->id}\n";
echo "Descrição: {$lancamento->descricao}\n";
echo "Valor: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
echo "Empresa ID: {$lancamento->empresa_id}\n";

// 3. Verificar se a empresa existe
$empresa = Empresa::find($lancamento->empresa_id);
if (!$empresa) {
    echo "❌ Empresa não encontrada!\n";
    exit(1);
}
echo "Empresa: {$empresa->nome_fantasia}\n";

// 4. Verificar pagamentos existentes
$pagamentosExistentes = $lancamento->pagamentos()->count();
$valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
$saldoDevedor = $lancamento->valor_final - $valorPago;

echo "\nResumo Financeiro:\n";
echo "- Pagamentos registrados: {$pagamentosExistentes}\n";
echo "- Valor pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";
echo "- Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";

// 5. Gerar URLs para teste
$baseUrl = "http://127.0.0.1:8000";
$showUrl = "{$baseUrl}/comerciantes/empresas/{$empresa->id}/financeiro/contas-pagar/{$lancamento->id}";
$indexUrl = "{$baseUrl}/comerciantes/empresas/{$empresa->id}/financeiro/contas-pagar";

echo "\n=== URLs PARA TESTE ===\n";
echo "📋 Lista de contas: {$indexUrl}\n";
echo "🔍 Detalhes da conta: {$showUrl}\n";

// 6. Verificar se as APIs estão funcionando
echo "\n=== VERIFICANDO APIs ===\n";

// Simular teste das formas de pagamento
try {
    $formasPagamento = DB::table('formas_pagamento')->where('tipo_operacao', 'saida')->get();
    echo "✅ Formas de pagamento encontradas: " . $formasPagamento->count() . "\n";

    if ($formasPagamento->count() > 0) {
        echo "   Exemplos: ";
        echo $formasPagamento->take(3)->pluck('nome')->implode(', ') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar formas de pagamento: " . $e->getMessage() . "\n";
}

// Simular teste das bandeiras
try {
    $bandeiras = DB::table('forma_pag_bandeiras')->get();
    echo "✅ Bandeiras encontradas: " . $bandeiras->count() . "\n";

    if ($bandeiras->count() > 0) {
        echo "   Exemplos: ";
        echo $bandeiras->take(3)->pluck('nome')->implode(', ') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar bandeiras: " . $e->getMessage() . "\n";
}

echo "\n=== INSTRUÇÕES PARA TESTE ===\n";
echo "1. Acesse a URL da lista de contas\n";
echo "2. Clique no botão 'Ver Detalhes' de uma conta\n";
echo "3. Na tela de detalhes, clique em 'Registrar Pagamento'\n";
echo "4. Preencha o formulário e teste o pagamento parcial\n";
echo "5. Verifique se o saldo devedor é atualizado corretamente\n";

echo "\n✅ Sistema pronto para teste!\n";
