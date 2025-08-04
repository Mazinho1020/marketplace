# 📋 **RESUMO RÁPIDO - PADRÃO BANCO DE DADOS**

## **Marketplace Mazinho1020 - Checklist Essencial**

---

## ✅ **CHECKLIST PARA NOVA TABELA**

### **1. Nomenclatura**

- [ ] Nome da tabela: `snake_case` plural
- [ ] Colunas: `snake_case`
- [ ] FK: `{tabela_singular}_id`

### **2. Campos Obrigatórios (TODAS AS TABELAS)**

```sql
id                  -- BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
empresa_id          -- INT UNSIGNED NOT NULL (multitenancy)
created_at          -- TIMESTAMP NULL
updated_at          -- TIMESTAMP NULL
deleted_at          -- TIMESTAMP NULL (SoftDeletes)
sync_hash           -- VARCHAR(64) NULL
sync_status         -- ENUM('pending','synced','error','ignored') DEFAULT 'pending'
sync_data           -- TIMESTAMP NULL
```

### **3. Índices Obrigatórios**

```sql
INDEX (empresa_id)
INDEX (created_at)
INDEX (sync_status)
INDEX (deleted_at)
INDEX (empresa_id, sync_status, sync_data)  -- Composto para sync
```

### **4. Foreign Key**

```sql
FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
```

---

## 🚀 **MIGRATION TEMPLATE MÍNIMO**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nome_tabela', function (Blueprint $table) {
            // OBRIGATÓRIOS
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            // CAMPOS ESPECÍFICOS
            $table->string('nome', 100);
            $table->decimal('valor', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);

            // SYNC OBRIGATÓRIO
            $table->string('sync_hash', 64)->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'error', 'ignored'])->default('pending');
            $table->timestamp('sync_data')->nullable();

            // TIMESTAMPS
            $table->timestamps();
            $table->softDeletes();

            // ÍNDICES OBRIGATÓRIOS
            $table->index(['empresa_id', 'is_active']);
            $table->index('sync_status');
            $table->index(['empresa_id', 'sync_status', 'sync_data'], 'idx_sync_control');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nome_tabela');
    }
};
```

---

## 🔧 **TIPOS DE DADOS COMUNS**

| Tipo                | Laravel                                | Exemplo                 |
| ------------------- | -------------------------------------- | ----------------------- |
| **Texto Curto**     | `string('campo', 100)`                 | nome, titulo            |
| **Texto Longo**     | `text('campo')`                        | descricao, observacoes  |
| **Valor Monetário** | `decimal('valor', 10, 2)`              | preco, total            |
| **Inteiro**         | `integer('campo')`                     | quantidade, estoque     |
| **Boolean**         | `boolean('campo')->default(true)`      | is_active, ativo        |
| **Data**            | `date('campo')`                        | data_nascimento         |
| **Data/Hora**       | `datetime('campo')`                    | data_evento             |
| **JSON**            | `json('campo')`                        | configuracoes, metadata |
| **Enum**            | `enum('status', ['ativo', 'inativo'])` | status, tipo            |

---

## 📝 **MODEL TEMPLATE MÍNIMO**

```php
<?php

namespace App\Models\Dominio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NomeModel extends Model
{
    use SoftDeletes;

    protected $table = 'nome_tabela';

    protected $fillable = [
        'empresa_id',
        'nome',
        'valor',
        'is_active',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'is_active' => 'boolean',
        'sync_data' => 'datetime',
    ];

    // CONSTANTES
    public const SYNC_PENDING = 'pending';
    public const SYNC_SYNCED = 'synced';

    // RELACIONAMENTOS
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class);
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
```

---

## ⚡ **COMANDOS RÁPIDOS**

```bash
# Criar migration
php artisan make:migration create_nome_tabela_table

# Executar migrations
php artisan migrate

# Ver status
php artisan migrate:status

# Rollback
php artisan migrate:rollback

# Criar model
php artisan make:model Dominio/NomeModel

# Model + Migration + Factory
php artisan make:model Dominio/NomeModel -mf
```

---

## 🔍 **VERIFICAÇÃO SQL**

```sql
-- Ver estrutura
DESCRIBE nome_tabela;

-- Ver índices
SHOW INDEX FROM nome_tabela;

-- Ver foreign keys
SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'nome_tabela' AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

## ❌ **ERROS COMUNS**

| ❌ Erro                  | ✅ Correto                         |
| ------------------------ | ---------------------------------- |
| `users` (sem empresa_id) | `users` + empresa_id               |
| `User` (PascalCase)      | `users` (snake_case)               |
| `user_id` (sem FK)       | `user_id` + foreign key            |
| `active` (sem boolean)   | `is_active BOOLEAN`                |
| Sem sync fields          | sync_hash, sync_status, sync_data  |
| Sem timestamps           | created_at, updated_at, deleted_at |
| Sem índices              | Índices obrigatórios               |

---

## 📖 **DOCUMENTAÇÃO COMPLETA**

Para detalhes completos, consulte:

- [`PADRAO_BANCO_DADOS.md`](./PADRAO_BANCO_DADOS.md) - Documento completo
- [`PADRONIZACAO_COMPLETA.md`](./PADRONIZACAO_COMPLETA.md) - Padrões gerais
- `/database/migrations/exemplo_*` - Exemplos práticos
- `/app/Models/Exemplo/` - Models de exemplo

---

**⚠️ IMPORTANTE**: Sempre fazer backup antes de alterar tabelas existentes!
