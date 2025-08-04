<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== VERIFICAÇÃO REAL DAS TABELAS ===\n";

    // Flush dos caches
    $pdo->query('FLUSH TABLES');

    // Contar tabelas no banco
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = "meufinanceiro"');
    $total = $stmt->fetch()['total'];

    echo "Total de tabelas no banco (após flush): $total\n\n";

    // Verificar as tabelas que supostamente foram criadas
    $tabelas_teste = [
        'empresa_cache',
        'empresa_certificados',
        'empresa_cnaes',
        'empresa_config_seguranca',
        'empresa_logs_permissoes',
        'empresa_papeis',
        'empresa_papel_permissoes',
        'empresa_permissoes',
        'empresa_socios',
        'empresa_usuarios_activity_log',
        'empresa_usuarios_remember_tokens',
        'empresa_usuarios_security_settings',
        'empresa_usuario_empresas',
        'empresa_usuario_papeis',
        'empresa_usuario_permissoes',
        'login',
        'produto_importar'
    ];

    $existem = 0;
    $nao_existem = 0;

    echo "Verificando tabelas uma por uma:\n";
    foreach ($tabelas_teste as $tabela) {
        $stmt = $pdo->prepare('SELECT COUNT(*) as existe FROM information_schema.tables WHERE table_schema = ? AND table_name = ?');
        $stmt->execute(['meufinanceiro', $tabela]);
        $existe = $stmt->fetch()['existe'] > 0;

        if ($existe) {
            echo "✅ $tabela - EXISTE\n";
            $existem++;
        } else {
            echo "❌ $tabela - NÃO EXISTE\n";
            $nao_existem++;
        }
    }

    echo "\n=== RESUMO ===\n";
    echo "Tabelas que existem: $existem\n";
    echo "Tabelas que não existem: $nao_existem\n";
    echo "Total verificado: " . count($tabelas_teste) . "\n";

    // Listar todas as tabelas do banco
    echo "\n=== TODAS AS TABELAS DO BANCO ===\n";
    $stmt = $pdo->query('SELECT table_name FROM information_schema.tables WHERE table_schema = "meufinanceiro" ORDER BY table_name');
    $todas_tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($todas_tabelas as $i => $tabela) {
        echo ($i + 1) . ". $tabela\n";
    }

    echo "\nTotal final: " . count($todas_tabelas) . " tabelas\n";
} catch (PDOException $e) {
    echo 'Erro: ' . $e->getMessage();
}
