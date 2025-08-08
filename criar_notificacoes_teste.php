<?php

require_once 'vendor/autoload.php';

// Criar dados de notificação de teste
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

$app = new Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Criar notificações de teste
    $notificacoes = [
        [
            'titulo' => 'Bem-vindo ao Sistema!',
            'corpo' => 'Seja bem-vindo ao nosso sistema de marketplace. Explore todas as funcionalidades disponíveis.',
            'empresa_relacionada_id' => 1,
            'aplicacao_id' => 1,
            'canal' => 'in_app',
            'tipo' => 'sistema',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'titulo' => 'Nova Funcionalidade Disponível',
            'corpo' => 'Agora você pode gerenciar suas notificações de forma mais eficiente.',
            'empresa_relacionada_id' => 1,
            'aplicacao_id' => 1,
            'canal' => 'push',
            'tipo' => 'funcionalidade',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2)
        ],
        [
            'titulo' => 'Atualização de Segurança',
            'corpo' => 'Uma atualização de segurança foi aplicada ao sistema.',
            'empresa_relacionada_id' => 1,
            'aplicacao_id' => 1,
            'canal' => 'email',
            'tipo' => 'seguranca',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay()
        ],
        [
            'titulo' => 'Manutenção Programada',
            'corpo' => 'O sistema passará por manutenção programada no próximo domingo.',
            'empresa_relacionada_id' => 1,
            'aplicacao_id' => 1,
            'canal' => 'in_app',
            'tipo' => 'manutencao',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3)
        ]
    ];

    foreach ($notificacoes as $notificacao) {
        DB::table('notificacao_enviada')->insert($notificacao);
    }

    echo "✅ Notificações de teste criadas com sucesso!\n";
    echo "Total de notificações inseridas: " . count($notificacoes) . "\n";
} catch (Exception $e) {
    echo "❌ Erro ao criar notificações de teste: " . $e->getMessage() . "\n";
}
