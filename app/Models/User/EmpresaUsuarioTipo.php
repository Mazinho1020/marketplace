<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmpresaUsuarioTipo extends Model
{
    use HasFactory, SoftDeletes;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'empresa_usuario_tipos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'nivel_acesso',
        'is_active',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'nivel_acesso' => 'integer',
        'sync_data' => 'datetime',
    ];

    // 2. CONSTANTES
    public const STATUS_ATIVO = true;
    public const STATUS_INATIVO = false;

    public const CODIGO_ADMIN = 'admin';
    public const CODIGO_COMERCIANTE = 'comerciante';
    public const CODIGO_CLIENTE = 'cliente';
    public const CODIGO_ENTREGADOR = 'entregador';

    public const NIVEL_CLIENTE = 10;
    public const NIVEL_ENTREGADOR = 20;
    public const NIVEL_COMERCIANTE = 50;
    public const NIVEL_ADMIN = 100;

    // Constantes para sincronização
    public const SYNC_PENDING = 'pending';
    public const SYNC_SYNCED = 'synced';
    public const SYNC_ERROR = 'error';
    public const SYNC_IGNORED = 'ignored';

    public const SYNC_STATUS_OPTIONS = [
        self::SYNC_PENDING => 'Pendente',
        self::SYNC_SYNCED => 'Sincronizado',
        self::SYNC_ERROR => 'Erro',
        self::SYNC_IGNORED => 'Ignorado',
    ];

    public const TIPOS_USUARIO = [
        self::CODIGO_ADMIN => 'Administrador',
        self::CODIGO_COMERCIANTE => 'Comerciante',
        self::CODIGO_CLIENTE => 'Cliente',
        self::CODIGO_ENTREGADOR => 'Entregador',
    ];

    // 3. RELACIONAMENTOS
    public function usuarios(): HasMany
    {
        return $this->hasMany(\App\Models\User\EmpresaUsuario::class, 'tipo_id');
    }

    public function usuarioTipoRel(): HasMany
    {
        return $this->hasMany(EmpresaUsuarioTipoRel::class, 'tipo_id');
    }

    // 4. SCOPES
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode(Builder $query, string $codigo): Builder
    {
        return $query->where('codigo', $codigo);
    }

    public function scopeByLevel(Builder $query, int $nivel): Builder
    {
        return $query->where('nivel_acesso', '>=', $nivel);
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    public function scopeSynced(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_SYNCED);
    }

    public function scopeSyncError(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_ERROR);
    }

    // 5. ACCESSORS/MUTATORS
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_active
                ? '<span class="badge bg-success">Ativo</span>'
                : '<span class="badge bg-danger">Inativo</span>'
        );
    }

    protected function syncStatusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->sync_status) {
                self::SYNC_SYNCED => '<span class="badge bg-success">Sincronizado</span>',
                self::SYNC_PENDING => '<span class="badge bg-warning">Pendente</span>',
                self::SYNC_ERROR => '<span class="badge bg-danger">Erro</span>',
                self::SYNC_IGNORED => '<span class="badge bg-secondary">Ignorado</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            }
        );
    }

    protected function nivelAcessoBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->nivel_acesso) {
                self::NIVEL_ADMIN => '<span class="badge bg-danger">Admin</span>',
                self::NIVEL_COMERCIANTE => '<span class="badge bg-warning">Comerciante</span>',
                self::NIVEL_ENTREGADOR => '<span class="badge bg-info">Entregador</span>',
                self::NIVEL_CLIENTE => '<span class="badge bg-primary">Cliente</span>',
                default => '<span class="badge bg-secondary">Padrão</span>'
            }
        );
    }

    // 6. MÉTODOS CUSTOMIZADOS
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function needsSync(): bool
    {
        return $this->sync_status === self::SYNC_PENDING;
    }

    public function isSynced(): bool
    {
        return $this->sync_status === self::SYNC_SYNCED;
    }

    public function generateSyncHash(): string
    {
        $data = collect($this->getAttributes())
            ->except(['id', 'sync_hash', 'sync_status', 'sync_data', 'created_at', 'updated_at'])
            ->toJson();

        return md5($data);
    }

    public function markForSync(): void
    {
        $this->update([
            'sync_status' => self::SYNC_PENDING,
            'sync_hash' => $this->generateSyncHash()
        ]);
    }

    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => self::SYNC_SYNCED,
            'sync_data' => now()
        ]);
    }

    public function markSyncError(): void
    {
        $this->update(['sync_status' => self::SYNC_ERROR]);
    }

    public function canAccess(int $requiredLevel): bool
    {
        return $this->nivel_acesso >= $requiredLevel;
    }

    /**
     * Métodos estáticos de conveniência
     */
    public static function getByCode(string $codigo): ?self
    {
        return static::where('codigo', $codigo)->first();
    }

    public static function getAdminType(): ?self
    {
        return static::getByCode(self::CODIGO_ADMIN);
    }

    public static function getComercianteType(): ?self
    {
        return static::getByCode(self::CODIGO_COMERCIANTE);
    }

    public static function getClienteType(): ?self
    {
        return static::getByCode(self::CODIGO_CLIENTE);
    }

    public static function getEntregadorType(): ?self
    {
        return static::getByCode(self::CODIGO_ENTREGADOR);
    }

    // 7. BOOT METHOD
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // Marcar para sincronização
            $model->sync_status = self::SYNC_PENDING;
            $model->sync_hash = $model->generateSyncHash();
        });

        static::updating(function ($model) {
            // Verificar se houve mudanças que requerem sincronização
            if ($model->isDirty() && !$model->isDirty(['sync_status', 'sync_data', 'sync_hash'])) {
                $model->sync_status = self::SYNC_PENDING;
                $model->sync_hash = $model->generateSyncHash();
            }
        });
    }
}
