<?php

/**
 * Script para criar usuário de teste com nível baixo
 * Para testar o sistema de controle de acesso
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap do Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=== CRIANDO USUÁRIO DE TESTE COM NÍVEL BAIXO ===\n\n";

    // Verificar se o usuário já existe
    $existingUser = DB::table('empresa_usuarios')
        ->where('email', 'operador@teste.com')
        ->first();

    if ($existingUser) {
        echo "❌ Usuário operador@teste.com já existe!\n";
        echo "Atualizando dados...\n\n";

        DB::table('empresa_usuarios')
            ->where('email', 'operador@teste.com')
            ->update([
                'nome' => 'Operador Teste',
                'password' => Hash::make('123456'),
                'tipo' => 'operador',
                'nivel_acesso' => 40,
                'status' => 'ativo',
                'updated_at' => now()
            ]);

        echo "✅ Usuário operador@teste.com atualizado!\n";
    } else {
        // Criar novo usuário operador
        $userId = DB::table('empresa_usuarios')->insertGetId([
            'nome' => 'Operador Teste',
            'email' => 'operador@teste.com',
            'password' => Hash::make('123456'),
            'tipo' => 'operador',
            'nivel_acesso' => 40,
            'status' => 'ativo',
            'empresa_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Usuário operador criado com ID: $userId\n";
    }

    // Verificar se o usuário consulta já existe
    $existingConsulta = DB::table('empresa_usuarios')
        ->where('email', 'consulta@teste.com')
        ->first();

    if ($existingConsulta) {
        echo "❌ Usuário consulta@teste.com já existe!\n";
        echo "Atualizando dados...\n\n";

        DB::table('empresa_usuarios')
            ->where('email', 'consulta@teste.com')
            ->update([
                'nome' => 'Consulta Teste',
                'password' => Hash::make('123456'),
                'tipo' => 'consulta',
                'nivel_acesso' => 20,
                'status' => 'ativo',
                'updated_at' => now()
            ]);

        echo "✅ Usuário consulta@teste.com atualizado!\n";
    } else {
        // Criar novo usuário consulta
        $userId = DB::table('empresa_usuarios')->insertGetId([
            'nome' => 'Consulta Teste',
            'email' => 'consulta@teste.com',
            'password' => Hash::make('123456'),
            'tipo' => 'consulta',
            'nivel_acesso' => 20,
            'status' => 'ativo',
            'empresa_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Usuário consulta criado com ID: $userId\n";
    }

    echo "\n=== USUÁRIOS DE TESTE DISPONÍVEIS ===\n";
    echo "1. Admin: admin@teste.com / 123456 (Nível 100)\n";
    echo "2. Operador: operador@teste.com / 123456 (Nível 40)\n";
    echo "3. Consulta: consulta@teste.com / 123456 (Nível 20)\n\n";

    echo "=== TESTE DE ACESSO ===\n";
    echo "• Admin pode acessar /admin/dashboard (requer nível 60+)\n";
    echo "• Operador NÃO pode acessar /admin/dashboard (tem nível 40)\n";
    echo "• Consulta NÃO pode acessar /admin/dashboard (tem nível 20)\n\n";

    echo "✅ Usuários de teste criados com sucesso!\n";
    echo "🌐 Teste em: http://127.0.0.1:8000/login\n\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
