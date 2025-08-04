<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\NotificacoesEnviadasController;

require_once 'vendor/autoload.php';

// Bootstrap do Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Criar uma requisição simulada
$request = Illuminate\Http\Request::create('/admin/notificacoes/enviadas/dados', 'GET');

// Processar a requisição através do kernel
$response = $kernel->handle($request);

echo "=== TESTE DE HORÁRIOS NA API ===\n";
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content-Type: " . $response->headers->get('Content-Type') . "\n\n";

$content = $response->getContent();
if (json_decode($content)) {
    $data = json_decode($content, true);

    if (isset($data['data']) && count($data['data']) > 0) {
        echo "=== HORÁRIOS DAS NOTIFICAÇÕES ===\n";
        foreach ($data['data'] as $notif) {
            echo "ID: " . $notif['id'] . "\n";
            echo "Enviado em: " . $notif['enviado_em'] . "\n";
            if ($notif['entregue_em']) {
                echo "Entregue em: " . $notif['entregue_em'] . "\n";
            }
            if ($notif['lido_em']) {
                echo "Lido em: " . $notif['lido_em'] . "\n";
            }
            echo "---\n";
        }

        if (isset($data['mock']) && $data['mock']) {
            echo "\n[DADOS MOCK UTILIZADOS - tabela não existe]\n";
        }
    }
} else {
    echo "Erro na resposta:\n";
    echo $content . "\n";
}

$kernel->terminate($request, $response);
