<?php
// Script para analisar duplicações no arquivo usuarios.blade.php
echo "🔍 ANÁLISE DE DUPLICAÇÕES NO ARQUIVO USUARIOS.BLADE.PHP\n\n";

$arquivo = 'resources/views/comerciantes/empresas/usuarios.blade.php';
$conteudo = file_get_contents($arquivo);

// Dividir por linhas
$linhas = explode("\n", $conteudo);
echo "📊 Total de linhas: " . count($linhas) . "\n\n";

// 1. Analisar botões duplicados
echo "🔍 BOTÕES ENCONTRADOS:\n";
echo "======================\n";

$botoes = [];
foreach ($linhas as $numero => $linha) {
    $linha = trim($linha);

    if (strpos($linha, 'btn ') !== false && strpos($linha, 'button') !== false) {
        $texto = '';

        // Extrair texto do botão
        if (preg_match('/>(.*?)<\/button>/', $linha, $matches)) {
            $texto = strip_tags($matches[1]);
        } elseif (preg_match('/data-bs-target="([^"]+)"/', $linha, $matches)) {
            $texto = $matches[1];
        }

        $botoes[] = [
            'linha' => $numero + 1,
            'codigo' => $linha,
            'texto' => trim($texto)
        ];
    }
}

// Agrupar botões similares
$grupos = [];
foreach ($botoes as $botao) {
    $chave = '';

    if (strpos($botao['codigo'], 'modalAdicionarUsuario') !== false) {
        $chave = 'Adicionar/Vincular Usuário';
    } elseif (strpos($botao['codigo'], 'modalCriarUsuario') !== false) {
        $chave = 'Criar Novo Usuário';
    } elseif (strpos($botao['codigo'], 'modalEditarUsuario') !== false) {
        $chave = 'Editar Usuário';
    } elseif (strpos($botao['codigo'], 'btn-close') !== false) {
        $chave = 'Fechar Modal';
    } elseif (strpos($botao['codigo'], 'Cancelar') !== false) {
        $chave = 'Cancelar';
    } else {
        $chave = 'Outros';
    }

    if (!isset($grupos[$chave])) {
        $grupos[$chave] = [];
    }

    $grupos[$chave][] = $botao;
}

foreach ($grupos as $tipo => $botoesList) {
    echo "📌 $tipo: " . count($botoesList) . " ocorrências\n";
    foreach ($botoesList as $botao) {
        echo "   Linha {$botao['linha']}: {$botao['texto']}\n";
    }
    echo "\n";
}

// 2. Analisar modals duplicados
echo "🏗️ MODAIS ENCONTRADOS:\n";
echo "======================\n";

$modais = [];
foreach ($linhas as $numero => $linha) {
    if (strpos($linha, 'modal fade') !== false && strpos($linha, 'id="') !== false) {
        if (preg_match('/id="([^"]+)"/', $linha, $matches)) {
            $modalId = $matches[1];
            $modais[] = [
                'id' => $modalId,
                'linha' => $numero + 1
            ];
        }
    }
}

foreach ($modais as $modal) {
    echo "📋 Modal: {$modal['id']} (Linha {$modal['linha']})\n";
}

// 3. Analisar funcionalidade de cada modal
echo "\n🎯 ANÁLISE FUNCIONAL:\n";
echo "=====================\n";

echo "1️⃣ modalAdicionarUsuario:\n";
echo "   - Função: VINCULAR usuário existente à empresa\n";
echo "   - Formulário: Solicita EMAIL do usuário + perfil + permissões\n";
echo "   - Rota: comerciantes.empresas.usuarios.store\n";
echo "   - Lógica: Busca usuário existente no sistema pelo email\n\n";

echo "2️⃣ modalCriarUsuario:\n";
echo "   - Função: CRIAR novo usuário e vincular à empresa\n";
echo "   - Formulário: Nome, username, email, telefone, senha + perfil + permissões\n";
echo "   - Rota: comerciantes.empresas.usuarios.create\n";
echo "   - Lógica: Cria novo registro de usuário no sistema\n\n";

echo "3️⃣ modalEditarUsuario:\n";
echo "   - Função: EDITAR usuário já vinculado\n";
echo "   - Formulário: Perfil, status, permissões (nome readonly)\n";
echo "   - Rota: Dinâmica via JavaScript\n";
echo "   - Lógica: Atualiza dados de vínculo usuário-empresa\n\n";

// 4. Identificar duplicações problemáticas
echo "⚠️ PROBLEMAS IDENTIFICADOS:\n";
echo "============================\n";

$problemas = [
    "Botões duplicados" => [
        "- Linha 40: Botão 'Adicionar Usuário' (topo da página)",
        "- Linha 90: Botão 'Vincular Usuário' (dentro da lista)",
        "- Ambos abrem o mesmo modal: modalAdicionarUsuario",
        "- Causa confusão: textos diferentes, mesma função"
    ],
    "Permissões duplicadas" => [
        "- Mesmo código de permissões em 3 modais diferentes",
        "- JavaScript duplicado para cada modal",
        "- Manutenção complexa: mudança em 3 lugares"
    ],
    "Formulários similares" => [
        "- modalAdicionarUsuario e modalCriarUsuario têm campos parecidos",
        "- Lógica de perfil 'administrador' duplicada",
        "- Validações repetidas"
    ]
];

foreach ($problemas as $problema => $detalhes) {
    echo "🔴 $problema:\n";
    foreach ($detalhes as $detalhe) {
        echo "   $detalhe\n";
    }
    echo "\n";
}

echo "💡 RECOMENDAÇÕES:\n";
echo "==================\n";
echo "1. Unificar botões: Um botão 'Gerenciar Usuários' com dropdown\n";
echo "2. Componentizar: Criar partial para lista de permissões\n";
echo "3. Consolidar JavaScript: Uma função para todos os modais\n";
echo "4. Padronizar textos: 'Vincular' vs 'Adicionar' → escolher um\n";
echo "5. Melhorar UX: Indicar claramente a diferença entre funções\n\n";

echo "✅ Análise concluída!\n";
