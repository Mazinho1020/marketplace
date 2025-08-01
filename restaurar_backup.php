<?php
echo "=== RESTAURANDO BANCO DE DADOS ===\n\n";

$backupFile = 'C:\\Users\\leoma\\Downloads\\meufinanceiro completa.sql';

if (!file_exists($backupFile)) {
    echo "❌ Arquivo de backup não encontrado: $backupFile\n";
    echo "Por favor, coloque o arquivo no diretório atual ou ajuste o caminho.\n";
    exit;
}

echo "📂 Arquivo de backup encontrado: " . basename($backupFile) . "\n";
echo "📊 Tamanho: " . number_format(filesize($backupFile) / 1024 / 1024, 2) . " MB\n\n";

try {
    // Configurações do banco
    $host = '127.0.0.1';
    $database = 'meufinanceiro';
    $username = 'root';
    $password = '';

    echo "🔌 Conectando ao banco...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    echo "✅ Conexão estabelecida!\n\n";

    // Desabilitar verificações temporariamente
    echo "⚙️ Configurando para importação...\n";
    $pdo->exec("SET foreign_key_checks = 0");
    $pdo->exec("SET sql_mode = ''");
    $pdo->exec("SET autocommit = 0");

    // Ler arquivo SQL
    echo "📖 Lendo arquivo SQL...\n";
    $sql = file_get_contents($backupFile);

    if ($sql === false) {
        throw new Exception("Não foi possível ler o arquivo de backup");
    }

    echo "✅ Arquivo lido com sucesso!\n";
    echo "📏 Tamanho do SQL: " . number_format(strlen($sql) / 1024, 2) . " KB\n\n";

    // Dividir em comandos individuais
    echo "🔨 Executando comandos SQL...\n";

    // Remover comentários e dividir por ;
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    $commands = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($cmd) {
            return !empty($cmd);
        }
    );

    echo "📊 Total de comandos: " . count($commands) . "\n";

    $executed = 0;
    $errors = 0;

    foreach ($commands as $i => $command) {
        if (empty(trim($command))) continue;

        try {
            $pdo->exec($command);
            $executed++;

            if ($executed % 100 == 0) {
                echo "⏳ Executados: $executed comandos...\n";
            }
        } catch (PDOException $e) {
            $errors++;

            // Mostrar apenas alguns erros para não poluir
            if ($errors <= 5) {
                echo "⚠️ Erro no comando " . ($i + 1) . ": " . $e->getMessage() . "\n";
                echo "   SQL: " . substr($command, 0, 100) . "...\n";
            }
        }
    }

    // Finalizar transação
    $pdo->exec("COMMIT");
    $pdo->exec("SET foreign_key_checks = 1");

    echo "\n=== RESULTADO ===\n";
    echo "✅ Comandos executados: $executed\n";
    echo "⚠️ Erros encontrados: $errors\n\n";

    // Verificar tabelas importantes
    echo "=== VERIFICANDO RESULTADO ===\n";

    $important_tables = [
        'empresa_usuarios' => 'Usuários',
        'empresas' => 'Empresas',
        'empresa_usuario_tipos' => 'Tipos de usuário',
        'fidelidade_carteiras' => 'Carteiras fidelidade'
    ];

    foreach ($important_tables as $table => $desc) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
            $count = $stmt->fetch()['total'];
            echo "✅ $desc ($table): $count registros\n";
        } catch (Exception $e) {
            echo "❌ $desc ($table): NÃO EXISTE\n";
        }
    }

    echo "\n🎉 RESTAURAÇÃO CONCLUÍDA!\n";
    echo "👉 Agora teste o login novamente\n";
} catch (Exception $e) {
    echo "❌ ERRO DURANTE RESTAURAÇÃO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
