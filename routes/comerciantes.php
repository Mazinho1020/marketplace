<?php

use Illuminate\Support\Facades\Route;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\EmpresaController;

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

// Rotas protegidas com permissões automáticas
Route::prefix('comerciantes')->name('comerciantes.')->middleware(['comerciantes.protected'])->group(function () {
    
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
    
});