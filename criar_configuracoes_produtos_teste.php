<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CRIANDO DADOS DE TESTE - CONFIGURAÇÕES DE PRODUTOS ===\n\n";

use App\Models\Produto;
use App\Models\ProdutoConfiguracao;
use App\Models\ProdutoConfiguracaoItem;

// 1. Verificar se já existem produtos
$produtos = Produto::where('empresa_id', 1)->get();

if ($produtos->count() == 0) {
    echo "❌ Nenhum produto encontrado. Execute primeiro o seeder de produtos.\n";
    exit;
}

echo "✅ Encontrados {$produtos->count()} produtos para configurar\n\n";

// 2. Configurações para Pizza Margherita (ID 1)
$pizza = $produtos->where('nome', 'Pizza Margherita')->first();
if ($pizza) {
    echo "🍕 Configurando Pizza Margherita...\n";

    // Configuração de Tamanhos
    $confTamanho = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => $pizza->id,
        'nome' => 'Tamanho da Pizza',
        'descricao' => 'Escolha o tamanho desejado',
        'tipo_configuracao' => 'tamanho',
        'obrigatorio' => true,
        'permite_multiplos' => false,
        'tipo_calculo' => 'substituicao',
        'ordem' => 1,
        'ativo' => true
    ]);

    // Itens de tamanho
    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTamanho->id,
        'nome' => 'Pequena (25cm)',
        'descricao' => 'Serve 1 pessoa',
        'valor_adicional' => -5.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTamanho->id,
        'nome' => 'Média (30cm)',
        'descricao' => 'Serve 2 pessoas',
        'valor_adicional' => 0.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTamanho->id,
        'nome' => 'Grande (35cm)',
        'descricao' => 'Serve 3-4 pessoas',
        'valor_adicional' => 8.00,
        'ordem' => 3,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTamanho->id,
        'nome' => 'Família (40cm)',
        'descricao' => 'Serve 4-6 pessoas',
        'valor_adicional' => 15.00,
        'ordem' => 4,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "   ✅ Configuração de tamanhos criada (4 opções)\n";

    // Configuração de Bordas
    $confBorda = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => $pizza->id,
        'nome' => 'Tipo de Borda',
        'descricao' => 'Escolha o tipo de borda',
        'tipo_configuracao' => 'complemento',
        'obrigatorio' => true,
        'permite_multiplos' => false,
        'tipo_calculo' => 'soma',
        'ordem' => 2,
        'ativo' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confBorda->id,
        'nome' => 'Borda Tradicional',
        'descricao' => 'Borda simples da massa',
        'valor_adicional' => 0.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confBorda->id,
        'nome' => 'Borda Recheada - Catupiry',
        'descricao' => 'Borda recheada com catupiry',
        'valor_adicional' => 6.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => false
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confBorda->id,
        'nome' => 'Borda Recheada - Cheddar',
        'descricao' => 'Borda recheada com cheddar',
        'valor_adicional' => 7.00,
        'ordem' => 3,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "   ✅ Configuração de bordas criada (3 opções)\n";

    // Configuração de Adicionais
    $confAdicionais = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => $pizza->id,
        'nome' => 'Ingredientes Adicionais',
        'descricao' => 'Adicione mais ingredientes',
        'tipo_configuracao' => 'ingrediente',
        'obrigatorio' => false,
        'permite_multiplos' => true,
        'qtd_maxima' => 5,
        'tipo_calculo' => 'soma',
        'ordem' => 3,
        'ativo' => true
    ]);

    $adicionais = [
        ['nome' => 'Bacon', 'valor' => 4.00],
        ['nome' => 'Calabresa', 'valor' => 3.50],
        ['nome' => 'Frango Desfiado', 'valor' => 3.50],
        ['nome' => 'Catupiry Extra', 'valor' => 3.00],
        ['nome' => 'Azeitona', 'valor' => 2.00],
        ['nome' => 'Milho', 'valor' => 1.50],
        ['nome' => 'Champignon', 'valor' => 3.00],
        ['nome' => 'Tomate Seco', 'valor' => 2.50]
    ];

    foreach ($adicionais as $index => $adicional) {
        ProdutoConfiguracaoItem::create([
            'empresa_id' => 1,
            'produto_configuracao_id' => $confAdicionais->id,
            'nome' => $adicional['nome'],
            'valor_adicional' => $adicional['valor'],
            'ordem' => $index + 1,
            'disponivel' => true,
            'padrao' => false
        ]);
    }

    echo "   ✅ Configuração de adicionais criada (8 opções)\n";
}

// 3. Configurações para Refrigerante (ID 2)
$refrigerante = $produtos->where('nome', 'Refrigerante Cola 350ml')->first();
if ($refrigerante) {
    echo "\n🥤 Configurando Refrigerante...\n";

    // Configuração de Temperatura
    $confTemp = ProdutoConfiguracao::create([
        'empresa_id' => 1,
        'produto_id' => $refrigerante->id,
        'nome' => 'Temperatura',
        'descricao' => 'Como prefere o refrigerante?',
        'tipo_configuracao' => 'personalizado',
        'obrigatorio' => true,
        'permite_multiplos' => false,
        'tipo_calculo' => 'substituicao',
        'ordem' => 1,
        'ativo' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTemp->id,
        'nome' => 'Gelado',
        'descricao' => 'Servido bem gelado',
        'valor_adicional' => 0.00,
        'ordem' => 1,
        'disponivel' => true,
        'padrao' => true
    ]);

    ProdutoConfiguracaoItem::create([
        'empresa_id' => 1,
        'produto_configuracao_id' => $confTemp->id,
        'nome' => 'Natural',
        'descricao' => 'Temperatura ambiente',
        'valor_adicional' => 0.00,
        'ordem' => 2,
        'disponivel' => true,
        'padrao' => false
    ]);

    echo "   ✅ Configuração de temperatura criada (2 opções)\n";
}

// 4. Estatísticas
$totalConfiguracoes = ProdutoConfiguracao::where('empresa_id', 1)->count();
$totalItens = ProdutoConfiguracaoItem::where('empresa_id', 1)->count();

echo "\n📊 RESUMO DOS DADOS CRIADOS:\n";
echo "   • Configurações: {$totalConfiguracoes}\n";
echo "   • Itens de configuração: {$totalItens}\n";

echo "\n✅ DADOS DE TESTE CRIADOS COM SUCESSO!\n";
echo "\n🌐 Acesse: http://localhost:8000/comerciantes/produtos/configuracoes\n";
echo "📋 Funcionalidades disponíveis:\n";
echo "   • Listar configurações\n";
echo "   • Criar novas configurações\n";
echo "   • Gerenciar itens de configuração\n";
echo "   • Ativar/desativar configurações\n";
echo "   • Filtros por tipo e produto\n";

// 5. Criar exemplo de uso
echo "\n💡 EXEMPLO DE USO:\n";
echo "   Produto: Pizza Margherita - R$ 25,90\n";
echo "   + Tamanho Grande: +R$ 8,00\n";
echo "   + Borda Catupiry: +R$ 6,00\n";
echo "   + Bacon: +R$ 4,00\n";
echo "   + Calabresa: +R$ 3,50\n";
echo "   = TOTAL: R$ 47,40\n";
