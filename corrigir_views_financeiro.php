<?php
/**
 * SCRIPT DE CORREÇÃO AUTOMÁTICA DAS VIEWS FINANCEIRO
 * 
 * Este script corrige automaticamente os erros identificados nas views
 * do sistema financeiro para compatibilidade com a nova estrutura integrada
 */

require_once 'vendor/autoload.php';

echo "🔧 INICIANDO CORREÇÃO AUTOMÁTICA DAS VIEWS FINANCEIRO\n";
echo "================================================================\n\n";

$viewsPath = 'resources/views/comerciantes/financeiro/';
$correcoesPrioritarias = [];

// Mapeamento de correções
$correcoes = [
    // Campos de valor
    'valor_original' => 'valor_liquido',
    'valor_final' => 'valor_liquido', 
    'valor_total' => 'valor_liquido',
    'valor_recebido' => 'valor_pago',
    
    // Situações financeiras
    "'recebido'" => "'pago'",
    "'quitado'" => "'pago'",
    "== 'recebido'" => "== 'pago'",
    "== 'quitado'" => "== 'pago'",
    
    // Enums com ->value
    'situacao_financeira->value' => 'situacao_financeira',
    '$conta->situacao_financeira->value' => '$conta->situacao_financeira',
    '$lancamento->situacao_financeira->value' => '$lancamento->situacao_financeira',
    
    // Labels de enum
    'situacao_financeira->label()' => 'ucfirst(str_replace("_", " ", $conta->situacao_financeira))',
];

// Verificar se as views existem
$arquivosParaCorrigir = [
    'contas-pagar/index.blade.php',
    'contas-pagar/show.blade.php', 
    'contas-pagar/pagamento.blade.php',
    'contas-receber/index.blade.php',
    'contas-receber/show.blade.php',
    'contas-receber/pagamento.blade.php'
];

$totalCorrections = 0;
$arquivosCorrigidos = 0;

foreach ($arquivosParaCorrigir as $arquivo) {
    $caminhoCompleto = $viewsPath . $arquivo;
    
    if (!file_exists($caminhoCompleto)) {
        echo "⚠️  ARQUIVO NÃO ENCONTRADO: {$caminhoCompleto}\n";
        continue;
    }
    
    echo "🔍 Analisando: {$arquivo}\n";
    
    $conteudo = file_get_contents($caminhoCompleto);
    $conteudoOriginal = $conteudo;
    $correcoesAplicadas = [];
    
    // Aplicar cada correção
    foreach ($correcoes as $buscar => $substituir) {
        if (strpos($conteudo, $buscar) !== false) {
            $conteudo = str_replace($buscar, $substituir, $conteudo);
            $correcoesAplicadas[] = "{$buscar} → {$substituir}";
        }
    }
    
    // Correções específicas por arquivo
    if (strpos($arquivo, 'index.blade.php') !== false) {
        // Corrigir cálculo de valor pago nas listagens
        $patterns = [
            '/\$conta->valor_pago\s*>\s*0/' => '$conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor") > 0',
            '/number_format\(\$conta->valor_pago,/' => 'number_format($conta->pagamentos()->where("status_pagamento", "confirmado")->sum("valor"),'
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $conteudo)) {
                $conteudo = preg_replace($pattern, $replacement, $conteudo);
                $correcoesAplicadas[] = "Cálculo valor_pago corrigido";
            }
        }
    }
    
    if (strpos($arquivo, 'show.blade.php') !== false) {
        // Corrigir cálculos de saldo devedor
        $patterns = [
            '/\$lancamento->valor_final\s*-\s*\$valorPago/' => '$lancamento->valor_liquido - $valorPago',
            '/\$lancamento->valor_final\s*>\s*0\s*\?\s*\(\$valorPago\s*\/\s*\$lancamento->valor_final\)/' => '$lancamento->valor_liquido > 0 ? ($valorPago / $lancamento->valor_liquido)'
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $conteudo)) {
                $conteudo = preg_replace($pattern, $replacement, $conteudo);
                $correcoesAplicadas[] = "Cálculo saldo devedor corrigido";
            }
        }
    }
    
    if (strpos($arquivo, 'pagamento.blade.php') !== false) {
        // Corrigir referências a valor_total
        $patterns = [
            '/\$contaPagar->valor_total\s*\?\?\s*\$contaPagar->valor/' => '$contaPagar->valor_liquido',
            '/\(\$contaPagar->valor_total\s*\?\?\s*\$contaPagar->valor\)\s*-\s*\$valorPago/' => '$contaPagar->valor_liquido - $valorPago'
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $conteudo)) {
                $conteudo = preg_replace($pattern, $replacement, $conteudo);
                $correcoesAplicadas[] = "Referência valor_total corrigida";
            }
        }
    }
    
    // Se houve correções, salvar arquivo
    if ($conteudo !== $conteudoOriginal) {
        // Criar backup
        $backupPath = $caminhoCompleto . '.backup-' . date('Y-m-d-H-i-s');
        file_put_contents($backupPath, $conteudoOriginal);
        
        // Salvar arquivo corrigido
        file_put_contents($caminhoCompleto, $conteudo);
        
        $arquivosCorrigidos++;
        $totalCorrections += count($correcoesAplicadas);
        
        echo "   ✅ CORRIGIDO ({" . count($correcoesAplicadas) . "} correções)\n";
        echo "   📁 Backup salvo: {$backupPath}\n";
        
        foreach ($correcoesAplicadas as $correcao) {
            echo "      • {$correcao}\n";
        }
        
        $correcoesPrioritarias[$arquivo] = $correcoesAplicadas;
    } else {
        echo "   ℹ️  Nenhuma correção necessária\n";
    }
    
    echo "\n";
}

echo "================================================================\n";
echo "📊 RESUMO DAS CORREÇÕES\n";
echo "================================================================\n";
echo "📁 Arquivos analisados: " . count($arquivosParaCorrigir) . "\n";
echo "✅ Arquivos corrigidos: {$arquivosCorrigidos}\n";
echo "🔧 Total de correções: {$totalCorrections}\n\n";

if (!empty($correcoesPrioritarias)) {
    echo "📋 DETALHES DAS CORREÇÕES APLICADAS:\n\n";
    
    foreach ($correcoesPrioritarias as $arquivo => $lista) {
        echo "📄 {$arquivo}:\n";
        foreach ($lista as $i => $correcao) {
            echo "   " . ($i + 1) . ". {$correcao}\n";
        }
        echo "\n";
    }
}

// Verificar se precisamos criar relacionamentos no Model
echo "🔍 VERIFICANDO MODELS...\n";

$modelLancamento = 'app/Models/Financeiro/Lancamento.php';
if (file_exists($modelLancamento)) {
    $modelContent = file_get_contents($modelLancamento);
    
    $relacionamentosFaltantes = [];
    
    if (strpos($modelContent, 'function pessoa()') === false) {
        $relacionamentosFaltantes[] = 'Relacionamento pessoa()';
    }
    
    if (strpos($modelContent, 'function contaGerencial()') === false) {
        $relacionamentosFaltantes[] = 'Relacionamento contaGerencial()';
    }
    
    if (strpos($modelContent, 'getValorPagoCalculadoAttribute') === false) {
        $relacionamentosFaltantes[] = 'Accessor getValorPagoCalculadoAttribute()';
    }
    
    if (!empty($relacionamentosFaltantes)) {
        echo "⚠️  RELACIONAMENTOS FALTANTES NO MODEL:\n";
        foreach ($relacionamentosFaltantes as $faltante) {
            echo "   • {$faltante}\n";
        }
        echo "\n";
    } else {
        echo "✅ Model Lancamento está completo\n\n";
    }
} else {
    echo "❌ Model Lancamento não encontrado: {$modelLancamento}\n\n";
}

echo "🎯 PRÓXIMOS PASSOS:\n";
echo "1. ✅ Views corrigidas automaticamente\n";
echo "2. ⚠️  Implementar relacionamentos faltantes no Model\n";
echo "3. 🧪 Testar funcionalidades corrigidas\n";
echo "4. 📝 Atualizar documentação\n\n";

echo "🏁 CORREÇÃO AUTOMÁTICA CONCLUÍDA!\n";
echo "================================================================\n";
