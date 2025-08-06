<?php
echo "=== TESTE FUNÇÃO ADMINISTRADOR ===\n";

// Conectar ao banco usando config Laravel
$host = '127.0.0.1';
$dbname = 'meufinanceiro';
$username = 'root';
$password = 'root';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

echo "🔍 Verificando permissões disponíveis no banco...\n";

$sql = "SELECT nome, descricao FROM empresa_permissoes ORDER BY nome";
$stmt = $pdo->query($sql);
$permissoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "📊 Total de permissões no banco: " . count($permissoes) . "\n";

echo "\n📋 Lista de permissões:\n";
foreach ($permissoes as $perm) {
    echo "  • {$perm['nome']} - {$perm['descricao']}\n";
}

echo "\n🔍 Verificando se existe usuário com role 'administrador'...\n";

$sql = "SELECT u.id, u.nome, u.email, u.perfil, u.permissoes 
        FROM empresa_usuarios u 
        WHERE u.perfil = 'administrador' 
        LIMIT 3";
$stmt = $pdo->query($sql);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($admins)) {
    echo "❌ Nenhum usuário com perfil 'administrador' encontrado\n";

    echo "\n🔍 Verificando perfis existentes...\n";
    $sql = "SELECT DISTINCT perfil, COUNT(*) as total FROM empresa_usuarios GROUP BY perfil";
    $stmt = $pdo->query($sql);
    $perfis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($perfis as $perfil) {
        echo "  • {$perfil['perfil']}: {$perfil['total']} usuários\n";
    }
} else {
    echo "✅ Encontrados " . count($admins) . " administradores:\n";
    foreach ($admins as $admin) {
        echo "\n👤 Admin: {$admin['nome']} ({$admin['email']})\n";
        echo "   Perfil: {$admin['perfil']}\n";

        if ($admin['permissoes']) {
            $permissoesAdmin = json_decode($admin['permissoes'], true);
            echo "   Permissões JSON: " . count($permissoesAdmin) . " itens\n";

            // Verificar se tem todas as permissões
            $permissoesNomes = array_column($permissoes, 'nome');
            $temTodas = true;

            foreach ($permissoesNomes as $permNome) {
                if (!in_array($permNome, $permissoesAdmin)) {
                    $temTodas = false;
                    break;
                }
            }

            if ($temTodas) {
                echo "   ✅ Tem TODAS as permissões\n";
            } else {
                echo "   ❌ NÃO tem todas as permissões\n";
                echo "   📊 Tem: " . count($permissoesAdmin) . " / " . count($permissoesNomes) . "\n";
            }
        } else {
            echo "   ❌ Campo permissões vazio\n";
        }
    }
}

echo "\n🎯 Teste do sistema de permissões:\n";
echo "1. Abra: http://localhost:8000/comerciante/empresas/usuarios\n";
echo "2. Clique em 'Adicionar Usuário'\n";
echo "3. Selecione perfil 'Administrador'\n";
echo "4. Verifique se todas as 85 permissões são selecionadas automaticamente\n";
echo "5. Crie o usuário e verifique se as permissões foram salvas\n";

echo "\n🔧 Se o JavaScript não funcionar, verificar:\n";
echo "• Console do navegador para erros\n";
echo "• Se o campo select tem name='perfil'\n";
echo "• Se os checkboxes têm name='permissoes[]'\n";
