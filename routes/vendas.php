<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendas\VendaController;
use App\Http\Controllers\Vendas\VendaStatusController;
use App\Http\Controllers\Vendas\VendaCancelamentoController;

/*
|--------------------------------------------------------------------------
| Rotas do Sistema de Vendas
|--------------------------------------------------------------------------
|
| Rotas para gestão completa de vendas/pedidos, aproveitando 100% da
| infraestrutura existente do marketplace.
|
*/

// Grupo protegido para comerciantes autenticados
Route::middleware(['auth', 'empresa'])->prefix('vendas')->name('vendas.')->group(function () {
    
    // CRUD principal de vendas
    Route::get('/', [VendaController::class, 'index'])->name('index');
    Route::get('/create', [VendaController::class, 'create'])->name('create');
    Route::post('/', [VendaController::class, 'store'])->name('store');
    Route::get('/{id}', [VendaController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [VendaController::class, 'edit'])->name('edit');
    Route::put('/{id}', [VendaController::class, 'update'])->name('update');
    
    // Ações específicas de vendas
    Route::post('/{id}/status', [VendaController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{id}/cancel', [VendaController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/duplicate', [VendaController::class, 'duplicate'])->name('duplicate');
    
    // Gestão de status
    Route::prefix('status')->name('status.')->group(function () {
        Route::get('/{vendaId}/historico', [VendaStatusController::class, 'historico'])->name('historico');
        Route::post('/alterar', [VendaStatusController::class, 'alterar'])->name('alterar');
        Route::get('/workflow', [VendaStatusController::class, 'workflow'])->name('workflow');
    });
    
    // Gestão de cancelamentos
    Route::prefix('cancelamentos')->name('cancelamentos.')->group(function () {
        Route::get('/', [VendaCancelamentoController::class, 'index'])->name('index');
        Route::post('/', [VendaCancelamentoController::class, 'store'])->name('store');
        Route::post('/{id}/aprovar', [VendaCancelamentoController::class, 'aprovar'])->name('aprovar');
        Route::post('/{id}/processar-reembolso', [VendaCancelamentoController::class, 'processarReembolso'])->name('processarReembolso');
    });
});

// APIs para integrações externas
Route::middleware(['auth:api', 'empresa'])->prefix('api/vendas')->name('api.vendas.')->group(function () {
    Route::get('/', [VendaController::class, 'apiIndex']);
    Route::get('/metrics', [VendaController::class, 'metrics']);
    Route::post('/', [VendaController::class, 'store']);
    Route::post('/{id}/status', [VendaController::class, 'updateStatus']);
});