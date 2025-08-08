<?php
echo "=== TESTE DE ROTA DIRETA ===\n\n";

use Illuminate\Support\Facades\Route;

echo "🔧 Criando rota de teste...\n";

// Registrar uma rota de teste que força o uso do middleware correto
Route::get('/teste-comerciante-auth', function () {
    return response()->json(['status' => 'autenticado']);
})->middleware('auth.comerciante');

echo "✅ Rota criada: /teste-comerciante-auth\n";
echo "🧪 Teste com: curl http://localhost:8000/teste-comerciante-auth\n";

// Testar a rota
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/teste-comerciante-auth");
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\n📊 RESULTADO DA ROTA DE TESTE:\n";
echo "   Status: $httpCode\n";

if ($httpCode === 302) {
    if (preg_match('/Location:\s*(.+)/i', $response, $matches)) {
        $location = trim($matches[1]);
        echo "   Redirecionando para: $location\n";
    }
}
