<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE PUSH NOTIFICATION ===\n";

try {
    // Dados para push
    $dadosPush = [
        'empresa_id' => 1,
        'aplicacao_id' => 1,
        'template_id' => 1,
        'email_destinatario' => null,
        'telefone_destinatario' => 'push-token-123456', // Token vai no telefone_destinatario para push
        'canal' => 'push',
        'titulo' => 'Teste Push Manual',
        'mensagem' => 'Esta é uma notificação push de teste',
        'status' => 'enviado',
        'prioridade' => 'media',
        'enviado_em' => now(),
        'entregue_em' => now(),
        'tentativas' => 1,
        'id_externo' => 'test_push_' . uniqid(),
        'dados_processados' => json_encode([
            'tipo_teste' => 'push_manual',
            'token' => 'push-token-123456'
        ]),
        'created_at' => now(),
        'updated_at' => now()
    ];

    $id = DB::table('notificacao_enviadas')->insertGetId($dadosPush);

    echo "✅ Push notification inserida com sucesso!\n";
    echo "ID: $id\n";

    // Verificar se foi inserida
    $notificacao = DB::table('notificacao_enviadas')->where('id', $id)->first();
    echo "Canal: {$notificacao->canal}\n";
    echo "Status: {$notificacao->status}\n";
    echo "Titulo: {$notificacao->titulo}\n";
    echo "Telefone/Token: {$notificacao->telefone_destinatario}\n";
} catch (Exception $e) {
    echo "❌ Erro ao inserir push notification: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICANDO ÚLTIMAS NOTIFICAÇÕES PUSH ===\n";

try {
    $pushes = DB::table('notificacao_enviadas')
        ->where('canal', 'push')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();

    echo "Total de push notifications encontradas: " . $pushes->count() . "\n";

    foreach ($pushes as $push) {
        echo "ID: {$push->id} | Status: {$push->status} | Título: {$push->titulo} | Token: {$push->telefone_destinatario}\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao buscar push notifications: " . $e->getMessage() . "\n";
}
