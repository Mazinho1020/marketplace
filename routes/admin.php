<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\AffiliateController;
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

    // Gestão de Merchants
    Route::prefix('merchants')->name('merchants.')->group(function () {
        Route::get('/', [MerchantController::class, 'index'])->name('index');
        Route::get('/{id}', [MerchantController::class, 'show'])->name('show');
        Route::get('/{id}/subscriptions', [MerchantController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/{id}/transactions', [MerchantController::class, 'transactions'])->name('transactions');
        Route::get('/{id}/usage', [MerchantController::class, 'usage'])->name('usage');
    });

    // Gestão de Assinaturas
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [SubscriptionController::class, 'show'])->name('show');
        Route::get('/plans/comparison', [SubscriptionController::class, 'plansComparison'])->name('plans.comparison');
        Route::get('/analytics', [SubscriptionController::class, 'analytics'])->name('analytics');
    });

    // Gestão de Afiliados
    Route::prefix('affiliates')->name('affiliates.')->group(function () {
        Route::get('/', [AffiliateController::class, 'index'])->name('index');
        Route::get('/{id}', [AffiliateController::class, 'show'])->name('show');
        Route::get('/{id}/commissions', [AffiliateController::class, 'commissions'])->name('commissions');
        Route::get('/{id}/referrals', [AffiliateController::class, 'referrals'])->name('referrals');
        Route::get('/{id}/payments', [AffiliateController::class, 'payments'])->name('payments');
        Route::get('/program/statistics', [AffiliateController::class, 'programStatistics'])->name('program.statistics');
        Route::get('/top-performers', [AffiliateController::class, 'topPerformers'])->name('top.performers');
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
        Route::get('/merchants', [ReportController::class, 'merchants'])->name('merchants');
        Route::get('/affiliates', [ReportController::class, 'affiliates'])->name('affiliates');
        Route::get('/subscriptions', [ReportController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });
});
