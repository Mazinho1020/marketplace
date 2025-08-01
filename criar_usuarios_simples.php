<?php
// Script simples para criar usuários de teste

// Conectar ao banco usando PDO diretamente
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CRIANDO USUÁRIOS DE TESTE ===\n\n";

    // Criar usuário operador
    $senha = password_hash('123456', PASSWORD_DEFAULT);

    // Verificar se operador existe
    $stmt = $pdo->prepare("SELECT id FROM empresa_usuarios WHERE email = ?");
    $stmt->execute(['operador@teste.com']);

    if ($stmt->fetch()) {
        // Atualizar
        $stmt = $pdo->prepare("UPDATE empresa_usuarios SET nome = ?, password = ?, tipo = ?, nivel_acesso = ? WHERE email = ?");
        $stmt->execute(['Operador Teste', $senha, 'operador', 40, 'operador@teste.com']);
        echo "✅ Usuário operador@teste.com atualizado!\n";
    } else {
        // Inserir
        $stmt = $pdo->prepare("INSERT INTO empresa_usuarios (nome, email, password, tipo, nivel_acesso, status, empresa_id) VALUES (?, ?, ?, ?, ?, 'ativo', 1)");
        $stmt->execute(['Operador Teste', 'operador@teste.com', $senha, 'operador', 40]);
        echo "✅ Usuário operador@teste.com criado!\n";
    }

    // Verificar se consulta existe
    $stmt = $pdo->prepare("SELECT id FROM empresa_usuarios WHERE email = ?");
    $stmt->execute(['consulta@teste.com']);

    if ($stmt->fetch()) {
        // Atualizar
        $stmt = $pdo->prepare("UPDATE empresa_usuarios SET nome = ?, password = ?, tipo = ?, nivel_acesso = ? WHERE email = ?");
        $stmt->execute(['Consulta Teste', $senha, 'consulta', 20, 'consulta@teste.com']);
        echo "✅ Usuário consulta@teste.com atualizado!\n";
    } else {
        // Inserir
        $stmt = $pdo->prepare("INSERT INTO empresa_usuarios (nome, email, password, tipo, nivel_acesso, status, empresa_id) VALUES (?, ?, ?, ?, ?, 'ativo', 1)");
        $stmt->execute(['Consulta Teste', 'consulta@teste.com', $senha, 'consulta', 20]);
        echo "✅ Usuário consulta@teste.com criado!\n";
    }

    echo "\n=== USUÁRIOS DISPONÍVEIS ===\n";
    echo "Admin: admin@teste.com / 123456 (Nível 100)\n";
    echo "Operador: operador@teste.com / 123456 (Nível 40)\n";
    echo "Consulta: consulta@teste.com / 123456 (Nível 20)\n\n";

    echo "=== TESTE ===\n";
    echo "Dashboard requer nível 60+\n";
    echo "Apenas admin pode acessar!\n\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
