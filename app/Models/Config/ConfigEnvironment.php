<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigEnvironment
 * 
 * @property int $id
 * @property int $empresa_id
 * @property string $codigo
 * @property string $nome
 * @property string|null $descricao
 * @property bool $is_producao
 * @property bool $ativo
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigEnvironment extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_environments';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'is_producao',
        'ativo',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'is_producao' => 'boolean',
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para status de sincronização
    public const SYNC_STATUS_PENDING = 'pending';
    public const SYNC_STATUS_SYNCED = 'synced';
    public const SYNC_STATUS_ERROR = 'error';

    public const SYNC_STATUSES = [
        self::SYNC_STATUS_PENDING,
        self::SYNC_STATUS_SYNCED,
        self::SYNC_STATUS_ERROR,
    ];

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sync_hash)) {
                $model->sync_hash = hash('sha256', uniqid('config_env_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_env_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(ConfigSite::class, 'ambiente_id');
    }

    public function urlMappings(): HasMany
    {
        return $this->hasMany(ConfigUrlMapping::class, 'ambiente_id');
    }

    public function dbConnections(): HasMany
    {
        return $this->hasMany(ConfigDbConnection::class, 'ambiente_id');
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

    public function scopeProducao($query)
    {
        return $query->where('is_producao', true);
    }

    public function scopeDesenvolvimento($query)
    {
        return $query->where('is_producao', false);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    // Accessors
    public function getIsProducaoTextAttribute(): string
    {
        return $this->is_producao ? 'Produção' : 'Desenvolvimento';
    }

    public function getAtivoTextAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getSyncStatusTextAttribute(): string
    {
        return match ($this->sync_status) {
            self::SYNC_STATUS_PENDING => 'Pendente',
            self::SYNC_STATUS_SYNCED => 'Sincronizado',
            self::SYNC_STATUS_ERROR => 'Erro',
            default => 'Desconhecido'
        };
    }

    // Métodos auxiliares
    public function isProducao(): bool
    {
        return $this->is_producao;
    }

    public function isDesenvolvimento(): bool
    {
        return !$this->is_producao;
    }

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
}
