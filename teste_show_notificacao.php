<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "=== TESTE DE NOTIFICAÇÃO SHOW ===\n";

// Buscar primeira notificação
$notificacao = DB::table('notificacao_enviadas')->first();

if ($notificacao) {
    echo "ID: {$notificacao->id}\n";
    echo "Título: {$notificacao->titulo}\n";
    echo "Created (string): {$notificacao->created_at}\n";
    echo "Lido em (string): " . ($notificacao->lido_em ?: 'null') . "\n";

    // Testar conversão para Carbon
    $created = \Carbon\Carbon::parse($notificacao->created_at);
    echo "Created (Carbon): " . $created->format('d/m/Y H:i:s') . "\n";

    if ($notificacao->lido_em) {
        $lido = \Carbon\Carbon::parse($notificacao->lido_em);
        echo "Lido (Carbon): " . $lido->format('d/m/Y H:i:s') . "\n";
    }

    echo "\n✅ Conversão funcionando!\n";
} else {
    echo "❌ Nenhuma notificação encontrada\n";
}
