<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;

echo "=== TESTE DE CRIAÇÃO DE LANÇAMENTO ===\n";

// Criar um novo lançamento de teste
$lancamento = LancamentoFinanceiro::create([
    'empresa_id' => 1,
    'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
    'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
    'descricao' => 'Teste de todas as datas',
    'valor' => 500.00,
    'valor_original' => 500.00,
    'valor_desconto' => 0,
    'valor_acrescimo' => 0,
    'valor_juros' => 0,
    'valor_multa' => 0,
    'valor_final' => 500.00,
    'data' => now(),
    'data_emissao' => now()->toDateString(),
    'data_competencia' => now()->toDateString(),
    'data_vencimento' => now()->addDays(30)->toDateString(),
    'cliente_id' => 3,
    'pessoa_id' => 3,
    'pessoa_tipo' => 'cliente',
    'conta_gerencial_id' => 1,
    'numero_documento' => 'TESTE-' . time(),
    'observacoes' => 'Lançamento de teste completo',
    'usuario_id' => 1,
]);

echo "Lançamento criado com ID: " . $lancamento->id . "\n";
echo "Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
echo "Data emissão: " . $lancamento->data_emissao . "\n";
echo "Data competência: " . $lancamento->data_competencia . "\n";
echo "Data vencimento: " . $lancamento->data_vencimento . "\n";
echo "Cliente ID: " . $lancamento->cliente_id . "\n";
echo "Pessoa ID: " . $lancamento->pessoa_id . "\n";
echo "Tipo pessoa: " . $lancamento->pessoa_tipo . "\n";

// Testar estatísticas
$hoje = Carbon::today();
$vencendoHoje = LancamentoFinanceiro::where('empresa_id', 1)
    ->where('natureza_financeira', 'receber')
    ->whereDate('data_vencimento', '=', $hoje)
    ->where('situacao_financeira', '!=', 'pago')
    ->sum('valor');

echo "\n=== ESTATÍSTICAS ===\n";
echo "Vencendo hoje: R$ " . number_format($vencendoHoje, 2, ',', '.') . "\n";
