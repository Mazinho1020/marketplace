<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use Carbon\Carbon;

echo "=== TESTE DE CRIAÇÃO DE PARCELAMENTO ===\n\n";

// Simular dados de request
$request = [
    'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
    'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
    'descricao' => 'Teste Parcelamento Manual',
    'valor_original' => 1200.00,
    'numero_parcelas' => 6,
    'data_vencimento' => '2025-01-20',
    'data_emissao' => '2025-01-13',
    'pessoa_id' => null,
    'conta_gerencial_id' => null,
    'numero_documento' => 'TEST-001',
    'observacoes' => 'Teste de criação manual de parcelamento',
    'intervalo_parcelas' => 30,
    'desconto' => 0,
    'juros' => 0,
    'multa' => 0
];

$empresaId = 1;

echo "Criando parcelamento com {$request['numero_parcelas']} parcelas...\n";

try {
    // Calcular valores
    $valorOriginal = $request['valor_original'];
    $valorTotal = $valorOriginal;
    $valorParcela = $valorTotal / $request['numero_parcelas'];
    $dataVencimento = Carbon::parse($request['data_vencimento']);
    $grupoParcelas = uniqid('CR_' . $empresaId . '_');
    $dataEmissao = Carbon::parse($request['data_emissao']);

    $registrosCriados = [];

    for ($i = 1; $i <= $request['numero_parcelas']; $i++) {
        $lancamento = LancamentoFinanceiro::create([
            'empresa_id' => $empresaId,
            'natureza_financeira' => $request['natureza_financeira'],
            'situacao_financeira' => $request['situacao_financeira'],
            'descricao' => $request['descricao'] . " (Parcela {$i}/{$request['numero_parcelas']})",
            'valor' => round($valorParcela, 2),
            'valor_original' => round($valorOriginal / $request['numero_parcelas'], 2),
            'valor_total' => round($valorParcela, 2),
            'data' => $dataEmissao,
            'data_emissao' => $dataEmissao->toDateString(),
            'data_competencia' => $dataEmissao->toDateString(),
            'data_vencimento' => $dataVencimento->copy()->toDateString(),
            'pessoa_id' => $request['pessoa_id'],
            'pessoa_tipo' => $request['pessoa_id'] ? 'cliente' : null,
            'conta_gerencial_id' => $request['conta_gerencial_id'],
            'numero_documento' => $request['numero_documento'],
            'observacoes' => $request['observacoes'],
            'parcela_atual' => $i,
            'total_parcelas' => $request['numero_parcelas'],
            'grupo_parcelas' => $grupoParcelas,
            'intervalo_parcelas' => $request['intervalo_parcelas'],
            'usuario_id' => 1
        ]);

        $registrosCriados[] = $lancamento->id;
        echo "Parcela {$i} criada - ID: {$lancamento->id} | Vencimento: {$dataVencimento->toDateString()}\n";

        // Calcular próxima data
        if ($i < $request['numero_parcelas']) {
            $dataVencimento->addDays($request['intervalo_parcelas']);
        }
    }

    echo "\n=== RESULTADO ===\n";
    echo "Grupo criado: " . substr($grupoParcelas, -8) . "\n";
    echo "Total de parcelas criadas: " . count($registrosCriados) . "\n";
    echo "IDs criados: " . implode(', ', $registrosCriados) . "\n";

    // Verificar no banco
    $verificacao = DB::table('lancamentos')
        ->where('grupo_parcelas', $grupoParcelas)
        ->select('id', 'descricao', 'parcela_atual', 'total_parcelas', 'valor', 'data_vencimento')
        ->orderBy('parcela_atual')
        ->get();

    echo "\n=== VERIFICAÇÃO NO BANCO ===\n";
    foreach ($verificacao as $reg) {
        echo "ID: {$reg->id} | Parcela: {$reg->parcela_atual}/{$reg->total_parcelas} | Valor: R\$ {$reg->valor} | Vencimento: {$reg->data_vencimento}\n";
    }
} catch (\Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
