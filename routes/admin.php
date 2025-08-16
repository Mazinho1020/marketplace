<?php

use App\Http\Controllers\Admin\Financeiro\ContasPagarController;
use App\Http\Controllers\Admin\Financeiro\ContasReceberController;
// use App\Http\Controllers\Admin\Financeiro\LancamentoController;

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::prefix('financeiro')->group(function () {
        
        // Lançamentos unificados (TEMPORARIAMENTE COMENTADO - CONTROLLER NÃO EXISTE)
        // Route::resource('lancamentos', LancamentoController::class);
        // Route::post('lancamentos/{lancamento}/pagamento', [LancamentoController::class, 'processarPagamento']);
        
        // Contas a Pagar (wrapper)
        Route::resource('contas-pagar', ContasPagarController::class);
        Route::post('contas-pagar/parcelado', [ContasPagarController::class, 'criarParcelado']);
        
        // Contas a Receber (wrapper)
        Route::resource('contas-receber', ContasReceberController::class);
        Route::post('contas-receber/parcelado', [ContasReceberController::class, 'criarParcelado']);
        
    });
});