<?php
// Script para gerar uma chave APP_KEY válida para Laravel

function generateValidAppKey()
{
    // Gera 32 bytes aleatórios (256 bits para AES-256-CBC)
    $key = random_bytes(32);

    // Codifica em base64
    $base64Key = base64_encode($key);

    return 'base64:' . $base64Key;
}

echo "=== GERADOR DE CHAVE LARAVEL ===<br><br>";

$newKey = generateValidAppKey();

echo "Nova APP_KEY gerada:<br>";
echo "<strong>APP_KEY={$newKey}</strong><br><br>";

echo "📋 <strong>INSTRUÇÕES:</strong><br>";
echo "1. Copie a linha 'APP_KEY=' acima<br>";
echo "2. Abra o arquivo .env<br>";
echo "3. Substitua a linha APP_KEY existente<br>";
echo "4. Salve o arquivo<br>";
echo "5. Acesse http://localhost:8000/login<br><br>";

echo "✅ Esta chave é válida para AES-256-CBC do Laravel 11!<br>";
