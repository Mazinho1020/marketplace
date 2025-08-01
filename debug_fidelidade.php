<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG FIDELIDADE CONTROLLER ===\n\n";

try {
    echo "1. Testando conexão com banco...\n";
    DB::connection()->getPdo();
    echo "✅ Conexão OK\n\n";

    echo "2. Testando query das regras de cashback...\n";
    $programas = DB::table('fidelidade_cashback_regras as fcr')
        ->leftJoin('empresas as e', 'fcr.empresa_id', '=', 'e.id')
        ->leftJoin(
            DB::raw('(SELECT empresa_id, COUNT(*) as total_transacoes FROM fidelidade_cashback_transacoes GROUP BY empresa_id) as trans_count'),
            'fcr.empresa_id',
            '=',
            'trans_count.empresa_id'
        )
        ->select(
            'fcr.*',
            'e.nome_fantasia as empresa_nome',
            DB::raw('COALESCE(trans_count.total_transacoes, 0) as total_transacoes')
        )
        ->orderBy('fcr.criado_em', 'desc')
        ->get();

    echo "✅ Query executada. Encontrados: " . $programas->count() . " programas\n\n";

    echo "3. Testando stats...\n";
    $stats = [
        'total_regras' => $programas->count(),
        'regras_ativas' => $programas->where('ativo', 1)->count(),
        'regras_inativas' => $programas->where('ativo', 0)->count(),
        'total_transacoes' => $programas->sum('total_transacoes')
    ];

    echo "✅ Stats calculadas:\n";
    foreach ($stats as $key => $value) {
        echo "   $key: $value\n";
    }

    echo "\n4. Verificando view...\n";
    $view_path = __DIR__ . '/resources/views/admin/fidelidade/programas.blade.php';
    if (file_exists($view_path)) {
        echo "✅ View existe: $view_path\n";
    } else {
        echo "❌ View não encontrada: $view_path\n";
    }

    echo "\n5. Testando FidelidadeController diretamente...\n";

    // Simular Request
    $controller = new \App\Http\Controllers\FidelidadeController();

    echo "Controller criado, testando método programas()...\n";

    // Capturar qualquer erro
    $result = $controller->programas();

    echo "✅ Método programas() executado com sucesso\n";
    echo "Tipo de resultado: " . get_class($result) . "\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG CONCLUÍDO ===\n";
