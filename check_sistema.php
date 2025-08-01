<?php
echo "🔍 VERIFICAÇÃO COMPLETA DO SISTEMA MULTIEMPRESA\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    // 1. Verificar todas as tabelas necessárias
    $tabelas = [
        'empresa_usuarios',
        'empresa_usuario_tipos',
        'empresa_usuario_tipo_rel'
    ];

    echo "📋 VERIFICANDO TABELAS:\n";
    foreach ($tabelas as $tabela) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
        $exists = $stmt->fetch();
        echo ($exists ? "✅" : "❌") . " $tabela\n";
    }

    // 2. Verificar dados nas tabelas
    echo "\n📊 DADOS NAS TABELAS:\n";

    // Usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empresa_usuarios");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "👥 empresa_usuarios: {$count['total']} registros\n";

    // Tipos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empresa_usuario_tipos");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "🏷️  empresa_usuario_tipos: {$count['total']} registros\n";

    // Relacionamentos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empresa_usuario_tipo_rel");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "🔗 empresa_usuario_tipo_rel: {$count['total']} registros\n";

    // 3. Verificar usuário específico
    echo "\n🔍 VERIFICANDO USUÁRIO 'mazinho@gmail.com':\n";
    $stmt = $pdo->prepare("
        SELECT u.id, u.nome, u.email, u.status
        FROM empresa_usuarios u 
        WHERE u.email = ?
    ");
    $stmt->execute(['mazinho@gmail.com']);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo "✅ Usuário encontrado:\n";
        echo "   - ID: {$usuario['id']}\n";
        echo "   - Nome: {$usuario['nome']}\n";
        echo "   - Status: {$usuario['status']}\n";

        // Verificar tipos do usuário
        $stmt = $pdo->prepare("
            SELECT t.codigo, t.nome, rel.is_primary
            FROM empresa_usuario_tipo_rel rel
            JOIN empresa_usuario_tipos t ON rel.tipo_id = t.id
            WHERE rel.usuario_id = ?
        ");
        $stmt->execute([$usuario['id']]);
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($tipos) {
            echo "   - Tipos atribuídos:\n";
            foreach ($tipos as $tipo) {
                $primary = $tipo['is_primary'] ? " (PRINCIPAL)" : "";
                echo "     • {$tipo['codigo']}: {$tipo['nome']}{$primary}\n";
            }
        } else {
            echo "   ⚠️  Nenhum tipo atribuído\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }

    // 4. Verificar tipos disponíveis
    echo "\n🏷️  TIPOS DISPONÍVEIS:\n";
    $stmt = $pdo->query("SELECT codigo, nome, status FROM empresa_usuario_tipos ORDER BY codigo");
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tipos) {
        foreach ($tipos as $tipo) {
            echo "   • {$tipo['codigo']}: {$tipo['nome']} ({$tipo['status']})\n";
        }
    } else {
        echo "   ❌ Nenhum tipo cadastrado\n";
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎯 DIAGNÓSTICO:\n";

    if (count($tipos) == 0) {
        echo "❌ PROBLEMA: Nenhum tipo de usuário cadastrado\n";
        echo "💡 SOLUÇÃO: Execute o script de criação de tipos\n";
    } elseif ($usuario && count($tipos) > 0) {
        echo "✅ Sistema configurado corretamente!\n";
        echo "🔐 Pronto para testar o login\n";
    } else {
        echo "⚠️  Sistema parcialmente configurado\n";
        echo "💡 Verifique os dados e relacionamentos\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
