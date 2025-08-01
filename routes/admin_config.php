<?php

use App\Http\Controllers\Admin\ConfigController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Admin Config Routes
|--------------------------------------------------------------------------
|
| Rotas para gerenciamento de configurações do sistema
| Seguindo padrões RESTful do Laravel
|
*/

Route::prefix('admin')->name('admin.')->middleware(['empresa'])->group(function () {

    // Rotas principais de configuração
    Route::resource('config', ConfigController::class)->except(['show']);

    // Rotas específicas para configurações
    Route::prefix('config')->name('config.')->group(function () {

        // Visualizar configurações por grupo
        Route::get('group/{groupCode}', [ConfigController::class, 'group'])
            ->name('group');

        // Definir valor de configuração via AJAX
        Route::post('set-value', [ConfigController::class, 'setValue'])
            ->name('set-value');

        // Limpar cache de configurações
        Route::post('clear-cache', [ConfigController::class, 'clearCache'])
            ->name('clear-cache');

        // Exportar configurações
        Route::get('export', [ConfigController::class, 'export'])
            ->name('export');

        // Histórico de alterações
        Route::get('{config}/history', [ConfigController::class, 'history'])
            ->name('history');

        // Detalhes do histórico
        Route::get('{config}/history/{history}', [ConfigController::class, 'historyDetail'])
            ->name('history-detail');

        // Restaurar valor do histórico
        Route::post('{config}/restore-value', [ConfigController::class, 'restoreValue'])
            ->name('restore-value');

        // APIs auxiliares
        Route::get('api/sites', function () {
            return response()->json(
                DB::table('config_sites')
                    ->select('id', 'codigo', 'nome')
                    ->where('ativo', true)
                    ->get()
            );
        })->name('api.sites');

        Route::get('api/environments', function () {
            return response()->json(
                DB::table('config_environments')
                    ->select('id', 'codigo', 'nome')
                    ->where('ativo', true)
                    ->get()
            );
        })->name('api.environments');
    });
});
