<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CRIANDO AS 18 TABELAS COM ERRO ===\n\n";

    // Lista das 18 tabelas que falharam
    $failedTables = [
        'empresa_cache',
        'empresa_certificados',
        'empresa_cnaes',
        'empresa_config_seguranca',
        'empresa_logs_permissoes',
        'empresa_papeis',
        'empresa_papel_permissoes',
        'empresa_permissoes',
        'empresa_socios',
        'empresa_usuarios_activity_log',
        'empresa_usuarios_remember_tokens',
        'empresa_usuarios_security_settings',
        'empresa_usuario_empresas',
        'empresa_usuario_papeis',
        'empresa_usuario_permissoes',
        'login',
        'produtos',
        'produto_importar'
    ];

    // Ler arquivo SQL completo
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2.sql';
    $sqlContent = file_get_contents($sqlFile);

    $created = 0;
    $errors = 0;

    foreach ($failedTables as $tableName) {
        echo "🔧 Tentando criar: {$tableName}\n";

        try {
            // Verificar se já existe
            $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($exists)) {
                echo "ℹ️  Já existe: {$tableName}\n";
                continue;
            }

            // Buscar estrutura específica da tabela no SQL
            $pattern = "/-- Copiando estrutura para tabela.*?{$tableName}.*?CREATE TABLE.*?;/s";
            if (preg_match($pattern, $sqlContent, $matches)) {

                // Extrair apenas o CREATE TABLE
                if (preg_match('/CREATE TABLE.*?;/s', $matches[0], $createMatch)) {
                    $createStatement = $createMatch[0];

                    // Remover constraints que podem causar problemas
                    $createStatement = str_replace('CHECK (json_valid(`credenciais`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`configuracoes`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`recursos`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`limites`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`metadados`))', '', $createStatement);

                    // Remover foreign keys temporariamente
                    $createStatement = preg_replace('/,\s*CONSTRAINT.*?REFERENCES.*?\)/', '', $createStatement);
                    $createStatement = preg_replace('/,\s*FOREIGN KEY.*?\)/', '', $createStatement);

                    // Limpar vírgulas duplas que podem ter sobrado
                    $createStatement = preg_replace('/,\s*,/', ',', $createStatement);
                    $createStatement = preg_replace('/,\s*\)/', ')', $createStatement);

                    // Executar criação
                    DB::statement($createStatement);
                    echo "✅ Criada: {$tableName}\n";
                    $created++;
                } else {
                    echo "❌ Não encontrou CREATE para: {$tableName}\n";
                    $errors++;
                }
            } else {
                echo "❌ Não encontrou estrutura para: {$tableName}\n";
                $errors++;
            }
        } catch (Exception $e) {
            $errorMsg = substr($e->getMessage(), 0, 150);
            echo "❌ Erro {$tableName}: {$errorMsg}...\n";
            $errors++;
        }

        echo "\n";
    }

    echo "=== RESUMO ===\n";
    echo "✅ Tabelas criadas: {$created}\n";
    echo "❌ Erros: {$errors}\n";
    echo "📊 Total tentativas: " . count($failedTables) . "\n";

    if ($created > 0) {
        echo "\n🎉 Sucesso! {$created} tabelas foram criadas!\n";
        echo "🔍 Execute 'php analyze_all_tables.php' para ver o resultado\n";
    }

    if ($errors > 0) {
        echo "\n⚠️  Algumas tabelas ainda têm problemas. Pode ser necessário ajustar manualmente.\n";
    }
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
