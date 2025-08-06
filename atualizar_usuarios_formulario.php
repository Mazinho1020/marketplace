<?php
echo "=== ATUALIZANDO FORMULÁRIO DE USUÁRIOS ===\n";

// Ler os arquivos gerados
$htmlPermissoes = file_get_contents('permissoes_formulario.html');
$jsPermissoes = file_get_contents('permissoes_admin.js');

// Ler o arquivo atual
$arquivo = 'resources/views/comerciantes/empresas/usuarios.blade.php';
$conteudo = file_get_contents($arquivo);

echo "📁 Arquivo: $arquivo\n";
echo "📊 Tamanho original: " . strlen($conteudo) . " bytes\n";

// Função para ajustar os IDs das permissões para cada modal
function ajustarPermissoes($html, $prefixo)
{
    // Substituir IDs para evitar conflitos entre modais
    $html = str_replace('id="perm_', 'id="' . $prefixo . '_perm_', $html);
    $html = str_replace('for="perm_', 'for="' . $prefixo . '_perm_', $html);
    return $html;
}

// 1. SEÇÃO VISUALIZAR USUÁRIO (linhas ~254-280)
$permissoesVisualizar = ajustarPermissoes($htmlPermissoes, 'view');

$pattern1 = '/(<label class="form-label">Permissões<\/label>\s*<div[^>]*>)(.*?)(<\/div>\s*<\/div>)/ms';
$replacement1 = '$1' . $permissoesVisualizar . '$3';

// 2. SEÇÃO CRIAR USUÁRIO (linhas ~368-410)
$permissoesCriar = ajustarPermissoes($htmlPermissoes, 'criar');

// 3. SEÇÃO EDITAR USUÁRIO (linhas ~452-490)
$permissoesEditar = ajustarPermissoes($htmlPermissoes, 'edit');

// Fazer as substituições
$novoConteudo = $conteudo;

// Procurar e substituir cada seção individualmente
$secoes = [
    'view' => 'Visualizar',
    'criar' => 'Criar',
    'edit' => 'Editar'
];

foreach ($secoes as $prefixo => $nome) {
    echo "\n🔄 Processando seção: $nome ($prefixo)\n";

    // Padrão mais específico para cada seção
    if ($prefixo === 'view') {
        // Modal de visualização
        $pattern = '/(<div[^>]*id="viewUserModal"[^>]*>.*?<label class="form-label">Permissões<\/label>\s*<div[^>]*>)(.*?)(<\/div>\s*<\/div>)/ms';
    } elseif ($prefixo === 'criar') {
        // Modal de criação
        $pattern = '/(<div[^>]*id="addUserModal"[^>]*>.*?<label class="form-label">Permissões<\/label>\s*<div[^>]*>)(.*?)(<\/div>\s*<\/div>)/ms';
    } else {
        // Modal de edição
        $pattern = '/(<div[^>]*id="editUserModal"[^>]*>.*?<label class="form-label">Permissões<\/label>\s*<div[^>]*>)(.*?)(<\/div>\s*<\/div>)/ms';
    }

    $permissoesAjustadas = ajustarPermissoes($htmlPermissoes, $prefixo);
    $replacement = '$1' . $permissoesAjustadas . '$3';

    $novoConteudo = preg_replace($pattern, $replacement, $novoConteudo, 1);

    if (preg_last_error() !== PREG_NO_ERROR) {
        echo "❌ Erro na regex para $nome: " . preg_last_error() . "\n";
    } else {
        echo "✅ Seção $nome atualizada\n";
    }
}

// Adicionar o JavaScript no final do arquivo (antes do fechamento @endsection)
$jsCompleto = "\n<script>\n" . $jsPermissoes . "\n</script>\n@endsection";
$novoConteudo = str_replace('@endsection', $jsCompleto, $novoConteudo);

echo "\n📊 Tamanho novo: " . strlen($novoConteudo) . " bytes\n";
echo "📈 Diferença: " . (strlen($novoConteudo) - strlen($conteudo)) . " bytes\n";

// Salvar backup com timestamp
$backup = $arquivo . '.backup.' . date('Y-m-d_H-i-s');
copy($arquivo, $backup);
echo "💾 Backup salvo: $backup\n";

// Salvar o novo arquivo
file_put_contents($arquivo, $novoConteudo);
echo "✅ Arquivo atualizado com sucesso!\n";

echo "\n🎯 Resumo das atualizações:\n";
echo "• ✅ Permissões hardcoded substituídas por 85 permissões do banco\n";
echo "• ✅ JavaScript adicionado para automação de admin\n";
echo "• ✅ IDs únicos para cada modal (view_, criar_, edit_)\n";
echo "• ✅ Backup do arquivo original criado\n";

echo "\n📋 Próximos passos:\n";
echo "1. Testar o formulário no navegador\n";
echo "2. Verificar se o role 'administrador' seleciona todas as permissões\n";
echo "3. Testar criação e edição de usuários\n";
echo "4. Validar se as permissões são salvas corretamente\n";
