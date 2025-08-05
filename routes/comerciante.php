<?php

use Illuminate\Support\Facades\Route;
use App\Comerciantes\Controllers\Auth\LoginController;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\MarcaController;
use App\Comerciantes\Controllers\EmpresaController;
use App\Comerciantes\Controllers\HorarioFuncionamentoController;

/**
 * Rotas do módulo de comerciantes
 * Usa a tabela empresa_usuarios para autenticação
 */
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {

    /**
     * ROTAS PÚBLICAS (sem autenticação)
     */
    Route::middleware('guest:comerciante')->group(function () {
        // Login
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        // Cadastro (se você quiser permitir auto-cadastro)
        // Route::get('/cadastro', [RegisterController::class, 'showRegistrationForm'])->name('register');
        // Route::post('/cadastro', [RegisterController::class, 'register']);
    });

    /**
     * ROTAS PROTEGIDAS (com autenticação)
     */
    Route::middleware(['auth:comerciante'])->group(function () {

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
            Route::put('/usuarios/{user}', [EmpresaController::class, 'editarUsuario'])->name('usuarios.update');
            Route::delete('/usuarios/{user}', [EmpresaController::class, 'removerUsuario'])->name('usuarios.destroy');
        });

        /**
         * HORÁRIOS DE FUNCIONAMENTO
         * Sistema completo de gerenciamento de horários padrão e exceções
         */
        Route::prefix('horarios')->name('horarios.')->group(function () {
            // Dashboard principal dos horários
            Route::get('/', [HorarioFuncionamentoController::class, 'index'])->name('index');
            
            // Horários Padrão
            Route::prefix('padrao')->name('padrao.')->group(function () {
                Route::get('/', [HorarioFuncionamentoController::class, 'horariosPadrao'])->name('index');
                Route::get('/criar', [HorarioFuncionamentoController::class, 'createPadrao'])->name('create');
                Route::post('/criar', [HorarioFuncionamentoController::class, 'storePadrao'])->name('store');
                Route::get('/{id}/editar', [HorarioFuncionamentoController::class, 'editPadrao'])->name('edit');
                Route::put('/{id}', [HorarioFuncionamentoController::class, 'updatePadrao'])->name('update');
            });

            // Exceções
            Route::prefix('excecoes')->name('excecoes.')->group(function () {
                Route::get('/', [HorarioFuncionamentoController::class, 'excecoes'])->name('index');
                Route::get('/criar', [HorarioFuncionamentoController::class, 'createExcecao'])->name('create');
                Route::post('/criar', [HorarioFuncionamentoController::class, 'storeExcecao'])->name('store');
                Route::get('/{id}/editar', [HorarioFuncionamentoController::class, 'editExcecao'])->name('edit');
                Route::put('/{id}', [HorarioFuncionamentoController::class, 'updateExcecao'])->name('update');
            });

            // Ações comuns (deletar)
            Route::delete('/{id}', [HorarioFuncionamentoController::class, 'destroy'])->name('destroy');

            // Relatórios e APIs
            Route::get('/relatorio', [HorarioFuncionamentoController::class, 'relatorio'])->name('relatorio');
            Route::get('/api/status', [HorarioFuncionamentoController::class, 'apiStatus'])->name('api.status');
            Route::get('/api/proximo-aberto', [HorarioFuncionamentoController::class, 'apiProximoAberto'])->name('api.proximo');
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
