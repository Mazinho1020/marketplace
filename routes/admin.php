<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;

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
    Route::get('/menu-preview', function() {
        return view('admin.menu-preview');
    })->name('menu-preview');

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
});
