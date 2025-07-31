<?php

namespace App\Models\Config;

use App\Models\BaseModel;
use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model ConfigDbConnection
 * 
 * @property int $id
 * @property int $empresa_id
 * @property string $nome
 * @property int $ambiente_id
 * @property string $driver
 * @property string $host
 * @property int $porta
 * @property string $banco
 * @property string $usuario
 * @property string $senha
 * @property string $charset
 * @property string $collation
 * @property string|null $prefixo
 * @property bool $padrao
 * @property string|null $sync_hash
 * @property string $sync_status
 * @property \Carbon\Carbon|null $sync_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ConfigDbConnection extends BaseModel
{
    use HasFactory, SoftDeletes;

    /** @var string */
    protected $table = 'config_db_connections';

    /** @var array<string> */
    protected $fillable = [
        'empresa_id',
        'nome',
        'ambiente_id',
        'driver',
        'host',
        'porta',
        'banco',
        'usuario',
        'senha',
        'charset',
        'collation',
        'prefixo',
        'padrao',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    /** @var array<string> */
    protected $hidden = [
        'senha',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'porta' => 'integer',
        'padrao' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para drivers
    public const DRIVER_MYSQL = 'mysql';
    public const DRIVER_POSTGRESQL = 'postgresql';
    public const DRIVER_SQLITE = 'sqlite';
    public const DRIVER_SQLSERVER = 'sqlserver';

    public const DRIVERS = [
        self::DRIVER_MYSQL,
        self::DRIVER_POSTGRESQL,
        self::DRIVER_SQLITE,
        self::DRIVER_SQLSERVER,
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
                $model->sync_hash = hash('sha256', uniqid('config_db_', true));
            }
            if (empty($model->sync_status)) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
            }

            // Criptografar senha antes de salvar
            if (!empty($model->senha)) {
                $model->senha = encrypt($model->senha);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty('sync_data')) {
                $model->sync_status = self::SYNC_STATUS_PENDING;
                $model->sync_hash = hash('sha256', uniqid('config_db_upd_', true));
            }

            // Criptografar senha se foi alterada
            if ($model->isDirty('senha') && !empty($model->senha)) {
                $model->senha = encrypt($model->senha);
            }
        });
    }

    // Relacionamentos
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'empresa_id');
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

    public function scopeForEnvironment($query, $ambienteId)
    {
        return $query->where('ambiente_id', $ambienteId);
    }

    public function scopePadrao($query)
    {
        return $query->where('padrao', true);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', self::SYNC_STATUS_PENDING);
    }

    public function scopeByDriver($query, $driver)
    {
        return $query->where('driver', $driver);
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

    public function getSenhaDecryptedAttribute(): string
    {
        try {
            return decrypt($this->senha);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getDsnAttribute(): string
    {
        $dsn = "{$this->driver}:host={$this->host}";

        if ($this->porta) {
            $dsn .= ";port={$this->porta}";
        }

        if ($this->banco) {
            $dsn .= ";dbname={$this->banco}";
        }

        if ($this->charset) {
            $dsn .= ";charset={$this->charset}";
        }

        return $dsn;
    }

    public function getConnectionConfigAttribute(): array
    {
        return [
            'driver' => $this->driver,
            'host' => $this->host,
            'port' => $this->porta,
            'database' => $this->banco,
            'username' => $this->usuario,
            'password' => $this->senha_decrypted,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'prefix' => $this->prefixo,
        ];
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

    public function isPadrao(): bool
    {
        return $this->padrao;
    }

    public function testConnection(): bool
    {
        try {
            $pdo = new \PDO(
                $this->dsn,
                $this->usuario,
                $this->senha_decrypted
            );
            $pdo = null; // Fecha conexão
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function makeDefault(): bool
    {
        // Remove o padrão de outras conexões do mesmo ambiente
        static::where('ambiente_id', $this->ambiente_id)
            ->where('empresa_id', $this->empresa_id)
            ->where('id', '!=', $this->id)
            ->update(['padrao' => false]);

        return $this->update(['padrao' => true]);
    }
}
