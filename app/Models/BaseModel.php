<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Classe base para todos os models do sistema
 * 
 * Implementa funcionalidades comuns como:
 * - Configurações padrão
 * - Helpers de sincronização
 * - Métodos utilitários
 * - Relações com Business
 */
class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be treated as dates.
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sync_data' => 'datetime',
    ];

    /**
     * Gera hash para sincronização
     */
    public function generateSyncHash(): string
    {
        $data = $this->getAttributes();
        unset($data['id'], $data['sync_hash'], $data['sync_status'], $data['sync_data'], $data['created_at'], $data['updated_at']);

        return hash('sha256', serialize($data));
    }

    /**
     * Marca como pendente para sincronização
     */
    public function markForSync(): void
    {
        $this->update([
            'sync_hash' => $this->generateSyncHash(),
            'sync_status' => 'pending',
            'sync_data' => now()
        ]);
    }

    /**
     * Marca como sincronizado
     */
    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => 'synced',
            'sync_data' => now()
        ]);
    }

    /**
     * Marca como erro na sincronização
     */
    public function markSyncError(): void
    {
        $this->update([
            'sync_status' => 'error',
            'sync_data' => now()
        ]);
    }

    /**
     * Verifica se precisa ser sincronizado
     */
    public function needsSync(): bool
    {
        return $this->sync_status === 'pending' || $this->sync_hash !== $this->generateSyncHash();
    }

    /**
     * Scope para registros que precisam de sincronização
     */
    public function scopeNeedsSync($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope para registros sincronizados
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope para registros com erro de sincronização
     */
    public function scopeSyncError($query)
    {
        return $query->where('sync_status', 'error');
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForBusiness($query, $businessId = null)
    {
        $businessId = $businessId ?? Auth::user()?->business_id;

        if ($businessId && $this->getTable() !== 'businesses') {
            return $query->where('business_id', $businessId);
        }

        return $query;
    }

    /**
     * Relacionamento com Business (quando aplicável)
     */
    public function business()
    {
        if ($this->getTable() !== 'businesses') {
            return $this->belongsTo(\App\Models\Business\Business::class);
        }

        return null;
    }
}
