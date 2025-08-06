<?php

use Illuminate\Support\Facades\Route;
use App\Comerciantes\Controllers\HorarioFuncionamentoController;

/**
 * ROTA DE TESTE TEMPORÁRIA - SEM AUTENTICAÇÃO
 * Para diagnosticar se o problema está no middleware ou no controller
 */
Route::get('/teste-horarios-debug/{empresa}', function ($empresa) {
    echo "<h1>TESTE DEBUG - HORÁRIOS</h1>";
    echo "<p>Empresa: $empresa</p>";
    echo "<p>Se você está vendo esta mensagem, a rota funciona!</p>";
    echo "<p>O problema pode estar no middleware de autenticação ou no controller.</p>";

    echo "<h2>Próximos testes:</h2>";
    echo "<ul>";
    echo "<li>1. Testar controller diretamente</li>";
    echo "<li>2. Testar middleware isoladamente</li>";
    echo "<li>3. Verificar redirecionamentos</li>";
    echo "</ul>";

    return response("<p>Status: OK</p>");
})->name('teste.horarios.debug');
