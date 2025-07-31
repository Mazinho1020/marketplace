# 🔧 **MELHORIAS PARA O SISTEMA DE CONFIGURAÇÕES**

## 📋 **Análise do Sistema Atual**

Seu sistema está **excelente**! É robusto e bem pensado. Aqui estão algumas melhorias para alinhá-lo aos padrões Laravel e ao documento de padronização:

---

## 🗄️ **1. AJUSTES NAS TABELAS (Padrão Laravel)**

### **Renomear Colunas para Snake Case:**

```sql
-- Alterações necessárias para seguir padrão Laravel
ALTER TABLE `config_environments`
CHANGE `criado_em` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
CHANGE `atualizado_em` `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `config_sites`
CHANGE `criado_em` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
CHANGE `atualizado_em` `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Aplicar em todas as tabelas...
```

### **Adicionar SoftDeletes onde necessário:**

```sql
-- Adicionar deleted_at para soft deletes
ALTER TABLE `config_definitions` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `config_groups` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `config_sites` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `config_environments` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL;
```

### **Adicionar business_id para Multitenancy:**

```sql
-- Adicionar business_id para isolamento de dados por empresa
ALTER TABLE `config_values` ADD `business_id` INT UNSIGNED NULL COMMENT 'ID da empresa (multitenancy)';
ALTER TABLE `config_values` ADD INDEX `config_values_business_id_foreign` (`business_id`);

-- Ajustar índice único
ALTER TABLE `config_values` DROP INDEX `config_values_unique`;
ALTER TABLE `config_values` ADD UNIQUE INDEX `config_values_unique`
(`config_id`, `site_id`, `ambiente_id`, `business_id`);
```

---

## 🏗️ **2. ESTRUTURA LARAVEL (Migration + Models)**

### **Migration para Config Environments:**

```php
<?php
// database/migrations/2025_07_30_100000_create_config_environments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('config_environments', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único do ambiente');
            $table->string('nome', 100)->comment('Nome de exibição do ambiente');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do ambiente');
            $table->boolean('is_producao')->default(false)->comment('Indica se é ambiente de produção');
            $table->boolean('ativo')->default(true)->comment('Status do ambiente');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ativo', 'is_producao']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('config_environments');
    }
};
```

### **Model Config Environment:**

```php
<?php
// app/Models/Config/ConfigEnvironment.php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ConfigEnvironment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'config_environments';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'is_producao',
        'ativo'
    ];

    protected $casts = [
        'is_producao' => 'boolean',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes
    public const CODIGO_DESENVOLVIMENTO = 'offline';
    public const CODIGO_PRODUCAO = 'online';
    public const CODIGO_HOMOLOGACAO = 'staging';

    public const CODIGOS_PADRAO = [
        self::CODIGO_DESENVOLVIMENTO => 'Desenvolvimento Local',
        self::CODIGO_PRODUCAO => 'Produção',
        self::CODIGO_HOMOLOGACAO => 'Homologação',
    ];

    // Relacionamentos
    public function configValues()
    {
        return $this->hasMany(ConfigValue::class, 'ambiente_id');
    }

    public function urlMappings()
    {
        return $this->hasMany(ConfigUrlMapping::class, 'ambiente_id');
    }

    public function dbConnections()
    {
        return $this->hasMany(ConfigDbConnection::class, 'ambiente_id');
    }

    // Scopes
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeProducao(Builder $query): Builder
    {
        return $query->where('is_producao', true);
    }

    public function scopeDesenvolvimento(Builder $query): Builder
    {
        return $query->where('is_producao', false);
    }

    public function scopePorCodigo(Builder $query, string $codigo): Builder
    {
        return $query->where('codigo', $codigo);
    }

    // Accessors
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ativo
                ? '<span class="badge bg-success">Ativo</span>'
                : '<span class="badge bg-danger">Inativo</span>'
        );
    }

    protected function tipoBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_producao
                ? '<span class="badge bg-danger">Produção</span>'
                : '<span class="badge bg-info">Desenvolvimento</span>'
        );
    }

    // Métodos customizados
    public function isProducao(): bool
    {
        return $this->is_producao === true;
    }

    public function isDesenvolvimento(): bool
    {
        return $this->is_producao === false;
    }

    public function getUrlMapping(int $siteId): ?ConfigUrlMapping
    {
        return $this->urlMappings()
            ->where('site_id', $siteId)
            ->first();
    }

    // Boot method
    protected static function booted(): void
    {
        static::creating(function ($environment) {
            // Garantir que código seja sempre lowercase
            $environment->codigo = strtolower($environment->codigo);
        });
    }
}
```

### **Model Config Definition (Principal):**

```php
<?php
// app/Models/Config/ConfigDefinition.php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ConfigDefinition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'config_definitions';

    protected $fillable = [
        'chave',
        'descricao',
        'tipo',
        'grupo_id',
        'valor_padrao',
        'obrigatorio',
        'validacao',
        'opcoes',
        'visivel_admin',
        'editavel',
        'avancado',
        'ordem',
        'dica',
        'ativo'
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'visivel_admin' => 'boolean',
        'editavel' => 'boolean',
        'avancado' => 'boolean',
        'ativo' => 'boolean',
        'opcoes' => 'array',
        'ordem' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para tipos
    public const TIPO_STRING = 'string';
    public const TIPO_INTEGER = 'integer';
    public const TIPO_FLOAT = 'float';
    public const TIPO_BOOLEAN = 'boolean';
    public const TIPO_ARRAY = 'array';
    public const TIPO_JSON = 'json';
    public const TIPO_DATE = 'date';
    public const TIPO_DATETIME = 'datetime';
    public const TIPO_EMAIL = 'email';
    public const TIPO_URL = 'url';
    public const TIPO_PASSWORD = 'password';

    public const TIPOS_DISPONIVEIS = [
        self::TIPO_STRING => 'Texto',
        self::TIPO_INTEGER => 'Número Inteiro',
        self::TIPO_FLOAT => 'Número Decimal',
        self::TIPO_BOOLEAN => 'Verdadeiro/Falso',
        self::TIPO_ARRAY => 'Array',
        self::TIPO_JSON => 'JSON',
        self::TIPO_DATE => 'Data',
        self::TIPO_DATETIME => 'Data e Hora',
        self::TIPO_EMAIL => 'E-mail',
        self::TIPO_URL => 'URL',
        self::TIPO_PASSWORD => 'Senha',
    ];

    // Relacionamentos
    public function grupo()
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_id');
    }

    public function values()
    {
        return $this->hasMany(ConfigValue::class, 'config_id');
    }

    public function permissions()
    {
        return $this->hasMany(ConfigPermission::class, 'config_id');
    }

    // Scopes
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeVisivelAdmin(Builder $query): Builder
    {
        return $query->where('visivel_admin', true);
    }

    public function scopeEditavel(Builder $query): Builder
    {
        return $query->where('editavel', true);
    }

    public function scopeObrigatorias(Builder $query): Builder
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeAvancadas(Builder $query): Builder
    {
        return $query->where('avancado', true);
    }

    public function scopeBasicas(Builder $query): Builder
    {
        return $query->where('avancado', false);
    }

    public function scopePorGrupo(Builder $query, int $grupoId): Builder
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeOrdenadas(Builder $query): Builder
    {
        return $query->orderBy('ordem', 'asc')
                    ->orderBy('chave', 'asc');
    }

    // Accessors
    protected function tipoFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => self::TIPOS_DISPONIVEIS[$this->tipo] ?? $this->tipo
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ativo
                ? '<span class="badge bg-success">Ativa</span>'
                : '<span class="badge bg-danger">Inativa</span>'
        );
    }

    protected function tipoIcone(): Attribute
    {
        return Attribute::make(
            get: fn() => match($this->tipo) {
                self::TIPO_STRING => 'uil-text',
                self::TIPO_INTEGER, self::TIPO_FLOAT => 'uil-calculator',
                self::TIPO_BOOLEAN => 'uil-toggle-on',
                self::TIPO_ARRAY, self::TIPO_JSON => 'uil-brackets-curly',
                self::TIPO_DATE, self::TIPO_DATETIME => 'uil-calendar-alt',
                self::TIPO_EMAIL => 'uil-envelope',
                self::TIPO_URL => 'uil-link',
                self::TIPO_PASSWORD => 'uil-key-skeleton',
                default => 'uil-setting'
            }
        );
    }

    // Métodos customizados
    public function getValue(int $siteId = null, int $ambienteId = null, int $businessId = null)
    {
        $value = $this->values()
            ->where('site_id', $siteId)
            ->where('ambiente_id', $ambienteId)
            ->where('business_id', $businessId)
            ->first();

        if (!$value) {
            return $this->valor_padrao;
        }

        return $this->castValue($value->valor);
    }

    public function setValue($valor, int $siteId = null, int $ambienteId = null, int $businessId = null, int $usuarioId = null): ConfigValue
    {
        return ConfigValue::updateOrCreate(
            [
                'config_id' => $this->id,
                'site_id' => $siteId,
                'ambiente_id' => $ambienteId,
                'business_id' => $businessId,
            ],
            [
                'valor' => $this->prepareValue($valor),
                'usuario_id' => $usuarioId ?? auth()->id(),
            ]
        );
    }

    private function castValue($value)
    {
        if ($value === null) {
            return null;
        }

        return match($this->tipo) {
            self::TIPO_INTEGER => (int) $value,
            self::TIPO_FLOAT => (float) $value,
            self::TIPO_BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::TIPO_ARRAY, self::TIPO_JSON => json_decode($value, true),
            self::TIPO_DATE => \Carbon\Carbon::parse($value)->toDateString(),
            self::TIPO_DATETIME => \Carbon\Carbon::parse($value),
            default => $value
        };
    }

    private function prepareValue($value): string
    {
        if ($value === null) {
            return '';
        }

        return match($this->tipo) {
            self::TIPO_BOOLEAN => $value ? '1' : '0',
            self::TIPO_ARRAY, self::TIPO_JSON => json_encode($value),
            self::TIPO_DATE => \Carbon\Carbon::parse($value)->toDateString(),
            self::TIPO_DATETIME => \Carbon\Carbon::parse($value)->toDateTimeString(),
            default => (string) $value
        };
    }

    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->obrigatorio) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Adicionar regras baseadas no tipo
        switch ($this->tipo) {
            case self::TIPO_INTEGER:
                $rules[] = 'integer';
                break;
            case self::TIPO_FLOAT:
                $rules[] = 'numeric';
                break;
            case self::TIPO_BOOLEAN:
                $rules[] = 'boolean';
                break;
            case self::TIPO_EMAIL:
                $rules[] = 'email:rfc,dns';
                break;
            case self::TIPO_URL:
                $rules[] = 'url';
                break;
            case self::TIPO_DATE:
                $rules[] = 'date';
                break;
            case self::TIPO_DATETIME:
                $rules[] = 'date';
                break;
            case self::TIPO_JSON:
                $rules[] = 'json';
                break;
        }

        // Adicionar regras customizadas de validação
        if ($this->validacao) {
            $customRules = explode('|', $this->validacao);
            $rules = array_merge($rules, $customRules);
        }

        return $rules;
    }

    // Boot method
    protected static function booted(): void
    {
        static::creating(function ($definition) {
            // Auto-incrementar ordem se não definida
            if (!$definition->ordem) {
                $maxOrdem = static::where('grupo_id', $definition->grupo_id)->max('ordem') ?? 0;
                $definition->ordem = $maxOrdem + 1;
            }
        });
    }
}
```

---

## 🛠️ **3. SERVICE PARA GERENCIAR CONFIGURAÇÕES**

```php
<?php
// app/Services/Config/ConfigService.php

namespace App\Services\Config;

use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigValue;
use App\Models\Config\ConfigEnvironment;
use App\Models\Config\ConfigSite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigService
{
    protected $cachePrefix = 'config:';
    protected $cacheTtl = 3600; // 1 hora

    public function get(string $chave, $default = null, int $siteId = null, int $ambienteId = null, int $businessId = null)
    {
        $cacheKey = $this->getCacheKey($chave, $siteId, $ambienteId, $businessId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($chave, $default, $siteId, $ambienteId, $businessId) {
            $definition = ConfigDefinition::where('chave', $chave)->first();

            if (!$definition) {
                return $default;
            }

            return $definition->getValue($siteId, $ambienteId, $businessId) ?? $default;
        });
    }

    public function set(string $chave, $valor, int $siteId = null, int $ambienteId = null, int $businessId = null, int $usuarioId = null): bool
    {
        try {
            DB::beginTransaction();

            $definition = ConfigDefinition::where('chave', $chave)->first();

            if (!$definition) {
                throw new \Exception("Configuração '{$chave}' não encontrada.");
            }

            if (!$definition->editavel) {
                throw new \Exception("Configuração '{$chave}' não é editável.");
            }

            // Validar valor
            $this->validateValue($definition, $valor);

            // Salvar valor
            $definition->setValue($valor, $siteId, $ambienteId, $businessId, $usuarioId);

            // Limpar cache
            $this->clearCache($chave, $siteId, $ambienteId, $businessId);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAll(int $siteId = null, int $ambienteId = null, int $businessId = null): array
    {
        $cacheKey = "all_configs:{$siteId}:{$ambienteId}:{$businessId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($siteId, $ambienteId, $businessId) {
            $definitions = ConfigDefinition::ativas()
                ->with(['grupo'])
                ->ordenadas()
                ->get();

            $configs = [];
            foreach ($definitions as $definition) {
                $configs[$definition->chave] = $definition->getValue($siteId, $ambienteId, $businessId);
            }

            return $configs;
        });
    }

    public function getAllByGroup(string $grupocodigo, int $siteId = null, int $ambienteId = null, int $businessId = null): array
    {
        $definitions = ConfigDefinition::ativas()
            ->whereHas('grupo', function ($query) use ($grupocodigo) {
                $query->where('codigo', $grupocodigo);
            })
            ->ordenadas()
            ->get();

        $configs = [];
        foreach ($definitions as $definition) {
            $configs[$definition->chave] = $definition->getValue($siteId, $ambienteId, $businessId);
        }

        return $configs;
    }

    public function validateValue(ConfigDefinition $definition, $valor): bool
    {
        $rules = $definition->getValidationRules();

        $validator = \Validator::make(
            ['valor' => $valor],
            ['valor' => $rules]
        );

        if ($validator->fails()) {
            throw new \Exception('Valor inválido: ' . $validator->errors()->first());
        }

        // Validar opções se existirem
        if ($definition->opcoes && is_array($definition->opcoes)) {
            if (!in_array($valor, array_keys($definition->opcoes))) {
                throw new \Exception('Valor deve ser uma das opções disponíveis.');
            }
        }

        return true;
    }

    protected function getCacheKey(string $chave, int $siteId = null, int $ambienteId = null, int $businessId = null): string
    {
        return $this->cachePrefix . "{$chave}:{$siteId}:{$ambienteId}:{$businessId}";
    }

    protected function clearCache(string $chave, int $siteId = null, int $ambienteId = null, int $businessId = null): void
    {
        $cacheKey = $this->getCacheKey($chave, $siteId, $ambienteId, $businessId);
        Cache::forget($cacheKey);

        // Limpar cache de todas as configurações também
        Cache::forget("all_configs:{$siteId}:{$ambienteId}:{$businessId}");
    }

    public function clearAllCache(): void
    {
        Cache::flush(); // Em produção, usar tags para ser mais específico
    }
}
```

---

## 🎯 **4. HELPER GLOBAL**

```php
<?php
// app/Helpers/config_helper.php

if (!function_exists('config_get')) {
    function config_get(string $chave, $default = null, int $siteId = null, int $ambienteId = null, int $businessId = null)
    {
        return app(\App\Services\Config\ConfigService::class)->get($chave, $default, $siteId, $ambienteId, $businessId);
    }
}

if (!function_exists('config_set')) {
    function config_set(string $chave, $valor, int $siteId = null, int $ambienteId = null, int $businessId = null): bool
    {
        return app(\App\Services\Config\ConfigService::class)->set($chave, $valor, $siteId, $ambienteId, $businessId);
    }
}

// Uso:
// $appName = config_get('app_name', 'Marketplace');
// config_set('empresa_nome', 'Nova Empresa');
```

---

## 📊 **5. CONTROLLER PARA ADMIN**

```php
<?php
// app/Http/Controllers/Admin/ConfigController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigGroup;
use App\Services\Config\ConfigService;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(
        protected ConfigService $configService
    ) {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $grupos = ConfigGroup::ativas()
            ->with(['definitions' => function ($query) {
                $query->ativas()->visivelAdmin()->ordenadas();
            }])
            ->whereNull('grupo_pai_id')
            ->orderBy('ordem')
            ->get();

        return view('admin.config.index', compact('grupos'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->input('configs', []) as $chave => $valor) {
                $this->configService->set(
                    $chave,
                    $valor,
                    $request->input('site_id'),
                    $request->input('ambiente_id'),
                    auth()->user()->business_id
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.config.index')
                ->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Erro ao atualizar configurações: ' . $e->getMessage())
                ->withInput();
        }
    }
}
```

---

## 🔗 **6. INTEGRAÇÃO COM FIDELIDADE**

```php
// Adicionar configurações específicas para o sistema de fidelidade

INSERT INTO `config_groups` (`codigo`, `nome`, `descricao`, `icone`, `ordem`)
VALUES ('fidelidade', 'Fidelidade', 'Configurações do sistema de fidelidade', 'fa-heart', 10);

INSERT INTO `config_definitions` (`chave`, `descricao`, `tipo`, `grupo_id`, `valor_padrao`, `obrigatorio`)
VALUES
('fidelidade_cashback_min_percentage', 'Percentual mínimo de cashback', 'float',
 (SELECT id FROM `config_groups` WHERE `codigo` = 'fidelidade'), '0.5', 1),
('fidelidade_cashback_max_percentage', 'Percentual máximo de cashback', 'float',
 (SELECT id FROM `config_groups` WHERE `codigo` = 'fidelidade'), '15.0', 1),
('fidelidade_pontos_real_conversao', 'Conversão Real para Pontos (R$ 1,00 = X pontos)', 'integer',
 (SELECT id FROM `config_groups` WHERE `codigo` = 'fidelidade'), '100', 1),
('fidelidade_saque_minimo', 'Valor mínimo para saque', 'float',
 (SELECT id FROM `config_groups` WHERE `codigo` = 'fidelidade'), '10.00', 1);
```

---

## ✅ **RESUMO DAS MELHORIAS:**

1. **✅ Padrões Laravel** - Nomes de colunas, SoftDeletes, estrutura de Models
2. **✅ Multitenancy** - Adição de business_id para isolamento por empresa
3. **✅ Service Layer** - Camada de serviço para lógica de negócio
4. **✅ Cache** - Sistema de cache para performance
5. **✅ Validações** - Validações automáticas baseadas no tipo
6. **✅ Helper Functions** - Funções globais para facilitar uso
7. **✅ Interface Admin** - Controller e views para gerenciar configurações
8. **✅ Integração** - Configurações específicas para fidelidade

O sistema está excelente! Essas melhorias vão torná-lo ainda mais robusto e alinhado com os padrões do Laravel. 🚀
