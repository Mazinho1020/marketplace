<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigDefinition
 * 
 * @property int $id
 * @property int $empresa_id
 * @property string $chave
 * @property string|null $descricao
 * @property string $tipo
 * @property int|null $grupo_id
 * @property string|null $valor_padrao
 * @property bool $obrigatorio
 * @property string|null $validacao
 * @property array|null $opcoes
 * @property bool $visivel_admin
 * @property bool $editavel
 * @property bool $avancado
 * @property int $ordem
 * @property string|null $dica
 * @property bool $ativo
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigDefinition extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_definitions';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
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
        'ativo',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'opcoes' => 'array',
        'obrigatorio' => 'boolean',
        'visivel_admin' => 'boolean',
        'editavel' => 'boolean',
        'avancado' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para tipos de dados
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_JSON = 'json';
    public const TYPE_DATE = 'date';
    public const TYPE_DATETIME = 'datetime';

    public const TYPES = [
        self::TYPE_STRING,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_BOOLEAN,
        self::TYPE_ARRAY,
        self::TYPE_JSON,
        self::TYPE_DATE,
        self::TYPE_DATETIME,
    ];

    // Constantes para status de sincronização
    public const SYNC_STATUS_PENDING = 'pending';
    public const SYNC_STATUS_SYNCED = 'synced';
    public const SYNC_STATUS_ERROR = 'error';

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sync_hash)) {
                $model->sync_hash = hash('sha256', uniqid('config_def_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_def_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_id');
    }

    // Alias para facilitar uso no controller
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_id');
    }

    // Alias para compatibilidade
    public function configGroup(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ConfigValue::class, 'config_id');
    }

    // Alias para facilitar uso no controller
    public function valores(): HasMany
    {
        return $this->hasMany(ConfigValue::class, 'config_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ConfigHistory::class, 'config_id');
    }

    // Alias para facilitar uso no controller  
    public function historico(): HasMany
    {
        return $this->hasMany(ConfigHistory::class, 'config_id');
    }

    // Scopes
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeVisivelAdmin($query)
    {
        return $query->where('visivel_admin', true);
    }

    public function scopeEditavel($query)
    {
        return $query->where('editavel', true);
    }

    public function scopeObrigatorio($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeAvancado($query)
    {
        return $query->where('avancado', true);
    }

    public function scopeBasico($query)
    {
        return $query->where('avancado', false);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeByType($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeByGroup($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopeOrderedByGroup($query)
    {
        return $query->orderBy('grupo_id')->orderBy('ordem')->orderBy('chave');
    }

    // Accessors
    public function getSyncStatusTextAttribute(): string
    {
        return match ($this->sync_status) {
            self::SYNC_STATUS_PENDING => 'Pendente',
            self::SYNC_STATUS_SYNCED => 'Sincronizado',
            self::SYNC_STATUS_ERROR => 'Erro',
            default => 'Desconhecido'
        };
    }

    public function getTipoTextAttribute(): string
    {
        return match ($this->tipo) {
            self::TYPE_STRING => 'Texto',
            self::TYPE_INTEGER => 'Número Inteiro',
            self::TYPE_FLOAT => 'Número Decimal',
            self::TYPE_BOOLEAN => 'Verdadeiro/Falso',
            self::TYPE_ARRAY => 'Lista',
            self::TYPE_JSON => 'JSON',
            self::TYPE_DATE => 'Data',
            self::TYPE_DATETIME => 'Data e Hora',
            default => 'Desconhecido'
        };
    }

    public function getObrigatorioTextAttribute(): string
    {
        return $this->obrigatorio ? 'Sim' : 'Não';
    }

    public function getEditavelTextAttribute(): string
    {
        return $this->editavel ? 'Sim' : 'Não';
    }

    public function getAvancadoTextAttribute(): string
    {
        return $this->avancado ? 'Sim' : 'Não';
    }

    public function getAtivoTextAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    // Métodos auxiliares
    public function needsSync(): bool
    {
        return $this->sync_status === self::SYNC_STATUS_PENDING;
    }

    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => self::SYNC_STATUS_SYNCED,
            'sync_data' => now(),
        ]);
    }

    public function markAsError(): void
    {
        $this->update([
            'sync_status' => self::SYNC_STATUS_ERROR,
        ]);
    }

    public function isObrigatorio(): bool
    {
        return $this->obrigatorio;
    }

    public function isEditavel(): bool
    {
        return $this->editavel;
    }

    public function isAvancado(): bool
    {
        return $this->avancado;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isVisivelAdmin(): bool
    {
        return $this->visivel_admin;
    }

    public function hasOpcoes(): bool
    {
        return !empty($this->opcoes);
    }

    /**
     * Obtém o valor padrão convertido para o tipo correto
     */
    public function getDefaultValue()
    {
        if ($this->valor_padrao === null) {
            return null;
        }

        return $this->castValue($this->valor_padrao);
    }

    /**
     * Converte o valor para o tipo correto
     */
    public function castValue($value)
    {
        if ($value === null) {
            return null;
        }

        return match ($this->tipo) {
            self::TYPE_INTEGER => (int) $value,
            self::TYPE_FLOAT => (float) $value,
            self::TYPE_BOOLEAN => (bool) $value,
            self::TYPE_ARRAY => is_string($value) ? explode(',', $value) : $value,
            self::TYPE_JSON => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }

    /**
     * Alias para castValue - formatar valor para o tipo correto
     */
    public function formatarValor($value)
    {
        return $this->castValue($value);
    }

    /**
     * Valida um valor baseado nas regras de validação
     */
    public function validateValue($value): bool
    {
        if ($this->obrigatorio && ($value === null || $value === '')) {
            return false;
        }

        if ($this->validacao && $value !== null) {
            // Implementar validações baseadas em regex ou regras específicas
            // Por exemplo: min:5, max:100, regex:/^[a-zA-Z]+$/
            return true; // Placeholder
        }

        return true;
    }
}
