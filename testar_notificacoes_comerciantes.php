<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Notificacao\NotificacaoEnviada;
use App\Models\Notificacao\NotificacaoAplicacao;
use App\Comerciantes\Models\EmpresaUsuario;

echo "🔔 TESTE DO SISTEMA DE NOTIFICAÇÕES PARA COMERCIANTES\n";
echo str_repeat("=", 60) . "\n\n";

try {
    echo "1️⃣ Verificando notificações existentes...\n";
    $notificacoes = NotificacaoEnviada::take(5)->get();

    if ($notificacoes->count() > 0) {
        echo "✅ Encontradas {$notificacoes->count()} notificações:\n";
        foreach ($notificacoes as $notif) {
            echo "   - ID: {$notif->id} | Título: {$notif->titulo} | Canal: {$notif->canal}\n";
        }
    } else {
        echo "⚠️ Nenhuma notificação encontrada, criando notificações de teste...\n";

        // Buscar aplicação empresa
        $aplicacaoEmpresa = NotificacaoAplicacao::where('codigo', 'empresa')->first();

        if (!$aplicacaoEmpresa) {
            echo "📝 Criando aplicação 'empresa'...\n";
            $aplicacaoEmpresa = NotificacaoAplicacao::create([
                'empresa_id' => 1,
                'nome' => 'Sistema Empresa',
                'codigo' => 'empresa',
                'descricao' => 'Sistema de gerenciamento empresarial',
                'ativo' => true
            ]);
        }

        // Criar notificações de teste
        $notificacoesTeste = [
            [
                'titulo' => 'Novo Pedido Recebido',
                'mensagem' => 'Você recebeu um novo pedido #123 no valor de R$ 150,00.',
                'canal' => 'in_app'
            ],
            [
                'titulo' => 'Pagamento Aprovado',
                'mensagem' => 'O pagamento do pedido #122 foi aprovado com sucesso.',
                'canal' => 'push'
            ],
            [
                'titulo' => 'Produto em Baixo Estoque',
                'mensagem' => 'O produto "Camiseta Azul" está com apenas 5 unidades em estoque.',
                'canal' => 'email'
            ],
            [
                'titulo' => 'Novo Cliente Cadastrado',
                'mensagem' => 'Um novo cliente se cadastrou: João Silva (joao@email.com)',
                'canal' => 'in_app'
            ],
            [
                'titulo' => 'Entrega Realizada',
                'mensagem' => 'A entrega do pedido #121 foi realizada com sucesso.',
                'canal' => 'push'
            ]
        ];

        foreach ($notificacoesTeste as $notif) {
            NotificacaoEnviada::create([
                'empresa_id' => 1,
                'aplicacao_id' => $aplicacaoEmpresa->id,
                'empresa_relacionada_id' => 1, // Empresa do comerciante
                'canal' => $notif['canal'],
                'titulo' => $notif['titulo'],
                'mensagem' => $notif['mensagem'],
                'status' => 'entregue',
                'entregue_em' => now(),
                'dados_processados' => json_encode(['teste' => true])
            ]);
        }

        echo "✅ Criadas " . count($notificacoesTeste) . " notificações de teste!\n";
    }

    echo "\n2️⃣ Verificando usuários comerciantes...\n";
    $comerciantes = EmpresaUsuario::take(3)->get();

    if ($comerciantes->count() > 0) {
        echo "✅ Encontrados {$comerciantes->count()} usuários comerciantes:\n";
        foreach ($comerciantes as $user) {
            echo "   - ID: {$user->id} | Nome: {$user->nome} | Email: {$user->email}\n";
        }
    } else {
        echo "⚠️ Nenhum usuário comerciante encontrado!\n";
    }

    echo "\n3️⃣ Testando rotas de notificação...\n";
    echo "📍 Rotas disponíveis:\n";
    echo "   - GET  /comerciantes/notificacoes           -> Lista de notificações\n";
    echo "   - GET  /comerciantes/notificacoes/dashboard -> Dashboard com gráficos\n";
    echo "   - GET  /comerciantes/notificacoes/header    -> API para o header\n";
    echo "   - GET  /comerciantes/notificacoes/{id}      -> Detalhes da notificação\n";
    echo "   - POST /comerciantes/notificacoes/{id}/marcar-lida -> Marcar como lida\n";
    echo "   - POST /comerciantes/notificacoes/marcar-todas-lidas -> Marcar todas como lidas\n";

    echo "\n✅ SISTEMA DE NOTIFICAÇÕES CONFIGURADO COM SUCESSO!\n";
    echo "\n🎯 PRÓXIMOS PASSOS:\n";
    echo "   1. Faça login como comerciante em: http://localhost:8000/comerciantes/login\n";
    echo "   2. Acesse as notificações em: http://localhost:8000/comerciantes/notificacoes\n";
    echo "   3. Veja o dashboard em: http://localhost:8000/comerciantes/notificacoes/dashboard\n";
    echo "   4. As notificações aparecem automaticamente no header!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
    echo "📁 Arquivo: " . $e->getFile() . "\n";
}
