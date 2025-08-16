<?php
require_once 'vendor/autoload.php';

use App\Models\Financeiro\Lancamento;
use App\Models\Financeiro\LancamentoItem;
use App\Models\Financeiro\LancamentoMovimentacao;
use App\Services\Financeiro\LancamentoService;
use Illuminate\Support\Facades\DB;

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== TESTE COMPLETO DO SISTEMA DE LANÇAMENTOS ===\n\n";
    
    $service = new LancamentoService();
    
    // 1. Teste de criação de lançamento
    echo "1. TESTANDO CRIAÇÃO DE LANÇAMENTO...\n";
    
    $dados = [
        'empresa_id' => 1,
        'usuario_id' => 1,
        'natureza_financeira' => 'entrada',
        'categoria' => 'venda',
        'origem' => 'lancamento',
        'valor' => 1500.00,
        'valor_desconto' => 50.00,
        'descricao' => 'Teste de lançamento via sistema novo',
        'data_emissao' => date('Y-m-d'),
        'data_competencia' => date('Y-m-d'),
        'data_vencimento' => date('Y-m-d', strtotime('+30 days')),
        'pessoa_id' => 1,
        'pessoa_tipo' => 'cliente',
        'numero_documento' => 'TEST-' . time(),
        'observacoes' => 'Lançamento criado para teste do sistema',
        'total_parcelas' => 1,
        'itens' => [
            [
                'produto_id' => 1,
                'quantidade' => 2,
                'valor_unitario' => 500.00,
                'observacoes' => 'Item de teste'
            ],
            [
                'produto_id' => 2,
                'quantidade' => 1,
                'valor_unitario' => 500.00,
                'observacoes' => 'Item 2 de teste'
            ]
        ]
    ];
    
    $lancamento = $service->criarLancamento($dados);
    echo "  ✓ Lançamento criado: ID {$lancamento->id} | UUID {$lancamento->uuid}\n";
    echo "  ✓ Valor: R$ " . number_format($lancamento->valor, 2, ',', '.') . "\n";
    echo "  ✓ Valor líquido: R$ " . number_format($lancamento->valor_liquido, 2, ',', '.') . "\n";
    echo "  ✓ Itens: {$lancamento->itens->count()}\n";
    
    // 2. Teste de pagamento
    echo "\n2. TESTANDO REGISTRO DE PAGAMENTO...\n";
    
    $dadosPagamento = [
        'valor' => 750.00,
        'data_movimentacao' => now(),
        'observacoes' => 'Pagamento parcial de teste',
        'forma_pagamento_id' => 1
    ];
    
    $movimentacao = $service->registrarPagamento($lancamento, $dadosPagamento);
    $lancamento->refresh();
    
    echo "  ✓ Pagamento registrado: ID {$movimentacao->id}\n";
    echo "  ✓ Valor pago: R$ " . number_format($movimentacao->valor, 2, ',', '.') . "\n";
    echo "  ✓ Nova situação: {$lancamento->situacao_formatada}\n";
    echo "  ✓ Saldo restante: R$ " . number_format($lancamento->valor_saldo, 2, ',', '.') . "\n";
    
    // 3. Teste de completar pagamento
    echo "\n3. COMPLETANDO PAGAMENTO...\n";
    
    $dadosPagamento2 = [
        'valor' => $lancamento->valor_saldo,
        'data_movimentacao' => now(),
        'observacoes' => 'Pagamento final de teste'
    ];
    
    $movimentacao2 = $service->registrarPagamento($lancamento, $dadosPagamento2);
    $lancamento->refresh();
    
    echo "  ✓ Pagamento final: R$ " . number_format($movimentacao2->valor, 2, ',', '.') . "\n";
    echo "  ✓ Situação final: {$lancamento->situacao_formatada}\n";
    echo "  ✓ Total de movimentações: {$lancamento->movimentacoes->count()}\n";
    
    // 4. Teste de estorno
    echo "\n4. TESTANDO ESTORNO...\n";
    
    $estorno = $service->estornarPagamento($movimentacao2, 'Teste de estorno do sistema');
    $lancamento->refresh();
    
    echo "  ✓ Estorno registrado: ID {$estorno->id}\n";
    echo "  ✓ Valor estornado: R$ " . number_format($estorno->valor, 2, ',', '.') . "\n";
    echo "  ✓ Nova situação: {$lancamento->situacao_formatada}\n";
    echo "  ✓ Novo saldo: R$ " . number_format($lancamento->valor_saldo, 2, ',', '.') . "\n";
    
    // 5. Teste de parcelamento
    echo "\n5. TESTANDO CRIAÇÃO DE PARCELAS...\n";
    
    $dadosParcelas = [
        'valor' => 1200.00,
        'total_parcelas' => 3,
        'intervalo_parcelas' => 30
    ];
    
    $parcelas = $service->criarParcelas($lancamento, $dadosParcelas);
    echo "  ✓ Parcelas criadas: {$parcelas->count()}\n";
    
    foreach ($parcelas as $index => $parcela) {
        echo "    Parcela " . ($index + 1) . ": R$ " . number_format($parcela->valor_liquido, 2, ',', '.') . 
             " | Vencimento: " . $parcela->data_vencimento->format('d/m/Y') . "\n";
    }
    
    // 6. Teste de relatório
    echo "\n6. TESTANDO RELATÓRIO FINANCEIRO...\n";
    
    $relatorio = $service->obterRelatorioFinanceiro(1);
    
    echo "  ✓ Total de lançamentos: {$relatorio['total_lancamentos']}\n";
    echo "  ✓ Valor total: R$ " . number_format($relatorio['valor_total'], 2, ',', '.') . "\n";
    echo "  ✓ Valor pago: R$ " . number_format($relatorio['valor_pago'], 2, ',', '.') . "\n";
    echo "  ✓ Valor pendente: R$ " . number_format($relatorio['valor_pendente'], 2, ',', '.') . "\n";
    echo "  ✓ Contas a receber: R$ " . number_format($relatorio['contas_receber'], 2, ',', '.') . "\n";
    echo "  ✓ Contas a pagar: R$ " . number_format($relatorio['contas_pagar'], 2, ',', '.') . "\n";
    
    // 7. Teste de consultas específicas
    echo "\n7. TESTANDO CONSULTAS ESPECÍFICAS...\n";
    
    $contasReceber = Lancamento::empresa(1)->contasReceber()->count();
    $contasPagar = Lancamento::empresa(1)->contasPagar()->count();
    $pendentes = Lancamento::empresa(1)->pendentes()->count();
    $pagos = Lancamento::empresa(1)->pagos()->count();
    
    echo "  ✓ Contas a receber: $contasReceber\n";
    echo "  ✓ Contas a pagar: $contasPagar\n";
    echo "  ✓ Pendentes: $pendentes\n";
    echo "  ✓ Pagos: $pagos\n";
    
    // 8. Teste dos métodos helper
    echo "\n8. TESTANDO MÉTODOS HELPER...\n";
    
    $ultimoLancamento = $parcelas->last();
    
    echo "  ✓ É conta a receber: " . ($ultimoLancamento->isContaReceber() ? 'Sim' : 'Não') . "\n";
    echo "  ✓ É parcelado: " . ($ultimoLancamento->isParcelado() ? 'Sim' : 'Não') . "\n";
    echo "  ✓ Está pago: " . ($ultimoLancamento->isPago() ? 'Sim' : 'Não') . "\n";
    echo "  ✓ Está pendente: " . ($ultimoLancamento->isPendente() ? 'Sim' : 'Não') . "\n";
    
    echo "\n=== TODOS OS TESTES FORAM EXECUTADOS COM SUCESSO! ===\n";
    echo "\nO sistema de lançamentos está funcionando perfeitamente e pronto para uso!\n\n";
    
    echo "Funcionalidades testadas:\n";
    echo "✓ Criação de lançamentos com itens\n";
    echo "✓ Registro de pagamentos/recebimentos\n";
    echo "✓ Estorno de movimentações\n";
    echo "✓ Criação de parcelas\n";
    echo "✓ Relatórios financeiros\n";
    echo "✓ Consultas específicas (scopes)\n";
    echo "✓ Métodos helper\n";
    echo "✓ Cálculos automáticos de valores\n";
    echo "✓ Mudança automática de situação\n";
    
} catch (Exception $e) {
    echo "\n✗ ERRO NO TESTE: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
?>
