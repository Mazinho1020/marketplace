<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\Api\ProdutoApiController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rotas administrativas para gerenciamento do sistema de pagamentos
|
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Preview do novo menu (temporário)
    Route::get('/menu-preview', function () {
        return view('admin.menu-preview');
    })->name('menu-preview');

    // Demo da integração de fidelidade (temporário)
    Route::get('/fidelidade-integrado', function () {
        return view('admin.fidelidade-integrado');
    })->name('fidelidade-integrado');

    // Sistema de Fidelidade (Admin View)
    Route::prefix('fidelidade')->name('fidelidade.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'dashboard'])->name('dashboard');
        Route::get('/index', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'dashboard'])->name('index');
        Route::get('/clientes', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'clientes'])->name('clientes');
        Route::get('/transacoes', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'transacoes'])->name('transacoes');
        Route::get('/cupons', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cupons'])->name('cupons');
        Route::get('/cashback', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cashback'])->name('cashback');
        Route::get('/relatorios', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'relatorios'])->name('relatorios');
        Route::get('/configuracoes', [\App\Http\Controllers\Admin\AdminFidelidadeController::class, 'configuracoes'])->name('configuracoes');
    });

    // Gestão de Empresas
    Route::prefix('empresas')->name('empresas.')->group(function () {
        Route::get('/', [EmpresaController::class, 'index'])->name('index');
        Route::get('/create', [EmpresaController::class, 'create'])->name('create');
        Route::get('/relatorio', [EmpresaController::class, 'relatorio'])->name('relatorio');
        Route::get('/export/{format}', [EmpresaController::class, 'export'])->name('export');
        Route::post('/', [EmpresaController::class, 'store'])->name('store');
        Route::get('/{id}', [EmpresaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EmpresaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EmpresaController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmpresaController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-status', [EmpresaController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Gestão de Pagamentos
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/dashboard', [PaymentController::class, 'index'])->name('dashboard'); // Alias para dashboard
        Route::get('/gateways', [PaymentController::class, 'gateways'])->name('gateways');
        Route::get('/methods', [PaymentController::class, 'methods'])->name('methods');
        Route::get('/webhooks', [PaymentController::class, 'webhooks'])->name('webhooks');
        Route::get('/reports', [PaymentController::class, 'reports'])->name('reports');
        Route::get('/settings', [PaymentController::class, 'settings'])->name('settings');
        Route::get('/transactions', [PaymentController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/{id}', [PaymentController::class, 'transactionDetails'])->name('transaction-details');
        Route::get('/analytics', [PaymentController::class, 'analytics'])->name('analytics');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show'); // Esta deve ficar por último
    });

    // Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // API de Produtos para Admin
    Route::prefix('api/produtos')->name('api.produtos.')->group(function () {
        Route::get('/dashboard', [ProdutoApiController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [ProdutoApiController::class, 'index'])->name('index');
        Route::post('/', [ProdutoApiController::class, 'store'])->name('store');
        Route::get('/{produto}', [ProdutoApiController::class, 'show'])->name('show');
        Route::put('/{produto}', [ProdutoApiController::class, 'update'])->name('update');
        Route::delete('/{produto}', [ProdutoApiController::class, 'destroy'])->name('destroy');
        Route::patch('/{produto}/estoque', [ProdutoApiController::class, 'atualizarEstoque'])->name('atualizar-estoque');
        Route::get('/{produto}/movimentacoes', [ProdutoApiController::class, 'relatorioMovimentacoes'])->name('movimentacoes');
        Route::post('/bulk-update', [ProdutoApiController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/relatorio/estoque-baixo', [ProdutoApiController::class, 'relatorioEstoqueBaixo'])->name('relatorio.estoque-baixo');
        Route::get('/relatorio/vendas-periodo', [ProdutoApiController::class, 'relatorioVendasPeriodo'])->name('relatorio.vendas-periodo');
    });
});
