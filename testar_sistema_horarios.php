<?php

/**
 * Teste rápido do sistema de horários de funcionamento
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Comerciantes\Models\HorarioFuncionamento;

// Bootstrap do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🧪 TESTE DO SISTEMA DE HORÁRIOS DE FUNCIONAMENTO\n";
    echo "=" . str_repeat("=", 50) . "\n\n";

    // 1. Verificar se as tabelas existem
    echo "1️⃣ Verificando estrutura do banco...\n";

    $tabelas = [
        'empresa_dias_semana',
        'empresa_horarios_funcionamento',
        'empresa_horarios_logs'
    ];

    foreach ($tabelas as $tabela) {
        $existe = DB::select("SHOW TABLES LIKE '$tabela'");
        if (count($existe) > 0) {
            $count = DB::table($tabela)->count();
            echo "   ✅ $tabela ($count registros)\n";
        } else {
            echo "   ❌ $tabela (não encontrada)\n";
        }
    }

    echo "\n2️⃣ Testando funcionalidades...\n";

    // 2. Testar consulta de status atual
    echo "   🔍 Verificando status atual (Empresa ID: 1)...\n";

    $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

    foreach ($sistemas as $sistema) {
        try {
            $status = HorarioFuncionamento::getStatusHoje(1, $sistema);
            $statusTexto = $status['aberto'] ? '🟢 ABERTO' : '🔴 FECHADO';
            echo "      $sistema: $statusTexto - {$status['mensagem']}\n";
        } catch (\Exception $e) {
            echo "      $sistema: ❌ Erro - {$e->getMessage()}\n";
        }
    }

    // 3. Testar próximo funcionamento
    echo "\n   🔮 Verificando próximo funcionamento...\n";

    foreach ($sistemas as $sistema) {
        try {
            $proximo = HorarioFuncionamento::getProximoDiaAberto(1, $sistema);
            if ($proximo) {
                echo "      $sistema: 📅 {$proximo['mensagem']}\n";
            } else {
                echo "      $sistema: ❓ Não configurado\n";
            }
        } catch (\Exception $e) {
            echo "      $sistema: ❌ Erro - {$e->getMessage()}\n";
        }
    }

    // 4. Testar relatório completo
    echo "\n   📊 Gerando relatório completo...\n";

    try {
        $relatorio = HorarioFuncionamento::getRelatorioStatus(1);
        echo "      ✅ Relatório gerado para " . count($relatorio) . " sistemas\n";

        foreach ($relatorio as $sistema => $dados) {
            $status = $dados['status_hoje']['aberto'] ? 'ABERTO' : 'FECHADO';
            echo "         • $sistema: $status\n";
        }
    } catch (\Exception $e) {
        echo "      ❌ Erro no relatório - {$e->getMessage()}\n";
    }

    // 5. Verificar alguns dados de exemplo
    echo "\n3️⃣ Verificando dados de exemplo...\n";

    $horarios = DB::table('empresa_horarios_funcionamento')
        ->join('empresa_dias_semana', 'empresa_horarios_funcionamento.dia_semana_id', '=', 'empresa_dias_semana.id')
        ->where('empresa_horarios_funcionamento.empresa_id', 1)
        ->where('empresa_horarios_funcionamento.is_excecao', 0)
        ->select('empresa_dias_semana.nome', 'empresa_horarios_funcionamento.sistema', 'empresa_horarios_funcionamento.aberto', 'empresa_horarios_funcionamento.hora_abertura', 'empresa_horarios_funcionamento.hora_fechamento')
        ->orderBy('empresa_dias_semana.numero')
        ->orderBy('empresa_horarios_funcionamento.sistema')
        ->get();

    if ($horarios->count() > 0) {
        echo "   📋 Horários padrão configurados:\n";
        foreach ($horarios->take(5) as $horario) {
            $status = $horario->aberto ? 'Aberto' : 'Fechado';
            $periodo = $horario->aberto ? "{$horario->hora_abertura} às {$horario->hora_fechamento}" : '-';
            echo "      • {$horario->nome} ({$horario->sistema}): $status $periodo\n";
        }

        if ($horarios->count() > 5) {
            echo "      ... e mais " . ($horarios->count() - 5) . " horários\n";
        }
    } else {
        echo "   ⚠️ Nenhum horário padrão encontrado\n";
    }

    // 6. Verificar exceções
    echo "\n4️⃣ Verificando exceções...\n";

    $excecoes = DB::table('empresa_horarios_funcionamento')
        ->where('empresa_id', 1)
        ->where('is_excecao', 1)
        ->orderBy('data_excecao')
        ->get();

    if ($excecoes->count() > 0) {
        echo "   🗓️ Exceções configuradas:\n";
        foreach ($excecoes as $excecao) {
            $status = $excecao->aberto ? 'Aberto' : 'Fechado';
            echo "      • {$excecao->data_excecao} ({$excecao->sistema}): $status - {$excecao->descricao_excecao}\n";
        }
    } else {
        echo "   ℹ️ Nenhuma exceção configurada\n";
    }

    echo "\n🎉 TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "🌐 Acesse: http://localhost:8000/comerciantes/horarios\n";
    echo "👤 Login: mazinho@gmail.com / 123456\n\n";
} catch (\Exception $e) {
    echo "❌ ERRO DURANTE O TESTE: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}
