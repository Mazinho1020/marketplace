<?php

require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\Auth;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "🔍 Debug do Sistema de Autenticação\n";
echo "=====================================\n\n";

// Verificar se há usuários no sistema
$totalUsuarios = User::count();
echo "👥 Total de usuários no sistema: $totalUsuarios\n\n";

if ($totalUsuarios > 0) {
    echo "👤 Primeiros 5 usuários:\n";
    $usuarios = User::take(5)->get(['id', 'name', 'email', 'created_at']);
    foreach ($usuarios as $user) {
        echo "   - ID: {$user->id}, Nome: {$user->name}, Email: {$user->email}\n";
    }
    echo "\n";
}

// Verificar configuração de autenticação
echo "🔧 Configuração de Auth:\n";
echo "   - Default Guard: " . config('auth.defaults.guard') . "\n";
echo "   - Default Provider: " . config('auth.defaults.passwords') . "\n";
echo "   - User Model: " . config('auth.providers.users.model') . "\n\n";

// Verificar se existe middleware de autenticação
echo "🛡️ Verificando middleware...\n";
echo "   - Auth middleware disponível: " . (class_exists('Illuminate\Auth\Middleware\Authenticate') ? 'Sim' : 'Não') . "\n\n";

// Simular autenticação com primeiro usuário
if ($totalUsuarios > 0) {
    $primeiroUsuario = User::first();
    echo "🔑 Simulando login com usuário: {$primeiroUsuario->name} (ID: {$primeiroUsuario->id})\n";

    // Fazer login programaticamente
    Auth::login($primeiroUsuario);

    echo "   - Usuário autenticado: " . (Auth::check() ? 'Sim' : 'Não') . "\n";
    echo "   - ID do usuário: " . (Auth::id() ?? 'null') . "\n";
    echo "   - Nome do usuário: " . (Auth::user()->name ?? 'null') . "\n\n";

    // Testar criação de recebimento com usuário autenticado
    echo "💰 Testando criação de recebimento com usuário autenticado...\n";

    try {
        $contasReceberService = new \App\Services\Financial\ContasReceberService();

        $dados = [
            'forma_pagamento_id' => 25, // Cartão de débito
            'bandeira_id' => 35, // Elo Débito
            'conta_bancaria_id' => 2,
            'tipo_id' => 2,
            'valor' => 100.00,
            'valor_principal' => 100.00,
            'valor_juros' => 0,
            'valor_multa' => 0,
            'valor_desconto' => 0,
            'data_recebimento' => '2025-08-14',
            'data_compensacao' => null,
            'observacao' => 'Teste com usuário autenticado',
            'comprovante_recebimento' => null,
            'taxa' => 0,
            'valor_taxa' => 0,
            'referencia_externa' => null,
            'usuario_id' => Auth::id(), // Usuário autenticado
            'status_recebimento' => 'confirmado'
        ];

        echo "   - Dados preparados:\n";
        echo "     * Usuário ID: " . $dados['usuario_id'] . "\n";
        echo "     * Lançamento ID: 391\n";
        echo "     * Valor: R$ " . number_format($dados['valor'], 2, ',', '.') . "\n";

        $recebimento = $contasReceberService->receber(391, $dados);

        echo "   ✅ Recebimento criado com sucesso!\n";
        echo "     * ID: {$recebimento->id}\n";
        echo "     * Valor: R$ " . number_format($recebimento->valor, 2, ',', '.') . "\n";
        echo "     * Status: {$recebimento->status_recebimento}\n";
        echo "     * Usuário: {$recebimento->usuario_id}\n";
    } catch (\Exception $e) {
        echo "   ❌ Erro: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️ Nenhum usuário encontrado no sistema. Criando usuário de teste...\n";

    try {
        $usuario = User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@marketplace.com',
            'password' => bcrypt('123456'),
            'email_verified_at' => now(),
        ]);

        echo "✅ Usuário criado: {$usuario->name} (ID: {$usuario->id})\n";

        // Fazer login
        Auth::login($usuario);
        echo "🔑 Login realizado com sucesso!\n";
        echo "   - Usuário autenticado: " . (Auth::check() ? 'Sim' : 'Não') . "\n";
        echo "   - ID do usuário: " . Auth::id() . "\n";
    } catch (\Exception $e) {
        echo "❌ Erro ao criar usuário: " . $e->getMessage() . "\n";
    }
}

echo "\n🏁 Debug finalizado!\n";
