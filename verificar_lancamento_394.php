<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;

echo "=== VERIFICANDO LANÇAMENTO 394 ===\n";

$lancamento = LancamentoFinanceiro::find(394);
if ($lancamento) {
    echo "✅ Lançamento 394 existe!\n";
    echo "   Descrição: {$lancamento->descricao}\n";
    echo "   Valor: R$ {$lancamento->valor}\n";
    echo "   Status: {$lancamento->situacao_financeira->value}\n";
    echo "   Empresa: {$lancamento->empresa_id}\n";
    echo "   Natureza: {$lancamento->natureza_financeira->value}\n";

    // Verificar recebimentos
    $recebimentos = $lancamento->recebimentos;
    echo "   Recebimentos: {$recebimentos->count()}\n";
    foreach ($recebimentos as $rec) {
        echo "     -> ID: {$rec->id}, Valor: R$ {$rec->valor}, Status: {$rec->status_recebimento}\n";
    }
} else {
    echo "❌ Lançamento 394 não encontrado\n";
}
