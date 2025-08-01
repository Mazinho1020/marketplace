<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User\EmpresaUsuario;

// Configurar o aplicativo
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $user = EmpresaUsuario::where('email', 'admin@teste.com')->first();

    if ($user) {
        echo "Usuario encontrado:\n";
        echo "ID: " . $user->id . "\n";
        echo "Nome: " . $user->nome . "\n";
        echo "Email: " . $user->email . "\n";
        echo "Status: " . $user->status . "\n";
        echo "Tipo ID: " . $user->tipo_id . "\n";

        // Testar se a senha está correta
        if (\Illuminate\Support\Facades\Hash::check('123456', $user->password)) {
            echo "Senha: CORRETA\n";
        } else {
            echo "Senha: INCORRETA\n";
        }
    } else {
        echo "Usuario não encontrado!\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
