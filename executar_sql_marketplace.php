<?php

/**
 * EXECUTAR SQL DO MARKETPLACE DIRETAMENTE
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🏗️ EXECUTANDO CRIAÇÃO DAS TABELAS\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Desabilitar verificações de chave estrangeira temporariamente
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    echo "1️⃣ Criando tabela 'marcas'...\n";

    // 1. Criar tabela marcas
    $sql1 = "
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
      KEY `marcas_slug_idx` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    DB::statement($sql1);
    echo "   ✅ Tabela 'marcas' criada!\n";

    echo "\n2️⃣ Criando tabela 'empresas_marketplace'...\n";

    // 2. Criar tabela empresas_marketplace
    $sql2 = "
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
      KEY `empresas_marketplace_proprietario_status_idx` (`proprietario_id`, `status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    DB::statement($sql2);
    echo "   ✅ Tabela 'empresas_marketplace' criada!\n";

    echo "\n3️⃣ Criando tabela 'empresa_user_vinculos'...\n";

    // 3. Criar tabela empresa_user_vinculos
    $sql3 = "
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
      KEY `empresa_user_vinculos_user_status_idx` (`user_id`, `status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    DB::statement($sql3);
    echo "   ✅ Tabela 'empresa_user_vinculos' criada!\n";

    echo "\n4️⃣ Adicionando chaves estrangeiras...\n";

    // Adicionar foreign keys separadamente
    try {
        DB::statement('ALTER TABLE `marcas` ADD CONSTRAINT `marcas_pessoa_fisica_id_foreign` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE');
        echo "   ✅ FK marcas -> empresa_usuarios\n";
    } catch (Exception $e) {
        echo "   ⚠️ FK marcas já existe ou erro: " . substr($e->getMessage(), 0, 100) . "\n";
    }

    try {
        DB::statement('ALTER TABLE `empresas_marketplace` ADD CONSTRAINT `empresas_marketplace_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL');
        echo "   ✅ FK empresas_marketplace -> marcas\n";
    } catch (Exception $e) {
        echo "   ⚠️ FK empresas_marketplace -> marcas já existe ou erro\n";
    }

    try {
        DB::statement('ALTER TABLE `empresas_marketplace` ADD CONSTRAINT `empresas_marketplace_proprietario_id_foreign` FOREIGN KEY (`proprietario_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE');
        echo "   ✅ FK empresas_marketplace -> empresa_usuarios\n";
    } catch (Exception $e) {
        echo "   ⚠️ FK empresas_marketplace -> empresa_usuarios já existe ou erro\n";
    }

    try {
        DB::statement('ALTER TABLE `empresa_user_vinculos` ADD CONSTRAINT `empresa_user_vinculos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_marketplace` (`id`) ON DELETE CASCADE');
        echo "   ✅ FK empresa_user_vinculos -> empresas_marketplace\n";
    } catch (Exception $e) {
        echo "   ⚠️ FK empresa_user_vinculos -> empresas_marketplace já existe ou erro\n";
    }

    try {
        DB::statement('ALTER TABLE `empresa_user_vinculos` ADD CONSTRAINT `empresa_user_vinculos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `empresa_usuarios` (`id`) ON DELETE CASCADE');
        echo "   ✅ FK empresa_user_vinculos -> empresa_usuarios\n";
    } catch (Exception $e) {
        echo "   ⚠️ FK empresa_user_vinculos -> empresa_usuarios já existe ou erro\n";
    }

    // Reabilitar verificações de chave estrangeira
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "\n5️⃣ Inserindo dados de exemplo...\n";

    // Verificar se já existem dados
    $marcaExiste = DB::table('marcas')->where('slug', 'pizzaria-tradicao')->exists();

    if (!$marcaExiste) {
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

        echo "   ✅ Marca criada: ID $marcaId\n";

        // Inserir empresas de exemplo
        DB::table('empresas_marketplace')->insert([
            [
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
            ],
            [
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
            ]
        ]);

        echo "   ✅ 2 empresas criadas\n";
    } else {
        echo "   ℹ️ Dados de exemplo já existem\n";
    }

    echo "\n6️⃣ Verificação final...\n";

    $marcas = DB::table('marcas')->count();
    $empresas = DB::table('empresas_marketplace')->count();
    $vinculos = DB::table('empresa_user_vinculos')->count();

    echo "   📊 Marcas: $marcas\n";
    echo "   📊 Empresas: $empresas\n";
    echo "   📊 Vínculos: $vinculos\n";

    echo "\n🎉 TABELAS CRIADAS COM SUCESSO!\n";
    echo "\n🚀 PRÓXIMOS PASSOS:\n";
    echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
    echo "   2. Faça login com um usuário da tabela empresa_usuarios\n";
    echo "   3. Explore o dashboard e funcionalidades\n";
} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 52) . "\n";
