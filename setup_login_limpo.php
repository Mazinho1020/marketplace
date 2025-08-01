<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "LIMPEZA CUIDADOSA E CONFIGURAÇÃO LOGIN\n";
echo "=====================================\n\n";

try {
    // 1. Verificar foreign keys existentes
    echo "1. Verificando foreign keys em empresa_usuarios...\n";

    $fks = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'empresa_usuarios' 
          AND REFERENCED_TABLE_NAME IS NOT NULL
    ");

    foreach ($fks as $fk) {
        echo "   - {$fk->CONSTRAINT_NAME}\n";
        try {
            DB::statement("ALTER TABLE empresa_usuarios DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            echo "     ✅ Removida\n";
        } catch (Exception $e) {
            echo "     ⚠️ Erro ao remover: {$e->getMessage()}\n";
        }
    }

    // 2. Verificar se tipo_id já existe
    echo "\n2. Verificando campo tipo_id...\n";
    $hasColumn = DB::select("SHOW COLUMNS FROM empresa_usuarios LIKE 'tipo_id'");
    echo "   Campo tipo_id existe: " . (count($hasColumn) > 0 ? "✅ SIM" : "❌ NÃO") . "\n";

    // 3. Criar/recriar tabela de tipos
    echo "\n3. Criando tabela empresa_usuario_tipos...\n";

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

    // 4. Popular com tipos
    echo "4. Inserindo tipos de usuário...\n";

    DB::table('empresa_usuario_tipos')->insert([
        ['codigo' => 'admin', 'nome' => 'Administrador', 'descricao' => 'Acesso completo', 'nivel_acesso' => 100],
        ['codigo' => 'gerente', 'nome' => 'Gerente', 'descricao' => 'Acesso gerencial', 'nivel_acesso' => 80],
        ['codigo' => 'supervisor', 'nome' => 'Supervisor', 'descricao' => 'Supervisão', 'nivel_acesso' => 60],
        ['codigo' => 'operador', 'nome' => 'Operador', 'descricao' => 'Operacional', 'nivel_acesso' => 40],
        ['codigo' => 'consulta', 'nome' => 'Consulta', 'descricao' => 'Somente leitura', 'nivel_acesso' => 20]
    ]);

    // 5. Atualizar campo tipo_id se não existir
    if (count($hasColumn) == 0) {
        echo "5. Adicionando campo tipo_id...\n";
        DB::statement("ALTER TABLE empresa_usuarios ADD COLUMN tipo_id bigint(20) unsigned DEFAULT NULL");
    }

    // 6. Definir todos os usuários como admin por padrão
    echo "6. Configurando usuários como admin...\n";
    $adminId = DB::table('empresa_usuario_tipos')->where('codigo', 'admin')->value('id');
    $updated = DB::table('empresa_usuarios')->update(['tipo_id' => $adminId]);
    echo "   Usuários configurados: $updated\n";

    // 7. Criar tabelas auxiliares
    echo "\n7. Criando tabelas auxiliares...\n";

    // Login attempts
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_login_attempts");
    DB::statement("
        CREATE TABLE empresa_usuarios_login_attempts (
            id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
            email varchar(255) NOT NULL,
            success tinyint(1) DEFAULT 0,
            ip_address varchar(255) NULL,
            user_agent text NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email_created (email, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "   ✅ empresa_usuarios_login_attempts\n";

    // Password resets  
    DB::statement("DROP TABLE IF EXISTS empresa_usuarios_password_resets");
    DB::statement("
        CREATE TABLE empresa_usuarios_password_resets (
            id bigint(20) unsigned AUTO_INCREMENT PRIMARY KEY,
            email varchar(255) NOT NULL,
            token varchar(255) NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            expires_at timestamp NULL,
            used tinyint(1) DEFAULT 0,
            INDEX idx_email (email),
            INDEX idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "   ✅ empresa_usuarios_password_resets\n";

    echo "\n✅ SISTEMA DE LOGIN CONFIGURADO COM SUCESSO!\n";
    echo "\nResumo:\n";
    echo "- ✅ Tabela empresa_usuario_tipos criada (5 tipos)\n";
    echo "- ✅ Campo tipo_id adicionado/configurado\n";
    echo "- ✅ Usuários definidos como admin\n";
    echo "- ✅ Tabelas auxiliares criadas\n";

    echo "\nTipos de usuário disponíveis:\n";
    $tipos = DB::table('empresa_usuario_tipos')->orderBy('nivel_acesso', 'desc')->get();
    foreach ($tipos as $tipo) {
        echo "- {$tipo->codigo}: {$tipo->nome} (nível {$tipo->nivel_acesso})\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
