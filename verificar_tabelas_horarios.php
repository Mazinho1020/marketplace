<?php

/**
 * Verificar se as tabelas de horários existem
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tabelas = DB::select("SHOW TABLES LIKE '%horario%'");

    echo "📋 Tabelas encontradas com 'horario':\n";
    foreach ($tabelas as $tabela) {
        $nomeTabela = array_values((array)$tabela)[0];
        echo "   - $nomeTabela\n";

        // Contar registros
        $count = DB::table($nomeTabela)->count();
        echo "     ($count registros)\n\n";
    }

    // Verificar tabela dias da semana
    $tabelasDias = DB::select("SHOW TABLES LIKE '%dias_semana%'");

    echo "📅 Tabelas encontradas com 'dias_semana':\n";
    foreach ($tabelasDias as $tabela) {
        $nomeTabela = array_values((array)$tabela)[0];
        echo "   - $nomeTabela\n";

        // Contar registros
        $count = DB::table($nomeTabela)->count();
        echo "     ($count registros)\n\n";
    }

    // Se as tabelas existem, mostrar alguns dados
    if (count($tabelas) > 0) {
        echo "🔍 Verificando dados de exemplo...\n";

        $horarios = DB::table('empresa_horarios_funcionamento')
            ->where('empresa_id', 1)
            ->limit(5)
            ->get();

        foreach ($horarios as $horario) {
            echo "   - Sistema: {$horario->sistema}, Dia: {$horario->dia_semana_id}, Aberto: " . ($horario->aberto ? 'Sim' : 'Não') . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
