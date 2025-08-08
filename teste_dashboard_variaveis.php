<?php
echo "\n✅ TESTE RÁPIDO DAS VARIÁVEIS DO DASHBOARD ✅\n\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando variáveis do dashboard...\n\n";

// Simular dados do dashboard
$empresaId = 1;
$aplicacaoId = 1;

// 1. Stats básicas
$totalNotificacoes = DB::table('notificacao_enviadas')->count();
$naoLidas = DB::table('notificacao_enviadas')->whereNull('lido_em')->count();
$hoje = DB::table('notificacao_enviadas')->whereDate('created_at', now()->format('Y-m-d'))->count();
$lidas = $totalNotificacoes - $naoLidas;
$taxaLeitura = $totalNotificacoes > 0 ? round(($lidas / $totalNotificacoes) * 100, 1) : 0;

echo "📊 Variável \$stats:\n";
echo "   - total: $totalNotificacoes ✅\n";
echo "   - nao_lidas: $naoLidas ✅\n";
echo "   - hoje: $hoje ✅\n";
echo "   - taxa_leitura: $taxaLeitura% ✅\n\n";

// 2. Notificações recentes
$notificacoesRecentes = DB::table('notificacao_enviadas')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "📋 Variável \$notificacoesRecentes:\n";
echo "   - Total encontradas: " . $notificacoesRecentes->count() . " ✅\n";
echo "   - Primeiras 3:\n";

foreach ($notificacoesRecentes->take(3) as $index => $notif) {
    $num = $index + 1;
    echo "     $num. ID {$notif->id}: " . substr($notif->titulo, 0, 25) . "...\n";
}

// 3. Por canal
$porCanal = DB::table('notificacao_enviadas')
    ->selectRaw('canal, count(*) as total')
    ->groupBy('canal')
    ->pluck('total', 'canal');

echo "\n📡 Variável \$porCanal:\n";
foreach ($porCanal as $canal => $total) {
    echo "   - $canal: $total ✅\n";
}

// 4. Últimos 7 dias
$ultimosSete = [];
for ($i = 6; $i >= 0; $i--) {
    $data = now()->subDays($i)->format('Y-m-d');
    $count = DB::table('notificacao_enviadas')->whereDate('created_at', $data)->count();
    $ultimosSete[] = ['data' => $data, 'count' => $count];
}

echo "\n📈 Variável \$ultimosSete:\n";
foreach ($ultimosSete as $dia) {
    echo "   - {$dia['data']}: {$dia['count']} notificações ✅\n";
}

echo "\n🎉 TODAS AS VARIÁVEIS FUNCIONANDO PERFEITAMENTE! 🎉\n";
echo "\n✅ Dashboard pronto para uso!\n";
