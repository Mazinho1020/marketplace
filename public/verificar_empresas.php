<?php

/**
 * Script para verificar empresas disponíveis
 */

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

echo "<h1>🏢 Empresas Disponíveis</h1>";

try {
    $empresas = \App\Models\Empresa::all(['id', 'nome_fantasia', 'razao_social', 'status']);

    if ($empresas->count() > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nome Fantasia</th><th>Razão Social</th><th>Status</th><th>Testar Horários</th></tr>";

        foreach ($empresas as $empresa) {
            $nome = $empresa->nome_fantasia ?? $empresa->razao_social;
            echo "<tr>";
            echo "<td>{$empresa->id}</td>";
            echo "<td>{$empresa->nome_fantasia}</td>";
            echo "<td>{$empresa->razao_social}</td>";
            echo "<td>{$empresa->status}</td>";
            echo "<td><a href='/comerciantes/empresas/{$empresa->id}/horarios' target='_blank'>Testar</a></td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<h2>🔗 Links Diretos</h2>";
        foreach ($empresas as $empresa) {
            $nome = $empresa->nome_fantasia ?? $empresa->razao_social;
            echo "<p><a href='/comerciantes/empresas/{$empresa->id}/horarios' target='_blank'>Horários da {$nome} (ID: {$empresa->id})</a></p>";
        }
    } else {
        echo "<p>❌ Nenhuma empresa encontrada!</p>";
        echo "<p>Vou criar uma empresa de teste...</p>";

        $empresa = \App\Models\Empresa::create([
            'razao_social' => 'Empresa Teste LTDA',
            'nome_fantasia' => 'Empresa Teste',
            'cnpj' => '12345678000199',
            'email' => 'contato@empresateste.com',
            'telefone' => '(65) 99999-9999',
            'endereco' => 'Rua Teste, 123',
            'cidade' => 'Cuiabá',
            'estado' => 'MT',
            'cep' => '78000-000',
            'status' => 'ativo'
        ]);

        echo "<p>✅ Empresa criada com ID: {$empresa->id}</p>";
        echo "<p><a href='/comerciantes/empresas/{$empresa->id}/horarios'>Testar Horários da Nova Empresa</a></p>";
    }
} catch (\Exception $e) {
    echo "<p><strong>ERRO:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='/comerciantes/login'>🔐 Fazer Login</a></p>";
echo "<p><a href='/debug_302.php'>🔍 Debug Completo</a></p>";
