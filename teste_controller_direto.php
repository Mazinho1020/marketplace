<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Empresa;
use App\Http\Controllers\Comerciantes\Financial\RecebimentoController;
use App\Services\Financial\ContasReceberService;
use Illuminate\Http\Request;

echo "🧪 Teste Direto do Controller de Recebimento\n";
echo "=============================================\n\n";

try {
    // Simular usuário autenticado
    $usuario = User::first();
    if ($usuario) {
        \Illuminate\Support\Facades\Auth::login($usuario);
        echo "👤 Usuário autenticado: {$usuario->nome} (ID: {$usuario->id})\n\n";
    }

    // Buscar empresa
    $empresa = Empresa::find(1);
    if (!$empresa) {
        echo "❌ Empresa não encontrada\n";
        exit;
    }

    echo "🏢 Empresa: {$empresa->nome} (ID: {$empresa->id})\n\n";

    // Preparar dados da requisição
    $dados = [
        'forma_pagamento_id' => '25',
        'bandeira_id' => '35',
        'conta_bancaria_id' => '2',
        'valor' => '50', // Valor menor para teste
        'valor_principal' => '50',
        'valor_juros' => '0',
        'valor_multa' => '0',
        'valor_desconto' => '0',
        'data_recebimento' => '2025-08-14',
        'observacao' => 'Teste direto do controller'
    ];

    // Criar requisição simulada
    $request = new Request($dados);
    $request->setMethod('POST');

    echo "📝 Dados da requisição:\n";
    echo json_encode($dados, JSON_PRETTY_PRINT) . "\n\n";

    // Instanciar o controller
    $contasReceberService = new ContasReceberService();
    $controller = new RecebimentoController($contasReceberService);

    echo "🚀 Executando controller...\n";

    // Executar o método store
    $response = $controller->store($request, $empresa, 392);

    // Verificar resposta
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = $response->getData(true);

        if ($data['success'] ?? false) {
            echo "✅ Recebimento criado com sucesso!\n";
            echo "   - ID: " . ($data['recebimento']['id'] ?? 'N/A') . "\n";
            echo "   - Valor: R$ " . number_format($data['recebimento']['valor'] ?? 0, 2, ',', '.') . "\n";
            echo "   - Status: " . ($data['recebimento']['status_recebimento'] ?? 'N/A') . "\n";
        } else {
            echo "❌ Erro: " . ($data['message'] ?? 'Erro desconhecido') . "\n";
        }
    } else {
        echo "⚠️ Resposta inesperada: " . get_class($response) . "\n";
        echo "Conteúdo: " . $response->getContent() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📄 Linha: " . $e->getLine() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
}

echo "\n🏁 Teste finalizado!\n";
