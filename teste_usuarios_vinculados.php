<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\DB;

echo "🧪 TESTE DE USUÁRIOS VINCULADOS\n";
echo "=" . str_repeat("=", 35) . "\n\n";

try {
    // Buscar primeira empresa
    $empresa = Empresa::with(['usuariosVinculados', 'proprietario'])->first();

    if ($empresa) {
        echo "✅ Empresa encontrada: {$empresa->nome}\n";
        echo "   Proprietário: {$empresa->proprietario->nome}\n";
        echo "   Usuários vinculados: " . $empresa->usuariosVinculados->count() . "\n\n";

        // Se não há usuários vinculados, vamos criar um vínculo de teste
        if ($empresa->usuariosVinculados->count() === 0) {
            echo "🔧 Criando vínculo proprietário automaticamente...\n";

            // Verificar se existe a tabela
            $hasTable = DB::getSchemaBuilder()->hasTable('empresa_user_vinculos');
            echo "   Tabela empresa_user_vinculos: " . ($hasTable ? "Existe" : "Não existe") . "\n";

            if ($hasTable) {
                // Criar vínculo do proprietário
                DB::table('empresa_user_vinculos')->insertOrIgnore([
                    'empresa_id' => $empresa->id,
                    'user_id' => $empresa->proprietario_id,
                    'perfil' => 'proprietario',
                    'status' => 'ativo',
                    'data_vinculo' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                echo "✅ Vínculo proprietário criado!\n";

                // Recarregar empresa
                $empresa->load('usuariosVinculados');
                echo "   Usuários vinculados após criação: " . $empresa->usuariosVinculados->count() . "\n";
            }
        }

        // Listar usuários vinculados
        if ($empresa->usuariosVinculados->count() > 0) {
            echo "\n👥 USUÁRIOS VINCULADOS:\n";
            foreach ($empresa->usuariosVinculados as $vinculo) {
                echo "   - {$vinculo->nome} ({$vinculo->pivot->perfil}) - {$vinculo->pivot->status}\n";
            }
        }
    } else {
        echo "❌ Nenhuma empresa encontrada\n";
    }

    echo "\n🎯 Teste agora: http://localhost:8000/comerciantes/empresas/{$empresa->id}/usuarios\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
}

echo "\n" . str_repeat("=", 37) . "\n";
