<?php

use Illuminate\Support\Facades\Route;
use App\Controllers\Payment\PaymentController;
use App\Controllers\Payment\WebhookController;

/*
|--------------------------------------------------------------------------
| Payment API Routes
|--------------------------------------------------------------------------
|
| Rotas da API para o sistema de pagamentos
|
*/

Route::prefix('payments')->group(function () {

    // Rotas principais de pagamento
    Route::post('/', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/methods', [PaymentController::class, 'paymentMethods'])->name('payments.methods');
    Route::get('/', [PaymentController::class, 'list'])->name('payments.list');
    Route::get('/{transactionId}', [PaymentController::class, 'show'])->name('payments.show');

    // Processamento de pagamentos
    Route::post('/{transactionId}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/{transactionId}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('/{transactionId}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
    Route::post('/{transactionId}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
});

// Rotas de webhooks (não precisam de autenticação)
Route::prefix('webhooks')->group(function () {

    // Webhook genérico por provider
    Route::post('/{provider}', [WebhookController::class, 'handle'])->name('webhooks.handle');

    // Webhooks específicos por gateway
    Route::post('/safe2pay', [WebhookController::class, 'safe2pay'])->name('webhooks.safe2pay');
});

// Rotas administrativas de webhooks (precisam de autenticação)
Route::prefix('admin/webhooks')->middleware(['auth:api'])->group(function () {

    Route::get('/', [WebhookController::class, 'list'])->name('admin.webhooks.list');
    Route::get('/{webhookId}', [WebhookController::class, 'show'])->name('admin.webhooks.show');
    Route::post('/{webhookId}/reprocess', [WebhookController::class, 'reprocess'])->name('admin.webhooks.reprocess');
    Route::post('/test', [WebhookController::class, 'test'])->name('admin.webhooks.test');
});
