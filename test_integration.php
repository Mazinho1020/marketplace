<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verificando integração funforcli + fidelidade_carteiras:\n\n";

// Teste da consulta do controller
$clientes = DB::table('funforcli as fc')
    ->leftJoin('fidelidade_carteiras as cart', 'fc.id', '=', 'cart.cliente_id')
    ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
    ->select(
        'fc.id',
        'fc.nome',
        'fc.sobrenome',
        'fc.email',
        'fc.ativo',
        'e.nome_fantasia as empresa_nome',
        'cart.saldo_cashback',
        'cart.saldo_total_disponivel',
        'cart.nivel_atual',
        'cart.xp_total',
        'cart.status as status_carteira'
    )
    ->where('fc.tipo', 'cliente')
    ->limit(5)
    ->get();

echo "Clientes encontrados: {$clientes->count()}\n\n";

foreach ($clientes as $cliente) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Cliente: {$cliente->nome} {$cliente->sobrenome}\n";
    echo "Email: {$cliente->email}\n";
    echo "Empresa: {$cliente->empresa_nome}\n";
    echo "Ativo: " . ($cliente->ativo ? 'Sim' : 'Não') . "\n";

    if ($cliente->status_carteira) {
        echo "--- Dados Fidelidade ---\n";
        echo "Saldo Cashback: R$ " . number_format($cliente->saldo_cashback, 2, ',', '.') . "\n";
        echo "Saldo Total: R$ " . number_format($cliente->saldo_total_disponivel, 2, ',', '.') . "\n";
        echo "Nível: {$cliente->nivel_atual}\n";
        echo "XP: {$cliente->xp_total}\n";
        echo "Status Carteira: {$cliente->status_carteira}\n";
    } else {
        echo "--- SEM CARTEIRA DE FIDELIDADE ---\n";
    }
    echo "\n";
}

// Estatísticas
$stats = [
    'total_clientes' => DB::table('funforcli')->where('tipo', 'cliente')->count(),
    'clientes_ativos' => DB::table('funforcli')->where('ativo', 1)->where('tipo', 'cliente')->count(),
    'clientes_inativos' => DB::table('funforcli')->where('ativo', 0)->where('tipo', 'cliente')->count(),
    'saldo_total' => DB::table('fidelidade_carteiras')->sum('saldo_total_disponivel') ?? 0
];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ESTATÍSTICAS DO SISTEMA:\n";
echo "Total de clientes: {$stats['total_clientes']}\n";
echo "Clientes ativos: {$stats['clientes_ativos']}\n";
echo "Clientes inativos: {$stats['clientes_inativos']}\n";
echo "Saldo total fidelidade: R$ " . number_format($stats['saldo_total'], 2, ',', '.') . "\n";
echo "\n✓ Integração testada com sucesso!\n";
