<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use App\Services\Financial\ContasReceberService;
use App\Models\User;

echo "🔧 Testando Criação de Recebimento com Usuário Correto\n";
echo "=====================================================\n\n";

try {
    // Pegar primeiro usuário da tabela empresa_usuarios
    $usuario = User::first();

    if (!$usuario) {
        echo "❌ Nenhum usuário encontrado na tabela empresa_usuarios\n";
        exit;
    }

    echo "👤 Usuário encontrado:\n";
    echo "   - ID: {$usuario->id}\n";
    echo "   - Nome: {$usuario->nome}\n";
    echo "   - Email: {$usuario->email}\n";
    echo "   - Empresa ID: " . ($usuario->empresa_id ?? 'NULL') . "\n\n";

    // Verificar se o lançamento 391 existe
    $lancamento = DB::table('lancamentos')->where('id', 391)->first();

    if (!$lancamento) {
        echo "❌ Lançamento 391 não encontrado\n";
        exit;
    }

    echo "💰 Lançamento 391 encontrado:\n";
    echo "   - Descrição: {$lancamento->descricao}\n";
    echo "   - Valor: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
    echo "   - Natureza: {$lancamento->natureza_financeira}\n\n";

    // Preparar dados do recebimento
    $dados = [
        'forma_pagamento_id' => 25, // Cartão de débito
        'bandeira_id' => 35, // Elo Débito
        'conta_bancaria_id' => 2,
        'valor' => 100.00,
        'valor_principal' => 100.00,
        'valor_juros' => 0,
        'valor_multa' => 0,
        'valor_desconto' => 0,
        'data_recebimento' => '2025-08-14',
        'data_compensacao' => null,
        'observacao' => 'Teste com usuário da empresa_usuarios',
        'comprovante_recebimento' => null,
        'taxa' => 0,
        'valor_taxa' => 0,
        'referencia_externa' => null,
        'usuario_id' => $usuario->id, // ID do usuário da empresa_usuarios
    ];

    echo "📝 Dados preparados para recebimento:\n";
    echo "   - Forma de pagamento: {$dados['forma_pagamento_id']}\n";
    echo "   - Bandeira: {$dados['bandeira_id']}\n";
    echo "   - Conta bancária: {$dados['conta_bancaria_id']}\n";
    echo "   - Valor: R$ " . number_format($dados['valor'], 2, ',', '.') . "\n";
    echo "   - Usuário ID: {$dados['usuario_id']}\n\n";

    // Criar o recebimento usando o Service
    echo "🚀 Criando recebimento...\n";

    $contasReceberService = new ContasReceberService();
    $recebimento = $contasReceberService->receber(391, $dados);

    echo "✅ Recebimento criado com sucesso!\n";
    echo "   - ID: {$recebimento->id}\n";
    echo "   - Valor: R$ " . number_format($recebimento->valor, 2, ',', '.') . "\n";
    echo "   - Status: {$recebimento->status_recebimento}\n";
    echo "   - Usuário ID: {$recebimento->usuario_id}\n";
    echo "   - Data: {$recebimento->data_recebimento}\n\n";

    echo "🎉 Teste finalizado com sucesso!\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📄 Linha: " . $e->getLine() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n\n";

    if (strpos($e->getMessage(), 'usuario_id') !== false) {
        echo "💡 Dica: O erro está relacionado ao campo usuario_id.\n";
        echo "   - Verifique se o modelo User está configurado corretamente\n";
        echo "   - Verifique se a tabela empresa_usuarios tem o campo id\n";
    }
}
