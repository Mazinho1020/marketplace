<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== ESTADO ATUAL DAS TABELAS DE CONFIGURAÇÃO ===\n\n";

try {
    // Inicializar Laravel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "📋 TABELA: config_environments\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $environments = \Illuminate\Support\Facades\DB::table('config_environments')
        ->select('id', 'codigo', 'nome', 'is_producao', 'ativo')
        ->orderBy('id')
        ->get();

    if ($environments->count() > 0) {
        echo "ID | Código        | Nome               | Produção | Ativo\n";
        echo "---|---------------|--------------------|---------|----- \n";
        foreach ($environments as $env) {
            $prod = $env->is_producao ? 'Sim' : 'Não';
            $ativo = $env->ativo ? 'Sim' : 'Não';
            echo sprintf(
                "%-2s | %-13s | %-18s | %-8s | %s\n",
                $env->id,
                $env->codigo,
                $env->nome,
                $prod,
                $ativo
            );
        }
    } else {
        echo "❌ Nenhum ambiente encontrado\n";
    }

    echo "\n📋 TABELA: config_db_connections\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $connections = \Illuminate\Support\Facades\DB::table('config_db_connections')
        ->select('id', 'ambiente_id', 'nome', 'host', 'banco', 'padrao', 'deleted_at')
        ->whereNull('deleted_at')  // Apenas registros não deletados
        ->orderBy('ambiente_id')
        ->orderBy('id')
        ->get();

    if ($connections->count() > 0) {
        echo "ID | Env | Nome           | Host      | Banco         | Padrão\n";
        echo "---|-----|----------------|-----------|---------------|--------\n";
        foreach ($connections as $conn) {
            $padrao = $conn->padrao ? 'Sim' : 'Não';
            echo sprintf(
                "%-2s | %-3s | %-14s | %-9s | %-13s | %s\n",
                $conn->id,
                $conn->ambiente_id,
                $conn->nome,
                $conn->host,
                $conn->banco,
                $padrao
            );
        }
    } else {
        echo "❌ Nenhuma conexão encontrada\n";
    }

    echo "\n🎯 INSTRUÇÕES PARA TESTAR:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "1. Abra seu cliente MySQL (phpMyAdmin, Workbench, etc.)\n";
    echo "2. Conecte ao banco 'meufinanceiro'\n";
    echo "3. Modifique algum registro nas tabelas acima\n";
    echo "4. Volte ao teste dinâmico e pressione ENTER\n\n";

    echo "💡 EXEMPLOS DE MUDANÇAS:\n";
    echo "• Alterar nome de um ambiente\n";
    echo "• Trocar qual conexão é padrão (padrao = 1)\n";
    echo "• Mudar host ou banco de uma conexão\n";
    echo "• Ativar/desativar um ambiente ou conexão\n\n";

    echo "🔗 COMANDOS SQL DE EXEMPLO:\n";
    echo "UPDATE config_environments SET nome = 'Novo Nome' WHERE id = 1;\n";
    echo "UPDATE config_db_connections SET banco = 'novo_banco' WHERE id = 1;\n";
    echo "UPDATE config_db_connections SET padrao = 0 WHERE id = 1;\n";
    echo "UPDATE config_db_connections SET padrao = 1 WHERE id = 2;\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
