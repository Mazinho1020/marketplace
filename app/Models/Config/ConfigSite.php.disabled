<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigSite
 * 
 * @property int $id
 * @property int $empresa_id
 * @property string $codigo
 * @property string $nome
 * @property string|null $descricao
 * @property string|null $base_url_padrao
 * @property bool $ativo
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigSite extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_sites';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'base_url_padrao',
        'ativo',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para sites
    public const SITE_SISTEMA = 'sistema';
    public const SITE_PDV = 'pdv';
    public const SITE_FIDELIDADE = 'fidelidade';
    public const SITE_DELIVERY = 'delivery';

    public const SITES = [
        self::SITE_SISTEMA,
        self::SITE_PDV,
        self::SITE_FIDELIDADE,
        self::SITE_DELIVERY,
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
                $model->sync_hash = hash('sha256', uniqid('config_site_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_site_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function urlMappings(): HasMany
    {
        return $this->hasMany(ConfigUrlMapping::class, 'site_id');
    }

    public function configValues(): HasMany
    {
        return $this->hasMany(ConfigValue::class, 'site_id');
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

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeByCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    // Accessors
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
    public function isAtivo(): bool
    {
        return $this->ativo;
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

    public function isSistema(): bool
    {
        return $this->codigo === self::SITE_SISTEMA;
    }

    public function isPdv(): bool
    {
        return $this->codigo === self::SITE_PDV;
    }

    public function isFidelidade(): bool
    {
        return $this->codigo === self::SITE_FIDELIDADE;
    }

    public function isDelivery(): bool
    {
        return $this->codigo === self::SITE_DELIVERY;
    }
}
