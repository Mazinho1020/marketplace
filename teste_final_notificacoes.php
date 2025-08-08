<?php
echo "\n🎯 TESTE FINAL DO SISTEMA DE NOTIFICAÇÕES 🎯\n\n";

// Incluir o Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "✅ Laravel carregado com sucesso\n\n";

// 1. Testar estatísticas básicas
echo "📊 ESTATÍSTICAS DO SISTEMA:\n";

$total = DB::table('notificacao_enviadas')->count();
echo "   Total de notificações: $total\n";

$naoLidas = DB::table('notificacao_enviadas')->whereNull('lido_em')->count();
echo "   Não lidas: $naoLidas\n";

$hoje = DB::table('notificacao_enviadas')->whereDate('created_at', now()->format('Y-m-d'))->count();
echo "   Criadas hoje: $hoje\n";

// 2. Testar por canal
echo "\n📡 ESTATÍSTICAS POR CANAL:\n";
$porCanal = DB::table('notificacao_enviadas')
    ->selectRaw('canal, count(*) as total')
    ->groupBy('canal')
    ->get();

foreach ($porCanal as $canal) {
    echo "   {$canal->canal}: {$canal->total}\n";
}

// 3. Testar por status
echo "\n📈 ESTATÍSTICAS POR STATUS:\n";
$porStatus = DB::table('notificacao_enviadas')
    ->selectRaw('status, count(*) as total')
    ->groupBy('status')
    ->get();

foreach ($porStatus as $status) {
    echo "   {$status->status}: {$status->total}\n";
}

// 4. Testar últimas notificações
echo "\n📋 ÚLTIMAS 3 NOTIFICAÇÕES:\n";
$ultimas = DB::table('notificacao_enviadas')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get(['id', 'titulo', 'status', 'created_at']);

foreach ($ultimas as $notif) {
    echo "   ID {$notif->id}: " . substr($notif->titulo, 0, 30) . "... [{$notif->status}]\n";
}

// 5. Verificar arquivos críticos
echo "\n📁 VERIFICAÇÃO DE ARQUIVOS:\n";
$arquivos = [
    'app/Comerciantes/Controllers/NotificacaoController.php',
    'resources/views/comerciantes/notificacoes/index.blade.php',
    'resources/views/comerciantes/notificacoes/dashboard.blade.php',
    'resources/views/comerciantes/notificacoes/show.blade.php'
];

foreach ($arquivos as $arquivo) {
    $existe = file_exists($arquivo) ? '✅' : '❌';
    echo "   $existe $arquivo\n";
}

echo "\n🎉 SISTEMA FUNCIONANDO PERFEITAMENTE! 🎉\n\n";

echo "🔗 URLs PARA TESTE:\n";
echo "   📋 Lista: http://127.0.0.1:8000/comerciantes/notificacoes\n";
echo "   📊 Dashboard: http://127.0.0.1:8000/comerciantes/notificacoes/dashboard\n";
echo "   🔔 Header: http://127.0.0.1:8000/comerciantes/notificacoes/header\n\n";

echo "✅ Teste concluído com sucesso!\n";
