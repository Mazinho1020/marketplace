<?php

/**
 * Importador SEGURO - NÃO deleta dados existentes
 * Só cria tabelas que não existem
 */

// Configuração da conexão
$host = '127.0.0.1';
$port = 3306;
$database = 'meufinanceiro';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL Docker\n";

    // 1. BUSCA TABELAS EXISTENTES NO BANCO
    $stmt = $pdo->query("SHOW TABLES");
    $tabelasExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Tabelas existentes: " . count($tabelasExistentes) . "\n";

    // 2. LÊ O ARQUIVO SQL
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2 - Copia.sql';
    $sql = file_get_contents($sqlFile);
    echo "📖 Arquivo SQL carregado (" . strlen($sql) . " caracteres)\n";

    // 3. EXTRAI APENAS CREATE TABLE (SEM DELETE/INSERT)
    preg_match_all('/CREATE TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`([^`]+)`[^;]*\([^;]*\)[^;]*;/is', $sql, $matches);
    $createCommands = $matches[0];
    $tableNames = $matches[1];

    echo "🔍 Comandos CREATE TABLE encontrados: " . count($createCommands) . "\n";

    // 4. FILTRA APENAS TABELAS QUE NÃO EXISTEM
    $tabelasParaCriar = [];
    $comandosParaCriar = [];

    foreach ($tableNames as $index => $tableName) {
        if (!in_array($tableName, $tabelasExistentes)) {
            $tabelasParaCriar[] = $tableName;
            // Força IF NOT EXISTS para segurança extra
            $comando = $createCommands[$index];
            if (stripos($comando, 'IF NOT EXISTS') === false) {
                $comando = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $comando);
            }
            $comandosParaCriar[] = $comando;
        }
    }

    echo "🆕 Tabelas novas para criar: " . count($tabelasParaCriar) . "\n";

    if (empty($tabelasParaCriar)) {
        echo "🎉 TODAS AS TABELAS JÁ EXISTEM! Nada para fazer.\n";
        exit;
    }

    // 5. MOSTRA QUAIS TABELAS SERÃO CRIADAS
    echo "\n📋 Tabelas que serão criadas:\n";
    foreach ($tabelasParaCriar as $i => $tabela) {
        echo sprintf("%2d. %s\n", $i + 1, $tabela);
    }

    echo "\n⚠️  ATENÇÃO: Este script SÓ CRIA tabelas novas, NÃO deleta dados!\n";
    echo "Pressione ENTER para continuar ou Ctrl+C para cancelar...";
    // readline(""); // Descomente para pedir confirmação

    // 6. EXECUTA APENAS OS CREATE TABLE SEGUROS
    $success = 0;
    $errors = 0;

    foreach ($comandosParaCriar as $index => $command) {
        try {
            $pdo->exec($command);
            $success++;
            echo "✅ Criada: " . $tabelasParaCriar[$index] . "\n";
        } catch (PDOException $e) {
            $errors++;
            echo "❌ Erro ao criar " . $tabelasParaCriar[$index] . ": " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 FINALIZADO SEGURO!\n";
    echo "✅ Tabelas criadas: $success\n";
    echo "❌ Erros: $errors\n";

    // 7. VERIFICA TOTAL FINAL
    $stmt = $pdo->query("SHOW TABLES");
    $tabelasFinais = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Total de tabelas agora: " . count($tabelasFinais) . "\n";
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}
