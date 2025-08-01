<?php
// Gerador de chave APP_KEY válida para Laravel

function generateAppKey()
{
    // Gerar 32 bytes aleatórios (256 bits) para AES-256-CBC
    $key = random_bytes(32);

    // Codificar em base64
    $keyBase64 = base64_encode($key);

    return "base64:{$keyBase64}";
}

echo "=== GERADOR DE APP_KEY ===\n\n";

for ($i = 1; $i <= 3; $i++) {
    $key = generateAppKey();
    echo "Opção {$i}: APP_KEY={$key}\n";
}

echo "\n=== TESTE DA CHAVE ===\n";

// Testar se a chave gerada funciona
$testKey = generateAppKey();
echo "Chave de teste: {$testKey}\n";

// Extrair a parte base64
$keyPart = substr($testKey, 7); // Remove "base64:"
$decoded = base64_decode($keyPart);

echo "Tamanho da chave decodificada: " . strlen($decoded) . " bytes\n";
echo "Deve ser 32 bytes para AES-256-CBC: " . (strlen($decoded) === 32 ? "✅ OK" : "❌ ERRO") . "\n";

echo "\n=== INSTRUÇÃO ===\n";
echo "1. Copie uma das chaves geradas acima\n";
echo "2. Cole no arquivo .env substituindo a APP_KEY atual\n";
echo "3. Salve o arquivo .env\n";
echo "4. Teste o login novamente\n";
