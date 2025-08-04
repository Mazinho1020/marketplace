<?php

// Conecta ao banco para verificar tabelas faltantes
$host = '127.0.0.1';
$port = 3306;
$database = 'meufinanceiro';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca tabelas no banco
    $stmt = $pdo->query("SHOW TABLES");
    $tabelasBanco = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Busca tabelas no arquivo SQL
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2 - Copia.sql';
    $sql = file_get_contents($sqlFile);

    preg_match_all('/CREATE TABLE.*?`([^`]+)`/i', $sql, $matches);
    $tabelasArquivo = $matches[1];

    echo "📊 Estatísticas:\n";
    echo "   Banco atual: " . count($tabelasBanco) . " tabelas\n";
    echo "   Arquivo SQL: " . count($tabelasArquivo) . " tabelas\n";
    echo "   Diferença: " . (count($tabelasArquivo) - count($tabelasBanco)) . " tabelas\n\n";

    // Encontra tabelas faltantes
    $tabelasFaltantes = array_diff($tabelasArquivo, $tabelasBanco);

    if (empty($tabelasFaltantes)) {
        echo "🎉 TODAS AS TABELAS FORAM IMPORTADAS!\n";
    } else {
        echo "❌ Tabelas ainda faltantes (" . count($tabelasFaltantes) . "):\n";
        foreach ($tabelasFaltantes as $tabela) {
            echo "   - $tabela\n";
        }
    }

    // Verifica tabelas extras no banco
    $tabelasExtras = array_diff($tabelasBanco, $tabelasArquivo);
    if (!empty($tabelasExtras)) {
        echo "\n➕ Tabelas extras no banco (não estão no arquivo SQL):\n";
        foreach ($tabelasExtras as $tabela) {
            echo "   - $tabela\n";
        }
    }
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}
