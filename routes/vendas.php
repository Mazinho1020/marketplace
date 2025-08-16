<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Comerciantes\Vendas\VendaController;
use App\Http\Controllers\Comerciantes\Vendas\DashboardVendaController;

/*
|--------------------------------------------------------------------------
| Rotas do Sistema de Vendas
|--------------------------------------------------------------------------
|
| Rotas do sistema de vendas integradas no contexto dos comerciantes.
| Cada empresa tem seu próprio conjunto de dados de vendas.
| 
| URLs seguem o padrão: /comerciantes/empresas/{empresa}/vendas/*
|
*/

// Rotas do Sistema de Vendas dentro do contexto de cada empresa
Route::prefix('comerciantes/empresas/{empresa}/vendas')->name('comerciantes.empresas.vendas.')->group(function () {

    // Dashboard de Vendas
    Route::get('/', [DashboardVendaController::class, 'index'])->name('dashboard');
    
    // APIs para dados dos gráficos
    Route::get('/api/dados-grafico', [DashboardVendaController::class, 'dadosGrafico'])->name('api.dados-grafico');
    Route::get('/api/top-produtos', [DashboardVendaController::class, 'topProdutos'])->name('api.top-produtos');
    
    // Exportar relatórios
    Route::get('/relatorio/exportar', [DashboardVendaController::class, 'exportarRelatorio'])->name('relatorio.exportar');

    // CRUD de Vendas
    Route::prefix('gerenciar')->name('gerenciar.')->group(function () {
        Route::get('/', [VendaController::class, 'index'])->name('index');
        Route::get('/create', [VendaController::class, 'create'])->name('create');
        Route::post('/', [VendaController::class, 'store'])->name('store');
        Route::get('/{venda}', [VendaController::class, 'show'])->name('show');
        Route::get('/{venda}/edit', [VendaController::class, 'edit'])->name('edit');
        Route::put('/{venda}', [VendaController::class, 'update'])->name('update');
        Route::delete('/{venda}', [VendaController::class, 'destroy'])->name('destroy');
        
        // Ações específicas de vendas
        Route::post('/{venda}/confirmar', [VendaController::class, 'confirmar'])->name('confirmar');
        Route::post('/{venda}/cancelar', [VendaController::class, 'cancelar'])->name('cancelar');
    });

    // PDV (Point of Sale) - Interface simplificada para vendas rápidas
    Route::prefix('pdv')->name('pdv.')->group(function () {
        Route::get('/', [VendaController::class, 'create'])->name('index');
        Route::post('/venda', [VendaController::class, 'store'])->name('venda');
        Route::get('/buscar-produtos', [VendaController::class, 'buscarProdutos'])->name('buscar-produtos');
    });

    // Relatórios específicos
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/vendas-periodo', function ($empresa) {
            return view('comerciantes.vendas.relatorios.periodo', compact('empresa'));
        })->name('vendas-periodo');
        
        Route::get('/produtos-mais-vendidos', function ($empresa) {
            return view('comerciantes.vendas.relatorios.produtos-mais-vendidos', compact('empresa'));
        })->name('produtos-mais-vendidos');
        
        Route::get('/vendedores', function ($empresa) {
            return view('comerciantes.vendas.relatorios.vendedores', compact('empresa'));
        })->name('vendedores');
        
        Route::get('/clientes', function ($empresa) {
            return view('comerciantes.vendas.relatorios.clientes', compact('empresa'));
        })->name('clientes');
    });

});