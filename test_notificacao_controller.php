<?php

// Teste simples para verificar se a classe NotificacaoController está bem formada
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = new Application(realpath(__DIR__));

// Tentar instanciar o controller
try {
    $controller = new App\Comerciantes\Controllers\NotificacaoController();
    echo "✅ Controller NotificacaoController criado com sucesso!\n";

    // Verificar se todos os métodos existem
    $metodosEsperados = [
        'index',
        'dashboard',
        'headerNotifications',
        'show',
        'marcarComoLida',
        'marcarTodasComoLidas'
    ];

    foreach ($metodosEsperados as $metodo) {
        if (method_exists($controller, $metodo)) {
            echo "✅ Método {$metodo} existe\n";
        } else {
            echo "❌ Método {$metodo} NÃO existe\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro ao criar controller: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
