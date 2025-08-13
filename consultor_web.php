<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultor BD - Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2>🔍 Consultor de Banco de Dados - Marketplace</h2>
        <p class="text-muted">Execute consultas no banco e compartilhe os resultados comigo</p>

        <div class="row">
            <div class="col-md-8">
                <form method="POST">
                    <div class="mb-3">
                        <label for="consulta" class="form-label">Consulta SQL:</label>
                        <textarea name="consulta" id="consulta" class="form-control" rows="5" placeholder="Digite sua consulta SQL aqui..."><?= $_POST['consulta'] ?? 'SELECT * FROM produtos LIMIT 5;' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">🚀 Executar Consulta</button>
                    <button type="button" class="btn btn-secondary" onclick="limparConsulta()">🗑️ Limpar</button>
                </form>

                <?php if ($_POST['consulta'] ?? false): ?>
                    <div class="result-box">
                        <?php
                        try {
                            $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $consulta = trim($_POST['consulta']);
                            echo "📋 EXECUTANDO: $consulta\n";
                            echo str_repeat("=", 60) . "\n\n";

                            if (stripos($consulta, 'SELECT') === 0) {
                                $stmt = $pdo->query($consulta);
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($results)) {
                                    echo "ℹ️ Nenhum resultado encontrado.\n";
                                } else {
                                    echo "📊 " . count($results) . " resultado(s) encontrado(s):\n\n";

                                    // Cabeçalhos
                                    if (!empty($results)) {
                                        $headers = array_keys($results[0]);
                                        echo "| " . implode(" | ", $headers) . " |\n";
                                        echo "|" . str_repeat("-", strlen(implode(" | ", $headers)) + 4) . "|\n";

                                        // Dados (limitado a 20 linhas para não sobrecarregar)
                                        foreach (array_slice($results, 0, 20) as $row) {
                                            echo "| " . implode(" | ", array_map(function ($v) {
                                                return $v === null ? 'NULL' : (strlen($v) > 30 ? substr($v, 0, 27) . '...' : $v);
                                            }, $row)) . " |\n";
                                        }

                                        if (count($results) > 20) {
                                            echo "\n⚠️ Mostrando apenas 20 de " . count($results) . " resultados.\n";
                                        }
                                    }
                                }
                            } else {
                                // Para outras operações (INSERT, UPDATE, DELETE)
                                $stmt = $pdo->prepare($consulta);
                                $stmt->execute();
                                echo "✅ Consulta executada com sucesso!\n";
                                echo "📊 Linhas afetadas: " . $stmt->rowCount() . "\n";
                            }
                        } catch (Exception $e) {
                            echo "❌ ERRO: " . $e->getMessage() . "\n";
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h5>📋 Consultas Rápidas:</h5>
                <div class="list-group">
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SELECT * FROM produtos LIMIT 10;')">
                        📦 Ver Produtos
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SELECT COUNT(*) as total FROM produtos;')">
                        📊 Total de Produtos
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SELECT * FROM produto_movimentacoes ORDER BY created_at DESC LIMIT 10;')">
                        📈 Movimentações Recentes
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SELECT p.nome, COUNT(pi.id) as imagens FROM produtos p LEFT JOIN produto_imagens pi ON p.id = pi.produto_id GROUP BY p.id;')">
                        🖼️ Produtos com Imagens
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SELECT nome, estoque_atual, estoque_minimo FROM produtos WHERE estoque_atual <= estoque_minimo;')">
                        🚨 Estoque Baixo
                    </button>
                    <button class="list-group-item list-group-item-action" onclick="setConsulta('SHOW TABLES;')">
                        🔧 Listar Tabelas
                    </button>
                </div>

                <div class="mt-4">
                    <h6>💡 Dica:</h6>
                    <p class="small text-muted">
                        Execute a consulta e copie o resultado para compartilhar comigo.
                        Assim posso analisar os dados e sugerir melhorias!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setConsulta(sql) {
            document.getElementById('consulta').value = sql;
        }

        function limparConsulta() {
            document.getElementById('consulta').value = '';
        }
    </script>
</body>

</html>