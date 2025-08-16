<?php
/**
 * SCRIPT PARA CORRIGIR CONTROLLER CONTAS A RECEBER
 * 
 * Corrige referências incorretas ao modelo e campos
 */

$controllerPath = 'app/Http/Controllers/Financial/ContasReceberController.php';

if (!file_exists($controllerPath)) {
    echo "❌ Arquivo não encontrado: {$controllerPath}\n";
    exit(1);
}

echo "🔧 CORRIGINDO CONTROLLER CONTAS A RECEBER\n";
echo "================================================================\n\n";

// Ler conteúdo do arquivo
$content = file_get_contents($controllerPath);

// Criar backup
$backupPath = $controllerPath . '.backup-' . date('Y-m-d-H-i-s');
file_put_contents($backupPath, $content);
echo "📁 Backup criado: {$backupPath}\n";

$changes = [];

// 1. Corrigir referências ao enum
$oldEnum = 'NaturezaFinanceiraEnum::RECEBER';
$newEnum = 'Lancamento::NATUREZA_ENTRADA';
if (strpos($content, $oldEnum) !== false) {
    $content = str_replace($oldEnum, $newEnum, $content);
    $changes[] = "Enum natureza: {$oldEnum} → {$newEnum}";
}

// 2. Corrigir todos os campos 'valor' para 'valor_liquido'
$patterns = [
    '/->sum\([\'"]valor[\'"]\)/' => "->sum('valor_liquido')",
    '/\bvalor\b(?=[\s]*[,\)])/' => 'valor_liquido',
];

foreach ($patterns as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $content = $newContent;
        $changes[] = "Campo valor → valor_liquido";
    }
}

// 3. Adicionar imports corretos no topo
$imports = [
    "use App\Models\Financeiro\Lancamento;",
];

$importSection = '';
foreach ($imports as $import) {
    if (strpos($content, $import) === false) {
        $importSection .= $import . "\n";
    }
}

// Inserir imports após os existentes
if ($importSection) {
    $content = preg_replace(
        '/^(use [^;]+;)$/m',
        "$1\n" . rtrim($importSection),
        $content,
        1
    );
    $changes[] = "Adicionados imports necessários";
}

// 4. Corrigir métodos que podem estar usando campos incorretos
$methodCorrections = [
    // Corrigir referências a campos antigos
    '/\$query->sum\([\'"]valor[\'"]\)/' => "\$query->sum('valor_liquido')",
    '/->whereColumn\([\'"]valor[\'"]/' => "->whereColumn('valor_liquido'",
];

foreach ($methodCorrections as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $content = $newContent;
        $changes[] = "Método corrigido: " . trim($pattern, '/');
    }
}

// Salvar arquivo corrigido
file_put_contents($controllerPath, $content);

echo "\n✅ CORREÇÕES APLICADAS:\n";
foreach ($changes as $i => $change) {
    echo "   " . ($i + 1) . ". {$change}\n";
}

// Verificar sintaxe
echo "\n🔍 VERIFICANDO SINTAXE...\n";
$output = [];
$returnCode = 0;
exec("php -l \"{$controllerPath}\" 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Sintaxe OK\n";
} else {
    echo "❌ ERRO DE SINTAXE:\n";
    foreach ($output as $line) {
        echo "   {$line}\n";
    }
}

echo "\n================================================================\n";
echo "🏁 CORREÇÃO DO CONTROLLER CONCLUÍDA!\n";
echo "================================================================\n";
