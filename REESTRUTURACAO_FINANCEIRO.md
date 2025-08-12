# Reestruturação do Sistema Financeiro - Conta Gerencial

Este documento explica a reestruturação implementada para simplificar o relacionamento entre contas gerenciais e suas categorias.

## Mudanças Realizadas

### 1. Estrutura Anterior (Problemática)
```
conta_gerencial (1) ←→ (N) conta_gerencial_naturezas (N) ←→ (1) conta_gerencial_natureza
```
- Relacionamento many-to-many desnecessário
- Complexidade excessiva para uma relação simples
- Confusão entre natureza contábil e categoria de negócio

### 2. Nova Estrutura (Simplificada)
```
conta_gerencial (N) ←→ (1) categorias_conta
```
- Relacionamento 1:N direto e simples
- Separação clara entre natureza contábil e categoria de negócio
- Melhor performance nas consultas

## Migration: `2025_08_12_120000_restructure_conta_gerencial_tables.php`

### Mudanças no Banco de Dados

1. **Renomeação de Tabela**
   - `conta_gerencial_natureza` → `categorias_conta`

2. **Novos Campos em `categorias_conta`**
   - `cor` (varchar 7) - Cor hex para UI (#007bff padrão)
   - `icone` (varchar 50) - Nome do ícone opcional
   - `e_custo` (boolean) - Marca se é categoria de custo
   - `e_despesa` (boolean) - Marca se é categoria de despesa  
   - `e_receita` (boolean) - Marca se é categoria de receita
   - `ativo` (boolean) - Status da categoria
   - `descricao` (text) - Descrição detalhada

3. **Novos Campos em `conta_gerencial`**
   - `categoria_id` (unsignedBigInteger) - FK para categorias_conta
   - `natureza_conta` (enum: débito/crédito) - Natureza contábil

4. **Remoção**
   - Tabela `conta_gerencial_naturezas` (intermediária)

## Models

### `App\Models\Financial\CategoriaContaGerencial`

Representa categorias de negócio das contas gerenciais.

#### Relacionamentos
```php
// Uma categoria tem muitas contas
$categoria->contasGerenciais; // HasMany

// Categoria pertence a uma empresa
$categoria->empresa; // BelongsTo
```

#### Scopes Disponíveis
```php
CategoriaContaGerencial::custos()->get();     // Apenas custos
CategoriaContaGerencial::despesas()->get();   // Apenas despesas
CategoriaContaGerencial::receitas()->get();   // Apenas receitas
CategoriaContaGerencial::ativas()->get();     // Apenas ativas
CategoriaContaGerencial::porEmpresa(1)->get(); // Por empresa
```

#### Métodos Helper
```php
$categoria->isCusto();    // bool
$categoria->isDespesa();  // bool  
$categoria->isReceita();  // bool
$categoria->isAtiva();    // bool
$categoria->getTipo();    // 'custo'|'despesa'|'receita'|'indefinido'
```

#### Atributos Virtuais
```php
$categoria->numero_contas;   // Conta contas vinculadas
$categoria->nome_exibicao;   // nome_completo ou nome
$categoria->cor;             // Com fallback automático por tipo
```

### `App\Models\Financial\ContaGerencial`

Representa uma conta do plano de contas gerencial.

#### Relacionamentos
```php
// Conta pertence a uma categoria
$conta->categoria; // BelongsTo

// Outros relacionamentos
$conta->empresa;   // BelongsTo
$conta->usuario;   // BelongsTo
```

#### Scopes Disponíveis
```php
ContaGerencial::custos()->get();    // Por categoria de custo
ContaGerencial::despesas()->get();  // Por categoria de despesa
ContaGerencial::receitas()->get();  // Por categoria de receita
ContaGerencial::ativas()->get();    // Com categoria ativa
ContaGerencial::debito()->get();    // Natureza débito
ContaGerencial::credito()->get();   // Natureza crédito
```

#### Métodos Helper
```php
// Por categoria
$conta->isCusto();       // bool
$conta->isDespesa();     // bool
$conta->isReceita();     // bool

// Por natureza contábil
$conta->isDebito();      // bool
$conta->isCredito();     // bool

// UI helpers
$conta->getCor();           // string (cor da categoria)
$conta->getIcone();         // string|null
$conta->getNomeCategoria(); // string
```

## Exemplos de Uso

### 1. Criar Nova Categoria
```php
$categoria = CategoriaContaGerencial::create([
    'nome' => 'despesa_marketing',
    'nome_completo' => 'Despesas de Marketing',
    'descricao' => 'Gastos com campanhas e publicidade',
    'cor' => '#fd7e14',
    'icone' => 'fa-bullhorn',
    'e_despesa' => true,
    'ativo' => true,
    'empresa_id' => 1
]);
```

### 2. Criar Nova Conta Gerencial
```php
$conta = ContaGerencial::create([
    'nome' => 'Google Ads',
    'descricao' => 'Campanhas do Google Ads',
    'categoria_id' => $categoria->id,
    'natureza_conta' => 'debito',
    'empresa_id' => 1,
    'usuario_id' => auth()->id()
]);
```

### 3. Consultas Úteis
```php
// Todas as contas de despesa ativas
$contasDespesa = ContaGerencial::despesas()
    ->ativas()
    ->with('categoria')
    ->get();

// Categorias de receita com suas contas
$categoriasReceita = CategoriaContaGerencial::receitas()
    ->with('contasGerenciais')
    ->get();

// Contas de débito por empresa
$contasDebito = ContaGerencial::debito()
    ->where('empresa_id', 1)
    ->get();

// Estatísticas por categoria
$stats = CategoriaContaGerencial::withCount('contasGerenciais')
    ->ativas()
    ->get();
```

### 4. Dados para Interface
```php
// Para select de categorias
$categorias = CategoriaContaGerencial::ativas()
    ->orderBy('nome_completo')
    ->get()
    ->map(function($cat) {
        return [
            'id' => $cat->id,
            'nome' => $cat->nome_exibicao,
            'cor' => $cat->cor,
            'tipo' => $cat->getTipo(),
            'icone' => $cat->icone
        ];
    });

// Para dashboard financeiro
$resumo = [
    'receitas' => ContaGerencial::receitas()->count(),
    'despesas' => ContaGerencial::despesas()->count(), 
    'custos' => ContaGerencial::custos()->count(),
];
```

## Vantagens da Nova Estrutura

✅ **Simplicidade**: Relacionamento 1:N mais intuitivo  
✅ **Performance**: Menos JOINs necessários  
✅ **Clareza**: Separação entre natureza contábil e categoria de negócio  
✅ **Flexibilidade**: Fácil criação de novas categorias  
✅ **Manutenabilidade**: Código mais limpo e direto  

## Migration Segura

A migration inclui:
- Migração automática dos dados existentes
- Configuração automática dos tipos de categoria baseado nos nomes
- Rollback completo caso necessário
- Preservação de todos os dados durante a transição

Para executar:
```bash
php artisan migrate
```

Para rollback (se necessário):
```bash
php artisan migrate:rollback
```