<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuários de Teste</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .user-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .level {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔧 Criar Usuários de Teste</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Conectar ao banco
                $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $senha_hash = password_hash('123456', PASSWORD_DEFAULT);

                // Criar/atualizar operador
                $stmt = $pdo->prepare("INSERT INTO empresa_usuarios (nome, email, password, tipo_id, nivel_acesso, status, empresa_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 'ativo', 1, NOW(), NOW()) ON DUPLICATE KEY UPDATE nome=VALUES(nome), password=VALUES(password), tipo_id=VALUES(tipo_id), nivel_acesso=VALUES(nivel_acesso), updated_at=NOW()");
                $stmt->execute(['Operador Teste', 'operador@teste.com', $senha_hash, 4, 40]); // tipo_id 4 = operador

                // Criar/atualizar supervisor
                $stmt->execute(['Supervisor Teste', 'supervisor@teste.com', $senha_hash, 3, 60]); // tipo_id 3 = supervisor

                // Criar/atualizar consulta
                $stmt->execute(['Consulta Teste', 'consulta@teste.com', $senha_hash, 5, 20]); // tipo_id 5 = consulta

                echo '<div class="success">✅ Usuários criados/atualizados com sucesso!</div>';
            } catch (Exception $e) {
                echo '<div class="error">❌ Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>

        <form method="POST">
            <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Criar Usuários de Teste
            </button>
        </form>

        <h2>📋 Usuários de Teste Disponíveis:</h2>

        <div class="user-box">
            <strong>👑 Administrador</strong><br>
            <strong>Email:</strong> admin@teste.com<br>
            <strong>Senha:</strong> 123456<br>
            <span class="level">Nível de Acesso: 100 (ADMIN)</span><br>
            <em>Pode acessar todas as áreas</em>
        </div>

        <div class="user-box">
            <strong>🔧 Operador</strong><br>
            <strong>Email:</strong> operador@teste.com<br>
            <strong>Senha:</strong> 123456<br>
            <span class="level">Nível de Acesso: 40 (OPERADOR)</span><br>
            <em>NÃO pode acessar dashboard admin (requer 60+)</em>
        </div>

        <div class="user-box">
            <strong>�‍💼 Supervisor</strong><br>
            <strong>Email:</strong> supervisor@teste.com<br>
            <strong>Senha:</strong> 123456<br>
            <span class="level">Nível de Acesso: 60 (SUPERVISOR)</span><br>
            <em>PODE acessar dashboard admin (tem nível 60)</em>
        </div>

        <div class="user-box">
            <strong>�👁️ Consulta</strong><br>
            <strong>Email:</strong> consulta@teste.com<br>
            <strong>Senha:</strong> 123456<br>
            <span class="level">Nível de Acesso: 20 (CONSULTA)</span><br>
            <em>NÃO pode acessar dashboard admin (requer 60+)</em>
        </div>

        <h2>🧪 Como Testar:</h2>
        <ol>
            <li>Acesse o <a href="/login" target="_blank">Sistema de Login</a></li>
            <li>Faça login com admin@teste.com (deve acessar dashboard - nível 100)</li>
            <li>Faça logout e tente com supervisor@teste.com (deve acessar dashboard - nível 60)</li>
            <li>Faça logout e tente com operador@teste.com (deve ver "Acesso Negado" - nível 40)</li>
            <li>Faça logout e tente com consulta@teste.com (deve ver "Acesso Negado" - nível 20)</li>
        </ol>

        <p><strong>Dashboard Admin requer nível 60+ (Supervisor ou superior)</strong></p>
    </div>
</body>

</html>