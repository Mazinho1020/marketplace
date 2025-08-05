<?php

/**
 * CRIAR TABELAS DO MARKETPLACE
 * 
 * Este script cria as tabelas necessárias para o sistema de comerciantes
 */

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🏗️ CRIANDO TABELAS DO MARKETPLACE\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // 1. Criar tabela marcas
    echo "1️⃣ Criando tabela 'marcas'...\n";

    DB::statement("
        CREATE TABLE IF NOT EXISTS `marcas` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
          `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
          `descricao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `identidade_visual` json DEFAULT NULL,
          `pessoa_fisica_id` bigint(20) unsigned NOT NULL,
          `status` enum('ativa','inativa','suspensa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
          `configuracoes` json DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `marcas_slug_unique` (`slug`),
          KEY `marcas_pessoa_fisica_status_idx` (`pessoa_fisica_id`, `status`),
          KEY `marcas_slug_idx` (`slug`),
          CONSTRAINT `marcas_pessoa_fisica_id_foreign` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✅ Tabela 'marcas' criada com sucesso!\n\n";

    // 2. Criar tabela empresas_marketplace
    echo "2️⃣ Criando tabela 'empresas_marketplace'...\n";

    DB::statement("
        CREATE TABLE IF NOT EXISTS `empresas_marketplace` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `nome` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
          `nome_fantasia` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
          `marca_id` bigint(20) unsigned DEFAULT NULL,
          `proprietario_id` bigint(20) unsigned NOT NULL,
          `endereco_cep` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_logradouro` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `endereco_estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `website` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `status` enum('ativa','inativa','suspensa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
          `configuracoes` json DEFAULT NULL,
          `horario_funcionamento` json DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `empresas_marketplace_cnpj_unique` (`cnpj`),
          UNIQUE KEY `empresas_marketplace_slug_unique` (`slug`),
          KEY `empresas_marketplace_marca_status_idx` (`marca_id`, `status`),
          KEY `empresas_marketplace_proprietario_status_idx` (`proprietario_id`, `status`),
          CONSTRAINT `empresas_marketplace_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL,
          CONSTRAINT `empresas_marketplace_proprietario_id_foreign` FOREIGN KEY (`proprietario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✅ Tabela 'empresas_marketplace' criada com sucesso!\n\n";

    // 3. Criar tabela empresa_user_vinculos
    echo "3️⃣ Criando tabela 'empresa_user_vinculos'...\n";

    DB::statement("
        CREATE TABLE IF NOT EXISTS `empresa_user_vinculos` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `empresa_id` bigint(20) unsigned NOT NULL,
          `user_id` bigint(20) unsigned NOT NULL,
          `perfil` enum('proprietario','administrador','gerente','colaborador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colaborador',
          `status` enum('ativo','inativo','suspenso') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
          `permissoes` json DEFAULT NULL,
          `data_vinculo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `empresa_user_vinculo_unique` (`empresa_id`, `user_id`),
          KEY `empresa_user_vinculos_user_status_idx` (`user_id`, `status`),
          CONSTRAINT `empresa_user_vinculos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_marketplace` (`id`) ON DELETE CASCADE,
          CONSTRAINT `empresa_user_vinculos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✅ Tabela 'empresa_user_vinculos' criada com sucesso!\n\n";

    // 4. Inserir dados de exemplo
    echo "4️⃣ Inserindo dados de exemplo...\n";

    // Verificar se existe usuário com ID 3
    $usuario = DB::table('empresa_usuarios')->where('id', 3)->first();

    if ($usuario) {
        echo "   Usuário encontrado: {$usuario->nome}\n";

        // Inserir marca de exemplo
        $marcaId = DB::table('marcas')->insertGetId([
            'nome' => 'Pizzaria Tradição',
            'slug' => 'pizzaria-tradicao',
            'descricao' => 'Rede de pizzarias tradicionais com receitas familiares',
            'pessoa_fisica_id' => 3,
            'status' => 'ativa',
            'identidade_visual' => json_encode([
                'cor_primaria' => '#2ECC71',
                'cor_secundaria' => '#27AE60'
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "   ✅ Marca criada com ID: $marcaId\n";

        // Inserir empresas de exemplo
        $empresa1Id = DB::table('empresas_marketplace')->insertGetId([
            'nome' => 'Pizzaria Tradição Concórdia',
            'slug' => 'pizzaria-tradicao-concordia',
            'marca_id' => $marcaId,
            'proprietario_id' => 3,
            'endereco_cidade' => 'Concórdia',
            'endereco_estado' => 'SC',
            'telefone' => '(47) 3442-1234',
            'status' => 'ativa',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $empresa2Id = DB::table('empresas_marketplace')->insertGetId([
            'nome' => 'Pizzaria Tradição Praça Central',
            'slug' => 'pizzaria-tradicao-praca-central',
            'marca_id' => $marcaId,
            'proprietario_id' => 3,
            'endereco_cidade' => 'Concórdia',
            'endereco_estado' => 'SC',
            'telefone' => '(47) 3442-5678',
            'status' => 'ativa',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "   ✅ Empresa 1 criada com ID: $empresa1Id\n";
        echo "   ✅ Empresa 2 criada com ID: $empresa2Id\n";
    } else {
        echo "   ⚠️ Usuário com ID 3 não encontrado. Pulando inserção de dados de exemplo.\n";
    }

    // 5. Verificar tabelas criadas
    echo "\n5️⃣ Verificando tabelas criadas...\n";

    $tabelas = [
        'marcas' => 'Marcas/Bandeiras',
        'empresas_marketplace' => 'Empresas/Unidades',
        'empresa_user_vinculos' => 'Vínculos usuário-empresa'
    ];

    foreach ($tabelas as $tabela => $descricao) {
        try {
            $count = DB::table($tabela)->count();
            echo "   ✅ $tabela ($descricao): $count registros\n";
        } catch (Exception $e) {
            echo "   ❌ $tabela: ERRO - " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 TABELAS DO MARKETPLACE CRIADAS COM SUCESSO!\n";
    echo "\n📋 PRÓXIMOS PASSOS:\n";
    echo "   1. Ajustar os models para usar pessoa_fisica_id\n";
    echo "   2. Atualizar os relacionamentos\n";
    echo "   3. Testar o painel de comerciantes\n";
    echo "\n" . str_repeat("=", 52) . "\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
}
