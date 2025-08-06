<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== GERANDO PERMISSOES PARA FORMULARIO ===\n";

// Buscar todas as permissões organizadas por recurso
$permissoes = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->orderBy('codigo')
    ->get();

$permissoesPorRecurso = [];
foreach ($permissoes as $perm) {
    $codigo = $perm->codigo;
    $partes = explode('.', $codigo);
    $recurso = $partes[0] ?? 'outros';

    $permissoesPorRecurso[$recurso][] = [
        'codigo' => $codigo,
        'nome' => $perm->nome,
        'descricao' => $perm->descricao ?? ''
    ];
}

echo "Permissões organizadas por recurso:\n\n";

// Gerar HTML para o formulário
$htmlPermissoes = '';
foreach ($permissoesPorRecurso as $recurso => $perms) {
    $recursoNome = ucfirst($recurso);

    echo "=== {$recursoNome} ===\n";

    $htmlPermissoes .= "<div class=\"mb-3\">\n";
    $htmlPermissoes .= "    <h6 class=\"text-primary mb-2\">{$recursoNome}</h6>\n";
    $htmlPermissoes .= "    <div class=\"row\">\n";

    foreach ($perms as $perm) {
        echo "  • {$perm['codigo']} - {$perm['nome']}\n";

        $id = str_replace('.', '_', $perm['codigo']);
        $htmlPermissoes .= "        <div class=\"col-md-6 mb-2\">\n";
        $htmlPermissoes .= "            <div class=\"form-check\">\n";
        $htmlPermissoes .= "                <input class=\"form-check-input\" type=\"checkbox\" name=\"permissoes[]\" value=\"{$perm['codigo']}\" id=\"perm_{$id}\">\n";
        $htmlPermissoes .= "                <label class=\"form-check-label\" for=\"perm_{$id}\">\n";
        $htmlPermissoes .= "                    {$perm['nome']}\n";
        $htmlPermissoes .= "                </label>\n";
        $htmlPermissoes .= "            </div>\n";
        $htmlPermissoes .= "        </div>\n";
    }

    $htmlPermissoes .= "    </div>\n";
    $htmlPermissoes .= "</div>\n\n";

    echo "\n";
}

// Salvar o HTML gerado em um arquivo
file_put_contents('c:\xampp\htdocs\marketplace\permissoes_formulario.html', $htmlPermissoes);

echo "✅ HTML das permissões gerado em: permissoes_formulario.html\n";
echo "📄 Total de permissões: " . $permissoes->count() . "\n";
echo "📁 Recursos encontrados: " . count($permissoesPorRecurso) . "\n";

// Também gerar JavaScript para administrador
$jsPermissoes = "const todasPermissoes = " . json_encode($permissoes->pluck('codigo')->toArray(), JSON_PRETTY_PRINT) . ";\n\n";
$jsPermissoes .= "function toggleTodasPermissoes(isAdmin) {\n";
$jsPermissoes .= "    const checkboxes = document.querySelectorAll('input[name=\"permissoes[]\"]');\n";
$jsPermissoes .= "    checkboxes.forEach(checkbox => {\n";
$jsPermissoes .= "        checkbox.checked = isAdmin;\n";
$jsPermissoes .= "        checkbox.disabled = isAdmin;\n";
$jsPermissoes .= "    });\n";
$jsPermissoes .= "}\n\n";
$jsPermissoes .= "// Adicionar event listener para o select de perfil\n";
$jsPermissoes .= "document.addEventListener('DOMContentLoaded', function() {\n";
$jsPermissoes .= "    const perfilSelect = document.querySelector('select[name=\"perfil\"]');\n";
$jsPermissoes .= "    if (perfilSelect) {\n";
$jsPermissoes .= "        perfilSelect.addEventListener('change', function() {\n";
$jsPermissoes .= "            toggleTodasPermissoes(this.value === 'administrador');\n";
$jsPermissoes .= "        });\n";
$jsPermissoes .= "    }\n";
$jsPermissoes .= "});\n";

file_put_contents('c:\xampp\htdocs\marketplace\permissoes_admin.js', $jsPermissoes);

echo "✅ JavaScript gerado em: permissoes_admin.js\n";
echo "\n🎯 Próximos passos:\n";
echo "1. Atualizar o arquivo usuarios.blade.php com as novas permissões\n";
echo "2. Implementar lógica para administrador ter todas as permissões\n";
echo "3. Conectar com o sistema de permissões do banco\n";
