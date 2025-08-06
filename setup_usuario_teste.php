<?php

/**
 * Script para criar usuário de teste
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "<h1>Criando Usuário de Teste</h1>";

try {
    // Verificar se existe empresa
    $empresa = \App\Models\Empresa::first();
    if (!$empresa) {
        echo "<p>Criando empresa de teste...</p>";
        $empresa = \App\Models\Empresa::create([
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
            'cnpj' => '12345678000100',
            'email' => 'contato@empresateste.com',
            'telefone' => '(65) 99999-9999',
            'endereco' => 'Rua Teste, 123',
            'cidade' => 'Cuiabá',
            'estado' => 'MT',
            'cep' => '78000-000',
            'status' => 'ativo'
        ]);
        echo "<p>Empresa criada com ID: " . $empresa->id . "</p>";
    } else {
        echo "<p>Empresa encontrada: " . $empresa->nome_fantasia . " (ID: " . $empresa->id . ")</p>";
    }

    // Verificar se existe usuário comerciante
    $user = \App\Comerciantes\Models\EmpresaUsuario::where('email', 'teste@empresateste.com')->first();

    if (!$user) {
        echo "<p>Criando usuário comerciante de teste...</p>";
        $user = \App\Comerciantes\Models\EmpresaUsuario::create([
            'nome' => 'Usuário Teste',
            'email' => 'teste@empresateste.com',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'empresa_id' => $empresa->id,
            'ativo' => true,
            'tipo' => 'admin'
        ]);
        echo "<p>Usuário criado com ID: " . $user->id . "</p>";
    } else {
        echo "<p>Usuário já existe: " . $user->email . " (ID: " . $user->id . ")</p>";
    }

    echo "<h2>Dados para Login:</h2>";
    echo "<p><strong>Email:</strong> teste@empresateste.com</p>";
    echo "<p><strong>Senha:</strong> 123456</p>";
    echo "<p><strong>Empresa ID:</strong> " . $empresa->id . "</p>";

    echo "<h2>Links:</h2>";
    echo "<p><a href='/comerciantes/login'>Fazer Login</a></p>";
    echo "<p><a href='/comerciantes/empresas/" . $empresa->id . "/horarios'>Testar Horários após login</a></p>";
} catch (\Exception $e) {
    echo "<p><strong>ERRO:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Arquivo: " . $e->getFile() . " Linha: " . $e->getLine() . "</p>";
}
