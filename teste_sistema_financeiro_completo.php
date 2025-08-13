<?php

// Teste básico do sistema financeiro
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTE DO SISTEMA FINANCEIRO COMPLETO ===\n\n";

try {
    // 1. Testar Enums
    echo "1. Testando Enums:\n";

    $natureza = \App\Enums\NaturezaFinanceiraEnum::PAGAR;
    echo "   ✅ NaturezaFinanceiraEnum: {$natureza->value} - {$natureza->label()}\n";

    $situacao = \App\Enums\SituacaoFinanceiraEnum::PENDENTE;
    echo "   ✅ SituacaoFinanceiraEnum: {$situacao->value} - {$situacao->label()}\n";

    $frequencia = \App\Enums\FrequenciaRecorrenciaEnum::MENSAL;
    echo "   ✅ FrequenciaRecorrenciaEnum: {$frequencia->value} - {$frequencia->label()}\n\n";

    // 2. Testar Model LancamentoFinanceiro
    echo "2. Testando Model LancamentoFinanceiro:\n";

    $lancamento = new \App\Models\Financial\LancamentoFinanceiro();
    echo "   ✅ Model criado com sucesso\n";
    echo "   ✅ Tabela: {$lancamento->getTable()}\n";

    $fillable = $lancamento->getFillable();
    echo "   ✅ Campos fillable: " . count($fillable) . " campos\n\n";

    // 3. Testar relacionamentos
    echo "3. Testando relacionamentos:\n";

    // Verificar se existem registros na tabela pessoas
    $totalPessoas = \App\Models\Cliente::count();
    echo "   ✅ Total de pessoas: {$totalPessoas}\n";

    // Verificar se existem contas gerenciais
    $totalContas = \App\Models\Financial\ContaGerencial::count();
    echo "   ✅ Total de contas gerenciais: {$totalContas}\n";

    // Verificar empresas
    $totalEmpresas = \App\Models\Empresa::count();
    echo "   ✅ Total de empresas: {$totalEmpresas}\n\n";

    // 4. Testar criação de lançamento
    echo "4. Testando criação de lançamento:\n";

    $dados = [
        'empresa_id' => 1,
        'natureza_financeira' => \App\Enums\NaturezaFinanceiraEnum::PAGAR,
        'situacao_financeira' => \App\Enums\SituacaoFinanceiraEnum::PENDENTE,
        'descricao' => 'Teste de conta a pagar',
        'valor' => 1000.00,
        'valor_original' => 1000.00,
        'data_vencimento' => now()->addDays(30),
        'pessoa_id' => 1, // Usar primeira pessoa
        'observacoes' => 'Teste criado via script',
    ];

    $lancamentoTeste = \App\Models\Financial\LancamentoFinanceiro::create($dados);
    echo "   ✅ Lançamento criado com ID: {$lancamentoTeste->id}\n";
    echo "   ✅ Descrição: {$lancamentoTeste->descricao}\n";
    echo "   ✅ Valor: R$ " . number_format($lancamentoTeste->valor_original, 2, ',', '.') . "\n";
    echo "   ✅ Vencimento: {$lancamentoTeste->data_vencimento->format('d/m/Y')}\n\n";

    // 5. Testar métodos do model
    echo "5. Testando métodos do model:\n";

    $valorFinal = $lancamentoTeste->calcularValorFinal();
    echo "   ✅ Valor final calculado: R$ " . number_format($valorFinal, 2, ',', '.') . "\n";

    $diasVencimento = $lancamentoTeste->diasParaVencimento();
    echo "   ✅ Dias para vencimento: {$diasVencimento}\n";

    $estaPendente = $lancamentoTeste->isPendente() ? 'Sim' : 'Não';
    echo "   ✅ Está pendente: {$estaPendente}\n\n";

    // 6. Testar scopes
    echo "6. Testando scopes:\n";

    $totalPendentes = \App\Models\Financial\LancamentoFinanceiro::pendentes()->count();
    echo "   ✅ Total de lançamentos pendentes: {$totalPendentes}\n";

    $totalPagar = \App\Models\Financial\LancamentoFinanceiro::where('natureza_financeira', \App\Enums\NaturezaFinanceiraEnum::PAGAR)->count();
    echo "   ✅ Total de contas a pagar: {$totalPagar}\n";

    $totalReceber = \App\Models\Financial\LancamentoFinanceiro::where('natureza_financeira', \App\Enums\NaturezaFinanceiraEnum::RECEBER)->count();
    echo "   ✅ Total de contas a receber: {$totalReceber}\n\n";

    // 7. Limpar teste
    echo "7. Limpando dados de teste:\n";
    $lancamentoTeste->delete();
    echo "   ✅ Lançamento de teste excluído\n\n";

    echo "🎉 TODOS OS TESTES PASSARAM COM SUCESSO!\n";
    echo "Sistema financeiro implementado corretamente.\n\n";

    echo "=== PRÓXIMOS PASSOS ===\n";
    echo "1. Criar as views (interfaces) para o sistema\n";
    echo "2. Implementar sistema de cobrança automática\n";
    echo "3. Integrar com gateway de pagamento para boletos\n";
    echo "4. Criar relatórios financeiros\n";
    echo "5. Implementar fluxo de caixa\n";
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n\n";

    // Informações de debug
    echo "=== DEBUG INFO ===\n";
    echo "Verifique se:\n";
    echo "1. A migração foi executada corretamente\n";
    echo "2. Os Enums foram criados corretamente\n";
    echo "3. O Model LancamentoFinanceiro existe\n";
    echo "4. Os relacionamentos estão corretos\n";
}
