<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;

echo "=== TESTE DE CRIAÇÃO COM TODAS AS DATAS ===\n";

$lancamento = LancamentoFinanceiro::create([
    'empresa_id' => 1,
    'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
    'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
    'descricao' => 'Teste com todas as datas preenchidas',
    'valor' => 750.00,
    'valor_original' => 750.00,
    'valor_desconto' => 0,
    'valor_acrescimo' => 0,
    'valor_juros' => 0,
    'valor_multa' => 0,
    'valor_final' => 750.00,
    'data' => Carbon::parse('2025-08-13 10:00:00'),
    'data_emissao' => '2025-08-13',
    'data_competencia' => '2025-08-13',
    'data_vencimento' => '2025-09-13',
    'cliente_id' => 3,
    'pessoa_id' => 3,
    'pessoa_tipo' => 'cliente',
    'conta_gerencial_id' => 1,
    'numero_documento' => 'DOC-' . time(),
    'observacoes' => 'Teste completo de todas as datas',
    'usuario_id' => 1,
]);

echo "✅ Lançamento criado com ID: " . $lancamento->id . "\n";
echo "📅 Data (datetime): " . $lancamento->data . "\n";
echo "📅 Data emissão: " . $lancamento->data_emissao . "\n";
echo "📅 Data competência: " . $lancamento->data_competencia . "\n";
echo "📅 Data vencimento: " . $lancamento->data_vencimento . "\n";
echo "💰 Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
echo "👤 Cliente ID: " . $lancamento->cliente_id . "\n";
echo "👤 Pessoa ID: " . $lancamento->pessoa_id . "\n";
echo "🏷️ Tipo pessoa: " . $lancamento->pessoa_tipo . "\n";

// Verificar no banco de dados direto
echo "\n=== VERIFICAÇÃO NO BANCO ===\n";
$verificacao = \DB::table('lancamentos')->where('id', $lancamento->id)->first();
echo "Data BD: " . $verificacao->data . "\n";
echo "Data emissão BD: " . $verificacao->data_emissao . "\n";
echo "Data competência BD: " . $verificacao->data_competencia . "\n";
echo "Data vencimento BD: " . $verificacao->data_vencimento . "\n";
