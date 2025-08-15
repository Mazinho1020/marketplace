<?php

// Usar o Artisan para executar o teste
$command = 'php artisan tinker --execute="
use App\Models\User;
use App\Services\Financial\ContasReceberService;
use Illuminate\Support\Facades\Auth;

echo \"🔍 Debug do Sistema de Autenticação\\n\";
echo \"=====================================\\n\\n\";

// Verificar usuários
$totalUsuarios = User::count();
echo \"👥 Total de usuários: $totalUsuarios\\n\";

if ($totalUsuarios == 0) {
    echo \"⚠️ Criando usuário de teste...\\n\";
    $usuario = User::create([
        'name' => 'Admin Sistema',
        'email' => 'admin@marketplace.com',
        'password' => bcrypt('123456'),
        'email_verified_at' => now(),
    ]);
    echo \"✅ Usuário criado: {$usuario->name} (ID: {$usuario->id})\\n\";
} else {
    $usuario = User::first();
    echo \"👤 Usando usuário: {$usuario->name} (ID: {$usuario->id})\\n\";
}

// Testar criação de recebimento
echo \"\\n💰 Testando criação de recebimento...\\n\";

try {
    $contasReceberService = new ContasReceberService();
    
    $dados = [
        'forma_pagamento_id' => 25,
        'bandeira_id' => 35,
        'conta_bancaria_id' => 2,
        'tipo_id' => 2,
        'valor' => 100.00,
        'valor_principal' => 100.00,
        'valor_juros' => 0,
        'valor_multa' => 0,
        'valor_desconto' => 0,
        'data_recebimento' => '2025-08-14',
        'usuario_id' => $usuario->id,
        'status_recebimento' => 'confirmado'
    ];
    
    echo \"   - Usuário ID: {$dados['usuario_id']}\\n\";
    echo \"   - Lançamento ID: 391\\n\";
    echo \"   - Valor: R$ \" . number_format($dados['valor'], 2, ',', '.') . \"\\n\";
    
    $recebimento = $contasReceberService->receber(391, $dados);
    
    echo \"   ✅ Recebimento criado com sucesso!\\n\";
    echo \"     * ID: {$recebimento->id}\\n\";
    echo \"     * Valor: R$ \" . number_format($recebimento->valor, 2, ',', '.') . \"\\n\";
    echo \"     * Status: {$recebimento->status_recebimento}\\n\";
    echo \"     * Usuário: {$recebimento->usuario_id}\\n\";
    
} catch (Exception $e) {
    echo \"   ❌ Erro: \" . $e->getMessage() . \"\\n\";
}

echo \"\\n🏁 Debug finalizado!\\n\";
"';

// Executar o comando
echo "Executando debug via Artisan Tinker...\n";
$output = shell_exec($command . ' 2>&1');
echo $output;
