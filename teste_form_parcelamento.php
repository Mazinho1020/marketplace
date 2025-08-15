<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== TESTE COMPLETO DO PARCELAMENTO ===\n\n";

// Simular um request de form como se fosse enviado pelo navegador
$requestData = [
    'valor_original' => 1800.00,
    'valor_total' => 1800.00,
    'descricao' => 'Teste Parcelamento via Form',
    'data_emissao' => '2025-01-13',
    'data_competencia' => '2025-01-13',
    'data_vencimento' => '2025-02-13',
    'numero_parcelas' => 6,
    'intervalo_parcelas' => 30,
    'habilitarParcelamento' => '1',
    'is_parcelado' => '1',
    'natureza_financeira' => 'receber',
    'situacao_financeira' => 'pendente',
    'pessoa_id' => null,
    'numero_documento' => 'TEST-FORM-001',
    'observacoes' => 'Teste de criação de parcelamento via simulação de form'
];

echo "Dados do request simulado:\n";
foreach ($requestData as $key => $value) {
    echo "  $key: $value\n";
}

echo "\nTestando condições de parcelamento:\n";

$numeroParcelas = (int)$requestData['numero_parcelas'];
$isParcelado = isset($requestData['habilitarParcelamento']) || isset($requestData['is_parcelado']) || $numeroParcelas > 1;

echo "  numero_parcelas: $numeroParcelas\n";
echo "  habilitarParcelamento: " . (isset($requestData['habilitarParcelamento']) ? 'SIM' : 'NÃO') . "\n";
echo "  is_parcelado: " . (isset($requestData['is_parcelado']) ? 'SIM' : 'NÃO') . "\n";
echo "  isParcelado (resultado): " . ($isParcelado ? 'SIM' : 'NÃO') . "\n";

if ($isParcelado && $numeroParcelas > 1) {
    echo "\n✅ CONDIÇÕES ATENDIDAS - Será criado parcelamento!\n";
    echo "✅ Deveria criar $numeroParcelas parcelas de R$ " . number_format($requestData['valor_original'] / $numeroParcelas, 2, ',', '.') . " cada\n";
} else {
    echo "\n❌ CONDIÇÕES NÃO ATENDIDAS - Será criado lançamento único!\n";
}

echo "\n=== VERIFICAÇÃO FINAL DOS DADOS NO BANCO ===\n";

// Contar registros antes
$antes = DB::table('lancamentos')->count();
echo "Registros antes: $antes\n";

// Contar registros com parcelamento
$comParcelamento = DB::table('lancamentos')->whereNotNull('grupo_parcelas')->count();
echo "Registros com parcelamento existentes: $comParcelamento\n";

if ($comParcelamento > 0) {
    echo "\n=== GRUPOS DE PARCELAMENTO EXISTENTES ===\n";
    $grupos = DB::table('lancamentos')
        ->select('grupo_parcelas', DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as ultima_criacao'))
        ->whereNotNull('grupo_parcelas')
        ->groupBy('grupo_parcelas')
        ->orderBy('ultima_criacao', 'desc')
        ->get();

    foreach ($grupos as $grupo) {
        echo "Grupo: " . substr($grupo->grupo_parcelas, -8) . " | Parcelas: {$grupo->total} | Criado: {$grupo->ultima_criacao}\n";
    }
}
