<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect()->route('fidelidade.dashboard');
});

// Rotas temporárias para simular autenticação
Route::get('/login', function () {
    return redirect()->route('fidelidade.dashboard');
})->name('login');

Route::post('/logout', function () {
    return redirect('/');
})->name('logout');

// Rotas de teste
Route::get('/teste-simples', function () {
    return 'Sistema funcionando! ✅';
});

Route::get('/teste-view', function () {
    return view('teste-simples');
});

// Rotas do Admin - Configurações
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard do Admin
    Route::get('/', function () {
        return redirect()->route('admin.config.index');
    })->name('dashboard');

    Route::resource('config', App\Http\Controllers\Admin\ConfigController::class);

    // Rotas temporárias para módulos em desenvolvimento
    Route::get('usuarios', function () {
        return view('admin.temp', ['module' => 'Usuários']);
    })->name('usuarios.index');

    Route::get('empresas', function () {
        return view('admin.temp', ['module' => 'Empresas']);
    })->name('empresas.index');

    Route::get('financeiro', function () {
        return view('admin.temp', ['module' => 'Financeiro']);
    })->name('financeiro.index');

    Route::get('pdv', function () {
        return view('admin.temp', ['module' => 'PDV']);
    })->name('pdv.index');

    Route::get('delivery', function () {
        return view('admin.temp', ['module' => 'Delivery']);
    })->name('delivery.index');

    Route::get('relatorios', function () {
        return view('admin.temp', ['module' => 'Relatórios']);
    })->name('relatorios.index');

    Route::get('sistema', function () {
        return view('admin.temp', ['module' => 'Sistema']);
    })->name('sistema.index');

    // Rotas adicionais para configurações
    Route::get('config/group/{group}', function ($group) {
        return redirect()->route('admin.config.index', ['grupo' => $group]);
    })->name('config.group');

    Route::get('config/export', function () {
        return response()->json(['message' => 'Export em desenvolvimento']);
    })->name('config.export');

    Route::post('config/set-value', function () {
        return response()->json(['success' => false, 'message' => 'Funcionalidade em desenvolvimento']);
    })->name('config.set-value');

    Route::post('config/clear-cache', function () {
        return response()->json(['success' => true, 'message' => 'Cache limpo com sucesso']);
    })->name('config.clear-cache');

    Route::get('config/{config}/history-detail/{history}', function ($config, $history) {
        return response()->json(['message' => 'Detalhes do histórico em desenvolvimento']);
    })->name('config.history-detail');

    Route::post('config/{config}/restore-value', function ($config) {
        return response()->json(['success' => false, 'message' => 'Restauração em desenvolvimento']);
    })->name('config.restore-value');

    Route::get('config/{config}/history', [App\Http\Controllers\Admin\ConfigController::class, 'history'])->name('config.history');

    // Rotas de Fidelidade Admin com Soft Deletes
    Route::resource('fidelidade', App\Http\Controllers\Admin\FidelidadeAdminController::class);
    Route::get('fidelidade/deletados/{tipo?}', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'deletados'])->name('fidelidade.deletados');
    Route::post('fidelidade/restaurar', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'restaurar'])->name('fidelidade.restaurar');
    Route::delete('fidelidade/deletar-permanente', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'deletarPermanente'])->name('fidelidade.deletar-permanente');
});

// Rotas do Sistema de Configurações Multi-Empresa
Route::prefix('config')->name('config.')->group(function () {
    Route::get('/', [App\Http\Controllers\ConfigAdminController::class, 'index'])->name('index');
    Route::get('/system-status', [App\Http\Controllers\ConfigAdminController::class, 'systemStatus'])->name('system-status');
    Route::get('/manage-client/{clientId}', [App\Http\Controllers\ConfigAdminController::class, 'manageClient'])->name('manage-client');
    Route::post('/update-client/{clientId}', [App\Http\Controllers\ConfigAdminController::class, 'updateClient'])->name('update-client');
});
