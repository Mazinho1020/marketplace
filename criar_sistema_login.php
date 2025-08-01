<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "LIMPEZA E CRIAÇÃO DO SISTEMA DE LOGIN SIMPLIFICADO\n";
echo "==================================================\n\n";

try {
    // 1. Criar tabela de tipos de usuário administrativo
    echo "1. Criando tabela empresa_usuario_tipos...\n";

    DB::statement("DROP TABLE IF EXISTS empresa_usuario_tipos");

    DB::statement("
        CREATE TABLE empresa_usuario_tipos (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            codigo varchar(50) NOT NULL COMMENT 'Código único do tipo (admin, gerente, operador, etc)',
            nome varchar(100) NOT NULL COMMENT 'Nome de exibição',
            descricao text DEFAULT NULL COMMENT 'Descrição do tipo de usuário',
            nivel_acesso int(11) DEFAULT 1 COMMENT 'Nível hierárquico de acesso (1=mais baixo)',
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            deleted_at timestamp NULL DEFAULT NULL,
            sync_status varchar(20) DEFAULT 'pendente',
            sync_data timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY codigo (codigo),
            KEY idx_sync (sync_status,sync_data)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");

    // 2. Popular com tipos básicos
    echo "2. Inserindo tipos de usuário padrão...\n";

    DB::table('empresa_usuario_tipos')->insert([
        [
            'codigo' => 'admin',
            'nome' => 'Administrador',
            'descricao' => 'Acesso completo ao sistema',
            'nivel_acesso' => 100
        ],
        [
            'codigo' => 'gerente',
            'nome' => 'Gerente',
            'descricao' => 'Acesso a funções gerenciais',
            'nivel_acesso' => 80
        ],
        [
            'codigo' => 'supervisor',
            'nome' => 'Supervisor',
            'descricao' => 'Acesso a funções de supervisão',
            'nivel_acesso' => 60
        ],
        [
            'codigo' => 'operador',
            'nome' => 'Operador',
            'descricao' => 'Acesso operacional básico',
            'nivel_acesso' => 40
        ],
        [
            'codigo' => 'consulta',
            'nome' => 'Consulta',
            'descricao' => 'Acesso somente leitura',
            'nivel_acesso' => 20
        ]
    ]);

    // 3. Adicionar campo tipo_id à empresa_usuarios se não existir
    echo "3. Adicionando campo tipo_id à empresa_usuarios...\n";

    $hasColumn = DB::select("SHOW COLUMNS FROM empresa_usuarios LIKE 'tipo_id'");

    if (empty($hasColumn)) {
        DB::statement("ALTER TABLE empresa_usuarios ADD COLUMN tipo_id bigint(20) unsigned DEFAULT NULL");
        DB::statement("ALTER TABLE empresa_usuarios ADD CONSTRAINT fk_usuario_tipo FOREIGN KEY (tipo_id) REFERENCES empresa_usuario_tipos (id)");
    }

    // 4. Criar tabelas auxiliares
    echo "4. Criando tabelas auxiliares...\n";

    // Password resets
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_password_resets");
    DB::statement("
        CREATE TABLE empresa_usuarios_password_resets (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            token varchar(255) NOT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            expires_at timestamp NULL DEFAULT NULL,
            used tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY idx_email (email),
            KEY idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Login attempts
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_login_attempts");
    DB::statement("
        CREATE TABLE empresa_usuarios_login_attempts (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            success tinyint(1) NOT NULL DEFAULT 0,
            ip_address varchar(255) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            KEY idx_email_created (email, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Remember tokens
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_remember_tokens");
    DB::statement("
        CREATE TABLE empresa_usuarios_remember_tokens (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            token varchar(255) NOT NULL,
            expires_at timestamp NOT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            KEY idx_user_expires (user_id, expires_at),
            CONSTRAINT fk_remember_user FOREIGN KEY (user_id) REFERENCES empresa_usuarios (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Activity log
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_activity_log");
    DB::statement("
        CREATE TABLE empresa_usuarios_activity_log (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            action varchar(50) NOT NULL,
            description text DEFAULT NULL,
            ip_address varchar(255) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            KEY idx_user_created (user_id, created_at),
            KEY idx_action_created (action, created_at),
            CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES empresa_usuarios (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 5. Atualizar usuários existentes para tipo admin
    echo "5. Atualizando usuários existentes...\n";

    $adminTipoId = DB::table('empresa_usuario_tipos')->where('codigo', 'admin')->value('id');

    if ($adminTipoId) {
        $updated = DB::table('empresa_usuarios')
            ->whereNull('tipo_id')
            ->update(['tipo_id' => $adminTipoId]);
        echo "   Usuários atualizados: $updated\n";
    }

    echo "\n✅ SISTEMA DE LOGIN SIMPLIFICADO CRIADO COM SUCESSO!\n";
    echo "\nTabelas criadas:\n";
    echo "- empresa_usuario_tipos (5 tipos padrão)\n";
    echo "- empresa_usuarios_password_resets\n";
    echo "- empresa_usuarios_login_attempts\n";
    echo "- empresa_usuarios_remember_tokens\n";
    echo "- empresa_usuarios_activity_log\n";
    echo "\nCampo adicionado:\n";
    echo "- empresa_usuarios.tipo_id\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
