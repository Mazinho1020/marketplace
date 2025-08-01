<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Estrutura da tabela fidelidade_carteiras:" . PHP_EOL;
$columns = DB::select('DESCRIBE fidelidade_carteiras');
foreach ($columns as $col) {
    echo "- {$col->Field} ({$col->Type})" . PHP_EOL;
}

echo PHP_EOL . "📋 Dados atuais:" . PHP_EOL;
$dados = DB::table('fidelidade_carteiras')->limit(3)->get();
foreach ($dados as $item) {
    echo json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
