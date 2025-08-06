<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Comerciantes\Models\Empresa;

echo "=== DEBUG USUÁRIOS VINCULADOS ===\n";

// Buscar primeira empresa
$empresa = Empresa::with('usuariosVinculados')->first();

if (!$empresa) {
    echo "Nenhuma empresa encontrada.\n";
    exit;
}

echo "Empresa: {$empresa->nome_fantasia}\n";
echo "Usuários vinculados: " . $empresa->usuariosVinculados->count() . "\n";

if ($empresa->usuariosVinculados->count() > 0) {
    echo "\n=== ESTRUTURA DO PRIMEIRO VÍNCULO ===\n";
    $vinculo = $empresa->usuariosVinculados->first();

    echo "Tipo da coleção: " . get_class($empresa->usuariosVinculados) . "\n";
    echo "Tipo do item: " . get_class($vinculo) . "\n";

    echo "\nAtributos do vínculo:\n";
    foreach ($vinculo->getAttributes() as $key => $value) {
        echo "  {$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
    }

    echo "\nInformações do pivot:\n";
    if ($vinculo->pivot) {
        foreach ($vinculo->pivot->getAttributes() as $key => $value) {
            echo "  pivot.{$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    } else {
        echo "  Pivot não encontrado\n";
    }
} else {
    echo "Nenhum usuário vinculado encontrado.\n";
}
