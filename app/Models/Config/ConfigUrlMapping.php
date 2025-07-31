<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigUrlMapping
 * 
 * @property int $id
 * @property int $empresa_id
 * @property int $site_id
 * @property int $ambiente_id
 * @property string $dominio
 * @property string $base_url
 * @property string|null $api_url
 * @property string|null $asset_url
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigUrlMapping extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_url_mappings';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'site_id',
        'ambiente_id',
        'dominio',
        'base_url',
        'api_url',
        'asset_url',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
                $model->sync_hash = hash('sha256', uniqid('config_url_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_url_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(ConfigSite::class, 'site_id');
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ConfigEnvironment::class, 'ambiente_id');
    }

    // Scopes
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForSite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    public function scopeForEnvironment($query, $ambienteId)
    {
        return $query->where('ambiente_id', $ambienteId);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeByDomain($query, $dominio)
    {
        return $query->where('dominio', $dominio);
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

    public function getFullApiUrlAttribute(): string
    {
        return $this->api_url ?: $this->base_url . '/api';
    }

    public function getFullAssetUrlAttribute(): string
    {
        return $this->asset_url ?: $this->base_url . '/assets';
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

    public function isSecure(): bool
    {
        return str_starts_with($this->base_url, 'https://');
    }

    public function isLocal(): bool
    {
        return str_contains($this->dominio, 'localhost') ||
            str_contains($this->dominio, '127.0.0.1') ||
            str_contains($this->dominio, '::1');
    }
}
