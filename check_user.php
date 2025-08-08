<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;

try {
    echo "=== VERIFICANDO USUÁRIOS ===\n";

    // Buscar qualquer usuário
    $users = EmpresaUsuario::take(3)->get();
    if ($users->count() > 0) {
        echo "Usuários encontrados:\n";
        foreach ($users as $user) {
            echo "- {$user->nome} ({$user->email}) - Status: {$user->status}\n";
        }
    } else {
        echo "Nenhum usuário encontrado!\n";
    }

    // Tentar admin@teste.com
    $adminUser = EmpresaUsuario::where('email', 'admin@teste.com')->first();
    if ($adminUser) {
        echo "\nUsuário admin@teste.com encontrado:\n";
        echo "- Nome: {$adminUser->nome}\n";
        echo "- Email: {$adminUser->email}\n";
        echo "- Status: {$adminUser->status}\n";
        echo "- Senha hash: " . substr($adminUser->senha, 0, 20) . "...\n";
    } else {
        echo "\nUsuário admin@teste.com NÃO encontrado!\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
