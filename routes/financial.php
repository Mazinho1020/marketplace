<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Financial\ContaGerencialController;
use App\Http\Controllers\Financial\CategoriaContaGerencialController;
use App\Http\Controllers\Financial\ContasPagarController;
use App\Http\Controllers\Financial\ContasReceberController;

/*
|--------------------------------------------------------------------------
| Rotas do Sistema Financeiro
|--------------------------------------------------------------------------
|
| Rotas do sistema financeiro integradas no contexto dos comerciantes.
| Cada empresa tem seu próprio conjunto de dados financeiros.
| 
| URLs seguem o padrão: /comerciantes/empresas/{empresa}/financeiro/*
|
*/

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
        Route::get('/{id}', [CategoriaContaGerencialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CategoriaContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoriaContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriaContaGerencialController::class, 'destroy'])->name('destroy');

        // Rotas especiais
        Route::get('/tipo/{tipo}', [CategoriaContaGerencialController::class, 'byType'])->name('by-type');
        Route::get('/api/selecao', [CategoriaContaGerencialController::class, 'forSelection'])->name('for-selection');
        Route::post('/{id}/duplicar', [CategoriaContaGerencialController::class, 'duplicate'])->name('duplicate');
        Route::post('/importar-padrao', [CategoriaContaGerencialController::class, 'importDefault'])->name('import-default');
        Route::get('/api/estatisticas', [CategoriaContaGerencialController::class, 'statistics'])->name('statistics');
    });

    // Rotas das Contas Gerenciais
    Route::prefix('contas')->name('contas.')->group(function () {
        Route::get('/', [ContaGerencialController::class, 'index'])->name('index');
        Route::get('/create', [ContaGerencialController::class, 'create'])->name('create');
        Route::post('/', [ContaGerencialController::class, 'store'])->name('store');
        Route::get('/{id}', [ContaGerencialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContaGerencialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContaGerencialController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContaGerencialController::class, 'destroy'])->name('destroy');

        // Rotas especiais
        Route::get('/api/hierarquia', [ContaGerencialController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/api/para-lancamento', [ContaGerencialController::class, 'forLaunch'])->name('for-launch');
        Route::get('/categoria/{categoriaId}', [ContaGerencialController::class, 'byCategory'])->name('by-category');
        Route::get('/natureza/{natureza}', [ContaGerencialController::class, 'byNature'])->name('by-nature');
        Route::post('/importar-padrao', [ContaGerencialController::class, 'importDefault'])->name('import-default');
    });

    // Rotas para Contas a Pagar
    Route::prefix('contas-pagar')->name('contas-pagar.')->group(function () {
        Route::get('/', [ContasPagarController::class, 'index'])->name('index');
        Route::get('/create', [ContasPagarController::class, 'create'])->name('create');
        Route::post('/', [ContasPagarController::class, 'store'])->name('store');
        Route::get('/{id}', [ContasPagarController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContasPagarController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContasPagarController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContasPagarController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pagar', [ContasPagarController::class, 'pagar'])->name('pagar');
    });

    // Rotas para Contas a Receber
    Route::prefix('contas-receber')->name('contas-receber.')->group(function () {
        Route::get('/', [ContasReceberController::class, 'index'])->name('index');
        Route::get('/create', [ContasReceberController::class, 'create'])->name('create');
        Route::post('/', [ContasReceberController::class, 'store'])->name('store');
        Route::get('/{id}', [ContasReceberController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ContasReceberController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ContasReceberController::class, 'update'])->name('update');
        Route::delete('/{id}', [ContasReceberController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/receber', [ContasReceberController::class, 'receber'])->name('receber');
        Route::post('/{id}/gerar-boleto', [ContasReceberController::class, 'gerarBoleto'])->name('gerar-boleto');
    });

    // Rotas para APIs gerais do financeiro
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/resumo', function () {
            return response()->json(['message' => 'API de resumo financeiro']);
        })->name('resumo');

        Route::get('/relatorios', function () {
            return response()->json(['message' => 'API de relatórios financeiros']);
        })->name('relatorios');
    });
});
