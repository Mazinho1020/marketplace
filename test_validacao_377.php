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

    echo "=== TESTE DE VALIDAÇÃO ===" . PHP_EOL;
    echo "Valor Final: " . $lancamento->valor_final . PHP_EOL;
    echo "Valor Pago: " . $valorPago . PHP_EOL;
    echo "Saldo Devedor: " . $saldoDevedor . PHP_EOL;
    echo "Saldo > 0? " . ($saldoDevedor > 0 ? 'SIM' : 'NÃO') . PHP_EOL;
    echo "Tipo do saldo: " . gettype($saldoDevedor) . PHP_EOL;

    // Testar validação similar ao controller
    $valorTeste1 = 200;
    $valorTeste2 = 300;

    echo "\n=== TESTE DE VALIDAÇÃO VALORES ===" . PHP_EOL;
    echo "Valor teste 1 (200) > saldo devedor (" . $saldoDevedor . ")? " . ($valorTeste1 > $saldoDevedor ? 'SIM' : 'NÃO') . PHP_EOL;
    echo "Valor teste 2 (300) > saldo devedor (" . $saldoDevedor . ")? " . ($valorTeste2 > $saldoDevedor ? 'SIM' : 'NÃO') . PHP_EOL;
}
