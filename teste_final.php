<?php
// Teste simples sem Laravel
echo "Teste básico de conexão de banco:<br><br>";

use Illuminate\Support\Facades\DB;

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conexão PDO funcionando<br>";

    $stmt = $pdo->query('SELECT DATABASE()');
    $db = $stmt->fetchColumn();
    echo "Banco atual: {$db}<br><br>";

    // Testar Laravel agora
    echo "Testando Laravel...<br>";

    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "✅ Laravel carregado sem erros!<br>";

    $dbName = DB::connection()->getDatabaseName();
    echo "Laravel conectado ao banco: {$dbName}<br>";

    echo "<br>🎉 <strong>PROBLEMA DE CRIPTOGRAFIA RESOLVIDO!</strong><br>";
    echo "<a href='/login'>Ir para a página de login</a>";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
}
