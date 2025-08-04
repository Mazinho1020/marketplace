<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANÁLISE DE PUSH NOTIFICATIONS FALHANDO ===\n";

// Verificar últimas push notifications
$pushes = DB::table('notificacao_enviadas')
    ->where('canal', 'push')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get(['id', 'canal', 'status', 'titulo', 'telefone_destinatario', 'mensagem_erro', 'created_at']);

echo "Últimas push notifications:\n";
foreach ($pushes as $p) {
    $erro = $p->mensagem_erro ? " | Erro: {$p->mensagem_erro}" : "";
    echo "ID: {$p->id} | Status: {$p->status} | Token: {$p->telefone_destinatario}{$erro}\n";
}

echo "\n=== TESTANDO NOVA INSERÇÃO ===\n";

// Teste de inserção direta
try {
    $novoId = DB::table('notificacao_enviadas')->insertGetId([
        'empresa_id' => 1,
        'aplicacao_id' => 1,
        'template_id' => 1,
        'canal' => 'push',
        'telefone_destinatario' => 'push-token-teste-debug',
        'titulo' => 'Teste Debug Push',
        'mensagem' => 'Teste para debugging push notifications',
        'status' => 'enviado', // Vamos forçar como enviado para ver se o problema é na lógica de falha
        'prioridade' => 'media',
        'enviado_em' => now(),
        'entregue_em' => now(),
        'tentativas' => 1,
        'id_externo' => 'debug_' . uniqid(),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "✅ Nova push inserida com ID: $novoId\n";
} catch (Exception $e) {
    echo "❌ Erro na inserção: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICANDO LÓGICA DA ROTA ===\n";

// Simulando a lógica da rota que pode estar causando falha
$dados = [
    'canal' => 'push',
    'destinatario' => 'push-token-123456',
    'tipo' => 'teste',
    'titulo' => 'Teste Push',
    'mensagem' => 'Teste de push'
];

echo "Dados de entrada:\n";
print_r($dados);

// Preparação dos dados como na rota
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
    'prioridade' => $dados['prioridade'] ?? 'media',
    'enviado_em' => now(),
    'entregue_em' => now(),
    'tentativas' => 1,
    'id_externo' => 'test_' . uniqid(),
    'created_at' => now(),
    'updated_at' => now()
];

echo "\nDados preparados para inserção:\n";
echo "Canal: {$dadosNotificacao['canal']}\n";
echo "Token (telefone_destinatario): " . ($dadosNotificacao['telefone_destinatario'] ?: 'NULL') . "\n";
echo "Status inicial: {$dadosNotificacao['status']}\n";

// Inserir e testar a lógica de falha
try {
    $id = DB::table('notificacao_enviadas')->insertGetId($dadosNotificacao);
    echo "✅ Inserção bem-sucedida com ID: $id\n";

    // Simular a lógica de falha da rota
    if ($dados['canal'] === 'push') {
        $sucesso = rand(1, 10) > 3; // 70% de chance de sucesso
        echo "Simulação de envio push: " . ($sucesso ? "SUCESSO" : "FALHA") . "\n";

        if (!$sucesso) {
            DB::table('notificacao_enviadas')
                ->where('id', $id)
                ->update([
                    'status' => 'falhou',
                    'mensagem_erro' => 'Token de push inválido ou expirado',
                    'entregue_em' => null,
                    'updated_at' => now()
                ]);

            echo "❌ Status atualizado para 'falhou' devido à simulação\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro na inserção de teste: " . $e->getMessage() . "\n";
}
