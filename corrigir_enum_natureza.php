<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== VERIFICANDO E CORRIGINDO ENUM NATUREZA_FINANCEIRA ===\n\n";
    
    // Verificar valores atuais
    $valores = DB::table('lancamentos')
                 ->select('natureza_financeira', DB::raw('COUNT(*) as total'))
                 ->groupBy('natureza_financeira')
                 ->get();
    
    echo "Valores atuais na coluna natureza_financeira:\n";
    foreach ($valores as $valor) {
        echo "- {$valor->natureza_financeira}: {$valor->total} registros\n";
    }
    echo "\n";
    
    // Verificar estrutura atual do enum
    $columnInfo = DB::select("SHOW COLUMNS FROM lancamentos LIKE 'natureza_financeira'");
    if (!empty($columnInfo)) {
        echo "Definição atual do enum:\n";
        echo $columnInfo[0]->Type . "\n\n";
    }
    
    DB::beginTransaction();
    
    // Primeiro, modificar o enum para aceitar os novos valores
    echo "1. Expandindo enum para aceitar novos valores...\n";
    DB::statement("ALTER TABLE lancamentos MODIFY COLUMN natureza_financeira ENUM('entrada','saida','receber','pagar') DEFAULT 'entrada'");
    echo "  ✓ Enum expandido\n";
    
    // Agora fazer a conversão dos dados
    echo "\n2. Convertendo dados...\n";
    
    $atualizados1 = DB::table('lancamentos')
                      ->where('natureza_financeira', 'receber')
                      ->update(['natureza_financeira' => 'entrada']);
    echo "  ✓ Convertidos $atualizados1 registros de 'receber' para 'entrada'\n";
    
    $atualizados2 = DB::table('lancamentos')
                      ->where('natureza_financeira', 'pagar')
                      ->update(['natureza_financeira' => 'saida']);
    echo "  ✓ Convertidos $atualizados2 registros de 'pagar' para 'saida'\n";
    
    // Finalmente, redefinir o enum apenas com os valores corretos
    echo "\n3. Finalizando enum com valores corretos...\n";
    DB::statement("ALTER TABLE lancamentos MODIFY COLUMN natureza_financeira ENUM('entrada','saida') DEFAULT 'entrada' COMMENT 'entrada=receber, saida=pagar'");
    echo "  ✓ Enum finalizado\n";
    
    // Verificar resultado
    echo "\n4. Verificando resultado...\n";
    $valoresFinais = DB::table('lancamentos')
                       ->select('natureza_financeira', DB::raw('COUNT(*) as total'))
                       ->groupBy('natureza_financeira')
                       ->get();
    
    echo "Valores finais na coluna natureza_financeira:\n";
    foreach ($valoresFinais as $valor) {
        echo "- {$valor->natureza_financeira}: {$valor->total} registros\n";
    }
    
    DB::commit();
    echo "\n✓ CONVERSÃO DO ENUM CONCLUÍDA COM SUCESSO!\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "\n✗ ERRO: " . $e->getMessage() . "\n";
}
?>
