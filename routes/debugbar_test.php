<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-debugbar', function () {
    // Forçar o carregamento do Debugbar
    if (app()->bound('debugbar')) {
        app('debugbar')->info('Debugbar está funcionando!');
        app('debugbar')->warning('Este é um teste do Debugbar');
        app('debugbar')->error('Mensagem de erro de teste');
    }

    return view('welcome')->with([
        'debugbar_status' => app()->bound('debugbar') ? 'Carregado' : 'Não carregado',
        'app_debug' => config('app.debug') ? 'true' : 'false',
        'debugbar_enabled' => config('debugbar.enabled') ? 'true' : 'false'
    ]);
});
