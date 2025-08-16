<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== VERIFICANDO ESTRUTURA DAS TABELAS ===\n\n";
    
    // Verificar se as tabelas existem
    $tables = ['lancamentos', 'lancamento_itens', 'lancamento_movimentacoes'];
    
    foreach ($tables as $table) {
        echo "Tabela: $table\n";
        try {
            $structure = DB::select("DESCRIBE $table");
            echo "✓ Existe\n";
            echo "Colunas: " . count($structure) . "\n";
            
            // Mostrar algumas colunas importantes
            $columns = array_column($structure, 'Field');
            $importantColumns = ['id', 'uuid', 'valor_bruto', 'valor_liquido', 'situacao_financeira'];
            $found = array_intersect($importantColumns, $columns);
            echo "Colunas importantes encontradas: " . implode(', ', $found) . "\n";
            
        } catch (Exception $e) {
            echo "✗ Não existe ou erro: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }
    
    // Verificar quantos lançamentos existem
    try {
        $count = DB::table('lancamentos')->count();
        echo "Total de lançamentos na base: $count\n\n";
        
        if ($count > 0) {
            $sample = DB::table('lancamentos')->first();
            echo "Exemplo de registro:\n";
            print_r($sample);
        }
        
    } catch (Exception $e) {
        echo "Erro ao contar lançamentos: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "\n";
}
?>
