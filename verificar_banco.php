<?php
// Verificação rápida do banco de dados
require_once 'vendor/autoload.php';

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CONEXÃO COM BANCO DE DADOS ===\n";
    echo "✅ Conexão estabelecida com sucesso!\n\n";

    echo "=== VERIFICANDO TABELAS NECESSÁRIAS ===\n";

    $tabelas_necessarias = [
        'empresa_usuarios',
        'empresas',
        'empresa_usuario_tipos',
        'empresa_usuario_tipo_rels'
    ];

    foreach ($tabelas_necessarias as $tabela) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tabela]);

        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela '$tabela' existe\n";

            // Verificar se tem registros
            $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM `$tabela`");
            $count_stmt->execute();
            $count = $count_stmt->fetch()['total'];
            echo "   📊 Registros: $count\n";
        } else {
            echo "❌ Tabela '$tabela' NÃO existe\n";
        }
    }

    echo "\n=== VERIFICANDO USUÁRIO DE TESTE ===\n";

    // Verificar se existe o usuário admin@teste.com
    $stmt = $pdo->prepare("SELECT id, nome, email, status FROM empresa_usuarios WHERE email = ?");
    $stmt->execute(['admin@teste.com']);

    if ($user = $stmt->fetch()) {
        echo "✅ Usuário admin@teste.com encontrado:\n";
        echo "   ID: {$user['id']}\n";
        echo "   Nome: {$user['nome']}\n";
        echo "   Status: {$user['status']}\n";
    } else {
        echo "❌ Usuário admin@teste.com NÃO encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
