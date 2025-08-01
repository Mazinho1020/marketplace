<!DOCTYPE html>
<html>

<head>
    <title>Verificação do Banco</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .warning {
            color: orange;
        }

        .info {
            color: blue;
        }
    </style>
</head>

<body>
    <h1>Verificação do Sistema</h1>

    <?php
    try {
        echo "<h2>1. Testando Conexão com Banco</h2>";

        $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p class='success'>✅ Conexão com banco estabelecida!</p>";

        echo "<h2>2. Verificando Tabelas</h2>";
        $tabelas = [
            'empresa_usuarios' => 'Usuários do sistema',
            'empresas' => 'Empresas',
            'empresa_usuario_tipos' => 'Tipos de usuário',
            'empresa_usuario_tipo_rels' => 'Relacionamento usuário-tipo'
        ];

        $tabelas_existem = true;

        foreach ($tabelas as $tabela => $descricao) {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$tabela]);

            if ($stmt->rowCount() > 0) {
                $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM `$tabela`");
                $count_stmt->execute();
                $count = $count_stmt->fetch()['total'];
                echo "<p class='success'>✅ $descricao ($tabela): $count registros</p>";
            } else {
                echo "<p class='error'>❌ $descricao ($tabela): NÃO EXISTE</p>";
                $tabelas_existem = false;
            }
        }

        if (!$tabelas_existem) {
            echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin: 20px 0;'>";
            echo "<h3>⚠️ PROBLEMA IDENTIFICADO</h3>";
            echo "<p>As tabelas necessárias para autenticação não existem no banco de dados.</p>";
            echo "<p><strong>Soluções:</strong></p>";
            echo "<ul>";
            echo "<li>1. Restaurar backup completo via phpMyAdmin</li>";
            echo "<li>2. Executar migrações do Laravel</li>";
            echo "</ul>";
            echo "</div>";
        }

        echo "<h2>3. Verificando Usuário de Teste</h2>";

        if ($tabelas_existem) {
            $stmt = $pdo->prepare("SELECT id, nome, email, status FROM empresa_usuarios WHERE email = ? LIMIT 1");
            $stmt->execute(['admin@teste.com']);

            if ($user = $stmt->fetch()) {
                echo "<p class='success'>✅ Usuário admin@teste.com encontrado!</p>";
                echo "<ul>";
                echo "<li>ID: {$user['id']}</li>";
                echo "<li>Nome: {$user['nome']}</li>";
                echo "<li>Status: {$user['status']}</li>";
                echo "</ul>";
            } else {
                echo "<p class='warning'>⚠️ Usuário admin@teste.com não encontrado</p>";
            }
        }

        echo "<h2>4. Testando Configuração Laravel</h2>";

        // Verificar se consegue conectar via Laravel
        try {
            require_once 'vendor/autoload.php';
            $app = require_once 'bootstrap/app.php';
            $app->make('kernel');

            echo "<p class='success'>✅ Laravel carregado com sucesso</p>";

            // Testar configuração de banco
            $config = include 'config/database.php';
            $dbConfig = $config['connections']['mysql'];

            echo "<p class='info'>📋 Configuração do banco:</p>";
            echo "<ul>";
            echo "<li>Host: {$dbConfig['host']}</li>";
            echo "<li>Database: {$dbConfig['database']}</li>";
            echo "<li>Username: {$dbConfig['username']}</li>";
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erro no Laravel: " . $e->getMessage() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ ERRO GERAL: " . $e->getMessage() . "</p>";
    }
    ?>

    <hr>
    <h2>Próximos Passos</h2>
    <div style="background: #e3f2fd; padding: 15px; border: 1px solid #2196f3;">
        <p><strong>Para corrigir o erro de login:</strong></p>
        <ol>
            <li>Abrir <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
            <li>Selecionar banco "meufinanceiro"</li>
            <li>Clicar em "Importar"</li>
            <li>Selecionar arquivo "meufinanceiro completa.sql"</li>
            <li>Executar importação</li>
        </ol>
    </div>
</body>

</html>