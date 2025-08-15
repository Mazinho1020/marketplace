<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\Comerciantes\Financial\PagamentoController;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Empresa;
use App\Services\Financial\ContasPagarService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Teste direto do Controller de Pagamento\n\n";

try {
    // 1. Preparar dados
    $empresa = Empresa::find(1);
    $lancamento = LancamentoFinanceiro::find(377);

    if (!$empresa || !$lancamento) {
        echo "❌ Empresa ou lançamento não encontrado\n";
        exit;
    }

    echo "✅ Empresa: {$empresa->razao_social}\n";
    echo "✅ Lançamento: {$lancamento->descricao}\n\n";

    // 2. Criar uma instância do Request com os dados
    $requestData = [
        'forma_pagamento_id' => 6,
        'bandeira_id' => null,
        'conta_bancaria_id' => 1,
        'valor' => 300.00,
        'valor_principal' => 300.00,
        'valor_juros' => 0,
        'valor_multa' => 0,
        'valor_desconto' => 0,
        'data_pagamento' => '2025-08-14',
        'data_compensacao' => null,
        'observacao' => 'Teste via Controller',
        'comprovante_pagamento' => null,
        'taxa' => 0,
        'valor_taxa' => 0,
        'referencia_externa' => null,
    ];

    // 3. Simular Request
    $request = new Request();
    $request->merge($requestData);
    $request->setMethod('POST');

    echo "📝 Request preparado com dados:\n";
    foreach ($requestData as $key => $value) {
        echo "   {$key}: " . ($value ?? 'null') . "\n";
    }
    echo "\n";

    // 4. Instanciar Controller
    echo "🎮 Instanciando Controller...\n";
    $service = new ContasPagarService();
    $controller = new PagamentoController($service);
    echo "✅ Controller instanciado\n\n";

    // 5. Simular autenticação
    echo "🔐 Configurando autenticação...\n";
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    echo "✅ Usuário autenticado (ID: 1)\n\n";

    // 6. Executar método store
    echo "💰 Executando Controller->store()...\n";

    \Illuminate\Support\Facades\DB::beginTransaction();

    $response = $controller->store($request, $empresa, $lancamento);

    echo "✅ Método executado!\n";
    echo "📡 Response status: {$response->getStatusCode()}\n";
    echo "📄 Response content:\n";

    $content = $response->getContent();
    echo $content . "\n\n";

    // Tentar decodificar JSON
    $data = json_decode($content, true);
    if ($data) {
        echo "📊 Dados da resposta:\n";
        echo "   Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "   Message: {$data['message']}\n";
        if (isset($data['pagamento'])) {
            echo "   Pagamento ID: {$data['pagamento']['id']}\n";
        }
    }

    \Illuminate\Support\Facades\DB::rollBack();
    echo "\n🔄 Transação revertida (teste)\n";
    echo "\n✅ Teste do Controller concluído com sucesso!\n";
} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "❌ Erro durante o teste do Controller:\n";
    echo "   Tipo: " . get_class($e) . "\n";
    echo "   Mensagem: {$e->getMessage()}\n";
    echo "   Arquivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n📋 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
