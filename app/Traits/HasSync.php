<?php

namespace App\Traits;

use App\Enums\SyncStatusEnum;
use Carbon\Carbon;

trait HasSync
{
    /**
     * Marca o registro para sincronização
     */
    public function markForSync(): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::PENDENTE,
            'sync_hash' => null,
            'sync_data' => Carbon::now()
        ]);
    }

    /**
     * Marca como sincronizado
     */
    public function markAsSynced(string $hash = null): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::SINCRONIZADO,
            'sync_hash' => $hash ?? $this->generateSyncHash(),
            'sync_data' => Carbon::now()
        ]);
    }

    /**
     * Marca erro na sincronização
     */
    public function markSyncError(): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::ERRO,
            'sync_data' => Carbon::now()
        ]);
    }

    /**
     * Gera hash de sincronização
     */
    protected function generateSyncHash(): string
    {
        $data = $this->getAttributes();
        unset($data['sync_hash'], $data['sync_data'], $data['sync_status'], $data['updated_at']);

        return md5(json_encode($data));
    }

    /**
     * Verifica se precisa sincronizar
     */
    public function needsSync(): bool
    {
        return $this->sync_status === SyncStatusEnum::PENDENTE ||
            $this->sync_hash !== $this->generateSyncHash();
    }

    // Scopes
    public function scopePendingSync($query)
    {
        return $query->where('sync_status', SyncStatusEnum::PENDENTE);
    }

    public function scopeSynced($query)
    {
        return $query->where('sync_status', SyncStatusEnum::SINCRONIZADO);
    }

    public function scopeSyncError($query)
    {
        return $query->where('sync_status', SyncStatusEnum::ERRO);
    }

    public function scopeNeedsSync($query)
    {
        return $query->where(function ($q) {
            $q->where('sync_status', SyncStatusEnum::PENDENTE)
                ->orWhere('sync_status', SyncStatusEnum::ERRO);
        });
    }

    /**
     * Boot do trait
     */
    protected static function bootHasSync()
    {
        static::creating(function ($model) {
            $model->sync_status = SyncStatusEnum::PENDENTE;
            $model->sync_data = Carbon::now();
        });

        static::updating(function ($model) {
            if ($model->isDirty() && !$model->isDirty(['sync_status', 'sync_hash', 'sync_data'])) {
                $model->sync_status = SyncStatusEnum::PENDENTE;
                $model->sync_hash = null;
                $model->sync_data = Carbon::now();
            }
        });
    }
}
