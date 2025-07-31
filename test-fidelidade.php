<?php
// Teste simples para verificar se o sistema está funcionando

echo "🔍 Testando Sistema de Fidelidade\n\n";

// Teste 1: Verificar se o PHP está funcionando
echo "✅ PHP funcionando: " . phpversion() . "\n";

// Teste 2: Verificar se consegue ler os arquivos
$controller = file_exists(__DIR__ . '/app/Http/Controllers/Fidelidade/FidelidadeController.php');
echo ($controller ? "✅" : "❌") . " Controller existe\n";

$service = file_exists(__DIR__ . '/app/Services/Fidelidade/FidelidadeService.php');
echo ($service ? "✅" : "❌") . " Service existe\n";

$view = file_exists(__DIR__ . '/resources/views/fidelidade/dashboard.blade.php');
echo ($view ? "✅" : "❌") . " View existe\n";

$routes = file_exists(__DIR__ . '/routes/fidelidade/web.php');
echo ($routes ? "✅" : "❌") . " Routes existe\n";

echo "\n🎯 Sistema pronto para teste!\n";
echo "📍 Acesse: http://127.0.0.1:8000/fidelidade\n";
