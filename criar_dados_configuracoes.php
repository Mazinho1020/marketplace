<?php

require_once 'vendor/autoload.php';

use App\Models\ProdutoConfiguracao;
use App\Models\ProdutoConfiguracaoItem;

// Configurar conexão com banco
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CRIANDO DADOS DE TESTE PARA CONFIGURAÇÕES ===\n\n";

try {
    // Configuração 1: Tamanhos de Pizza
    $configTamanho = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => 1, // Pizza Margherita
        'nome' => 'Tamanho da Pizza',
        'descricao' => 'Escolha o tamanho da sua pizza',
        'tipo_configuracao' => 'tamanho',
        'obrigatorio' => true,
        'permite_multiplos' => false,
        'qtd_minima' => 1,
        'qtd_maxima' => 1,
        'tipo_calculo' => 'substituicao',
        'ordem' => 1,
        'ativo' => true
    ]);

    // Itens da configuração de tamanho
    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configTamanho->id,
        'nome' => 'Pequena (25cm)',
        'descricao' => 'Pizza pequena de 25cm',
        'valor_adicional' => 0.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configTamanho->id,
        'nome' => 'Média (30cm)',
        'descricao' => 'Pizza média de 30cm',
        'valor_adicional' => 5.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configTamanho->id,
        'nome' => 'Grande (35cm)',
        'descricao' => 'Pizza grande de 35cm',
        'valor_adicional' => 12.00,
        'ordem' => 3,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "✅ Configuração de Tamanho criada com 3 itens\n";

    // Configuração 2: Bordas da Pizza
    $configBorda = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => 1, // Pizza Margherita
        'nome' => 'Tipo de Borda',
        'descricao' => 'Escolha o tipo de borda da sua pizza',
        'tipo_configuracao' => 'complemento',
        'obrigatorio' => false,
        'permite_multiplos' => false,
        'qtd_minima' => 0,
        'qtd_maxima' => 1,
        'tipo_calculo' => 'soma',
        'ordem' => 2,
        'ativo' => true
    ]);

    // Itens da configuração de borda
    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configBorda->id,
        'nome' => 'Borda Tradicional',
        'descricao' => 'Borda simples sem recheio',
        'valor_adicional' => 0.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configBorda->id,
        'nome' => 'Borda Recheada com Catupiry',
        'descricao' => 'Borda recheada com catupiry',
        'valor_adicional' => 8.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configBorda->id,
        'nome' => 'Borda Recheada com Cheddar',
        'descricao' => 'Borda recheada com cheddar',
        'valor_adicional' => 10.00,
        'ordem' => 3,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "✅ Configuração de Borda criada com 3 itens\n";

    // Configuração 3: Sabores de Refrigerante
    $configSabor = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => 2, // Refrigerante
        'nome' => 'Sabor do Refrigerante',
        'descricao' => 'Escolha o sabor do refrigerante',
        'tipo_configuracao' => 'sabor',
        'obrigatorio' => true,
        'permite_multiplos' => false,
        'qtd_minima' => 1,
        'qtd_maxima' => 1,
        'tipo_calculo' => 'substituicao',
        'ordem' => 1,
        'ativo' => true
    ]);

    // Itens da configuração de sabor
    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configSabor->id,
        'nome' => 'Cola',
        'descricao' => 'Refrigerante sabor cola',
        'valor_adicional' => 0.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configSabor->id,
        'nome' => 'Laranja',
        'descricao' => 'Refrigerante sabor laranja',
        'valor_adicional' => 0.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $configSabor->id,
        'nome' => 'Guaraná',
        'descricao' => 'Refrigerante sabor guaraná',
        'valor_adicional' => 0.00,
        'ordem' => 3,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "✅ Configuração de Sabor criada com 3 itens\n";

    echo "\n=== RESUMO ===\n";
    echo "Total de configurações criadas: " . ProdutoConfiguracao::count() . "\n";
    echo "Total de itens de configuração criados: " . ProdutoConfiguracaoItem::count() . "\n";

    echo "\n✅ DADOS DE TESTE CRIADOS COM SUCESSO!\n";
    echo "Acesse: http://localhost:8000/comerciantes/produtos/configuracoes\n";
} catch (\Exception $e) {
    echo "❌ Erro ao criar dados de teste: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
