<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__ . '/routes/web.php',
        commands: __DIR__ . '/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;

$lancamento = LancamentoFinanceiro::find(377);

if ($lancamento) {
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $saldoDevedor = $lancamento->valor_final - $valorPago;

    echo "=== DADOS DA CONTA 377 ===" . PHP_EOL;
    echo "Valor Final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . PHP_EOL;
    echo "Valor Pago: R$ " . number_format($valorPago, 2, ',', '.') . PHP_EOL;
    echo "Saldo Devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . PHP_EOL;
    echo "Total de Pagamentos: " . $lancamento->pagamentos()->count() . PHP_EOL;

    echo "\n=== DETALHES DOS PAGAMENTOS ===" . PHP_EOL;
    foreach ($lancamento->pagamentos as $pagamento) {
        echo "Pagamento ID: " . $pagamento->id . PHP_EOL;
        echo "  Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . PHP_EOL;
        echo "  Status: " . $pagamento->status_pagamento . PHP_EOL;
        echo "  Data: " . $pagamento->created_at . PHP_EOL;
        echo "---" . PHP_EOL;
    }
} else {
    echo "Lançamento 377 não encontrado!" . PHP_EOL;
}
