<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Model ConfigValue
 * 
 * @property int $id
 * @property int $empresa_id
 * @property int $config_id
 * @property int|null $site_id
 * @property int|null $ambiente_id
 * @property string|null $valor
 * @property int|null $usuario_id
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigValue extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_values';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'config_id',
        'site_id',
        'ambiente_id',
        'valor',
        'usuario_id',
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
                $model->sync_hash = hash('sha256', uniqid('config_val_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }
            if (empty($model->usuario_id) && Auth::check()) {
                $model->usuario_id = Auth::id();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_val_upd_', true));
            }
            if ($model->isDirty('valor') && Auth::check()) {
                $model->usuario_id = Auth::id();
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(ConfigDefinition::class, 'config_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(ConfigSite::class, 'site_id');
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ConfigEnvironment::class, 'ambiente_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ConfigHistory::class, 'config_value_id');
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

    public function scopeGlobal($query)
    {
        return $query->whereNull('site_id')->whereNull('ambiente_id');
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
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

    public function getCastedValueAttribute()
    {
        if (!$this->definition) {
            return $this->valor;
        }

        return $this->castValue($this->valor, $this->definition->tipo);
    }

    public function getScopeTextAttribute(): string
    {
        if ($this->site_id && $this->ambiente_id) {
            return "Site: {$this->site->nome} | Ambiente: {$this->environment->nome}";
        } elseif ($this->site_id) {
            return "Site: {$this->site->nome} | Todos os ambientes";
        } elseif ($this->ambiente_id) {
            return "Todos os sites | Ambiente: {$this->environment->nome}";
        } else {
            return "Global (todos os sites e ambientes)";
        }
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

    public function isGlobal(): bool
    {
        return is_null($this->site_id) && is_null($this->ambiente_id);
    }

    public function isSiteSpecific(): bool
    {
        return !is_null($this->site_id);
    }

    public function isEnvironmentSpecific(): bool
    {
        return !is_null($this->ambiente_id);
    }

    /**
     * Converte o valor para o tipo correto
     */
    private function castValue($value, $type)
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => (bool) $value,
            'array' => explode(',', $value),
            'json' => json_decode($value, true) ?: [],
            'date', 'datetime' => $value,
            default => $value,
        };
    }

    /**
     * Prepara um valor para armazenamento
     */
    public static function prepareForStorage($value, $type): string
    {
        return match ($type) {
            'array' => is_array($value) ? implode(',', $value) : $value,
            'json' => is_array($value) || is_object($value) ? json_encode($value) : $value,
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }
}
