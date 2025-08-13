<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financial\ContaGerencialController;
use App\Http\Controllers\Financial\CategoriaContaGerencialController;
use App\Http\Controllers\Financial\ContasPagarController;
use App\Http\Controllers\Financial\ContasReceberController;
use App\Http\Controllers\Financial\DashboardFinanceiroController;

/*
|--------------------------------------------------------------------------
| API Routes do Sistema Financeiro
|--------------------------------------------------------------------------
|
| Complete REST API for the financial system with 1:N relationship
| between lancamentos and pagamentos.
| 
| All routes require empresa_id parameter for multi-tenancy.
|
*/

// ===== API ROUTES =====
Route::prefix('api')->name('api.')->group(function () {
    
    // Dashboard Financeiro
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('dashboard', [DashboardFinanceiroController::class, 'index'])->name('dashboard');
        Route::get('dashboard/resumo', [DashboardFinanceiroController::class, 'resumo'])->name('dashboard.resumo');
        Route::get('dashboard/fluxo-caixa', [DashboardFinanceiroController::class, 'fluxoCaixa'])->name('dashboard.fluxo-caixa');
        Route::get('dashboard/graficos', [DashboardFinanceiroController::class, 'graficos'])->name('dashboard.graficos');
    });

    // Contas a Pagar - Complete REST API
    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContasPagarController::class, 'index'])->name('index');
        Route::post('/', [ContasPagarController::class, 'store'])->name('store');
        Route::get('/dashboard', [ContasPagarController::class, 'dashboard'])->name('dashboard');
        Route::get('/vencidas', [ContasPagarController::class, 'vencidas'])->name('vencidas');
        
        Route::prefix('{id}')->group(function () {
            Route::get('/', [ContasPagarController::class, 'show'])->name('show');
            Route::put('/', [ContasPagarController::class, 'update'])->name('update');
            Route::post('/pagar', [ContasPagarController::class, 'pagar'])->name('pagar');
            Route::post('/cancelar', [ContasPagarController::class, 'cancelar'])->name('cancelar');
            Route::get('/pagamentos', [ContasPagarController::class, 'pagamentos'])->name('pagamentos');
        });
        
        Route::delete('/pagamentos/{pagamentoId}', [ContasPagarController::class, 'estornarPagamento'])->name('estornar-pagamento');
    });

    // Contas a Receber - Complete REST API
    Route::prefix('contas-receber')->name('contas-receber.')->group(function () {
        Route::get('/', [ContasReceberController::class, 'index'])->name('index');
        Route::post('/', [ContasReceberController::class, 'store'])->name('store');
        Route::get('/dashboard', [ContasReceberController::class, 'dashboard'])->name('dashboard');
        Route::get('/vencidas', [ContasReceberController::class, 'vencidas'])->name('vencidas');
        Route::get('/inadimplencia', [ContasReceberController::class, 'inadimplencia'])->name('inadimplencia');
        
        Route::prefix('{id}')->group(function () {
            Route::get('/', [ContasReceberController::class, 'show'])->name('show');
            Route::put('/', [ContasReceberController::class, 'update'])->name('update');
            Route::post('/receber', [ContasReceberController::class, 'receber'])->name('receber');
            Route::post('/cancelar', [ContasReceberController::class, 'cancelar'])->name('cancelar');
            Route::get('/pagamentos', [ContasReceberController::class, 'pagamentos'])->name('pagamentos');
        });
        
        Route::delete('/pagamentos/{pagamentoId}', [ContasReceberController::class, 'estornarPagamento'])->name('estornar-pagamento');
    });

    // Conta Gerencial - Management
    Route::prefix('conta-gerencial')->name('conta-gerencial.')->group(function () {
        Route::get('/', [ContaGerencialController::class, 'index'])->name('index');
        Route::post('/', [ContaGerencialController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaGerencialController::class, 'show'])->name('show');
        Route::put('/{id}', [ContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaGerencialController::class, 'destroy'])->name('destroy');
    });

    // Categorias - Management
    Route::prefix('categorias-conta')->name('categorias-conta.')->group(function () {
        Route::get('/', [CategoriaContaGerencialController::class, 'index'])->name('index');
        Route::post('/', [CategoriaContaGerencialController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoriaContaGerencialController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoriaContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriaContaGerencialController::class, 'destroy'])->name('destroy');
    });
});

// ===== WEB ROUTES (Optional - for Blade views) =====
// Rotas do Sistema Financeiro dentro do contexto de cada empresa
Route::prefix('comerciantes/empresas/{empresa}/financeiro')->name('comerciantes.empresas.financeiro.')->group(function () {

    // Dashboard Financeiro
    Route::get('/', function ($empresa) {
        return view('comerciantes.financeiro.dashboard', compact('empresa'));
    })->name('dashboard');

    // Rotas das Categorias de Conta
    Route::prefix('categorias')->name('categorias.')->group(function () {
        Route::get('/', [CategoriaContaGerencialController::class, 'index'])->name('index');
        Route::get('/create', [CategoriaContaGerencialController::class, 'create'])->name('create');
        Route::post('/', [CategoriaContaGerencialController::class, 'store'])->name('store');
        Route::get('/{categoria}', [CategoriaContaGerencialController::class, 'show'])->name('show');
        Route::get('/{categoria}/edit', [CategoriaContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{categoria}', [CategoriaContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{categoria}', [CategoriaContaGerencialController::class, 'destroy'])->name('destroy');
    });

    // Rotas do Plano de Contas
    Route::prefix('contas')->name('contas.')->group(function () {
        Route::get('/', [ContaGerencialController::class, 'index'])->name('index');
        Route::get('/create', [ContaGerencialController::class, 'create'])->name('create');
        Route::post('/', [ContaGerencialController::class, 'store'])->name('store');
        Route::get('/{conta}', [ContaGerencialController::class, 'show'])->name('show');
        Route::get('/{conta}/edit', [ContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{conta}', [ContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{conta}', [ContaGerencialController::class, 'destroy'])->name('destroy');
    });

    // Rotas das Contas a Pagar
    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContasPagarController::class, 'index'])->name('index');
        Route::get('/create', [ContasPagarController::class, 'create'])->name('create');
        Route::post('/', [ContasPagarController::class, 'store'])->name('store');
        Route::get('/{conta}', [ContasPagarController::class, 'show'])->name('show');
        Route::get('/{conta}/edit', [ContasPagarController::class, 'edit'])->name('edit');
        Route::put('/{conta}', [ContasPagarController::class, 'update'])->name('update');
        Route::delete('/{conta}', [ContasPagarController::class, 'destroy'])->name('destroy');
    });

    // Rotas das Contas a Receber  
    Route::prefix('contas-receber')->name('contas-receber.')->group(function () {
        Route::get('/', [ContasReceberController::class, 'index'])->name('index');
        Route::get('/create', [ContasReceberController::class, 'create'])->name('create');
        Route::post('/', [ContasReceberController::class, 'store'])->name('store');
        Route::get('/{conta}', [ContasReceberController::class, 'show'])->name('show');
        Route::get('/{conta}/edit', [ContasReceberController::class, 'edit'])->name('edit');
        Route::put('/{conta}', [ContasReceberController::class, 'update'])->name('update');
        Route::delete('/{conta}', [ContasReceberController::class, 'destroy'])->name('destroy');
    });
});