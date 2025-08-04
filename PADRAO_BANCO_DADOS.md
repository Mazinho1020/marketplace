# 🗄️ **PADRÃO PARA INSERÇÃO DE CAMPOS E TABELAS NO BANCO DE DADOS**

## **Marketplace Mazinho1020 - Guia Completo de Estrutura de Banco de Dados**

---

## 📚 **ÍNDICE**

1. [Convenções de Nomenclatura](#convenções-de-nomenclatura)
2. [Campos Obrigatórios](#campos-obrigatórios)
3. [Estrutura de Migration Padrão](#estrutura-de-migration-padrão)
4. [Tipos de Dados Padronizados](#tipos-de-dados-padronizados)
5. [Índices Obrigatórios](#índices-obrigatórios)
6. [Foreign Keys e Relacionamentos](#foreign-keys-e-relacionamentos)
7. [Script para Tabelas Existentes](#script-para-tabelas-existentes)
8. [Exemplos Práticos](#exemplos-práticos)
9. [Checklist de Validação](#checklist-de-validação)

---

## 🏷️ **CONVENÇÕES DE NOMENCLATURA**

### **Tabelas**

- **Formato**: `snake_case` no plural
- **Padrão**: `{dominio}_{entidade}` ou apenas `{entidade}` se único
- **Exemplos**:

  ```
  ✅ CORRETO:
  - empresas
  - usuarios
  - fidelidade_cashback_transacoes
  - fidelidade_cupons
  - pdv_vendas

  ❌ INCORRETO:
  - Empresa
  - usuario
  - fidelidadeCashbackTransacao
  - FidelidadeCupons
  ```

### **Colunas**

- **Formato**: `snake_case`
- **Padrões específicos**:

  ```sql
  -- Chave primária (SEMPRE)
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

  -- Chaves estrangeiras
  {tabela_singular}_id
  -- Exemplos: empresa_id, usuario_id, cliente_id

  -- Timestamps Laravel (OBRIGATÓRIOS)
  created_at TIMESTAMP NULL
  updated_at TIMESTAMP NULL
  deleted_at TIMESTAMP NULL  -- Para SoftDeletes

  -- Campos de status
  status ENUM(...) DEFAULT 'ativo'
  is_active BOOLEAN DEFAULT TRUE
  ativo BOOLEAN DEFAULT TRUE  -- Para compatibilidade

  -- Campos monetários
  valor DECIMAL(10,2) DEFAULT 0.00
  preco DECIMAL(8,2) DEFAULT 0.00

  -- Campos de texto
  nome VARCHAR(100) NOT NULL
  descricao TEXT NULL
  observacoes TEXT NULL
  ```

---

## 🔧 **CAMPOS OBRIGATÓRIOS**

### **1. Campos de Controle (TODAS AS TABELAS)**

```sql
-- Chave primária
`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

-- Multitenancy (OBRIGATÓRIO)
`empresa_id` INT UNSIGNED NOT NULL COMMENT 'ID da empresa (multitenancy)',

-- Timestamps Laravel (OBRIGATÓRIO)
`created_at` TIMESTAMP NULL DEFAULT NULL,
`updated_at` TIMESTAMP NULL DEFAULT NULL,
`deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'SoftDeletes',

-- Sincronização Multi-Sites (OBRIGATÓRIO)
`sync_hash` VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
`sync_status` ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
`sync_data` TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização'
```

### **2. Campos Específicos por Tipo de Entidade**

#### **Entidades de Negócio**

```sql
-- Para tabelas principais
`status` ENUM('ativo', 'inativo', 'suspenso', 'bloqueado') DEFAULT 'ativo',
`is_active` BOOLEAN DEFAULT TRUE,

-- Para entidades financeiras
`valor` DECIMAL(10,2) DEFAULT 0.00,
`valor_original` DECIMAL(10,2) DEFAULT 0.00,
`percentual` DECIMAL(5,2) DEFAULT 0.00,

-- Para entidades com datas
`data_inicio` DATE NULL,
`data_fim` DATE NULL,
`data_expiracao` DATE NULL,

-- Para entidades com códigos únicos
`codigo` VARCHAR(50) UNIQUE NULL,
`uuid` CHAR(36) UNIQUE NULL
```

#### **Entidades de Usuário**

```sql
-- Campos básicos
`nome` VARCHAR(100) NOT NULL,
`email` VARCHAR(255) UNIQUE NULL,
`telefone` VARCHAR(20) NULL,
`cpf` VARCHAR(11) UNIQUE NULL,
`cnpj` VARCHAR(14) UNIQUE NULL,

-- Controle de acesso
`email_verified_at` TIMESTAMP NULL,
`password` VARCHAR(255) NULL,
`remember_token` VARCHAR(100) NULL,

-- Informações adicionais
`data_nascimento` DATE NULL,
`sexo` ENUM('M', 'F', 'O') NULL,
`avatar` VARCHAR(255) NULL
```

---

## 🏗️ **ESTRUTURA DE MIGRATION PADRÃO**

### **Template Base para Nova Tabela**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nome_da_tabela', function (Blueprint $table) {
            // 1. CHAVE PRIMÁRIA (OBRIGATÓRIO)
            $table->id();

            // 2. MULTITENANCY (OBRIGATÓRIO)
            $table->foreignId('empresa_id')
                  ->constrained('empresas')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('ID da empresa (multitenancy)');

            // 3. CAMPOS ESPECÍFICOS DA TABELA
            $table->string('nome', 100)->comment('Nome da entidade');
            $table->text('descricao')->nullable()->comment('Descrição detalhada');
            $table->decimal('valor', 10, 2)->default(0)->comment('Valor em reais');
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
            $table->boolean('is_active')->default(true)->comment('Status ativo/inativo');
            $table->json('metadata')->nullable()->comment('Dados adicionais em JSON');

            // 4. SINCRONIZAÇÃO MULTI-SITES (OBRIGATÓRIO)
            $table->string('sync_hash', 64)->nullable()->comment('Hash MD5 para controle de sincronização');
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])
                  ->default('pending')
                  ->comment('Status da sincronização');
            $table->timestamp('sync_data')->nullable()->comment('Data da última sincronização');

            // 5. TIMESTAMPS PADRÃO (OBRIGATÓRIO)
            $table->timestamps();
            $table->softDeletes();

            // 6. ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'is_active'], 'idx_empresa_active');
            $table->index('created_at', 'idx_created_at');
            $table->index('sync_status', 'idx_sync_status');
            $table->index('deleted_at', 'idx_deleted_at');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');

            // 7. ÍNDICES ESPECÍFICOS (conforme necessidade)
            $table->index('status', 'idx_status');
            $table->unique(['empresa_id', 'codigo'], 'unique_empresa_codigo'); // Se aplicável
        });

        // 8. COMENTÁRIO DA TABELA
        DB::statement("ALTER TABLE nome_da_tabela COMMENT = 'Descrição da tabela e sua função'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nome_da_tabela');
    }
};
```

---

## 📊 **TIPOS DE DADOS PADRONIZADOS**

### **Campos de Texto**

```php
// Nomes curtos
$table->string('nome', 100);
$table->string('titulo', 150);

// Códigos
$table->string('codigo', 50);
$table->string('sku', 100);

// E-mails
$table->string('email', 255);

// Telefones
$table->string('telefone', 20);
$table->string('celular', 20);

// Documentos
$table->string('cpf', 11);
$table->string('cnpj', 14);

// Texto longo
$table->text('descricao');
$table->longText('conteudo'); // Para textos muito grandes

// Senhas
$table->string('password', 255);
```

### **Campos Numéricos**

```php
// Valores monetários
$table->decimal('valor', 10, 2);      // Até 99.999.999,99
$table->decimal('preco', 8, 2);       // Até 999.999,99
$table->decimal('percentual', 5, 2);  // Até 999,99%

// Quantidades
$table->integer('quantidade')->default(0);
$table->unsignedInteger('estoque')->default(0);

// IDs e contadores
$table->bigInteger('contador')->default(0);
$table->unsignedBigInteger('views')->default(0);
```

### **Campos de Data/Hora**

```php
// Datas simples
$table->date('data_nascimento');
$table->date('data_vencimento');

// Data e hora
$table->datetime('data_evento');
$table->timestamp('data_processamento');

// Timestamps especiais
$table->timestamps();           // created_at, updated_at
$table->softDeletes();         // deleted_at
$table->timestamp('email_verified_at')->nullable();
```

### **Campos Especiais**

```php
// Booleanos
$table->boolean('is_active')->default(true);
$table->boolean('ativo')->default(true);

// Enums (usar com parcimônia)
$table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
$table->enum('tipo', ['fisica', 'juridica'])->default('fisica');

// JSON (Laravel 5.7+)
$table->json('configuracoes');
$table->json('metadata');

// UUID
$table->uuid('uuid');
$table->char('codigo_hash', 32); // Para MD5
$table->char('codigo_hash', 64); // Para SHA256
```

---

## 🔗 **ÍNDICES OBRIGATÓRIOS**

### **1. Índices Básicos (TODAS AS TABELAS)**

```php
// Performance básica
$table->index('created_at', 'idx_created_at');
$table->index('deleted_at', 'idx_deleted_at');

// Multitenancy
$table->index('empresa_id', 'idx_empresa_id');
$table->index(['empresa_id', 'is_active'], 'idx_empresa_active');

// Sincronização
$table->index('sync_status', 'idx_sync_status');
$table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
```

### **2. Índices por Tipo de Consulta**

```php
// Busca por status
$table->index('status', 'idx_status');
$table->index(['empresa_id', 'status'], 'idx_empresa_status');

// Busca por datas
$table->index('data_vencimento', 'idx_data_vencimento');
$table->index(['empresa_id', 'data_inicio', 'data_fim'], 'idx_empresa_periodo');

// Busca textual
$table->index('email', 'idx_email');
$table->fullText(['nome', 'descricao'], 'ft_busca_textual'); // Para MySQL 5.7+

// Códigos únicos
$table->unique(['empresa_id', 'codigo'], 'unique_empresa_codigo');
$table->unique(['empresa_id', 'email'], 'unique_empresa_email');
```

---

## 🔗 **FOREIGN KEYS E RELACIONAMENTOS**

### **1. Padrão de Nomenclatura**

```php
// Formato: fk_{tabela_origem}_{campo}
CONSTRAINT `fk_usuarios_empresa_id`
    FOREIGN KEY (`empresa_id`)
    REFERENCES `empresas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
```

### **2. Implementação no Laravel**

```php
// Forma recomendada (Laravel 7+)
$table->foreignId('empresa_id')
      ->constrained('empresas')
      ->onDelete('cascade')
      ->onUpdate('cascade');

// Forma explícita
$table->unsignedBigInteger('categoria_id');
$table->foreign('categoria_id', 'fk_produtos_categoria_id')
      ->references('id')
      ->on('categorias')
      ->onDelete('restrict')
      ->onUpdate('cascade');
```

### **3. Estratégias de Exclusão**

```php
// CASCADE - Exclui registros relacionados
->onDelete('cascade')   // Use com CUIDADO!

// RESTRICT - Impede exclusão se houver relacionados
->onDelete('restrict')  // Recomendado para dados importantes

// SET NULL - Define como NULL
->onDelete('set null')  // Para relacionamentos opcionais

// NO ACTION - Não faz nada (padrão)
->onDelete('no action')
```

---

## 🔄 **SCRIPT PARA TABELAS EXISTENTES**

### **Comando para Atualizar Tabela Existente**

```sql
-- SEMPRE FAZER BACKUP ANTES DE EXECUTAR!

-- 1. Adicionar campos obrigatórios
ALTER TABLE `nome_da_tabela`
ADD COLUMN `empresa_id` INT UNSIGNED NULL COMMENT 'ID da empresa (multitenancy)' AFTER `id`,
ADD COLUMN `sync_hash` VARCHAR(64) NULL COMMENT 'Hash MD5 para controle de sincronização',
ADD COLUMN `sync_status` ENUM('pending', 'synced', 'error', 'ignored') DEFAULT 'pending' COMMENT 'Status da sincronização',
ADD COLUMN `sync_data` TIMESTAMP NULL DEFAULT NULL COMMENT 'Data da última sincronização',
ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL;

-- 2. Atualizar empresa_id com valor padrão (ID da primeira empresa)
UPDATE `nome_da_tabela` SET `empresa_id` = 1 WHERE `empresa_id` IS NULL;

-- 3. Tornar empresa_id obrigatório
ALTER TABLE `nome_da_tabela` MODIFY `empresa_id` INT UNSIGNED NOT NULL;

-- 4. Adicionar índices obrigatórios
ALTER TABLE `nome_da_tabela`
ADD INDEX `idx_empresa_id` (`empresa_id`),
ADD INDEX `idx_created_at` (`created_at`),
ADD INDEX `idx_sync_status` (`sync_status`),
ADD INDEX `idx_deleted_at` (`deleted_at`),
ADD INDEX `idx_sync_control` (`empresa_id`, `sync_status`, `sync_data`);

-- 5. Adicionar foreign key
ALTER TABLE `nome_da_tabela`
ADD CONSTRAINT `fk_nome_da_tabela_empresa_id`
    FOREIGN KEY (`empresa_id`)
    REFERENCES `empresas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

-- 6. Renomear colunas para padrão Laravel (se necessário)
ALTER TABLE `nome_da_tabela`
CHANGE `criado_em` `created_at` TIMESTAMP NULL DEFAULT NULL,
CHANGE `atualizado_em` `updated_at` TIMESTAMP NULL DEFAULT NULL;
```

### **Migration para Atualização**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nome_da_tabela', function (Blueprint $table) {
            // Adicionar campos obrigatórios
            $table->foreignId('empresa_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('empresas')
                  ->onDelete('cascade');

            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
            $table->timestamp('sync_data')->nullable();
            $table->softDeletes();

            // Adicionar índices
            $table->index('sync_status');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
        });

        // Atualizar registros existentes
        DB::table('nome_da_tabela')
          ->whereNull('empresa_id')
          ->update(['empresa_id' => 1]);

        // Tornar empresa_id obrigatório
        Schema::table('nome_da_tabela', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('nome_da_tabela', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropIndex(['empresa_id', 'sync_status', 'sync_data']);
            $table->dropIndex(['sync_status']);
            $table->dropColumn([
                'empresa_id',
                'sync_hash',
                'sync_status',
                'sync_data',
                'deleted_at'
            ]);
        });
    }
};
```

---

## 📝 **EXEMPLOS PRÁTICOS**

### **1. Tabela de Produtos**

```php
Schema::create('produtos', function (Blueprint $table) {
    // Obrigatórios
    $table->id();
    $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

    // Campos específicos
    $table->string('nome', 100);
    $table->string('sku', 50)->nullable();
    $table->text('descricao')->nullable();
    $table->decimal('preco', 8, 2);
    $table->decimal('preco_promocional', 8, 2)->nullable();
    $table->integer('estoque')->default(0);
    $table->boolean('controla_estoque')->default(true);
    $table->enum('status', ['ativo', 'inativo', 'descontinuado'])->default('ativo');

    // Relacionamentos
    $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
    $table->foreignId('marca_id')->nullable()->constrained('marcas')->onDelete('set null');

    // Obrigatórios de sincronização
    $table->string('sync_hash', 64)->nullable();
    $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
    $table->timestamp('sync_data')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Índices
    $table->index(['empresa_id', 'status']);
    $table->index(['categoria_id', 'status']);
    $table->index('sku');
    $table->unique(['empresa_id', 'sku'], 'unique_empresa_sku');
    $table->fullText(['nome', 'descricao'], 'ft_produtos_busca');

    // Índices obrigatórios
    $table->index('sync_status');
    $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
});
```

### **2. Tabela de Clientes**

```php
Schema::create('clientes', function (Blueprint $table) {
    // Obrigatórios
    $table->id();
    $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

    // Campos específicos
    $table->string('nome', 100);
    $table->string('email', 255)->nullable();
    $table->string('telefone', 20)->nullable();
    $table->string('cpf', 11)->nullable();
    $table->date('data_nascimento')->nullable();
    $table->enum('sexo', ['M', 'F', 'O'])->nullable();
    $table->enum('tipo_pessoa', ['fisica', 'juridica'])->default('fisica');
    $table->boolean('aceita_marketing')->default(false);
    $table->boolean('is_active')->default(true);

    // Endereço (opcional - pode ser tabela separada)
    $table->string('cep', 8)->nullable();
    $table->string('endereco')->nullable();
    $table->string('numero', 10)->nullable();
    $table->string('complemento', 50)->nullable();
    $table->string('bairro', 100)->nullable();
    $table->string('cidade', 100)->nullable();
    $table->string('uf', 2)->nullable();

    // Obrigatórios de sincronização
    $table->string('sync_hash', 64)->nullable();
    $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
    $table->timestamp('sync_data')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Índices
    $table->index(['empresa_id', 'is_active']);
    $table->index('email');
    $table->index('cpf');
    $table->unique(['empresa_id', 'email'], 'unique_empresa_email');
    $table->unique(['empresa_id', 'cpf'], 'unique_empresa_cpf');

    // Índices obrigatórios
    $table->index('sync_status');
    $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
});
```

### **3. Tabela de Transações Financeiras**

```php
Schema::create('transacoes_financeiras', function (Blueprint $table) {
    // Obrigatórios
    $table->id();
    $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

    // Campos específicos
    $table->string('numero_transacao', 50)->unique();
    $table->enum('tipo', ['credito', 'debito']);
    $table->decimal('valor', 10, 2);
    $table->string('descricao');
    $table->enum('categoria', ['venda', 'devolucao', 'taxa', 'cashback', 'promocao']);
    $table->enum('status', ['pendente', 'processada', 'cancelada', 'estornada'])->default('pendente');

    // Relacionamentos
    $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
    $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('set null');

    // Datas importantes
    $table->timestamp('data_transacao');
    $table->timestamp('data_processamento')->nullable();
    $table->date('data_vencimento')->nullable();

    // Dados adicionais
    $table->json('metadata')->nullable();
    $table->text('observacoes')->nullable();

    // Obrigatórios de sincronização
    $table->string('sync_hash', 64)->nullable();
    $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
    $table->timestamp('sync_data')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Índices
    $table->index(['empresa_id', 'status']);
    $table->index(['empresa_id', 'tipo', 'data_transacao']);
    $table->index(['cliente_id', 'status']);
    $table->index('data_transacao');
    $table->index('numero_transacao');

    // Índices obrigatórios
    $table->index('sync_status');
    $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
});
```

---

## ✅ **CHECKLIST DE VALIDAÇÃO**

### **Antes de Criar a Migration**

- [ ] Nome da tabela em `snake_case` e no plural
- [ ] Todos os campos obrigatórios incluídos
- [ ] Campo `empresa_id` para multitenancy
- [ ] Campos de sincronização (`sync_hash`, `sync_status`, `sync_data`)
- [ ] Timestamps (`created_at`, `updated_at`, `deleted_at`)
- [ ] Tipos de dados apropriados
- [ ] Comentários nos campos importantes
- [ ] Valores padrão definidos quando necessário

### **Índices e Performance**

- [ ] Índice em `empresa_id`
- [ ] Índice em `created_at`
- [ ] Índice em `sync_status`
- [ ] Índice composto para sincronização
- [ ] Índices específicos para consultas frequentes
- [ ] Índices únicos onde necessário
- [ ] Foreign keys com ações adequadas

### **Após Executar a Migration**

- [ ] Tabela criada com todos os campos
- [ ] Índices criados corretamente
- [ ] Foreign keys funcionando
- [ ] Comentários da tabela e campos visíveis
- [ ] Testar inserção de dados
- [ ] Testar consultas com filtros
- [ ] Verificar performance com EXPLAIN

### **Integração com o Sistema**

- [ ] Model criado seguindo padrões
- [ ] Relacionamentos definidos no Model
- [ ] Scopes básicos implementados
- [ ] Factory criada para testes
- [ ] Seeder criado se necessário
- [ ] Testes unitários implementados

---

## 🚀 **COMANDOS ÚTEIS**

### **Criação de Migration**

```bash
# Nova tabela
php artisan make:migration create_nome_tabela_table

# Modificar tabela existente
php artisan make:migration add_campos_to_nome_tabela_table --table=nome_tabela

# Executar migrations
php artisan migrate

# Rollback da última migration
php artisan migrate:rollback

# Ver status das migrations
php artisan migrate:status
```

### **Verificação de Estrutura**

```sql
-- Ver estrutura da tabela
DESCRIBE nome_da_tabela;

-- Ver índices
SHOW INDEX FROM nome_da_tabela;

-- Ver foreign keys
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'nome_da_tabela'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Verificar tamanho da tabela
SELECT
    table_name AS "Tabela",
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Tamanho (MB)"
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
AND table_name = 'nome_da_tabela';
```

---

## 📋 **RESUMO DOS PADRÕES**

### **Nomenclatura**

- Tabelas: `snake_case` plural
- Colunas: `snake_case`
- Índices: `idx_campo` ou `idx_descricao`
- Foreign Keys: `fk_tabela_campo`
- Unique: `unique_descricao`

### **Campos Obrigatórios**

- `id` (bigint unsigned auto_increment)
- `empresa_id` (multitenancy)
- `created_at`, `updated_at`, `deleted_at`
- `sync_hash`, `sync_status`, `sync_data`

### **Tipos Comuns**

- Textos: VARCHAR(100), TEXT
- Valores: DECIMAL(10,2)
- Datas: DATE, DATETIME, TIMESTAMP
- Status: ENUM ou BOOLEAN
- JSON: JSON (MySQL 5.7+)

### **Índices Mínimos**

- `empresa_id`
- `created_at`
- `sync_status`
- `[empresa_id, sync_status, sync_data]`

Este padrão garante **consistência**, **performance** e **escalabilidade** para o banco de dados do marketplace! 🚀
