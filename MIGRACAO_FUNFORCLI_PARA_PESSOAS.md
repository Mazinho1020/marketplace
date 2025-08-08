# Migração de funforcli para pessoas

## 📋 Análise Completa do Sistema

### 🔍 Arquivos que precisam ser atualizados:

#### 1. Models

- ✅ `app/Models/Cliente.php` - Precisa apontar para a tabela `pessoas`
- ❌ `app/Models/Cliente/Cliente.php` - Verificar se existe e atualizar

#### 2. Controllers

- ❌ `app/Http/Controllers/Fidelidade/CarteirasController.php`
- ❌ `app/Http/Controllers/Fidelidade/TransacoesController.php`
- ❌ `app/Http/Controllers/Fidelidade/TransacoesControllerNew.php`
- ❌ `app/Http/Controllers/Admin/AdminFidelidadeController.php`

#### 3. Requests

- ❌ `app/Http/Requests/Fidelidade/StoreTransacaoRequest.php`

#### 4. Seeders

- ❌ `database/seeders/FunforcliSeeder.php`

#### 5. Rotas

- ❌ `routes/teste.php`

#### 6. Outros arquivos

- ❌ `create_tables_individual.php`
- ❌ `app/Models/PDV/Sale.php`
- ❌ Vários modelos de Fidelidade

## 🗂️ Comparação de Estruturas

### Campos em funforcli → pessoas:

- `id` → `id` ✅
- `nome` → `nome` ✅
- `email` → `email` ✅
- `telefone` → `telefone` ✅
- `cpf` → `cpf_cnpj` ⚠️ (mudança de nome)
- `data_nascimento` → `data_nascimento` ✅
- `endereco` → endereços em tabela separada ⚠️
- `cidade` → endereços em tabela separada ⚠️
- `estado` → endereços em tabela separada ⚠️
- `cep` → endereços em tabela separada ⚠️
- `status` → `status` ✅ (mas valores podem diferir)
- `ativo` → `status` ⚠️ (campo booleano vs enum)

### Novos campos na tabela pessoas:

- `empresa_id` (obrigatório)
- `tipo` (enum: cliente, funcionario, fornecedor, entregador)
- `sobrenome`
- `nome_social`
- `whatsapp`
- Muitos outros campos específicos...

## 🚧 Problemas Identificados:

1. **Estrutura de endereços**: A nova tabela usa tabela separada para endereços
2. **Campo de status**: Mudou de boolean `ativo` para enum `status`
3. **Campo CPF**: Mudou de `cpf` para `cpf_cnpj`
4. **Empresa obrigatória**: Nova tabela requer `empresa_id`
5. **Tipo obrigatório**: Nova tabela requer campo `tipo`

## 📝 Plano de Ação:

### Fase 1: Preparação

1. Criar script de migração de dados
2. Atualizar modelo Cliente
3. Criar aliases para compatibilidade

### Fase 2: Migração de Dados

1. Migrar dados da tabela funforcli para pessoas
2. Migrar endereços para tabela separada
3. Ajustar valores de status

### Fase 3: Atualização de Controllers

1. Atualizar todos os controllers que usam funforcli
2. Ajustar validações
3. Atualizar queries

### Fase 4: Testes

1. Testar todas as funcionalidades
2. Verificar integridade dos dados
3. Corrigir bugs encontrados
