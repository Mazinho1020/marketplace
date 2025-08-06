<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG RELACIONAMENTO USUÁRIOS VINCULADOS ===\n";

// Verificar estrutura das tabelas
try {
    echo "1. Verificando tabela empresa_user_vinculos...\n";
    $estrutura = DB::select("DESCRIBE empresa_user_vinculos");
    if (empty($estrutura)) {
        echo "❌ Tabela empresa_user_vinculos não existe!\n";

        echo "\n2. Criando tabela empresa_user_vinculos...\n";
        DB::statement("
            CREATE TABLE IF NOT EXISTS `empresa_user_vinculos` (
                `id` int NOT NULL AUTO_INCREMENT,
                `empresa_id` int NOT NULL,
                `user_id` int NOT NULL,
                `perfil` enum('proprietario','administrador','gerente','colaborador') NOT NULL DEFAULT 'colaborador',
                `status` enum('ativo','inativo','suspenso') NOT NULL DEFAULT 'ativo',
                `permissoes` json DEFAULT NULL,
                `data_vinculo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_empresa_user` (`empresa_id`, `user_id`),
                KEY `idx_empresa_id` (`empresa_id`),
                KEY `idx_user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Tabela empresa_user_vinculos criada!\n";
    } else {
        echo "✅ Tabela empresa_user_vinculos existe!\n";
        foreach ($estrutura as $coluna) {
            echo "  - {$coluna->Field}: {$coluna->Type}\n";
        }
    }

    echo "\n3. Verificando dados existentes...\n";
    $vinculos = DB::table('empresa_user_vinculos')->get();
    echo "Total de vínculos: " . $vinculos->count() . "\n";

    if ($vinculos->count() === 0) {
        echo "\n4. Criando vínculos de teste...\n";

        // Buscar primeira empresa e usuários
        $empresa = Empresa::first();
        $usuarios = EmpresaUsuario::limit(3)->get();

        if ($empresa && $usuarios->count() > 0) {
            echo "Empresa: {$empresa->nome_fantasia} (ID: {$empresa->id})\n";

            foreach ($usuarios as $index => $usuario) {
                $perfil = match ($index) {
                    0 => 'proprietario',
                    1 => 'administrador',
                    default => 'colaborador'
                };

                DB::table('empresa_user_vinculos')->insert([
                    'empresa_id' => $empresa->id,
                    'user_id' => $usuario->id,
                    'perfil' => $perfil,
                    'status' => 'ativo',
                    'permissoes' => json_encode(['produtos.view', 'vendas.view']),
                    'data_vinculo' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Vínculo criado: {$usuario->nome} como {$perfil}\n";
            }
        }
    }

    echo "\n5. Testando relacionamento...\n";
    $empresa = Empresa::with('usuariosVinculados')->first();

    if ($empresa) {
        echo "Empresa: {$empresa->nome_fantasia}\n";
        echo "Usuários vinculados: " . $empresa->usuariosVinculados->count() . "\n";

        foreach ($empresa->usuariosVinculados as $vinculo) {
            echo "  - {$vinculo->nome} ({$vinculo->email}) - Perfil: {$vinculo->pivot->perfil}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}

echo "\n=== FIM DO DEBUG ===\n";
