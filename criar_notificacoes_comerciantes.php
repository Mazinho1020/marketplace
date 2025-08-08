<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Notificacao\NotificacaoEnviada;
use App\Models\Notificacao\NotificacaoAplicacao;

echo "🔔 CRIANDO NOTIFICAÇÕES ESPECÍFICAS PARA COMERCIANTES\n";
echo str_repeat("=", 60) . "\n\n";

try {
    $aplicacao = NotificacaoAplicacao::where('codigo', 'empresa')->first();

    if (!$aplicacao) {
        echo "❌ Aplicação 'empresa' não encontrada!\n";
        exit(1);
    }

    // Notificações para comerciantes
    $notificacoes = [
        [
            'titulo' => 'Novo Pedido Online',
            'mensagem' => 'Você recebeu um novo pedido online #PED-2024-001 no valor de R$ 249,90. Cliente: Maria Silva',
            'canal' => 'in_app',
        ],
        [
            'titulo' => 'Pagamento PIX Recebido',
            'mensagem' => 'Pagamento PIX de R$ 150,00 foi confirmado para o pedido #PED-2024-002.',
            'canal' => 'push',
        ],
        [
            'titulo' => 'Estoque Baixo - Ação Necessária',
            'mensagem' => 'Atenção! O produto "Smartphone Galaxy" está com apenas 3 unidades em estoque.',
            'canal' => 'email',
        ],
        [
            'titulo' => 'Cliente Aguardando Entrega',
            'mensagem' => 'O cliente João Santos está aguardando a entrega do pedido #PED-2024-003 há 2 dias.',
            'canal' => 'in_app',
        ],
        [
            'titulo' => 'Meta de Vendas Atingida',
            'mensagem' => 'Parabéns! Você atingiu 85% da sua meta mensal de vendas.',
            'canal' => 'push',
        ],
        [
            'titulo' => 'Novo Cliente Cadastrado',
            'mensagem' => 'Um novo cliente se cadastrou: Ana Costa (ana.costa@email.com). Bem-vindo à sua loja!',
            'canal' => 'in_app',
        ]
    ];

    foreach ($notificacoes as $notif) {
        NotificacaoEnviada::create([
            'empresa_id' => 1,
            'aplicacao_id' => $aplicacao->id,
            'empresa_relacionada_id' => 1, // Empresa do comerciante
            'canal' => $notif['canal'],
            'titulo' => $notif['titulo'],
            'mensagem' => $notif['mensagem'],
            'status' => 'entregue',
            'entregue_em' => now(),
            'dados_processados' => json_encode([
                'comerciante' => true,
                'tipo' => 'negocio',
                'prioridade' => 'normal'
            ])
        ]);
    }

    echo "✅ Criadas " . count($notificacoes) . " notificações específicas para comerciantes!\n\n";

    // Criar algumas como não lidas para teste
    $notificacoesNaoLidas = NotificacaoEnviada::where('empresa_relacionada_id', 1)
        ->whereIn('canal', ['in_app', 'push'])
        ->latest()
        ->take(3)
        ->get();

    foreach ($notificacoesNaoLidas as $notif) {
        $notif->update(['lido_em' => null]);
    }

    echo "📬 Marcadas 3 notificações como NÃO LIDAS para teste!\n\n";

    echo "🎯 TESTE AGORA:\n";
    echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
    echo "   2. Faça login com qualquer usuário comerciante\n";
    echo "   3. Veja o ícone de notificação no header com badge\n";
    echo "   4. Clique para ver as notificações no dropdown\n";
    echo "   5. Acesse o menu 'Notificações' para ver a lista completa\n\n";

    echo "📊 ESTATÍSTICAS:\n";
    $total = NotificacaoEnviada::where('empresa_relacionada_id', 1)->count();
    $naoLidas = NotificacaoEnviada::where('empresa_relacionada_id', 1)
        ->whereNull('lido_em')->count();

    echo "   Total de notificações: {$total}\n";
    echo "   Não lidas: {$naoLidas}\n";
    echo "   Lidas: " . ($total - $naoLidas) . "\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
