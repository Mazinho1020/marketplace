<?php

/**
 * SCRIPT PARA CORRIGIR GUARDS DE AUTENTICAÇÃO
 */

require_once 'vendor/autoload.php';

echo "🔧 CORRIGINDO GUARDS DE AUTENTICAÇÃO\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Arquivos e suas correções
$arquivos = [
    'app/Comerciantes/Controllers/MarcaController.php',
    'app/Comerciantes/Controllers/EmpresaController.php'
];

foreach ($arquivos as $arquivo) {
    echo "📝 Corrigindo: $arquivo\n";

    if (!file_exists($arquivo)) {
        echo "   ❌ Arquivo não encontrado!\n\n";
        continue;
    }

    $conteudo = file_get_contents($arquivo);

    // Substituições
    $substituicoes = [
        'Auth::user()' => "Auth::guard('comerciante')->user()",
        'Auth::id()' => "Auth::guard('comerciante')->id()"
    ];

    $alteracoes = 0;
    foreach ($substituicoes as $buscar => $substituir) {
        $conteudoNovo = str_replace($buscar, $substituir, $conteudo);
        $count = substr_count($conteudo, $buscar);
        if ($count > 0) {
            $conteudo = $conteudoNovo;
            $alteracoes += $count;
            echo "   ✅ $buscar → $substituir ($count ocorrências)\n";
        }
    }

    if ($alteracoes > 0) {
        file_put_contents($arquivo, $conteudo);
        echo "   💾 Arquivo salvo com $alteracoes alterações\n";
    } else {
        echo "   ℹ️ Nenhuma alteração necessária\n";
    }

    echo "\n";
}

echo "🎉 CORREÇÃO CONCLUÍDA!\n";
echo "\nAgora teste o login novamente.\n";
echo "=" . str_repeat("=", 52) . "\n";
