<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Verificando dados de clientes no banco...\n\n";

try {
    // Contar carteiras (que são nossos clientes)
    $totalCarteiras = DB::table('fidelidade_carteiras')->count();
    echo "📊 Total de carteiras/clientes: {$totalCarteiras}\n";

    if ($totalCarteiras > 0) {
        echo "\n📋 Primeiras 5 carteiras:\n";
        $carteiras = DB::table('fidelidade_carteiras')->limit(5)->get();
        foreach ($carteiras as $carteira) {
            echo "- ID: {$carteira->id}, Cliente ID: {$carteira->cliente_id}, Status: {$carteira->status}\n";
        }
    } else {
        echo "❌ Nenhuma carteira encontrada!\n";
        echo "\n🔧 Criando dados de teste...\n";

        // Criar algumas carteiras de teste
        for ($i = 1; $i <= 3; $i++) {
            DB::table('fidelidade_carteiras')->insert([
                'cliente_id' => 1000 + $i,
                'empresa_id' => 1, // Usar empresa existente
                'nivel_atual' => ['bronze', 'prata', 'ouro'][rand(0, 2)],
                'xp_total' => rand(100, 1000),
                'saldo_total_disponivel' => rand(10, 500),
                'status' => 'ativa',
                'criado_em' => now(),
                'atualizado_em' => now()
            ]);
        }

        echo "✅ Criadas 3 carteiras de teste!\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🎯 Teste concluído!\n";
