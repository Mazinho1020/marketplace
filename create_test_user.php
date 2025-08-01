<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User\EmpresaUsuario;
use App\Models\User\EmpresaUsuarioTipo;

// Configurar o aplicativo
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Criar usuário admin de teste
    $admin = EmpresaUsuario::create([
        'empresa_id' => 1,
        'nome' => 'Administrador',
        'email' => 'admin@teste.com',
        'password' => \Illuminate\Support\Facades\Hash::make('123456'),
        'status' => EmpresaUsuario::STATUS_ATIVO,
        'tipo_id' => 1, // Admin
        'telefone' => '(11) 99999-9999',
        'cpf' => '123.456.789-00',
    ]);

    echo "Usuario admin criado com sucesso!\n";
    echo "ID: " . $admin->id . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Status: " . $admin->status . "\n";
} catch (Exception $e) {
    echo "Erro ao criar usuario: " . $e->getMessage() . "\n";
}
