<?php
ini_set('max_execution_time', 10);

echo "🚀 TESTE DINÂMICO FUNCIONANDO! 🚀\n";
echo "═══════════════════════════════════\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro;charset=utf8', 'root', '', [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conectado ao banco: meufinanceiro\n\n";

    // 1. Estado ANTES das mudanças
    echo "📋 ESTADO ATUAL DAS TABELAS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    echo "🏢 AMBIENTES:\n";
    $stmt = $pdo->query("SELECT id, codigo, nome, is_producao, ativo FROM config_environments ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tipo = $row['is_producao'] ? '🏭 Produção' : '💻 Desenvolvimento';
        $status = $row['ativo'] ? '✅ Ativo' : '❌ Inativo';
        echo "  • ID {$row['id']}: {$row['nome']} ({$row['codigo']}) - {$tipo} - {$status}\n";
    }

    echo "\n🔗 CONEXÕES DE BANCO:\n";
    $stmt = $pdo->query("
        SELECT c.id, c.ambiente_id, c.nome, c.host, c.banco, c.padrao, e.codigo as env_codigo, e.nome as env_nome
        FROM config_db_connections c
        JOIN config_environments e ON c.ambiente_id = e.id
        WHERE c.deleted_at IS NULL
        ORDER BY c.ambiente_id, c.id
    ");

    $conexoes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $padrao = $row['padrao'] ? '⭐ PADRÃO' : '   Normal';
        echo "  • ID {$row['id']}: {$row['nome']} @ {$row['host']}/{$row['banco']} ({$row['env_codigo']}) - {$padrao}\n";
        $conexoes[$row['id']] = $row;
    }

    echo "\n" . str_repeat("═", 60) . "\n";
    echo "💡 COMANDOS SQL PARA TESTAR MUDANÇAS DINÂMICAS:\n";
    echo str_repeat("═", 60) . "\n\n";

    $comandos = [
        "-- 1. Definir 'Banco Local' como padrão para desenvolvimento",
        "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = 2 AND nome = 'Banco Local';",
        "",
        "-- 2. Remover padrão de outras conexões do desenvolvimento",
        "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = 2 AND nome != 'Banco Local';",
        "",
        "-- 3. Definir 'Banco Produção' como padrão para produção",
        "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = 1 AND nome = 'Banco Produção';",
        "",
        "-- 4. Remover padrão de outras conexões da produção",
        "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = 1 AND nome != 'Banco Produção';",
    ];

    foreach ($comandos as $cmd) {
        echo $cmd . "\n";
    }

    echo "\n" . str_repeat("═", 60) . "\n";
    echo "🔄 APÓS EXECUTAR OS COMANDOS SQL:\n";
    echo "Execute novamente: php teste_dinamico_final.php\n";
    echo "Para ver as mudanças refletidas automaticamente!\n";
    echo str_repeat("═", 60) . "\n";

    // Mostrar ambiente atual detectado
    echo "\n🎯 DETECÇÃO DE AMBIENTE:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $hostname = gethostname();
    $cwd = getcwd();
    $isLocal = (str_contains(strtolower($hostname), 'desktop') ||
        str_contains(strtolower($hostname), 'laptop') ||
        str_contains(strtolower($hostname), 'servidor') ||
        str_contains($cwd, 'xampp') ||
        str_contains($cwd, 'laragon'));

    echo "• Hostname: {$hostname}\n";
    echo "• Working Dir: {$cwd}\n";
    echo "• É ambiente local: " . ($isLocal ? 'SIM' : 'NÃO') . "\n";
    $ambienteAtual = $isLocal ? 'desenvolvimento' : 'producao';
    echo "• Ambiente mapeado: {$ambienteAtual}\n";
    $ambienteId = $isLocal ? 2 : 1;

    if ($isLocal) {
        echo "\n🎯 Como estamos em DESENVOLVIMENTO, o sistema deveria usar:\n";
        echo "   ⭐ A conexão marcada como PADRÃO do ambiente ID 2 (desenvolvimento)\n";
    } else {
        echo "\n🎯 Como estamos em PRODUÇÃO, o sistema deveria usar:\n";
        echo "   ⭐ A conexão marcada como PADRÃO do ambiente ID 1 (produção)\n";
    }

    // NOVA SEÇÃO: Verificar e corrigir automaticamente
    echo "\n" . str_repeat("═", 60) . "\n";
    echo "🔧 VERIFICAÇÃO E CORREÇÃO AUTOMÁTICA:\n";
    echo str_repeat("═", 60) . "\n\n";

    // Verificar se tem mais de uma conexão padrão no ambiente atual
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM config_db_connections 
        WHERE ambiente_id = ? AND padrao = 1
    ");
    $stmt->execute([$ambienteId]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $conexaoEsperadaId = $isLocal ? 1 : 2; // ID 1 para local, ID 2 para produção

    if ($count > 1) {
        echo "⚠️ Detectadas {$count} conexões padrão para o ambiente atual. Corrigindo...\n";

        // Começa uma transação
        $pdo->beginTransaction();

        try {
            // Primeiro, remove o padrão de todas as conexões do ambiente
            $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = ?");
            $stmt->execute([$ambienteId]);

            // Depois, define a conexão correta como padrão
            $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 1 WHERE id = ?");
            $stmt->execute([$conexaoEsperadaId]);

            $pdo->commit();
            echo "✅ Correção aplicada com sucesso!\n";

            // Mostrar estado atual após a correção
            $stmt = $pdo->prepare("
                SELECT c.id, c.nome, c.host, c.banco, c.padrao 
                FROM config_db_connections c
                WHERE c.ambiente_id = ?
                ORDER BY c.id
            ");
            $stmt->execute([$ambienteId]);

            echo "\nEstado atual das conexões para o ambiente atual:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $padrao = $row['padrao'] ? '⭐ PADRÃO' : '   Normal';
                echo "  • ID {$row['id']}: {$row['nome']} @ {$row['host']}/{$row['banco']} - {$padrao}\n";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "❌ Erro ao aplicar correção: " . $e->getMessage() . "\n";
        }
    } else {
        // Verificar se a conexão padrão é a esperada
        $stmt = $pdo->prepare("
            SELECT id, nome, host, banco 
            FROM config_db_connections 
            WHERE ambiente_id = ? AND padrao = 1
            LIMIT 1
        ");
        $stmt->execute([$ambienteId]);
        $conexaoPadrao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conexaoPadrao) {
            echo "⚠️ Nenhuma conexão padrão definida para o ambiente atual. Corrigindo...\n";

            // Definir a conexão correta como padrão
            $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 1 WHERE id = ?");
            $stmt->execute([$conexaoEsperadaId]);

            echo "✅ Conexão ID {$conexaoEsperadaId} definida como padrão!\n";
        } else if ($conexaoPadrao['id'] != $conexaoEsperadaId) {
            echo "⚠️ Conexão padrão atual (ID {$conexaoPadrao['id']}: {$conexaoPadrao['nome']}) não é a esperada. Corrigindo...\n";

            // Começa uma transação
            $pdo->beginTransaction();

            try {
                // Primeiro, remove o padrão de todas as conexões do ambiente
                $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = ?");
                $stmt->execute([$ambienteId]);

                // Depois, define a conexão correta como padrão
                $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 1 WHERE id = ?");
                $stmt->execute([$conexaoEsperadaId]);

                $pdo->commit();
                echo "✅ Correção aplicada com sucesso!\n";
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "❌ Erro ao aplicar correção: " . $e->getMessage() . "\n";
            }
        } else {
            echo "✅ Configuração correta! Conexão ID {$conexaoPadrao['id']}: {$conexaoPadrao['nome']} está definida como padrão.\n";
        }
    }

    // Verificar também o outro ambiente
    $outroAmbienteId = $isLocal ? 1 : 2;
    $outraConexaoEsperadaId = $isLocal ? 2 : 1;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM config_db_connections 
        WHERE ambiente_id = ? AND padrao = 1
    ");
    $stmt->execute([$outroAmbienteId]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    echo "\nVerificando também o outro ambiente (ID {$outroAmbienteId}):\n";

    if ($count != 1) {
        echo "⚠️ Encontradas {$count} conexões padrão para o outro ambiente. Corrigindo...\n";

        // Começa uma transação
        $pdo->beginTransaction();

        try {
            // Primeiro, remove o padrão de todas as conexões do outro ambiente
            $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = ?");
            $stmt->execute([$outroAmbienteId]);

            // Depois, define a conexão correta como padrão
            $stmt = $pdo->prepare("UPDATE config_db_connections SET padrao = 1 WHERE id = ?");
            $stmt->execute([$outraConexaoEsperadaId]);

            $pdo->commit();
            echo "✅ Correção aplicada com sucesso!\n";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "❌ Erro ao aplicar correção: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✅ Configuração correta para o outro ambiente.\n";
    }

    echo "\n⚠️ IMPORTANTE: Se as correções foram aplicadas, você deve reiniciar o aplicativo\n";
    echo "   e limpar qualquer cache para que as mudanças tenham efeito!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("═", 60) . "\n";
echo "🔄 EXECUÇÃO CONCLUÍDA: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("═", 60) . "\n";
