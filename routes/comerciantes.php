<?php

use Illuminate\Support\Facades\Route;
use App\Comerciantes\Controllers\Auth\LoginController as AuthController;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\EmpresaController;
use App\Comerciantes\Controllers\NotificacaoController;
use App\Comerciantes\Controllers\ProdutoController;
use App\Comerciantes\Controllers\ProdutoCategoriaController;
use App\Comerciantes\Controllers\ProdutoMarcaController;

/*
|--------------------------------------------------------------------------
| Rotas de Comerciantes
|--------------------------------------------------------------------------
*/

// Rotas de autenticação (sem proteção automática)
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Rotas autenticadas (protegidas por login)
Route::prefix('comerciantes')->name('comerciantes.')->middleware(['auth.comerciante'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Empresas
    Route::resource('empresas', EmpresaController::class);
    Route::prefix('empresas/{empresa}')->name('empresas.')->group(function () {
        Route::get('usuarios', [EmpresaController::class, 'usuarios'])->name('usuarios.index');
        Route::post('usuarios', [EmpresaController::class, 'adicionarUsuario'])->name('usuarios.store');
        Route::put('usuarios/{usuario}', [EmpresaController::class, 'editarUsuario'])->name('usuarios.update');
        Route::delete('usuarios/{usuario}', [EmpresaController::class, 'removerUsuario'])->name('usuarios.destroy');
    });

    // Produtos
    Route::prefix('produtos')->name('produtos.')->group(function () {
        // CRUD de Produtos
        Route::get('/', [ProdutoController::class, 'index'])->name('index');
        Route::get('/create', [ProdutoController::class, 'create'])->name('create');
        Route::post('/', [ProdutoController::class, 'store'])->name('store');
        Route::get('/{produto}', [ProdutoController::class, 'show'])->name('show');
        Route::get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('edit');
        Route::put('/{produto}', [ProdutoController::class, 'update'])->name('update');
        Route::delete('/{produto}', [ProdutoController::class, 'destroy'])->name('destroy');

        // Ações especiais de produtos
        Route::post('/{produto}/movimentacao', [ProdutoController::class, 'movimentacao'])->name('movimentacao');
        Route::post('/{produto}/duplicate', [ProdutoController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{produto}/estoque', [ProdutoController::class, 'atualizarEstoque'])->name('atualizar-estoque');
        Route::get('/relatorio/estoque', [ProdutoController::class, 'relatorioEstoque'])->name('relatorio-estoque');
        Route::post('/verificar-estoque-baixo', [ProdutoController::class, 'verificarEstoqueBaixo'])->name('verificar-estoque-baixo');

        // Categorias
        Route::prefix('categorias')->name('categorias.')->group(function () {
            Route::get('/', [ProdutoCategoriaController::class, 'index'])->name('index');
            Route::get('/create', [ProdutoCategoriaController::class, 'create'])->name('create');
            Route::post('/', [ProdutoCategoriaController::class, 'store'])->name('store');
            Route::get('/{categoria}/edit', [ProdutoCategoriaController::class, 'edit'])->name('edit');
            Route::put('/{categoria}', [ProdutoCategoriaController::class, 'update'])->name('update');
            Route::delete('/{categoria}', [ProdutoCategoriaController::class, 'destroy'])->name('destroy');
        });

        // Marcas
        Route::prefix('marcas')->name('marcas.')->group(function () {
            Route::get('/', [ProdutoMarcaController::class, 'index'])->name('index');
            Route::get('/create', [ProdutoMarcaController::class, 'create'])->name('create');
            Route::post('/', [ProdutoMarcaController::class, 'store'])->name('store');
            Route::get('/{marca}/edit', [ProdutoMarcaController::class, 'edit'])->name('edit');
            Route::put('/{marca}', [ProdutoMarcaController::class, 'update'])->name('update');
            Route::delete('/{marca}', [ProdutoMarcaController::class, 'destroy'])->name('destroy');
        });
    });

    // Notificações
    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        Route::get('/', [NotificacaoController::class, 'index'])->name('index');
        Route::get('/dashboard', [NotificacaoController::class, 'dashboard'])->name('dashboard');
        Route::get('/header', [NotificacaoController::class, 'headerNotifications'])->name('header');
        Route::get('/{id}', [NotificacaoController::class, 'show'])->name('show');
        Route::post('/{id}/marcar-lida', [NotificacaoController::class, 'marcarComoLida'])->name('marcar-lida');
        Route::post('/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('marcar-todas-lidas');
    });
});
