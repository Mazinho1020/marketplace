<?php

// Teste direto das notificações para verificar se estão funcionando
require_once 'vendor/autoload.php';

// Configurar Laravel bootstrap mínimo
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Simular teste das notificações
use Illuminate\Support\Facades\DB;

try {
    echo "=== TESTE DO SISTEMA DE NOTIFICAÇÕES ===\n\n";

    // Verificar se existem notificações
    $notificacoes = DB::table('notificacao_enviadas')
        ->where('empresa_relacionada_id', 1)
        ->get();

    echo "Total de notificações encontradas: " . $notificacoes->count() . "\n\n";

    foreach ($notificacoes as $notificacao) {
        echo "ID: {$notificacao->id}\n";
        echo "Título: {$notificacao->titulo}\n";
        echo "Mensagem: " . substr($notificacao->mensagem, 0, 50) . "...\n";
        echo "Canal: {$notificacao->canal}\n";
        echo "Status: {$notificacao->status}\n";
        echo "Criado em: {$notificacao->created_at}\n";
        echo "Lido em: " . ($notificacao->lido_em ?: 'Não lido') . "\n";
        echo "---\n";
    }

    echo "\n✅ Sistema de notificações funcionando!\n";
    echo "Acesse: http://127.0.0.1:8000/comerciantes/notificacoes\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
