<?php
// Verificação de Sintaxe do arquivo usuarios.blade.php

echo "<h2>🔍 Verificação de Sintaxe - usuarios.blade.php</h2>";

$arquivo = 'resources/views/comerciantes/empresas/usuarios.blade.php';

if (!file_exists($arquivo)) {
    echo "<p>❌ Arquivo não encontrado: {$arquivo}</p>";
    exit;
}

// 1. Verificar sintaxe PHP
$output = [];
$return_var = 0;
exec("php -l {$arquivo} 2>&1", $output, $return_var);

echo "<h3>📋 Resultado da Verificação:</h3>";

if ($return_var === 0) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>✅ SINTAXE PHP VÁLIDA</h4>";
    echo "<p>" . implode('<br>', $output) . "</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>❌ ERRO DE SINTAXE</h4>";
    echo "<p>" . implode('<br>', $output) . "</p>";
    echo "</div>";
}

// 2. Verificar estruturas Blade
$conteudo = file_get_contents($arquivo);

echo "<h3>🔧 Verificação de Estruturas Blade:</h3>";

$verificacoes = [
    'Abertura de @if' => substr_count($conteudo, '@if'),
    'Fechamento @endif' => substr_count($conteudo, '@endif'),
    'Abertura @forelse' => substr_count($conteudo, '@forelse'),
    'Fechamento @endforelse' => substr_count($conteudo, '@endforelse'),
    'Tags @else' => substr_count($conteudo, '@else'),
    'Tags @empty' => substr_count($conteudo, '@empty'),
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Estrutura</th><th>Quantidade</th><th>Status</th></tr>";

foreach ($verificacoes as $estrutura => $count) {
    $status = "✅ OK";
    if (strpos($estrutura, 'Abertura') !== false) {
        $fechamento = str_replace('Abertura', 'Fechamento', $estrutura);
        if (isset($verificacoes[$fechamento]) && $verificacoes[$fechamento] !== $count) {
            $status = "⚠️ Desbalanceado";
        }
    }

    echo "<tr><td>{$estrutura}</td><td>{$count}</td><td>{$status}</td></tr>";
}
echo "</table>";

// 3. Verificar problemas específicos
echo "<h3>🔍 Verificação de Problemas Específicos:</h3>";

$problemas = [];

// Verificar tags mal formadas
if (strpos($conteudo, '</div></small>') !== false) {
    $problemas[] = "❌ Tag mal formada: </div></small>";
}

// Verificar duplicação de small
if (preg_match('/<small[^>]*>.*<small[^>]*>/', $conteudo)) {
    $problemas[] = "❌ Tags <small> aninhadas incorretamente";
}

// Verificar @else órfãos
$lines = explode("\n", $conteudo);
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) === '@else') {
        // Verificar se há um @if antes
        $temIf = false;
        for ($j = $i - 1; $j >= 0; $j--) {
            if (strpos($lines[$j], '@if') !== false || strpos($lines[$j], '@forelse') !== false) {
                $temIf = true;
                break;
            }
            if (strpos($lines[$j], '@endif') !== false || strpos($lines[$j], '@endforelse') !== false) {
                break;
            }
        }
        if (!$temIf) {
            $problemas[] = "❌ @else órfão na linha " . ($i + 1);
        }
    }
}

if (empty($problemas)) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "<p>✅ Nenhum problema específico encontrado!</p>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h4>⚠️ Problemas Encontrados:</h4>";
    foreach ($problemas as $problema) {
        echo "<p>{$problema}</p>";
    }
    echo "</div>";
}

echo "<br><p><em>Verificação concluída em " . date('Y-m-d H:i:s') . "</em></p>";
