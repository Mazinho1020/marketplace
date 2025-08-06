<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Controllers\Auth\LoginController;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\MarcaController;
use App\Comerciantes\Controllers\EmpresaController;
use App\Comerciantes\Controllers\HorarioController;

use function Laravel\Prompts\alert;

/**
 * Rotas do módulo de comerciantes
 * Usa a tabela empresa_usuarios para autenticação
 */
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {

    // ========================
    // ROTAS PÚBLICAS (Login)
    // ========================
    Route::middleware(['guest:comerciante'])->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        // Route::get('/cadastro', [RegisterController::class, 'showRegistrationForm'])->name('register');
        // Route::post('/cadastro', [RegisterController::class, 'register']);
    });
});

// ===========================
// ROTAS PROTEGIDAS (SEPARADAS)
// ===========================
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {
    Route::middleware(['comerciantes.protected'])->group(function () {
        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        /**
         * DASHBOARD
         */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/empresa/{empresa}', [DashboardController::class, 'selecionarEmpresa'])->name('dashboard.empresa');
        Route::get('/dashboard/limpar-empresa', [DashboardController::class, 'limparSelecaoEmpresa'])->name('dashboard.limpar');

        // APIs do dashboard
        Route::get('/dashboard/estatisticas', [DashboardController::class, 'estatisticas'])->name('dashboard.estatisticas');
        Route::get('/dashboard/progresso', [DashboardController::class, 'atualizarProgresso'])->name('dashboard.progresso');

        /**
         * MARCAS
         * Resource completo: index, create, store, show, edit, update, destroy
         */
        Route::resource('marcas', MarcaController::class);

        /**
         * EMPRESAS
         * Resource completo + rotas extras para gerenciar usuários vinculados
         */
        Route::resource('empresas', EmpresaController::class);

        // Gerenciamento de usuários vinculados às empresas
        Route::prefix('empresas/{empresa}')->name('empresas.')->group(function () {
            Route::get('/usuarios', [EmpresaController::class, 'usuarios'])->name('usuarios.index');
            Route::post('/usuarios', [EmpresaController::class, 'adicionarUsuario'])->name('usuarios.store');
            Route::post('/usuarios/criar', [EmpresaController::class, 'criarEVincularUsuario'])->name('usuarios.create');
            Route::get('/usuarios/{user}', [EmpresaController::class, 'mostrarUsuario'])->name('usuarios.show');
            Route::get('/usuarios/{user}/edit', [EmpresaController::class, 'editarUsuarioForm'])->name('usuarios.edit');
            Route::put('/usuarios/{user}', [EmpresaController::class, 'editarUsuario'])->name('usuarios.update');
            Route::delete('/usuarios/{user}', [EmpresaController::class, 'removerUsuario'])->name('usuarios.destroy');
        });

        /**
         * HORÁRIOS DE FUNCIONAMENTO
         * Rotas organizadas por empresa
         */

        Route::prefix('empresas/{empresa}/horarios')->name('horarios.')->group(function () {
            // Dashboard principal
            Route::get('/', [HorarioController::class, 'index'])->name('index');

            // Horários Padrão
            Route::prefix('padrao')->name('padrao.')->group(function () {
                Route::get('/', [HorarioController::class, 'padrao'])->name('index');
                Route::get('/criar', [HorarioController::class, 'criarPadrao'])->name('create');
                Route::post('/criar', [HorarioController::class, 'salvarPadrao'])->name('store');
                Route::get('/{id}/editar', [HorarioController::class, 'editarPadrao'])->name('edit');
                Route::put('/{id}', [HorarioController::class, 'atualizarPadrao'])->name('update');
            });

            // Exceções
            Route::prefix('excecoes')->name('excecoes.')->group(function () {
                Route::get('/', [HorarioController::class, 'excecoes'])->name('index');
                Route::get('/criar', [HorarioController::class, 'criarExcecao'])->name('create');
                Route::post('/criar', [HorarioController::class, 'salvarExcecao'])->name('store');
            });

            // Ações gerais
            Route::delete('/{id}', [HorarioController::class, 'deletar'])->name('destroy');

            // API
            Route::get('/api/status', [HorarioController::class, 'apiStatus'])->name('api.status');
        });

        /**
         * RELATÓRIOS (futuro)
         */
        // Route::prefix('relatorios')->name('relatorios.')->group(function () {
        //     Route::get('/marcas', [RelatorioController::class, 'marcas'])->name('marcas');
        //     Route::get('/empresas', [RelatorioController::class, 'empresas'])->name('empresas');
        //     Route::get('/financeiro', [RelatorioController::class, 'financeiro'])->name('financeiro');
        // });

    });
});
