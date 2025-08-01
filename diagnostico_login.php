<?php
echo "🔍 DIAGNÓSTICO DO PROBLEMA DE LOGIN\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    // 1. Verificar se usuário existe
    echo "👤 Verificando usuário mazinho@gmail.com...\n";
    $stmt = $pdo->prepare("SELECT id, nome, email, status FROM empresa_usuarios WHERE email = ?");
    $stmt->execute(['mazinho@gmail.com']);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "❌ Usuário não encontrado!\n";
        exit;
    }

    echo "✅ Usuário encontrado:\n";
    echo "   - ID: {$usuario['id']}\n";
    echo "   - Nome: {$usuario['nome']}\n";
    echo "   - Status: {$usuario['status']}\n";

    $usuarioId = $usuario['id'];

    // 2. Verificar estrutura da tabela empresa_usuarios
    echo "\n📋 Estrutura da tabela empresa_usuarios:\n";
    $stmt = $pdo->query("DESCRIBE empresa_usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $temTipoId = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'tipo_id') {
            $temTipoId = true;
        }
        echo "   • {$column['Field']}: {$column['Type']}\n";
    }

    if ($temTipoId) {
        echo "✅ Campo tipo_id encontrado\n";
    } else {
        echo "⚠️  Campo tipo_id NÃO encontrado (usando apenas relacionamento)\n";
    }

    // 3. Verificar tipos disponíveis
    echo "\n🏷️  Verificando tipos disponíveis...\n";
    $stmt = $pdo->query("SELECT id, codigo, nome, nivel_acesso FROM empresa_usuario_tipos ORDER BY nivel_acesso DESC");
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tipos) {
        foreach ($tipos as $tipo) {
            echo "   ✅ {$tipo['codigo']}: {$tipo['nome']} (Nível {$tipo['nivel_acesso']})\n";
        }
    } else {
        echo "   ❌ Nenhum tipo encontrado!\n";

        // Criar tipos básicos
        echo "\n🔧 Criando tipos básicos...\n";
        $tiposBasicos = [
            ['admin', 'Administrador', 'Acesso completo', 100],
            ['user', 'Usuário', 'Usuário comum', 20]
        ];

        foreach ($tiposBasicos as $tipo) {
            $stmt = $pdo->prepare("
                INSERT INTO empresa_usuario_tipos (codigo, nome, descricao, nivel_acesso, status, sync_status) 
                VALUES (?, ?, ?, ?, 'ativo', 'sincronizado')
            ");
            $stmt->execute($tipo);
            echo "   ✅ Tipo '{$tipo[0]}' criado\n";
        }

        // Recarregar tipos
        $stmt = $pdo->query("SELECT id, codigo, nome, nivel_acesso FROM empresa_usuario_tipos ORDER BY nivel_acesso DESC");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Verificar relacionamentos do usuário
    echo "\n🔗 Verificando relacionamentos do usuário...\n";
    $stmt = $pdo->prepare("
        SELECT t.id, t.codigo, t.nome, t.nivel_acesso, rel.is_primary
        FROM empresa_usuario_tipo_rel rel
        JOIN empresa_usuario_tipos t ON rel.tipo_id = t.id
        WHERE rel.usuario_id = ?
        ORDER BY rel.is_primary DESC, t.nivel_acesso DESC
    ");
    $stmt->execute([$usuarioId]);
    $tiposUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tiposUsuario) {
        echo "✅ Tipos atribuídos ao usuário:\n";
        foreach ($tiposUsuario as $tipo) {
            $principal = $tipo['is_primary'] ? " (PRINCIPAL)" : "";
            echo "   • {$tipo['codigo']}: {$tipo['nome']} - Nível {$tipo['nivel_acesso']}{$principal}\n";
        }
    } else {
        echo "❌ Nenhum tipo atribuído ao usuário!\n";

        // Atribuir tipo admin
        if ($tipos) {
            $tipoAdmin = null;
            foreach ($tipos as $tipo) {
                if ($tipo['codigo'] === 'admin') {
                    $tipoAdmin = $tipo;
                    break;
                }
            }

            if ($tipoAdmin) {
                echo "\n🔧 Atribuindo tipo admin ao usuário...\n";
                $stmt = $pdo->prepare("
                    INSERT INTO empresa_usuario_tipo_rel 
                    (usuario_id, tipo_id, is_primary, sync_status, sync_data, created_at, updated_at) 
                    VALUES (?, ?, 1, 'sincronizado', NOW(), NOW(), NOW())
                ");
                $stmt->execute([$usuarioId, $tipoAdmin['id']]);
                echo "   ✅ Tipo admin atribuído como principal\n";
            }
        }
    }

    // 5. Teste final de nível de acesso
    echo "\n🧪 Testando verificação de nível de acesso...\n";

    $stmt = $pdo->prepare("
        SELECT MAX(t.nivel_acesso) as nivel_maximo
        FROM empresa_usuario_tipo_rel r
        JOIN empresa_usuario_tipos t ON r.tipo_id = t.id
        WHERE r.usuario_id = ? AND r.deleted_at IS NULL
    ");
    $stmt->execute([$usuarioId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nivelMaximo = $result['nivel_maximo'] ?: 0;

    echo "   📊 Nível máximo do usuário: {$nivelMaximo}\n";

    if ($nivelMaximo > 0) {
        echo "   ✅ Verificação de acesso funcionando!\n";
        echo "\n🎉 SISTEMA PRONTO PARA TESTE!\n";
        echo "🔐 Teste o login em: http://localhost:8000/login\n";
    } else {
        echo "   ❌ Problema na verificação de acesso\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
}
