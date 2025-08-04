<?php

/**
 * Importador simples para análise
 */

// Lê o arquivo SQL
$sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2 - Copia.sql';
$sql = file_get_contents($sqlFile);

echo "📖 Arquivo SQL carregado (" . strlen($sql) . " caracteres)\n";

// Conta quantas tabelas CREATE TABLE existem
$createTables = preg_match_all('/CREATE TABLE.*?`([^`]+)`/i', $sql, $matches);
echo "🔍 Total de CREATE TABLE encontrados: $createTables\n";

// Lista as primeiras 20 tabelas
if (isset($matches[1])) {
    echo "📋 Primeiras tabelas encontradas:\n";
    foreach (array_slice($matches[1], 0, 20) as $i => $tableName) {
        echo sprintf("%2d. %s\n", $i + 1, $tableName);
    }

    if (count($matches[1]) > 20) {
        echo "... e mais " . (count($matches[1]) - 20) . " tabelas\n";
    }

    echo "\n📊 TOTAL DE TABELAS NO ARQUIVO: " . count($matches[1]) . "\n";
}

// Verifica se as principais tabelas estão no arquivo
$tabelasEsperadas = [
    'empresas',
    'empresa_usuarios',
    'produtos',
    'vendas',
    'vendas_itens',
    'estoque',
    'movimentacoes_estoque',
    'clientes',
    'fornecedores'
];

echo "\n🔍 Verificando tabelas importantes:\n";
foreach ($tabelasEsperadas as $tabela) {
    $existe = stripos($sql, "CREATE TABLE `$tabela`") !== false ||
        stripos($sql, "CREATE TABLE IF NOT EXISTS `$tabela`") !== false;
    echo ($existe ? "✅" : "❌") . " $tabela\n";
}
