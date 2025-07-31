<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigGroup
 * 
 * @property int $id
 * @property int $empresa_id
 * @property string $codigo
 * @property string $nome
 * @property string|null $descricao
 * @property int|null $grupo_pai_id
 * @property string|null $icone
 * @property int $ordem
 * @property bool $ativo
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigGroup extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_groups';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'grupo_pai_id',
        'icone',
        'ordem',
        'ativo',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'ordem' => 'integer',
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para grupos padrão
    public const GROUP_GERAL = 'geral';
    public const GROUP_EMPRESA = 'empresa';
    public const GROUP_SYNC = 'sync';
    public const GROUP_TELEGRAM = 'telegram';
    public const GROUP_URL = 'url';
    public const GROUP_DB = 'db';
    public const GROUP_FIDELIDADE = 'fidelidade';
    public const GROUP_PDV = 'pdv';

    public const GROUPS = [
        self::GROUP_GERAL,
        self::GROUP_EMPRESA,
        self::GROUP_SYNC,
        self::GROUP_TELEGRAM,
        self::GROUP_URL,
        self::GROUP_DB,
        self::GROUP_FIDELIDADE,
        self::GROUP_PDV,
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
                $model->sync_hash = hash('sha256', uniqid('config_grp_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_grp_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ConfigGroup::class, 'grupo_pai_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ConfigGroup::class, 'grupo_pai_id');
    }

    public function definitions(): HasMany
    {
        return $this->hasMany(ConfigDefinition::class, 'grupo_id');
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

    public function scopeRaiz($query)
    {
        return $query->whereNull('grupo_pai_id');
    }

    public function scopeFilhos($query, $parentId)
    {
        return $query->where('grupo_pai_id', $parentId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeByCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
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

    public function getAtivoTextAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_name . ' > ' . $this->nome;
        }
        return $this->nome;
    }

    public function getIconeClassAttribute(): string
    {
        if ($this->icone) {
            return $this->icone;
        }

        // Ícones padrão baseados no código
        return match ($this->codigo) {
            self::GROUP_GERAL => 'uil uil-cog',
            self::GROUP_EMPRESA => 'uil uil-building',
            self::GROUP_SYNC => 'uil uil-sync',
            self::GROUP_TELEGRAM => 'uil uil-telegram-alt',
            self::GROUP_URL => 'uil uil-link',
            self::GROUP_DB => 'uil uil-database',
            self::GROUP_FIDELIDADE => 'uil uil-star',
            self::GROUP_PDV => 'uil uil-shopping-cart',
            default => 'uil uil-folder'
        };
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

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function isRaiz(): bool
    {
        return is_null($this->grupo_pai_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function hasDefinitions(): bool
    {
        return $this->definitions()->exists();
    }

    /**
     * Obtém todos os filhos de forma recursiva
     */
    public function getAllChildren(): \Illuminate\Database\Eloquent\Collection
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    /**
     * Obtém todas as definições incluindo dos filhos
     */
    public function getAllDefinitions(): \Illuminate\Database\Eloquent\Collection
    {
        $definitions = $this->definitions;

        foreach ($this->children as $child) {
            $definitions = $definitions->merge($child->getAllDefinitions());
        }

        return $definitions;
    }

    /**
     * Verifica se pode ser excluído (não tem filhos nem definições)
     */
    public function canBeDeleted(): bool
    {
        return !$this->hasChildren() && !$this->hasDefinitions();
    }
}
