<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Populando tabela fidelidade_carteiras com dados dos clientes funforcli...\n";

// Buscar clientes da tabela funforcli
$clientes = DB::table('funforcli')
    ->where('tipo', 'cliente')
    ->select('id', 'nome', 'email')
    ->get();

echo "Encontrados {$clientes->count()} clientes na tabela funforcli\n";

foreach ($clientes as $cliente) {
    // Verificar se já existe carteira para este cliente
    $exists = DB::table('fidelidade_carteiras')
        ->where('cliente_id', $cliente->id)
        ->exists();

    if (!$exists) {
        // Gerar dados aleatórios para o programa de fidelidade
        $niveis = ['bronze', 'prata', 'ouro'];
        $nivel = $niveis[array_rand($niveis)];
        $saldo_cashback = rand(50, 500);
        $xp_total = rand(100, 2000);

        DB::table('fidelidade_carteiras')->insert([
            'cliente_id' => $cliente->id,
            'empresa_id' => 1,
            'saldo_cashback' => $saldo_cashback,
            'saldo_creditos' => rand(10, 100),
            'saldo_bloqueado' => 0,
            'saldo_total_disponivel' => $saldo_cashback,
            'nivel_atual' => $nivel,
            'xp_total' => $xp_total,
            'status' => 'ativa',
            'criado_em' => now(),
            'atualizado_em' => now()
        ]);

        echo "✓ Carteira criada para {$cliente->nome} - Nível: {$nivel}, Saldo: R$ {$saldo_cashback}\n";
    } else {
        echo "- Carteira já existe para {$cliente->nome}\n";
    }
}

echo "\nResumo final:\n";
$total_carteiras = DB::table('fidelidade_carteiras')->count();
$total_saldo = DB::table('fidelidade_carteiras')->sum('saldo_total_disponivel');
echo "Total de carteiras: {$total_carteiras}\n";
echo "Saldo total do sistema: R$ {$total_saldo}\n";
