<?php
// Verificação Final das Correções do usuários.blade.php

$arquivo = file_get_contents('resources/views/comerciantes/empresas/usuarios.blade.php');

if (!$arquivo) {
    die("❌ Erro: Não foi possível ler o arquivo");
}

echo "<h2>📋 Relatório Final de Verificação</h2>";

// 1. Interface dropdown implementada
$dropdown = strpos($arquivo, 'dropdown-toggle') !== false;
echo "<p>" . ($dropdown ? "✅" : "❌") . " 1. Interface dropdown implementada</p>";

// 2. modalVincularUsuario criado
$modalVincular = strpos($arquivo, 'modalVincularUsuario') !== false;
echo "<p>" . ($modalVincular ? "✅" : "❌") . " 2. Modal Vincular Usuário criado</p>";

// 3. Botões estilizados corretamente
$btnInfo = strpos($arquivo, 'btn-info') !== false;
$btnSuccess = strpos($arquivo, 'btn-success') !== false;
echo "<p>" . (($btnInfo && $btnSuccess) ? "✅" : "❌") . " 3. Botões estilizados corretamente</p>";

// 4. Alerts informativos nos modais
$alertInfo = strpos($arquivo, 'alert alert-info') !== false;
$alertSuccess = strpos($arquivo, 'alert alert-success') !== false;
$alertWarning = strpos($arquivo, 'alert alert-warning') !== false;
echo "<p>" . (($alertInfo && $alertSuccess && $alertWarning) ? "✅" : "❌") . " 4. Alerts informativos nos modais</p>";

// 5. Função setupAdminPermissions consolidada
$setupAdmin = strpos($arquivo, 'setupAdminPermissions') !== false;
echo "<p>" . ($setupAdmin ? "✅" : "❌") . " 5. Função setupAdminPermissions consolidada</p>";

// 6. Componente permissions-list incluído nos modais
$includeVincular = strpos($arquivo, "@include('components.permissions-list', ['prefix' => 'vincular'])") !== false;
$includeCriar = strpos($arquivo, "@include('components.permissions-list', ['prefix' => 'criar'])") !== false;
$includeEdit = strpos($arquivo, "@include('components.permissions-list', ['prefix' => 'edit'])") !== false;
echo "<p>" . (($includeVincular && $includeCriar && $includeEdit) ? "✅" : "❌") . " 6. Componente permissions-list incluído nos 3 modais</p>";

// 7. Duplicações removidas
$duplicacoesBotoes = substr_count($arquivo, 'Adicionar Usuário');
$duplicacoesPermissoes = substr_count($arquivo, 'produtos.view');
echo "<p>" . (($duplicacoesBotoes <= 1 && $duplicacoesPermissoes <= 1) ? "✅" : "❌") . " 7. Duplicações removidas</p>";

echo "<br><h3>📊 Resumo</h3>";
$total = 7;
$implementados = 0;

if ($dropdown) $implementados++;
if ($modalVincular) $implementados++;
if ($btnInfo && $btnSuccess) $implementados++;
if ($alertInfo && $alertSuccess && $alertWarning) $implementados++;
if ($setupAdmin) $implementados++;
if ($includeVincular && $includeCriar && $includeEdit) $implementados++;
if ($duplicacoesBotoes <= 1 && $duplicacoesPermissoes <= 1) $implementados++;

echo "<p><strong>Implementações concluídas: {$implementados}/{$total} (" . round(($implementados / $total) * 100) . "%)</strong></p>";

if ($implementados == $total) {
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>🎉 TODAS AS CORREÇÕES FORAM IMPLEMENTADAS COM SUCESSO!</h4>";
    echo "<p>O arquivo usuarios.blade.php agora possui:</p>";
    echo "<ul>";
    echo "<li>Interface dropdown unificada</li>";
    echo "<li>Modais distintos com alertas explicativos</li>";
    echo "<li>Componente reutilizável de permissões</li>";
    echo "<li>JavaScript consolidado</li>";
    echo "<li>Código limpo sem duplicações</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>⚠️ Algumas implementações ainda precisam ser finalizadas</h4>";
    echo "</div>";
}

echo "<br><p><em>Verificação concluída em " . date('Y-m-d H:i:s') . "</em></p>";
