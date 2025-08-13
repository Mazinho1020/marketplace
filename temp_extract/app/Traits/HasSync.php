<?php

namespace App\Traits;

use App\Enums\SyncStatusEnum;
use Carbon\Carbon;

trait HasSync
{
    public function markForSync(): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::PENDENTE,
            'sync_data' => Carbon::now(),
            'sync_hash' => null
        ]);
    }

    public function markAsSynced(string $hash = null): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::SINCRONIZADO,
            'sync_hash' => $hash ?? $this->generateSyncHash()
        ]);
    }

    public function markSyncError(): void
    {
        $this->update([
            'sync_status' => SyncStatusEnum::ERRO,
            'sync_data' => Carbon::now()
        ]);
    }

    protected function generateSyncHash(): string
    {
        return md5(serialize($this->toArray()));
    }

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
}