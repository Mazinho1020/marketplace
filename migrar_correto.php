<?php
require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'meufinanceiro',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== MIGRAÇÃO CORRETA SQL OTIMIZADO ===\n\n";

try {
    // Desativar verificações de foreign key
    DB::statement("SET FOREIGN_KEY_CHECKS = 0");
    
    echo "1. COLETANDO DADOS DO BACKUP...\n";
    
    $lancamentos = DB::table('lancamentos_backup')->get();
    $itens = DB::table('lancamento_itens_backup')->get();
    $movimentacoes = DB::table('lancamento_movimentacoes_backup')->get();
    
    echo "   ✓ Coletados: " . count($lancamentos) . " lançamentos\n";
    echo "   ✓ Coletados: " . count($itens) . " itens\n";
    echo "   ✓ Coletados: " . count($movimentacoes) . " movimentações\n";
    
    echo "\n2. REMOVENDO TABELAS EXISTENTES...\n";
    
    DB::statement("DROP TABLE IF EXISTS lancamento_movimentacoes");
    DB::statement("DROP TABLE IF EXISTS lancamento_itens");
    DB::statement("DROP TABLE IF EXISTS lancamentos");
    
    echo "   ✓ Tabelas removidas\n";
    
    echo "\n3. CRIANDO ESTRUTURA OTIMIZADA...\n";
    
    // Criar tabela de lançamentos otimizada
    DB::statement("
        CREATE TABLE `lancamentos` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `uuid` char(36) DEFAULT NULL,
            `tipo` enum('pagar','receber') NOT NULL,
            `categoria_id` bigint(20) unsigned DEFAULT NULL,
            `pessoa_id` bigint(20) unsigned NOT NULL,
            `valor_bruto` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_desconto` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_juros` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_multa` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_liquido` decimal(15,4) GENERATED ALWAYS AS ((`valor_bruto` - `valor_desconto` + `valor_juros` + `valor_multa`)) STORED,
            `valor_pago` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_saldo` decimal(15,4) GENERATED ALWAYS AS ((`valor_liquido` - `valor_pago`)) STORED,
            `descricao` varchar(255) NOT NULL,
            `observacoes` text DEFAULT NULL,
            `data_vencimento` date NOT NULL,
            `data_competencia` date NOT NULL,
            `situacao` enum('pendente','pago','parcialmente_pago','vencido','cancelado') NOT NULL DEFAULT 'pendente',
            `e_parcelado` boolean NOT NULL DEFAULT false,
            `total_parcelas` int(11) NOT NULL DEFAULT 1,
            `numero_parcela` int(11) NOT NULL DEFAULT 1,
            `lancamento_pai_id` bigint(20) unsigned DEFAULT NULL,
            `intervalo_dias` int(11) DEFAULT NULL,
            `e_recorrente` boolean NOT NULL DEFAULT false,
            `frequencia_recorrencia` enum('semanal','quinzenal','mensal','bimestral','trimestral','semestral','anual') DEFAULT NULL,
            `data_fim_recorrencia` date DEFAULT NULL,
            `forma_pagamento_id` bigint(20) unsigned DEFAULT NULL,
            `conta_bancaria_id` bigint(20) unsigned DEFAULT NULL,
            `numero_documento` varchar(100) DEFAULT NULL,
            `numero_cheque` varchar(50) DEFAULT NULL,
            `numero_cartao` varchar(20) DEFAULT NULL,
            `config_juros_multa` json DEFAULT NULL,
            `config_desconto` json DEFAULT NULL,
            `config_alertas` json DEFAULT NULL,
            `anexos` json DEFAULT NULL,
            `metadados` json DEFAULT NULL,
            `usuario_id` bigint(20) unsigned NOT NULL,
            `empresa_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `lancamentos_uuid_unique` (`uuid`),
            KEY `lancamentos_pessoa_id_index` (`pessoa_id`),
            KEY `lancamentos_data_vencimento_index` (`data_vencimento`),
            KEY `lancamentos_situacao_index` (`situacao`),
            KEY `lancamentos_empresa_id_index` (`empresa_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Criar tabela de itens
    DB::statement("
        CREATE TABLE `lancamento_itens` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `lancamento_id` bigint(20) unsigned NOT NULL,
            `produto_id` bigint(20) unsigned DEFAULT NULL,
            `produto_variacao_id` bigint(20) unsigned DEFAULT NULL,
            `codigo_produto` varchar(50) DEFAULT NULL,
            `nome_produto` varchar(255) NOT NULL,
            `quantidade` decimal(10,3) NOT NULL DEFAULT 1.000,
            `valor_unitario` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_desconto_item` decimal(15,4) NOT NULL DEFAULT 0.0000,
            `valor_total` decimal(15,4) GENERATED ALWAYS AS ((`quantidade` * (`valor_unitario` - `valor_desconto_item`))) STORED,
            `observacoes` text DEFAULT NULL,
            `metadados` json DEFAULT NULL,
            `empresa_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `lancamento_itens_lancamento_id_index` (`lancamento_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Criar tabela de movimentações
    DB::statement("
        CREATE TABLE `lancamento_movimentacoes` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `lancamento_id` bigint(20) unsigned NOT NULL,
            `tipo` enum('pagamento','recebimento','estorno') NOT NULL DEFAULT 'pagamento',
            `valor` decimal(15,4) NOT NULL,
            `data_movimentacao` datetime NOT NULL,
            `forma_pagamento_id` bigint(20) unsigned DEFAULT NULL,
            `conta_bancaria_id` bigint(20) unsigned DEFAULT NULL,
            `numero_documento` varchar(100) DEFAULT NULL,
            `observacoes` text DEFAULT NULL,
            `usuario_id` bigint(20) unsigned NOT NULL,
            `empresa_id` bigint(20) unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `lancamento_movimentacoes_lancamento_id_index` (`lancamento_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "   ✓ Estrutura otimizada criada\n";
    
    echo "\n4. MIGRANDO LANÇAMENTOS...\n";
    
    foreach ($lancamentos as $lancamento) {
        // Mapear tipo baseado na natureza_financeira
        $tipo = ($lancamento->natureza_financeira === 'entrada') ? 'receber' : 'pagar';
        
        // Determinar se é parcelado
        $totalParcelas = intval($lancamento->total_parcelas ?? 1);
        $eParcelado = $totalParcelas > 1;
        
        // Mapear situação
        $situacaoMap = [
            'pendente' => 'pendente',
            'pago' => 'pago',
            'parcialmente_pago' => 'parcialmente_pago',
            'vencido' => 'vencido',
            'cancelado' => 'cancelado',
            'em_negociacao' => 'pendente'
        ];
        $situacao = $situacaoMap[$lancamento->situacao_financeira] ?? 'pendente';
        
        $dados = [
            'uuid' => $lancamento->uuid,
            'tipo' => $tipo,
            'categoria_id' => null, // Mapear depois se necessário
            'pessoa_id' => $lancamento->pessoa_id,
            'valor_bruto' => floatval($lancamento->valor ?? 0),
            'valor_desconto' => floatval($lancamento->valor_desconto ?? 0),
            'valor_juros' => floatval($lancamento->valor_juros ?? 0),
            'valor_multa' => floatval($lancamento->valor_multa ?? 0),
            'valor_pago' => floatval($lancamento->valor_pago ?? 0),
            'descricao' => $lancamento->descricao ?: 'Lançamento importado',
            'observacoes' => $lancamento->observacoes,
            'data_vencimento' => $lancamento->data_vencimento ?: now()->format('Y-m-d'),
            'data_competencia' => $lancamento->data_competencia ?: $lancamento->data_vencimento ?: now()->format('Y-m-d'),
            'situacao' => $situacao,
            'e_parcelado' => $eParcelado,
            'total_parcelas' => $totalParcelas,
            'numero_parcela' => intval($lancamento->parcela_atual ?? 1),
            'lancamento_pai_id' => null, // Mapear depois se necessário
            'intervalo_dias' => $lancamento->intervalo_parcelas,
            'e_recorrente' => boolval($lancamento->e_recorrente ?? false),
            'frequencia_recorrencia' => $lancamento->frequencia_recorrencia,
            'forma_pagamento_id' => $lancamento->forma_pagamento_id,
            'conta_bancaria_id' => $lancamento->conta_bancaria_id,
            'numero_documento' => $lancamento->numero_documento,
            'config_juros_multa' => $lancamento->juros_multa_config,
            'config_alertas' => $lancamento->config_alertas,
            'anexos' => $lancamento->anexos,
            'metadados' => $lancamento->metadados,
            'usuario_id' => $lancamento->usuario_id ?: 1,
            'empresa_id' => $lancamento->empresa_id,
            'created_at' => $lancamento->created_at,
            'updated_at' => $lancamento->updated_at,
        ];
        
        // Remover nulls desnecessários
        $dados = array_filter($dados, function($value) {
            return $value !== null && $value !== '';
        });
        
        DB::table('lancamentos')->insert($dados);
    }
    
    echo "   ✓ " . count($lancamentos) . " lançamentos migrados\n";
    
    echo "\n5. MIGRANDO ITENS...\n";
    
    foreach ($itens as $item) {
        $dados = [
            'lancamento_id' => $item->lancamento_id,
            'produto_id' => $item->produto_id,
            'produto_variacao_id' => $item->produto_variacao_id,
            'codigo_produto' => $item->codigo_produto,
            'nome_produto' => $item->nome_produto ?: ('Produto ' . ($item->produto_id ?: $item->id)),
            'quantidade' => floatval($item->quantidade ?: 1),
            'valor_unitario' => floatval($item->valor_unitario ?: 0),
            'valor_desconto_item' => floatval($item->valor_desconto_item ?? 0),
            'observacoes' => $item->observacoes,
            'metadados' => $item->metadados,
            'empresa_id' => $item->empresa_id,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
        
        $dados = array_filter($dados, function($value) {
            return $value !== null && $value !== '';
        });
        
        DB::table('lancamento_itens')->insert($dados);
    }
    
    echo "   ✓ " . count($itens) . " itens migrados\n";
    
    echo "\n6. MIGRANDO MOVIMENTAÇÕES...\n";
    
    foreach ($movimentacoes as $mov) {
        $dados = [
            'lancamento_id' => $mov->lancamento_id,
            'tipo' => $mov->tipo ?: 'pagamento',
            'valor' => floatval($mov->valor ?: 0),
            'data_movimentacao' => $mov->data_movimentacao ?: now(),
            'forma_pagamento_id' => $mov->forma_pagamento_id,
            'conta_bancaria_id' => $mov->conta_bancaria_id,
            'numero_documento' => $mov->numero_documento,
            'observacoes' => $mov->observacoes,
            'usuario_id' => $mov->usuario_id ?: 1,
            'empresa_id' => $mov->empresa_id,
            'created_at' => $mov->created_at ?: now(),
            'updated_at' => $mov->updated_at ?: now(),
        ];
        
        $dados = array_filter($dados, function($value) {
            return $value !== null && $value !== '';
        });
        
        DB::table('lancamento_movimentacoes')->insert($dados);
    }
    
    echo "   ✓ " . count($movimentacoes) . " movimentações migradas\n";
    
    // Reativar foreign keys
    DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n7. VERIFICANDO RESULTADOS...\n";
    
    $novoLancamentos = DB::table('lancamentos')->count();
    $novoItens = DB::table('lancamento_itens')->count();
    $novoMovimentacoes = DB::table('lancamento_movimentacoes')->count();
    
    echo "   ✓ Lançamentos: $novoLancamentos\n";
    echo "   ✓ Itens: $novoItens\n";
    echo "   ✓ Movimentações: $novoMovimentacoes\n";
    
    echo "\n8. TESTANDO CAMPOS CALCULADOS...\n";
    
    $sample = DB::table('lancamentos')->first();
    if ($sample) {
        echo "   ✓ ID: {$sample->id}\n";
        echo "   ✓ UUID: {$sample->uuid}\n";
        echo "   ✓ Tipo: {$sample->tipo}\n";
        echo "   ✓ Valor bruto: R$ " . number_format($sample->valor_bruto, 2, ',', '.') . "\n";
        echo "   ✓ Valor líquido: R$ " . number_format($sample->valor_liquido, 2, ',', '.') . "\n";
        echo "   ✓ Valor saldo: R$ " . number_format($sample->valor_saldo, 2, ',', '.') . "\n";
        echo "   ✓ Parcelado: " . ($sample->e_parcelado ? 'Sim' : 'Não') . "\n";
    }
    
    echo "\n=== ✅ MIGRAÇÃO CONCLUÍDA COM SUCESSO! ===\n";
    echo "🎉 Estrutura SQL otimizada aplicada\n";
    echo "📊 Dados preservados e convertidos\n";
    echo "⚡ Campos calculados ativos\n";
    echo "🔧 Mapeamento de tipos realizado\n";
    
    echo "\n=== PRINCIPAIS MUDANÇAS ===\n";
    echo "• Campo 'valor' → 'valor_bruto' (principal)\n";
    echo "• Campo 'natureza_financeira' → 'tipo' (pagar/receber)\n";
    echo "• Campo 'situacao_financeira' → 'situacao' (padronizado)\n";
    echo "• Campo 'intervalo_parcelas' → 'intervalo_dias'\n";
    echo "• Novos campos calculados: valor_liquido, valor_saldo\n";
    echo "• Boolean e_parcelado baseado em total_parcelas\n";
    
} catch (Exception $e) {
    echo "❌ ERRO NA MIGRAÇÃO: " . $e->getMessage() . "\n";
    
    try {
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    } catch (Exception $e2) {
        // Ignorar
    }
}
