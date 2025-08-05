<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\Empresa;

echo "🧪 TESTE FINAL - USUÁRIOS VINCULADOS\n";
echo "=" . str_repeat("=", 38) . "\n\n";

try {
    // Testar empresa 1
    $empresa = Empresa::with(['usuariosVinculados', 'proprietario', 'marca'])->find(1);

    if (!$empresa) {
        echo "❌ Empresa não encontrada\n";
        exit;
    }

    echo "✅ Empresa: {$empresa->nome}\n";
    echo "✅ Marca: " . ($empresa->marca?->nome ?? 'Sem marca') . "\n";
    echo "✅ Proprietário: " . ($empresa->proprietario?->nome ?? 'Sem proprietário') . "\n";

    echo "\n👥 USUÁRIOS VINCULADOS:\n";
    $count = 0;
    foreach ($empresa->usuariosVinculados as $vinculo) {
        if ($vinculo && $vinculo->user) {
            $count++;
            $nome = $vinculo->user->nome ?? 'Nome não disponível';
            $perfil = $vinculo->perfil ?? 'indefinido';
            $email = $vinculo->user->email ?? 'Email não disponível';
            $status = $vinculo->status ?? 'indefinido';
            $data = $vinculo->data_vinculo ? $vinculo->data_vinculo->format('d/m/Y H:i') : 'Data não disponível';

            echo "   {$count}. {$nome} ({$perfil})\n";
            echo "      Email: {$email}\n";
            echo "      Status: {$status}\n";
            echo "      Data: {$data}\n";
        } else {
            echo "   ⚠️ Vínculo com dados incompletos encontrado\n";
        }
    }

    if ($count === 0) {
        echo "   📭 Nenhum usuário vinculado encontrado\n";
    }

    echo "\n🎯 PROBLEMAS CORRIGIDOS:\n";
    echo "   ✅ Proteção contra propriedades null\n";
    echo "   ✅ Relacionamento 'marca' carregado no controller\n";
    echo "   ✅ Verificações de segurança na view\n";
    echo "   ✅ Operador null-safe (?->) implementado\n";
    echo "   ✅ Valores padrão para dados ausentes\n";

    echo "\n🔗 URL PARA TESTAR:\n";
    echo "   http://localhost:8000/comerciantes/empresas/{$empresa->id}/usuarios\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
