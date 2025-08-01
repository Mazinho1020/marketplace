<?php
echo "🔧 CONFIGURAÇÃO INICIAL DO SISTEMA MULTIEMPRESA\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    // 1. Criar tipos básicos se não existirem
    echo "📋 Criando tipos de usuário...\n";

    $tipos = [
        ['admin', 'Administrador', 'Usuário administrador do sistema'],
        ['user', 'Usuário', 'Usuário comum do sistema'],
        ['comerciante', 'Comerciante', 'Usuário comerciante'],
        ['cliente', 'Cliente', 'Usuário cliente'],
        ['entregador', 'Entregador', 'Usuário entregador']
    ];

    foreach ($tipos as $tipo) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO empresa_usuario_tipos 
            (codigo, nome, descricao, status, sync_status, sync_data) 
            VALUES (?, ?, ?, 'ativo', 'sincronizado', NOW())
        ");
        $stmt->execute($tipo);
        echo "   ✅ Tipo '{$tipo[0]}' criado/verificado\n";
    }

    // 2. Atribuir tipo admin ao usuário mazinho@gmail.com
    echo "\n👤 Configurando usuário mazinho@gmail.com...\n";

    // Buscar usuário
    $stmt = $pdo->prepare("SELECT id, nome, email FROM empresa_usuarios WHERE email = ?");
    $stmt->execute(['mazinho@gmail.com']);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "❌ Usuário mazinho@gmail.com não encontrado!\n";
        exit;
    }

    echo "   ✅ Usuário encontrado: {$usuario['nome']} (ID: {$usuario['id']})\n";

    // Buscar tipo admin
    $stmt = $pdo->prepare("SELECT id FROM empresa_usuario_tipos WHERE codigo = 'admin'");
    $stmt->execute();
    $tipoAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tipoAdmin) {
        echo "❌ Tipo 'admin' não encontrado!\n";
        exit;
    }

    // Verificar se já tem o relacionamento
    $stmt = $pdo->prepare("
        SELECT id FROM empresa_usuario_tipo_rel 
        WHERE usuario_id = ? AND tipo_id = ?
    ");
    $stmt->execute([$usuario['id'], $tipoAdmin['id']]);
    $relacionamentoExiste = $stmt->fetch();

    if ($relacionamentoExiste) {
        echo "   ✅ Usuário já tem tipo admin atribuído\n";

        // Garantir que é principal
        $stmt = $pdo->prepare("
            UPDATE empresa_usuario_tipo_rel 
            SET is_primary = 1, sync_status = 'sincronizado', sync_data = NOW()
            WHERE usuario_id = ? AND tipo_id = ?
        ");
        $stmt->execute([$usuario['id'], $tipoAdmin['id']]);
        echo "   ✅ Tipo admin marcado como principal\n";
    } else {
        // Criar relacionamento
        $stmt = $pdo->prepare("
            INSERT INTO empresa_usuario_tipo_rel 
            (usuario_id, tipo_id, is_primary, sync_status, sync_data, created_at, updated_at) 
            VALUES (?, ?, 1, 'sincronizado', NOW(), NOW(), NOW())
        ");
        $stmt->execute([$usuario['id'], $tipoAdmin['id']]);
        echo "   ✅ Tipo admin atribuído como principal\n";
    }

    // 3. Verificar configuração final
    echo "\n🔍 VERIFICAÇÃO FINAL:\n";

    $stmt = $pdo->prepare("
        SELECT t.codigo, t.nome, rel.is_primary
        FROM empresa_usuario_tipo_rel rel
        JOIN empresa_usuario_tipos t ON rel.tipo_id = t.id
        WHERE rel.usuario_id = ?
        ORDER BY rel.is_primary DESC
    ");
    $stmt->execute([$usuario['id']]);
    $tiposUsuario = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($tiposUsuario) {
        foreach ($tiposUsuario as $tipo) {
            $principal = $tipo['is_primary'] ? " (PRINCIPAL)" : "";
            echo "   ✅ {$tipo['codigo']}: {$tipo['nome']}{$principal}\n";
        }
    } else {
        echo "   ❌ Nenhum tipo atribuído\n";
    }

    echo "\n🎉 CONFIGURAÇÃO CONCLUÍDA!\n";
    echo "🔐 Agora você pode testar o login em: http://localhost:8000/login\n";
    echo "📧 Email: mazinho@gmail.com\n";
    echo "🔑 Use sua senha atual\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
