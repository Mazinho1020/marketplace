<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTANDO VIA ROTA ===\n";

// Simulando dados que viriam da página de teste
$dadosTeste = [
    'canal' => 'push',
    'tipo' => 'teste_interface',
    'destinatario' => 'token-push-interface-123',
    'titulo' => 'Teste Push via Interface',
    'mensagem' => 'Teste de push notification através da página de teste',
    'prioridade' => 'alta'
];

// Aplicando a mesma lógica da rota
if (empty($dadosTeste['destinatario']) || empty($dadosTeste['tipo']) || empty($dadosTeste['canal'])) {
    echo "❌ Dados obrigatórios não informados\n";
    exit;
}

$dadosNotificacao = [
    'empresa_id' => 1,
    'aplicacao_id' => 1,
    'template_id' => 1,
    'email_destinatario' => $dadosTeste['canal'] === 'email' ? $dadosTeste['destinatario'] : null,
    'telefone_destinatario' => in_array($dadosTeste['canal'], ['sms', 'push']) ? $dadosTeste['destinatario'] : null,
    'canal' => $dadosTeste['canal'],
    'titulo' => $dadosTeste['titulo'] ?? 'Teste de Notificação',
    'mensagem' => $dadosTeste['mensagem'] ?? 'Esta é uma notificação de teste',
    'status' => 'enviado',
    'prioridade' => $dadosTeste['prioridade'] ?? 'media',
    'enviado_em' => now(),
    'entregue_em' => now(),
    'lido_em' => rand(1, 10) > 5 ? now() : null,
    'mensagem_erro' => null,
    'tentativas' => 1,
    'id_externo' => 'test_' . uniqid(),
    'dados_processados' => json_encode([
        'tipo_teste' => $dadosTeste['tipo'],
        'prioridade' => $dadosTeste['prioridade'] ?? 'normal',
        'agendamento' => $dadosTeste['agendamento'] ?? false
    ]),
    'dados_evento_origem' => json_encode($dadosTeste),
    'created_at' => now(),
    'updated_at' => now()
];

echo "Dados preparados:\n";
echo "- Canal: {$dadosNotificacao['canal']}\n";
echo "- Email destinatário: " . ($dadosNotificacao['email_destinatario'] ?: 'NULL') . "\n";
echo "- Telefone destinatário: " . ($dadosNotificacao['telefone_destinatario'] ?: 'NULL') . "\n";
echo "- Título: {$dadosNotificacao['titulo']}\n";

try {
    $id = DB::table('notificacao_enviadas')->insertGetId($dadosNotificacao);

    echo "✅ Push notification inserida com sucesso! ID: $id\n";

    // Verificar se foi inserida corretamente
    $notificacao = DB::table('notificacao_enviadas')->where('id', $id)->first();
    echo "Verificação - Canal: {$notificacao->canal}, Token: {$notificacao->telefone_destinatario}\n";
} catch (Exception $e) {
    echo "❌ Erro ao inserir: " . $e->getMessage() . "\n";
}

// Contar push notifications
$totalPush = DB::table('notificacao_enviadas')->where('canal', 'push')->count();
echo "\n📊 Total de push notifications no banco: $totalPush\n";
