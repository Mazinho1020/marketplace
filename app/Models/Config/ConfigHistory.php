<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Model ConfigHistory
 * 
 * @property int $id
 * @property int $empresa_id
 * @property int $config_value_id
 * @property string|null $valor_anterior
 * @property string|null $valor_novo
 * @property int|null $usuario_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $motivo
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigHistory extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_history';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'config_value_id',
        'valor_anterior',
        'valor_novo',
        'usuario_id',
        'ip',
        'user_agent',
        'motivo',
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
                $model->sync_hash = hash('sha256', uniqid('config_hist_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }

            // Auto-preenchimento de dados do usuário e requisição
            if (empty($model->usuario_id) && Auth::check()) {
                $model->usuario_id = Auth::id();
            }
            if (empty($model->ip)) {
                $model->ip = request()->ip();
            }
            if (empty($model->user_agent)) {
                $model->user_agent = request()->userAgent();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_hist_upd_', true));
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
    }

    public function configValue(): BelongsTo
    {
        return $this->belongsTo(ConfigValue::class, 'config_value_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    // Alias para facilitar uso no controller
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(ConfigSite::class, 'site_id');
    }

    // Scopes
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForConfigValue($query, $configValueId)
    {
        return $query->where('config_value_id', $configValueId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('usuario_id', $userId);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
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

    public function getTipoAlteracaoAttribute(): string
    {
        if ($this->valor_anterior === null && $this->valor_novo !== null) {
            return 'Criação';
        } elseif ($this->valor_anterior !== null && $this->valor_novo === null) {
            return 'Exclusão';
        } else {
            return 'Alteração';
        }
    }

    public function getResumoAlteracaoAttribute(): string
    {
        $tipo = $this->tipo_alteracao;

        switch ($tipo) {
            case 'Criação':
                return "Valor criado: {$this->valor_novo}";
            case 'Exclusão':
                return "Valor removido: {$this->valor_anterior}";
            case 'Alteração':
                return "De: {$this->valor_anterior} → Para: {$this->valor_novo}";
            default:
                return 'Alteração realizada';
        }
    }

    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Sistema';
    }

    public function getIpLocationAttribute(): string
    {
        if (!$this->ip) {
            return 'Desconhecido';
        }

        // Verificar se é IP local
        if (in_array($this->ip, ['127.0.0.1', '::1', 'localhost'])) {
            return 'Local';
        }

        return $this->ip;
    }

    public function getBrowserAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Desconhecido';
        }

        // Simplificar user agent para mostrar apenas o browser principal
        if (str_contains($this->user_agent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($this->user_agent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($this->user_agent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($this->user_agent, 'Edge')) {
            return 'Edge';
        } else {
            return 'Outro';
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

    public function isCriacao(): bool
    {
        return $this->tipo_alteracao === 'Criação';
    }

    public function isExclusao(): bool
    {
        return $this->tipo_alteracao === 'Exclusão';
    }

    public function isAlteracao(): bool
    {
        return $this->tipo_alteracao === 'Alteração';
    }

    /**
     * Cria um registro de histórico para uma mudança de configuração
     */
    public static function recordChange(
        int $empresaId,
        int $configValueId,
        $valorAnterior = null,
        $valorNovo = null,
        string $motivo = null
    ): self {
        return static::create([
            'empresa_id' => $empresaId,
            'config_value_id' => $configValueId,
            'valor_anterior' => $valorAnterior,
            'valor_novo' => $valorNovo,
            'motivo' => $motivo,
        ]);
    }
}
