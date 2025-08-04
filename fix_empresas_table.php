<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ADICIONANDO COLUNAS FALTANTES NA TABELA EMPRESAS ===\n\n";

    // Colunas relacionadas a assinatura/subscription que estão sendo usadas no código
    $empresasColumns = [
        'subscription_plan' => 'ALTER TABLE empresas ADD COLUMN subscription_plan varchar(50) DEFAULT NULL AFTER plano',
        'subscription_ends_at' => 'ALTER TABLE empresas ADD COLUMN subscription_ends_at datetime DEFAULT NULL AFTER subscription_plan',
        'trial_ends_at' => 'ALTER TABLE empresas ADD COLUMN trial_ends_at datetime DEFAULT NULL AFTER subscription_ends_at',
        'uuid' => 'ALTER TABLE empresas ADD COLUMN uuid varchar(36) DEFAULT NULL AFTER id',
        'trade_name' => 'ALTER TABLE empresas ADD COLUMN trade_name varchar(255) DEFAULT NULL AFTER razao_social',
        'document' => 'ALTER TABLE empresas ADD COLUMN document varchar(20) DEFAULT NULL AFTER trade_name',
        'document_type' => 'ALTER TABLE empresas ADD COLUMN document_type enum("cpf","cnpj") DEFAULT "cnpj" AFTER document',
        'inscricao_estadual' => 'ALTER TABLE empresas ADD COLUMN inscricao_estadual varchar(20) DEFAULT NULL AFTER cnpj',
        'inscricao_municipal' => 'ALTER TABLE empresas ADD COLUMN inscricao_municipal varchar(20) DEFAULT NULL AFTER inscricao_estadual',
        'data_abertura' => 'ALTER TABLE empresas ADD COLUMN data_abertura date DEFAULT NULL AFTER inscricao_municipal',
        'celular' => 'ALTER TABLE empresas ADD COLUMN celular varchar(20) DEFAULT NULL AFTER telefone',
        'site' => 'ALTER TABLE empresas ADD COLUMN site varchar(255) DEFAULT NULL AFTER email',
        'logradouro' => 'ALTER TABLE empresas ADD COLUMN logradouro varchar(255) DEFAULT NULL AFTER endereco',
        'numero' => 'ALTER TABLE empresas ADD COLUMN numero varchar(10) DEFAULT NULL AFTER logradouro',
        'complemento' => 'ALTER TABLE empresas ADD COLUMN complemento varchar(100) DEFAULT NULL AFTER numero',
        'bairro' => 'ALTER TABLE empresas ADD COLUMN bairro varchar(100) DEFAULT NULL AFTER complemento',
        'uf' => 'ALTER TABLE empresas ADD COLUMN uf varchar(2) DEFAULT NULL AFTER estado',
        'pais' => 'ALTER TABLE empresas ADD COLUMN pais varchar(100) DEFAULT "Brasil" AFTER uf',
        'regime_tributario' => 'ALTER TABLE empresas ADD COLUMN regime_tributario varchar(50) DEFAULT NULL AFTER pais',
        'optante_simples' => 'ALTER TABLE empresas ADD COLUMN optante_simples tinyint(1) DEFAULT 0 AFTER regime_tributario',
        'incentivo_fiscal' => 'ALTER TABLE empresas ADD COLUMN incentivo_fiscal tinyint(1) DEFAULT 0 AFTER optante_simples',
        'cnae_principal' => 'ALTER TABLE empresas ADD COLUMN cnae_principal varchar(10) DEFAULT NULL AFTER incentivo_fiscal',
        'banco_nome' => 'ALTER TABLE empresas ADD COLUMN banco_nome varchar(100) DEFAULT NULL AFTER cnae_principal',
        'banco_agencia' => 'ALTER TABLE empresas ADD COLUMN banco_agencia varchar(10) DEFAULT NULL AFTER banco_nome',
        'banco_conta' => 'ALTER TABLE empresas ADD COLUMN banco_conta varchar(20) DEFAULT NULL AFTER banco_agencia',
        'banco_tipo_conta' => 'ALTER TABLE empresas ADD COLUMN banco_tipo_conta enum("corrente","poupanca") DEFAULT NULL AFTER banco_conta',
        'banco_pix' => 'ALTER TABLE empresas ADD COLUMN banco_pix varchar(255) DEFAULT NULL AFTER banco_tipo_conta',
        'moeda_padrao' => 'ALTER TABLE empresas ADD COLUMN moeda_padrao varchar(3) DEFAULT "BRL" AFTER banco_pix',
        'fuso_horario' => 'ALTER TABLE empresas ADD COLUMN fuso_horario varchar(50) DEFAULT "America/Sao_Paulo" AFTER moeda_padrao',
        'idioma_padrao' => 'ALTER TABLE empresas ADD COLUMN idioma_padrao varchar(5) DEFAULT "pt-BR" AFTER fuso_horario',
        'logo_url' => 'ALTER TABLE empresas ADD COLUMN logo_url varchar(255) DEFAULT NULL AFTER idioma_padrao',
        'cor_principal' => 'ALTER TABLE empresas ADD COLUMN cor_principal varchar(7) DEFAULT "#007bff" AFTER logo_url',
        'ativo' => 'ALTER TABLE empresas ADD COLUMN ativo tinyint(1) DEFAULT 1 AFTER cor_principal',
        'data_cadastro' => 'ALTER TABLE empresas ADD COLUMN data_cadastro datetime DEFAULT NULL AFTER ativo',
        'data_atualizacao' => 'ALTER TABLE empresas ADD COLUMN data_atualizacao datetime DEFAULT NULL AFTER data_cadastro',
        'sync_data' => 'ALTER TABLE empresas ADD COLUMN sync_data datetime DEFAULT NULL AFTER data_atualizacao',
        'sync_hash' => 'ALTER TABLE empresas ADD COLUMN sync_hash varchar(64) DEFAULT NULL AFTER sync_data',
        'sync_status' => 'ALTER TABLE empresas ADD COLUMN sync_status enum("pendente","sincronizado","erro") DEFAULT "pendente" AFTER sync_hash'
    ];

    $added = 0;
    $existing = 0;
    $errors = 0;

    foreach ($empresasColumns as $column => $sql) {
        try {
            // Verificar se a coluna já existe
            $exists = DB::select("SHOW COLUMNS FROM empresas LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
                $added++;
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
                $existing++;
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    echo "\n=== RESUMO ===\n";
    echo "✅ Colunas adicionadas: {$added}\n";
    echo "ℹ️  Colunas já existiam: {$existing}\n";
    echo "❌ Erros: {$errors}\n";

    // Verificar estrutura final
    echo "\n=== ESTRUTURA FINAL DA TABELA EMPRESAS ===\n";
    $finalStructure = DB::select('DESCRIBE empresas');
    foreach ($finalStructure as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }

    echo "\n🎉 Atualização da tabela empresas concluída!\n";
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
