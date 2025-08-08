<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Verificando notificações criadas:\n";
    $notificacoes = DB::table('comerciante_notificacoes')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    if ($notificacoes->isEmpty()) {
        echo "Nenhuma notificação encontrada.\n";
    } else {
        echo "Total de notificações: " . $notificacoes->count() . "\n\n";
        foreach ($notificacoes as $notificacao) {
            echo "ID: {$notificacao->id}\n";
            echo "Empresa: {$notificacao->empresa_id}\n";
            echo "Tipo: {$notificacao->tipo}\n";
            echo "Título: {$notificacao->titulo}\n";
            echo "Mensagem: {$notificacao->mensagem}\n";
            echo "Prioridade: {$notificacao->prioridade}\n";
            echo "Lida: " . ($notificacao->lida ? 'Sim' : 'Não') . "\n";
            echo "Data: {$notificacao->created_at}\n";
            echo "---\n";
        }
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
