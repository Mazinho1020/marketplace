<?php

require_once 'vendor/autoload.php';

// Bootstrap do Laravel
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    // Testa inserção com now() (função helper do Laravel)
    $testId = DB::table('notificacao_enviadas')->insertGetId([
        'email_destinatario' => 'teste@timezone.com',
        'canal' => 'email',
        'titulo' => 'Teste de Timezone',
        'mensagem' => 'Teste para verificar se o horário está sendo salvo corretamente',
        'status' => 'enviado',
        'tentativas' => 1,
        'enviado_em' => now(), // Usando helper do Laravel
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "=== TESTE DE INSERÇÃO COM TIMEZONE ===\n";
    echo "Registro inserido com ID: $testId\n\n";

    // Recupera o registro recém inserido
    $registro = DB::table('notificacao_enviadas')->where('id', $testId)->first();

    echo "=== DADOS DO REGISTRO INSERIDO ===\n";
    echo "ID: " . $registro->id . "\n";
    echo "Email: " . $registro->email_destinatario . "\n";
    echo "Enviado em: " . $registro->enviado_em . "\n";
    echo "Created at: " . $registro->created_at . "\n";
    echo "Updated at: " . $registro->updated_at . "\n\n";

    echo "=== COMPARAÇÃO DE HORÁRIOS ===\n";
    echo "Horário atual do sistema (PHP): " . date('Y-m-d H:i:s') . "\n";
    echo "Horário atual (Laravel now()): " . now()->format('Y-m-d H:i:s') . "\n";
    echo "Horário atual (Carbon Cuiabá): " . Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s') . "\n";
    echo "Horário atual (Carbon UTC): " . Carbon::now('UTC')->format('Y-m-d H:i:s') . "\n\n";

    // Testa inserção com Carbon explícito
    $testId2 = DB::table('notificacao_enviadas')->insertGetId([
        'email_destinatario' => 'teste2@timezone.com',
        'canal' => 'email',
        'titulo' => 'Teste de Timezone com Carbon',
        'mensagem' => 'Teste com Carbon explícito',
        'status' => 'enviado',
        'tentativas' => 1,
        'enviado_em' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'),
        'created_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s')
    ]);

    $registro2 = DB::table('notificacao_enviadas')->where('id', $testId2)->first();

    echo "=== SEGUNDO TESTE (CARBON EXPLÍCITO) ===\n";
    echo "ID: " . $registro2->id . "\n";
    echo "Email: " . $registro2->email_destinatario . "\n";
    echo "Enviado em: " . $registro2->enviado_em . "\n";
    echo "Created at: " . $registro2->created_at . "\n";
    echo "Updated at: " . $registro2->updated_at . "\n\n";

    echo "=== CONCLUSÃO ===\n";
    if ($registro->enviado_em === $registro2->enviado_em) {
        echo "✅ SUCESSO: Ambos os métodos estão produzindo o mesmo horário!\n";
        echo "✅ O timezone America/Cuiaba está sendo aplicado corretamente.\n";
    } else {
        echo "⚠️  ATENÇÃO: Os métodos estão produzindo horários diferentes:\n";
        echo "   now(): " . $registro->enviado_em . "\n";
        echo "   Carbon explícito: " . $registro2->enviado_em . "\n";
    }
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
