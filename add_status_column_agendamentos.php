<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🔧 Adicionando coluna 'status' na tabela notificacao_agendamentos...\n";

    // Verificar se a coluna já existe
    $columns = DB::select('DESCRIBE notificacao_agendamentos');
    $hasStatus = false;

    foreach ($columns as $column) {
        if ($column->Field === 'status') {
            $hasStatus = true;
            break;
        }
    }

    if ($hasStatus) {
        echo "⚠️ A coluna 'status' já existe na tabela!\n";
        return;
    }

    echo "📋 Estrutura atual da tabela:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }

    echo "\n🚀 Adicionando coluna 'status'...\n";

    // Adicionar a coluna status
    DB::statement("
        ALTER TABLE `notificacao_agendamentos` 
        ADD COLUMN `status` ENUM('pendente', 'agendado', 'processando', 'enviado', 'falhou', 'cancelado') 
        DEFAULT 'pendente' 
        AFTER `sync_status`
    ");

    echo "✅ Coluna 'status' adicionada com sucesso!\n";

    // Atualizar registros existentes baseado no sync_status
    echo "\n🔄 Atualizando registros existentes...\n";

    $updated = DB::update("
        UPDATE `notificacao_agendamentos` 
        SET `status` = CASE 
            WHEN `sync_status` = 'pendente' THEN 'pendente'
            WHEN `sync_status` = 'sincronizado' THEN 'agendado'
            WHEN `sync_status` = 'processando' THEN 'processando'
            WHEN `sync_status` = 'enviado' THEN 'enviado'
            WHEN `sync_status` = 'erro' THEN 'falhou'
            ELSE 'pendente'
        END
        WHERE `status` = 'pendente'
    ");

    echo "📊 {$updated} registros atualizados.\n";

    // Verificar a estrutura final
    echo "\n📋 Estrutura final da tabela:\n";
    $newColumns = DB::select('DESCRIBE notificacao_agendamentos');
    foreach ($newColumns as $column) {
        $indicator = $column->Field === 'status' ? '🆕 ' : '   ';
        echo "{$indicator}{$column->Field} ({$column->Type})\n";
    }

    // Mostrar alguns registros de exemplo
    echo "\n📄 Registros de exemplo:\n";
    $samples = DB::select("SELECT id, sync_status, status, created_at FROM notificacao_agendamentos LIMIT 5");

    if (count($samples) > 0) {
        echo "ID | Sync Status | Status | Created At\n";
        echo "---|-------------|--------|------------\n";
        foreach ($samples as $sample) {
            echo "{$sample->id} | {$sample->sync_status} | {$sample->status} | {$sample->created_at}\n";
        }
    } else {
        echo "Nenhum registro encontrado na tabela.\n";
    }

    echo "\n🎉 Processo concluído com sucesso!\n";
    echo "📝 Agora você pode usar a coluna 'status' nas consultas.\n";
    echo "🔍 sync_status = usado para sincronização\n";
    echo "🎯 status = usado para controle de estado dos agendamentos\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📚 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
