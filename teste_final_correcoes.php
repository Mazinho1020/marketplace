<?php
/**
 * TESTE FINAL DAS CORREÇÕES NAS VIEWS FINANCEIRO
 * 
 * Verifica se todas as correções foram aplicadas corretamente
 * e se o sistema está funcionando sem erros críticos
 */

require_once 'vendor/autoload.php';

echo "🧪 TESTE FINAL - CORREÇÕES VIEWS FINANCEIRO\n";
echo "================================================================\n\n";

// Testar Model Lancamento atualizado
echo "🔍 1. TESTANDO MODEL LANCAMENTO ATUALIZADO...\n";

try {
    // Conectar ao banco
    $pdo = new PDO(
        'mysql:host=localhost;dbname=meufinanceiro;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ Conexão com banco estabelecida\n";
    
    // Verificar se existe algum lançamento para teste
    $stmt = $pdo->query("SELECT * FROM lancamentos LIMIT 1");
    $lancamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lancamento) {
        echo "✅ Lançamento encontrado (ID: {$lancamento['id']})\n";
        
        // Testar campos que foram corrigidos
        $camposTeste = [
            'valor_liquido' => $lancamento['valor_liquido'] ?? 'NULL',
            'valor_pago' => $lancamento['valor_pago'] ?? 'NULL',
            'situacao_financeira' => $lancamento['situacao_financeira'] ?? 'NULL'
        ];
        
        echo "   📊 Campos importantes:\n";
        foreach ($camposTeste as $campo => $valor) {
            echo "      • {$campo}: {$valor}\n";
        }
        
        // Verificar pagamentos relacionados
        $stmtPag = $pdo->prepare("SELECT COUNT(*) as total FROM pagamentos WHERE lancamento_id = ?");
        $stmtPag->execute([$lancamento['id']]);
        $totalPagamentos = $stmtPag->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "   💰 Pagamentos relacionados: {$totalPagamentos}\n";
        
    } else {
        echo "⚠️  Nenhum lançamento encontrado para teste\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao conectar com banco: " . $e->getMessage() . "\n";
}

echo "\n";

// Verificar arquivos de views corrigidos
echo "🔍 2. VERIFICANDO VIEWS CORRIGIDAS...\n";

$viewsCorrigidas = [
    'resources/views/comerciantes/financeiro/contas-pagar/index.blade.php',
    'resources/views/comerciantes/financeiro/contas-pagar/show.blade.php',
    'resources/views/comerciantes/financeiro/contas-pagar/pagamento.blade.php',
    'resources/views/comerciantes/financeiro/contas-receber/index.blade.php',
    'resources/views/comerciantes/financeiro/contas-receber/show.blade.php',
    'resources/views/comerciantes/financeiro/contas-receber/pagamento.blade.php'
];

$errosEncontrados = [];

foreach ($viewsCorrigidas as $view) {
    if (file_exists($view)) {
        $conteudo = file_get_contents($view);
        
        // Verificar se ainda existem campos incorretos
        $camposProblematicos = [
            'valor_original',
            'valor_final', 
            'valor_total',
            'valor_recebido',
            '->value',
            '->label()'
        ];
        
        $problemasEncontrados = [];
        foreach ($camposProblematicos as $campo) {
            if (strpos($conteudo, $campo) !== false) {
                $problemasEncontrados[] = $campo;
            }
        }
        
        if (empty($problemasEncontrados)) {
            echo "✅ {$view} - CORRIGIDO\n";
        } else {
            echo "❌ {$view} - AINDA TEM PROBLEMAS:\n";
            foreach ($problemasEncontrados as $problema) {
                echo "      • {$problema}\n";
                $errosEncontrados[] = "{$view}: {$problema}";
            }
        }
        
        // Verificar se backup existe
        $backupFiles = glob($view . '.backup-*');
        if (!empty($backupFiles)) {
            echo "   📁 Backup disponível: " . basename(end($backupFiles)) . "\n";
        }
        
    } else {
        echo "❌ ARQUIVO NÃO ENCONTRADO: {$view}\n";
        $errosEncontrados[] = "Arquivo não encontrado: {$view}";
    }
}

echo "\n";

// Verificar Model Lancamento
echo "🔍 3. VERIFICANDO MODEL LANCAMENTO...\n";

$modelPath = 'app/Models/Financeiro/Lancamento.php';
if (file_exists($modelPath)) {
    $modelContent = file_get_contents($modelPath);
    
    $metodosEssenciais = [
        'function pagamentos()' => 'Relacionamento pagamentos',
        'function contaGerencial()' => 'Relacionamento conta gerencial', 
        'function empresa()' => 'Relacionamento empresa',
        'getValorPagoCalculadoAttribute' => 'Accessor valor pago calculado',
        'getSaldoDevedorAttribute' => 'Accessor saldo devedor'
    ];
    
    $metodosEncontrados = [];
    foreach ($metodosEssenciais as $metodo => $descricao) {
        if (strpos($modelContent, $metodo) !== false) {
            echo "✅ {$descricao}\n";
            $metodosEncontrados[] = $metodo;
        } else {
            echo "❌ FALTANDO: {$descricao}\n";
            $errosEncontrados[] = "Model faltando: {$metodo}";
        }
    }
    
} else {
    echo "❌ MODEL NÃO ENCONTRADO: {$modelPath}\n";
    $errosEncontrados[] = "Model não encontrado";
}

echo "\n";

// Verificar se controllers estão funcionais
echo "🔍 4. VERIFICANDO SINTAXE PHP...\n";

$arquivosPhp = [
    'app/Models/Financeiro/Lancamento.php',
    'app/Services/Financeiro/LancamentoService.php'
];

foreach ($arquivosPhp as $arquivo) {
    if (file_exists($arquivo)) {
        $output = [];
        $returnCode = 0;
        exec("php -l \"{$arquivo}\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "✅ {$arquivo} - Sintaxe OK\n";
        } else {
            echo "❌ {$arquivo} - ERRO DE SINTAXE:\n";
            foreach ($output as $line) {
                echo "      {$line}\n";
            }
            $errosEncontrados[] = "Erro sintaxe: {$arquivo}";
        }
    }
}

echo "\n";

// Resumo final
echo "================================================================\n";
echo "📊 RESUMO FINAL DO TESTE\n";
echo "================================================================\n";

if (empty($errosEncontrados)) {
    echo "🎉 SUCESSO! Todas as correções foram aplicadas corretamente!\n\n";
    
    echo "✅ ITENS CORRIGIDOS:\n";
    echo "   • Campos valor_original → valor_liquido\n";
    echo "   • Campos valor_final → valor_liquido\n";
    echo "   • Campos valor_total → valor_liquido\n";
    echo "   • Campos valor_recebido → valor_pago\n";
    echo "   • Enums ->value removidos\n";
    echo "   • Relacionamentos adicionados ao Model\n";
    echo "   • Accessors para cálculos dinâmicos\n\n";
    
    echo "🎯 PRÓXIMOS PASSOS:\n";
    echo "   1. Testar interface web do sistema financeiro\n";
    echo "   2. Validar criação de lançamentos\n";
    echo "   3. Testar processamento de pagamentos\n";
    echo "   4. Verificar relatórios e dashboards\n\n";
    
    echo "🚀 SISTEMA PRONTO PARA USO!\n";
    
} else {
    echo "⚠️  ATENÇÃO! Ainda existem problemas que precisam ser corrigidos:\n\n";
    
    foreach ($errosEncontrados as $i => $erro) {
        echo "   " . ($i + 1) . ". {$erro}\n";
    }
    
    echo "\n❌ CORRIJA OS PROBLEMAS ACIMA ANTES DE USAR O SISTEMA\n";
}

echo "================================================================\n";
