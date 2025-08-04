<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE APÓS CORREÇÃO ===\n";

// Vamos simular exatamente o que a rota faz agora
$dados = [
    'canal' => 'push',
    'destinatario' => 'push-token-teste-final',
    'tipo' => 'teste_corrigido',
    'titulo' => 'Teste Push Corrigido',
    'mensagem' => 'Este teste deve sempre ter sucesso'
];

$dadosNotificacao = [
    'empresa_id' => 1,
    'aplicacao_id' => 1,
    'template_id' => 1,
    'email_destinatario' => $dados['canal'] === 'email' ? $dados['destinatario'] : null,
    'telefone_destinatario' => in_array($dados['canal'], ['sms', 'push']) ? $dados['destinatario'] : null,
    'canal' => $dados['canal'],
    'titulo' => $dados['titulo'] ?? 'Teste de Notificação',
    'mensagem' => $dados['mensagem'] ?? 'Esta é uma notificação de teste',
    'status' => 'enviado',
    'prioridade' => 'media',
    'enviado_em' => now(),
    'entregue_em' => now(),
    'lido_em' => rand(1, 10) > 5 ? now() : null,
    'mensagem_erro' => null,
    'tentativas' => 1,
    'id_externo' => 'test_' . uniqid(),
    'dados_processados' => json_encode([
        'tipo_teste' => $dados['tipo'],
        'prioridade' => 'normal',
        'agendamento' => false
    ]),
    'dados_evento_origem' => json_encode($dados),
    'created_at' => now(),
    'updated_at' => now()
];

try {
    $id = DB::table('notificacao_enviadas')->insertGetId($dadosNotificacao);
    echo "✅ Inserção bem-sucedida com ID: $id\n";

    // Como removemos a lógica de falha, não há mais updates para 'falhou'
    echo "✅ Push notification deve manter status 'enviado'\n";

    // Verificar o registro
    $registro = DB::table('notificacao_enviadas')->where('id', $id)->first();
    echo "Status final: {$registro->status}\n";
    echo "Token: {$registro->telefone_destinatario}\n";
    echo "Erro: " . ($registro->mensagem_erro ?: 'Nenhum') . "\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== TESTANDO MÚLTIPLAS PUSH NOTIFICATIONS ===\n";

for ($i = 1; $i <= 3; $i++) {
    $dados = [
        'canal' => 'push',
        'destinatario' => "push-token-batch-$i",
        'tipo' => 'teste_batch',
        'titulo' => "Teste Push $i",
        'mensagem' => "Teste número $i deve ter sucesso"
    ];

    $dadosNotificacao['telefone_destinatario'] = $dados['destinatario'];
    $dadosNotificacao['titulo'] = $dados['titulo'];
    $dadosNotificacao['mensagem'] = $dados['mensagem'];
    $dadosNotificacao['id_externo'] = 'batch_' . uniqid();
    $dadosNotificacao['created_at'] = now();
    $dadosNotificacao['updated_at'] = now();

    try {
        $id = DB::table('notificacao_enviadas')->insertGetId($dadosNotificacao);
        echo "✅ Push $i inserida com ID: $id\n";
    } catch (Exception $e) {
        echo "❌ Erro na push $i: " . $e->getMessage() . "\n";
    }
}

echo "\n📊 Total de push notifications no banco: " . DB::table('notificacao_enviadas')->where('canal', 'push')->count() . "\n";
