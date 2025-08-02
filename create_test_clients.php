<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Criando clientes de teste...\n";

$clientes = [
    ['nome' => 'Ana', 'sobrenome' => 'Silva', 'email' => 'ana@teste.com', 'cpf_cnpj' => '12345678901'],
    ['nome' => 'João', 'sobrenome' => 'Santos', 'email' => 'joao@teste.com', 'cpf_cnpj' => '98765432109'],
    ['nome' => 'Maria', 'sobrenome' => 'Costa', 'email' => 'maria@teste.com', 'cpf_cnpj' => '11122233344']
];

foreach ($clientes as $cliente) {
    // Verificar se já existe
    $exists = DB::table('funforcli')->where('email', $cliente['email'])->exists();

    if (!$exists) {
        $id = DB::table('funforcli')->insertGetId([
            'empresa_id' => 1,
            'nome' => $cliente['nome'],
            'sobrenome' => $cliente['sobrenome'],
            'email' => $cliente['email'],
            'cpf_cnpj' => $cliente['cpf_cnpj'],
            'tipo' => 'cliente',
            'ativo' => 1,
            'status' => 'ativo',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Criar carteira de fidelidade
        $niveis = ['bronze', 'prata', 'ouro'];
        $nivel = $niveis[array_rand($niveis)];
        $saldo = rand(100, 1000);

        DB::table('fidelidade_carteiras')->insert([
            'cliente_id' => $id,
            'empresa_id' => 1,
            'saldo_cashback' => $saldo,
            'saldo_creditos' => rand(50, 200),
            'saldo_total_disponivel' => $saldo,
            'nivel_atual' => $nivel,
            'xp_total' => rand(500, 3000),
            'status' => 'ativa',
            'criado_em' => now(),
            'atualizado_em' => now()
        ]);

        echo "✓ Cliente {$cliente['nome']} criado - Nível: {$nivel}, Saldo: R$ {$saldo}\n";
    } else {
        echo "- Cliente {$cliente['nome']} já existe\n";
    }
}

echo "\nResumo:\n";
echo "Total de clientes: " . DB::table('funforcli')->where('tipo', 'cliente')->count() . "\n";
echo "Total de carteiras: " . DB::table('fidelidade_carteiras')->count() . "\n";
