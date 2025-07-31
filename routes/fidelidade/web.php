<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fidelidade\FidelidadeController;
use App\Http\Controllers\Fidelidade\CarteirasController;
use App\Http\Controllers\Fidelidade\CuponsController;
use App\Http\Controllers\Fidelidade\RegrasController;
use App\Http\Controllers\Fidelidade\TransacoesController;
use App\Http\Controllers\Fidelidade\RelatoriosController;

/*
|--------------------------------------------------------------------------
| Rotas do Módulo Fidelidade
|--------------------------------------------------------------------------
|
| Todas as rotas relacionadas ao sistema de fidelidade e cashback
|
*/

Route::group(['prefix' => 'fidelidade', 'as' => 'fidelidade.'], function () {

    // Dashboard Principal
    Route::get('/', [FidelidadeController::class, 'index'])->name('dashboard');
    Route::get('/configuracoes', [FidelidadeController::class, 'configuracoes'])->name('configuracoes');
    Route::post('/configuracoes', [FidelidadeController::class, 'salvarConfiguracoes'])->name('configuracoes.salvar');

    // Gestão de Carteiras
    Route::group(['prefix' => 'carteiras', 'as' => 'carteiras.'], function () {
        Route::get('/', [CarteirasController::class, 'index'])->name('index');
        Route::get('/criar', [CarteirasController::class, 'create'])->name('create');
        Route::post('/', [CarteirasController::class, 'store'])->name('store');
        Route::get('/{id}', [CarteirasController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [CarteirasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CarteirasController::class, 'update'])->name('update');
        Route::delete('/{id}', [CarteirasController::class, 'destroy'])->name('destroy');

        // Ações especiais de carteiras
        Route::patch('/{id}/ajustar-saldo', [CarteirasController::class, 'ajustarSaldo'])->name('ajustar-saldo');
        Route::patch('/{id}/bloquear', [CarteirasController::class, 'bloquear'])->name('bloquear');
        Route::patch('/{id}/desbloquear', [CarteirasController::class, 'desbloquear'])->name('desbloquear');
        Route::get('/exportar/csv', [CarteirasController::class, 'exportar'])->name('exportar');
    });

    // Gestão de Cupons
    Route::group(['prefix' => 'cupons', 'as' => 'cupons.'], function () {
        Route::get('/', [CuponsController::class, 'index'])->name('index');
        Route::get('/criar', [CuponsController::class, 'create'])->name('create');
        Route::post('/', [CuponsController::class, 'store'])->name('store');
        Route::get('/{id}', [CuponsController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [CuponsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CuponsController::class, 'update'])->name('update');
        Route::delete('/{id}', [CuponsController::class, 'destroy'])->name('destroy');

        // API para validação e aplicação de cupons
        Route::post('/validar', [CuponsController::class, 'validarCupom'])->name('validar');
        Route::post('/aplicar', [CuponsController::class, 'aplicarCupom'])->name('aplicar');
    });

    // Gestão de Regras de Cashback
    Route::group(['prefix' => 'regras', 'as' => 'regras.'], function () {
        Route::get('/', [RegrasController::class, 'index'])->name('index');
        Route::get('/criar', [RegrasController::class, 'create'])->name('create');
        Route::post('/', [RegrasController::class, 'store'])->name('store');
        Route::get('/{id}', [RegrasController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [RegrasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RegrasController::class, 'update'])->name('update');
        Route::delete('/{id}', [RegrasController::class, 'destroy'])->name('destroy');

        // Ações especiais de regras
        Route::post('/{id}/duplicar', [RegrasController::class, 'duplicar'])->name('duplicar');
        Route::post('/{id}/ativar', [RegrasController::class, 'ativar'])->name('ativar');
        Route::post('/{id}/desativar', [RegrasController::class, 'desativar'])->name('desativar');
        Route::post('/ordenar', [RegrasController::class, 'ordenar'])->name('ordenar');
        Route::post('/{id}/testar', [RegrasController::class, 'testarRegra'])->name('testar');
    });

    // Gestão de Transações
    Route::group(['prefix' => 'transacoes', 'as' => 'transacoes.'], function () {
        Route::get('/', [TransacoesController::class, 'index'])->name('index');
        Route::get('/criar', [TransacoesController::class, 'create'])->name('create');
        Route::post('/', [TransacoesController::class, 'store'])->name('store');
        Route::get('/dashboard', [TransacoesController::class, 'dashboard'])->name('dashboard');
        Route::get('/{transacao}', [TransacoesController::class, 'show'])->name('show');
        Route::get('/{transacao}/editar', [TransacoesController::class, 'edit'])->name('edit');
        Route::put('/{transacao}', [TransacoesController::class, 'update'])->name('update');
        Route::delete('/{transacao}', [TransacoesController::class, 'destroy'])->name('destroy');

        // Ações de processamento
        Route::post('/{id}/processar', [TransacoesController::class, 'processar'])->name('processar');
        Route::post('/{id}/cancelar', [TransacoesController::class, 'cancelar'])->name('cancelar');
        Route::post('/{id}/estornar', [TransacoesController::class, 'estornar'])->name('estornar');
        Route::post('/criar-manual', [TransacoesController::class, 'criarManual'])->name('criar-manual');
        Route::get('/exportar/csv', [TransacoesController::class, 'exportar'])->name('exportar');
    });

    // Relatórios
    Route::group(['prefix' => 'relatorios', 'as' => 'relatorios.'], function () {
        Route::get('/', [RelatoriosController::class, 'index'])->name('index');
        Route::get('/dashboard', [RelatoriosController::class, 'dashboard'])->name('dashboard');
        Route::get('/transacoes', [RelatoriosController::class, 'transacoes'])->name('transacoes');
        Route::get('/clientes', [RelatoriosController::class, 'clientes'])->name('clientes');
        Route::get('/cupons', [RelatoriosController::class, 'cupons'])->name('cupons');
        Route::get('/performance', [RelatoriosController::class, 'performance'])->name('performance');

        // Exportação de relatórios
        Route::get('/exportar/transacoes', [RelatoriosController::class, 'exportarTransacoes'])->name('exportar.transacoes');
        Route::get('/exportar/clientes', [RelatoriosController::class, 'exportarClientes'])->name('exportar.clientes');
    });

    // API Routes para integração
    Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
        Route::post('/calcular-cashback', function (\Illuminate\Http\Request $request) {
            // Endpoint para calcular cashback baseado nas regras
            return response()->json(['cashback' => 0, 'pontos' => 0]);
        })->name('calcular-cashback');

        Route::post('/processar-transacao', function (\Illuminate\Http\Request $request) {
            // Endpoint para processar transação de cashback
            return response()->json(['sucesso' => true]);
        })->name('processar-transacao');

        Route::get('/saldo-cliente/{cliente_id}', function ($clienteId) {
            // Endpoint para consultar saldo do cliente
            $carteira = \App\Models\Fidelidade\FidelidadeCarteira::where('cliente_id', $clienteId)->first();
            return response()->json([
                'saldo_cashback' => $carteira->saldo_cashback ?? 0,
                'saldo_creditos' => $carteira->saldo_creditos ?? 0,
                'saldo_total' => $carteira->saldo_total_disponivel ?? 0
            ]);
        })->name('saldo-cliente');
    });
});
