<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🔍 Estrutura da Tabela empresa_usuarios</h1>\n";

    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'empresa_usuarios'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ Tabela 'empresa_usuarios' não existe!</p>\n";
        echo "<h2>📋 Tabelas disponíveis:</h2>\n";
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "<p>- {$row[0]}</p>\n";
        }
        exit;
    }

    // Mostrar estrutura da tabela
    echo "<h2>📊 Colunas da tabela:</h2>\n";
    $stmt = $pdo->query("DESCRIBE empresa_usuarios");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<p><strong>{$row['Field']}</strong> - {$row['Type']} - {$row['Null']} - {$row['Key']}</p>\n";
    }

    // Mostrar alguns registros
    echo "<h2>👥 Registros na tabela:</h2>\n";
    $stmt = $pdo->query("SELECT * FROM empresa_usuarios LIMIT 3");
    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        echo "<h3>Usuário {$count}:</h3>\n";
        foreach ($row as $col => $val) {
            if ($col === 'password' || $col === 'senha') {
                $val = substr($val, 0, 20) . '...';
            }
            echo "<p><strong>{$col}:</strong> {$val}</p>\n";
        }
        echo "<hr>\n";
    }

    if ($count == 0) {
        echo "<p>❌ Nenhum registro encontrado na tabela.</p>\n";
    }
} catch (Exception $e) {
    echo "<h1>❌ Erro: {$e->getMessage()}</h1>\n";
}
