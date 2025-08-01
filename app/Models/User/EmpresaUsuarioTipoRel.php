<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaUsuarioTipoRel extends Model
{
    use HasFactory, SoftDeletes;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'empresa_usuario_tipo_rel';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'tipo_id',
        'is_primary',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_primary' => 'boolean',
        'sync_data' => 'datetime',
    ];

    // 2. CONSTANTES
    // Constantes para sincronização
    public const SYNC_PENDING = 'pendente';
    public const SYNC_SYNCED = 'sincronizado';
    public const SYNC_ERROR = 'erro';
    public const SYNC_IGNORED = 'ignorado';

    public const SYNC_STATUS_OPTIONS = [
        self::SYNC_PENDING => 'Pendente',
        self::SYNC_SYNCED => 'Sincronizado',
        self::SYNC_ERROR => 'Erro',
        self::SYNC_IGNORED => 'Ignorado',
    ];

    // 3. RELACIONAMENTOS
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(EmpresaUsuario::class, 'usuario_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(EmpresaUsuarioTipo::class, 'tipo_id');
    }

    // 4. SCOPES
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('usuario_id', $userId);
    }

    public function scopeForType(Builder $query, int $tipoId): Builder
    {
        return $query->where('tipo_id', $tipoId);
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

    // 5. MÉTODOS CUSTOMIZADOS
    public function isPrimary(): bool
    {
        return $this->is_primary === true;
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

    /**
     * Métodos estáticos de conveniência
     */
    public static function setPrimaryType(int $userId, int $tipoId): bool
    {
        // Primeiro, remover primary de todos os tipos do usuário
        static::where('usuario_id', $userId)->update(['is_primary' => false]);

        // Depois, definir o tipo especificado como primário
        return static::updateOrCreate(
            ['usuario_id' => $userId, 'tipo_id' => $tipoId],
            ['is_primary' => true]
        );
    }

    public static function getUserTypes(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return static::with('tipo')
            ->where('usuario_id', $userId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public static function getUserPrimaryType(int $userId): ?EmpresaUsuarioTipo
    {
        $rel = static::with('tipo')
            ->where('usuario_id', $userId)
            ->where('is_primary', true)
            ->first();

        return $rel ? $rel->tipo : null;
    }

    // 6. BOOT METHOD
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

        // Quando um tipo for definido como primário, remover primary dos outros
        static::updating(function ($model) {
            if ($model->isDirty('is_primary') && $model->is_primary) {
                static::where('usuario_id', $model->usuario_id)
                    ->where('id', '!=', $model->id)
                    ->update(['is_primary' => false]);
            }
        });

        static::creating(function ($model) {
            if ($model->is_primary) {
                static::where('usuario_id', $model->usuario_id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
