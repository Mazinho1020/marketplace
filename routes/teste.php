<?php

use Illuminate\Support\Facades\Route;

// Rota de teste simples
Route::get('/teste-simples', function () {
    return 'Sistema funcionando! ✅';
});

// Rota de teste com view simples
Route::get('/teste-view', function () {
    return view('teste-simples');
});
