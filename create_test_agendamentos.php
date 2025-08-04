<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "🎯 Criando registros de teste para notificacao_agendamentos...\n";

    // Verificar se já existem registros
    $count = DB::table('notificacao_agendamentos')->count();
    echo "📊 Registros existentes: {$count}\n";

    if ($count > 0) {
        echo "⚠️ Já existem registros na tabela. Pulando criação.\n";

        // Mostrar alguns registros existentes
        $existing = DB::table('notificacao_agendamentos')
            ->select('id', 'nome', 'status', 'sync_status', 'ativo')
            ->limit(5)
            ->get();

        echo "\n📋 Registros existentes:\n";
        foreach ($existing as $record) {
            echo "- ID: {$record->id} | Nome: {$record->nome} | Status: {$record->status} | Ativo: " . ($record->ativo ? 'Sim' : 'Não') . "\n";
        }
        return;
    }

    // Criar registros de teste
    $agendamentos = [
        [
            'empresa_id' => 1,
            'tipo_evento_id' => 1, // Assumindo que existe
            'nome' => 'Envio Diário de Relatórios',
            'descricao' => 'Envio automático de relatórios diários para administradores',
            'tipo_agendamento' => 'cron',
            'expressao_cron' => '0 8 * * *', // Todo dia às 8h
            'aplicacoes_alvo' => json_encode(['email', 'in_app']),
            'filtros_usuario' => json_encode(['nivel_acesso' => 'admin']),
            'maximo_destinatarios' => 100,
            'tamanho_lote' => 10,
            'tentativas_retry' => 3,
            'timeout_minutos' => 30,
            'ativo' => true,
            'status' => 'agendado',
            'sync_status' => 'synced',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'empresa_id' => 1,
            'tipo_evento_id' => 2,
            'nome' => 'Notificação de Backup',
            'descricao' => 'Notificação semanal sobre status do backup',
            'tipo_agendamento' => 'cron',
            'expressao_cron' => '0 2 * * 0', // Domingo às 2h
            'aplicacoes_alvo' => json_encode(['email']),
            'filtros_usuario' => json_encode(['departamento' => 'TI']),
            'maximo_destinatarios' => 50,
            'tamanho_lote' => 5,
            'tentativas_retry' => 2,
            'timeout_minutos' => 15,
            'ativo' => true,
            'status' => 'pendente',
            'sync_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'empresa_id' => 1,
            'tipo_evento_id' => 3,
            'nome' => 'Lembrete de Reunião',
            'descricao' => 'Lembrete enviado 1 hora antes das reuniões',
            'tipo_agendamento' => 'evento',
            'aplicacoes_alvo' => json_encode(['email', 'sms', 'push']),
            'filtros_usuario' => json_encode(['participa_reuniao' => true]),
            'maximo_destinatarios' => 200,
            'tamanho_lote' => 20,
            'tentativas_retry' => 3,
            'timeout_minutos' => 10,
            'ativo' => true,
            'status' => 'agendado',
            'sync_status' => 'synced',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'empresa_id' => 1,
            'tipo_evento_id' => 4,
            'nome' => 'Promoções Semanais',
            'descricao' => 'Envio de promoções toda sexta-feira',
            'tipo_agendamento' => 'cron',
            'expressao_cron' => '0 10 * * 5', // Sexta às 10h
            'aplicacoes_alvo' => json_encode(['email', 'push']),
            'filtros_usuario' => json_encode(['aceita_promocoes' => true]),
            'maximo_destinatarios' => 1000,
            'tamanho_lote' => 50,
            'tentativas_retry' => 2,
            'timeout_minutos' => 60,
            'ativo' => false,
            'status' => 'cancelado',
            'sync_status' => 'ignored',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    echo "\n🚀 Inserindo registros de teste...\n";

    foreach ($agendamentos as $index => $agendamento) {
        try {
            $id = DB::table('notificacao_agendamentos')->insertGetId($agendamento);
            echo "✅ Agendamento #{$id}: {$agendamento['nome']} - Status: {$agendamento['status']}\n";
        } catch (Exception $e) {
            echo "❌ Erro ao inserir agendamento {$index}: " . $e->getMessage() . "\n";
        }
    }

    // Verificar quantos registros foram criados
    $newCount = DB::table('notificacao_agendamentos')->count();
    echo "\n📊 Total de registros após inserção: {$newCount}\n";

    // Testar consultas que estavam falhando
    echo "\n🔍 Testando consultas que estavam com erro...\n";

    try {
        $pendentes = DB::table('notificacao_agendamentos')
            ->where('status', 'pendente')
            ->whereNull('deleted_at')
            ->count();
        echo "✅ Consulta pendentes: {$pendentes} registros\n";

        $agendados = DB::table('notificacao_agendamentos')
            ->where('status', 'agendado')
            ->whereNull('deleted_at')
            ->count();
        echo "✅ Consulta agendados: {$agendados} registros\n";

        $ativos = DB::table('notificacao_agendamentos')
            ->where('ativo', true)
            ->count();
        echo "✅ Consulta ativos: {$ativos} registros\n";
    } catch (Exception $e) {
        echo "❌ Erro nas consultas de teste: " . $e->getMessage() . "\n";
    }

    echo "\n🎉 Processo concluído!\n";
    echo "📝 Registros de teste criados com diferentes status:\n";
    echo "   - pendente: Agendamentos aguardando processamento\n";
    echo "   - agendado: Agendamentos configurados e ativos\n";
    echo "   - cancelado: Agendamentos desativados\n";
    echo "\n🔗 Agora você pode acessar: http://localhost:8000/admin/notificacoes\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    echo "📚 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
