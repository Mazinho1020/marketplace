<!DOCTYPE html>
<html>

<head>
    <title>Teste de Login Simples</title>
</head>

<body>
    <h1>Teste de Login Debug</h1>

    <?php
    if ($_POST) {
        echo "<h2>Processando Login...</h2>";

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        echo "Email: {$email}<br>";
        echo "Password: " . (strlen($password) > 0 ? str_repeat('*', strlen($password)) : 'vazio') . "<br>";

        try {
            // Conexão direta
            $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "✅ Conexão estabelecida<br>";

            // Buscar usuário
            $stmt = $pdo->prepare('SELECT * FROM empresa_usuarios WHERE email = ? AND status = "ativo"');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo "✅ Usuário encontrado: {$user['email']}<br>";
                echo "Empresa ID: {$user['empresa_id']}<br>";
                echo "Status: {$user['status']}<br>";
                echo "Hash da senha: " . substr($user['password'], 0, 20) . "...<br>";

                // Verificar senha
                if (password_verify($password, $user['password'])) {
                    echo "✅ Senha correta!<br>";
                    echo "🎉 <strong>LOGIN SERIA BEM-SUCEDIDO!</strong><br>";
                } else {
                    echo "❌ Senha incorreta<br>";
                }
            } else {
                echo "❌ Usuário não encontrado ou inativo<br>";

                // Verificar se usuário existe mas está inativo
                $stmt = $pdo->prepare('SELECT * FROM empresa_usuarios WHERE email = ?');
                $stmt->execute([$email]);
                $userAny = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userAny) {
                    echo "ℹ️ Usuário existe mas status: {$userAny['status']}<br>";
                } else {
                    echo "ℹ️ Email não existe no banco<br>";
                }
            }
        } catch (Exception $e) {
            echo "❌ Erro no teste: " . $e->getMessage() . "<br>";
        }
    }
    ?>

    <hr>
    <h2>Formulário de Teste</h2>
    <form method="POST">
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="admin@teste.com" style="width: 300px;">
        </p>
        <p>
            <label>Senha:</label><br>
            <input type="password" name="password" style="width: 300px;">
        </p>
        <p>
            <button type="submit">Testar Login</button>
        </p>
    </form>

    <hr>
    <h3>Usuários Disponíveis</h3>
    <?php
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro", 'root', '');
        $stmt = $pdo->query('SELECT email, status, empresa_id FROM empresa_usuarios LIMIT 10');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Email</th><th>Status</th><th>Empresa ID</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "<td>{$user['empresa_id']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "Erro ao listar usuários: " . $e->getMessage();
    }
    ?>

</body>

</html>