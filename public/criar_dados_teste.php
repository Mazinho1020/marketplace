<?php

/**
 * Script para criar dados de teste completos
 */

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

echo "<h1>🚀 Criando Dados de Teste</h1>";

try {
    // 1. Verificar/Criar empresas
    echo "<h2>1. Verificando Empresas</h2>";
    $empresaCount = \App\Models\Empresa::count();
    echo "<p>Empresas existentes: {$empresaCount}</p>";

    if ($empresaCount == 0) {
        echo "<p>Criando empresas de teste...</p>";

        $empresas = [
            [
                'razao_social' => 'Restaurante Bom Sabor LTDA',
                'nome_fantasia' => 'Restaurante Bom Sabor',
                'cnpj' => '12345678000101',
                'email' => 'contato@bomsabor.com',
                'telefone' => '(65) 3333-1111',
                'endereco' => 'Av. Principal, 100',
                'cidade' => 'Cuiabá',
                'estado' => 'MT',
                'cep' => '78000-100',
                'status' => 'ativo'
            ],
            [
                'razao_social' => 'Lanchonete Rapidão LTDA',
                'nome_fantasia' => 'Lanchonete Rapidão',
                'cnpj' => '12345678000102',
                'email' => 'contato@rapidao.com',
                'telefone' => '(65) 3333-2222',
                'endereco' => 'Rua Comercial, 200',
                'cidade' => 'Cuiabá',
                'estado' => 'MT',
                'cep' => '78000-200',
                'status' => 'ativo'
            ]
        ];

        foreach ($empresas as $dadosEmpresa) {
            $empresa = \App\Models\Empresa::create($dadosEmpresa);
            echo "<p>✅ Empresa criada: {$empresa->nome_fantasia} (ID: {$empresa->id})</p>";
        }
    } else {
        echo "<p>✅ Empresas já existem</p>";
    }

    // 2. Verificar/Criar usuários comerciantes
    echo "<h2>2. Verificando Usuários Comerciantes</h2>";
    $userCount = \App\Comerciantes\Models\EmpresaUsuario::count();
    echo "<p>Usuários comerciantes existentes: {$userCount}</p>";

    if ($userCount == 0) {
        echo "<p>Criando usuários de teste...</p>";

        $empresas = \App\Models\Empresa::all();
        foreach ($empresas as $empresa) {
            $user = \App\Comerciantes\Models\EmpresaUsuario::create([
                'nome' => 'Admin ' . $empresa->nome_fantasia,
                'email' => 'admin@' . strtolower(str_replace(' ', '', $empresa->nome_fantasia)) . '.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'empresa_id' => $empresa->id,
                'ativo' => true,
                'tipo' => 'admin'
            ]);
            echo "<p>✅ Usuário criado: {$user->email} para empresa {$empresa->nome_fantasia}</p>";
        }
    } else {
        echo "<p>✅ Usuários já existem</p>";
    }

    // 3. Listar tudo criado
    echo "<h2>3. Resumo dos Dados</h2>";

    $empresas = \App\Models\Empresa::with(['horarios'])->get();

    foreach ($empresas as $empresa) {
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0;'>";
        echo "<h3>🏢 {$empresa->nome_fantasia} (ID: {$empresa->id})</h3>";
        echo "<p><strong>Email:</strong> {$empresa->email}</p>";
        echo "<p><strong>Status:</strong> {$empresa->status}</p>";

        // Buscar usuário
        $user = \App\Comerciantes\Models\EmpresaUsuario::where('empresa_id', $empresa->id)->first();
        if ($user) {
            echo "<p><strong>Usuário Admin:</strong> {$user->email} (senha: 123456)</p>";
        }

        // Verificar horários
        $horariosCount = \App\Models\HorarioFuncionamento::where('empresa_id', $empresa->id)->count();
        echo "<p><strong>Horários cadastrados:</strong> {$horariosCount}</p>";

        echo "<p><strong>🔗 Links:</strong></p>";
        echo "<ul>";
        echo "<li><a href='/comerciantes/empresas/{$empresa->id}/horarios' target='_blank'>Gerenciar Horários</a></li>";
        echo "<li><a href='/comerciantes/login' target='_blank'>Fazer Login</a></li>";
        echo "</ul>";
        echo "</div>";
    }
} catch (\Exception $e) {
    echo "<p><strong>ERRO:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
