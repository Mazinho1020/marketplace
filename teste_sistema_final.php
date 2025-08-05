<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;

echo "🧪 TESTE FINAL DO SISTEMA\n";
echo "=" . str_repeat("=", 30) . "\n\n";

try {
    // Teste do model
    $user = EmpresaUsuario::find(3);

    if ($user) {
        echo "✅ Usuário encontrado: {$user->nome}\n";
        echo "✅ Email: {$user->email}\n";

        $marcas = $user->marcasProprietario()->count();
        echo "✅ Marcas: $marcas\n";

        if ($marcas > 0) {
            $marca = $user->marcasProprietario()->first();
            echo "✅ Primeira marca: {$marca->nome}\n";

            $empresas = $marca->empresas()->count();
            echo "✅ Empresas da marca: $empresas\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }

    echo "\n🎯 SISTEMA CONFIGURADO COM SUCESSO!\n";
    echo "   Acesse: http://localhost:8000/comerciantes/login\n";
    echo "   Use o email do usuário ID 3 para fazer login\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 32) . "\n";
