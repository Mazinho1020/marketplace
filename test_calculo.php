<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;
use App\Models\Financial\LancamentoFinanceiro;

$hoje = Carbon::today();
echo "Data de hoje: " . $hoje->toDateString() . "\n";

$valor = LancamentoFinanceiro::where('empresa_id', 1)
    ->where('natureza_financeira', 'receber')
    ->whereDate('data_vencimento', '=', $hoje)
    ->where('situacao_financeira', '!=', 'pago')
    ->sum('valor');

echo "Vencendo hoje: R$ " . number_format($valor, 2, ',', '.') . "\n";

// Verificar também pendentes
$pendentes = LancamentoFinanceiro::where('empresa_id', 1)
    ->where('natureza_financeira', 'receber')
    ->where('situacao_financeira', 'pendente')
    ->sum('valor');

echo "Total pendente: R$ " . number_format($pendentes, 2, ',', '.') . "\n";
