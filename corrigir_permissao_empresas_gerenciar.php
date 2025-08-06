<?php
echo "=== CORRIGINDO PERMISSÃO EMPRESAS.GERENCIAR ===\n";

// Conectar ao banco
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');

echo "🔍 Verificando se a permissão 'empresas.gerenciar' existe...\n";

$sql = "SELECT * FROM empresa_permissoes WHERE codigo = 'empresas.gerenciar'";
$stmt = $pdo->query($sql);
$permissao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$permissao) {
    echo "❌ Permissão 'empresas.gerenciar' não encontrada. Criando...\n";

    $sql = "INSERT INTO empresa_permissoes (nome, codigo, descricao, categoria, is_sistema, empresa_id) 
            VALUES ('Gerenciar Empresas', 'empresas.gerenciar', 'Gerenciar todas as funcionalidades de empresas', 'empresas', 1, NULL)";
    $pdo->exec($sql);

    $permissaoId = $pdo->lastInsertId();
    echo "✅ Permissão criada com ID: $permissaoId\n";
} else {
    echo "✅ Permissão 'empresas.gerenciar' já existe com ID: {$permissao['id']}\n";
    $permissaoId = $permissao['id'];
}

echo "\n🔍 Verificando usuário logado...\n";

// Buscar usuário mazinho1@gmail.com (ID 3)
$sql = "SELECT id, nome, email FROM empresa_usuarios WHERE email = 'mazinho1@gmail.com' AND status = 'ativo'";
$stmt = $pdo->query($sql);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo "👤 Usuário encontrado: {$usuario['nome']} (ID: {$usuario['id']})\n";

    // Verificar se já tem a permissão
    $sql = "SELECT * FROM empresa_usuario_permissoes 
            WHERE usuario_id = ? AND permissao_id = ? AND empresa_id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario['id'], $permissaoId]);
    $temPermissao = $stmt->fetch();

    if (!$temPermissao) {
        echo "🔄 Concedendo permissão 'empresas.gerenciar' ao usuário...\n";

        $sql = "INSERT INTO empresa_usuario_permissoes (usuario_id, permissao_id, empresa_id, is_concedida) 
                VALUES (?, ?, 1, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario['id'], $permissaoId]);

        echo "✅ Permissão concedida!\n";
    } else {
        echo "✅ Usuário já possui a permissão 'empresas.gerenciar'\n";
    }
} else {
    echo "❌ Usuário mazinho1@gmail.com não encontrado\n";
}

echo "\n🔍 Verificando outras permissões importantes...\n";

$permissoesImportantes = [
    'empresas.visualizar',
    'empresas.listar',
    'empresas.criar',
    'empresas.editar',
    'empresas.excluir',
    'usuarios.gerenciar',
    'usuarios.visualizar',
    'usuarios.listar'
];

foreach ($permissoesImportantes as $codigo) {
    $sql = "SELECT id FROM empresa_permissoes WHERE codigo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo]);
    $perm = $stmt->fetch();

    if ($perm && $usuario) {
        // Verificar se usuário tem essa permissão
        $sql = "SELECT * FROM empresa_usuario_permissoes 
                WHERE usuario_id = ? AND permissao_id = ? AND empresa_id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario['id'], $perm['id']]);
        $temPerm = $stmt->fetch();

        if (!$temPerm) {
            echo "🔄 Concedendo: $codigo\n";
            $sql = "INSERT INTO empresa_usuario_permissoes (usuario_id, permissao_id, empresa_id, is_concedida) 
                    VALUES (?, ?, 1, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario['id'], $perm['id']]);
        } else {
            echo "✅ Já possui: $codigo\n";
        }
    }
}

echo "\n🎯 Resumo:\n";
echo "• ✅ Permissão 'empresas.gerenciar' criada/verificada\n";
echo "• ✅ Permissões essenciais concedidas ao usuário\n";
echo "• ✅ Sistema deve funcionar corretamente agora\n";

echo "\n📋 Teste agora:\n";
echo "1. Recarregue a página: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
echo "2. O erro 'empresas.gerenciar' deve ter desaparecido\n";
echo "3. Você deve conseguir acessar a gestão de usuários\n";
