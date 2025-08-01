<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "VERIFICAÇÃO E CRIAÇÃO SIMPLIFICADA\n";
echo "==================================\n\n";

try {
    // 1. Verificar se empresa_usuarios existe
    $tables = DB::select("SHOW TABLES LIKE 'empresa_usuarios'");
    echo "1. Tabela empresa_usuarios existe: " . (count($tables) > 0 ? "✅ SIM" : "❌ NÃO") . "\n";

    if (count($tables) > 0) {
        $columns = DB::select("DESCRIBE empresa_usuarios");
        echo "   Colunas da tabela:\n";
        foreach ($columns as $col) {
            echo "   - {$col->Field} ({$col->Type})\n";
        }
    }

    // 2. Criar tabela de tipos (sem foreign key primeiro)
    echo "\n2. Criando empresa_usuario_tipos...\n";

    DB::statement("DROP TABLE IF EXISTS empresa_usuario_tipos");
    DB::statement("
        CREATE TABLE empresa_usuario_tipos (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            codigo varchar(50) NOT NULL,
            nome varchar(100) NOT NULL,
            descricao text DEFAULT NULL,
            nivel_acesso int(11) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY codigo (codigo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 3. Popular tipos
    echo "3. Inserindo tipos de usuário...\n";

    DB::table('empresa_usuario_tipos')->insert([
        ['codigo' => 'admin', 'nome' => 'Administrador', 'nivel_acesso' => 100],
        ['codigo' => 'gerente', 'nome' => 'Gerente', 'nivel_acesso' => 80],
        ['codigo' => 'supervisor', 'nome' => 'Supervisor', 'nivel_acesso' => 60],
        ['codigo' => 'operador', 'nome' => 'Operador', 'nivel_acesso' => 40],
        ['codigo' => 'consulta', 'nome' => 'Consulta', 'nivel_acesso' => 20]
    ]);

    // 4. Adicionar tipo_id se não existir
    if (count($tables) > 0) {
        echo "4. Adicionando campo tipo_id...\n";

        $hasColumn = DB::select("SHOW COLUMNS FROM empresa_usuarios LIKE 'tipo_id'");

        if (empty($hasColumn)) {
            DB::statement("ALTER TABLE empresa_usuarios ADD COLUMN tipo_id bigint(20) unsigned DEFAULT NULL");
        }

        // 5. Atualizar usuários existentes
        echo "5. Atualizando usuários para admin...\n";
        $adminId = DB::table('empresa_usuario_tipos')->where('codigo', 'admin')->value('id');
        $updated = DB::table('empresa_usuarios')->whereNull('tipo_id')->update(['tipo_id' => $adminId]);
        echo "   Usuários atualizados: $updated\n";
    }

    // 6. Criar tabelas auxiliares (sem foreign keys por enquanto)
    echo "6. Criando tabelas auxiliares...\n";

    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_login_attempts");
    DB::statement("
        CREATE TABLE empresa_usuarios_login_attempts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            success tinyint(1) NOT NULL DEFAULT 0,
            ip_address varchar(255) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_email_created (email, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_password_resets");
    DB::statement("
        CREATE TABLE empresa_usuarios_password_resets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            token varchar(255) NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            expires_at timestamp NULL DEFAULT NULL,
            used tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    echo "\n✅ SISTEMA BÁSICO CRIADO COM SUCESSO!\n";
    echo "\nPróximos passos:\n";
    echo "1. Implementar LoginController\n";
    echo "2. Criar views de login\n";
    echo "3. Configurar rotas\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
