<?php
// Teste rápido para verificar se a página de clientes está funcionando

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔍 Testando sistema de clientes..." . PHP_EOL;

    // Testar conexão com banco
    $totalCarteiras = DB::table('fidelidade_carteiras')->count();
    echo "✅ Total de carteiras no banco: $totalCarteiras" . PHP_EOL;

    // Testar query do controller
    $clientes = DB::table('fidelidade_carteiras as fc')
        ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
        ->select('fc.*', 'e.nome_fantasia as empresa_nome')
        ->orderBy('fc.criado_em', 'desc')
        ->paginate(15);

    echo "✅ Query de paginação funcionando" . PHP_EOL;
    echo "📊 Total de registros: " . $clientes->total() . PHP_EOL;
    echo "📄 Registros por página: " . $clientes->perPage() . PHP_EOL;
    echo "🔢 Primeiro item: " . ($clientes->firstItem() ?? 0) . PHP_EOL;
    echo "🔢 Último item: " . ($clientes->lastItem() ?? 0) . PHP_EOL;

    if ($clientes->count() > 0) {
        echo "👥 Clientes encontrados:" . PHP_EOL;
        foreach ($clientes as $cliente) {
            echo "  - {$cliente->nome_cliente} (Empresa: " . ($cliente->empresa_nome ?? 'N/A') . ")" . PHP_EOL;
        }
    } else {
        echo "⚠️  Nenhum cliente encontrado" . PHP_EOL;
    }

    echo "🎯 Teste concluído com sucesso!" . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . PHP_EOL;
}
