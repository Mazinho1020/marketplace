<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWebhook extends Model
{
    protected $fillable = [
        'transaction_id',
        'gateway_provider',
        'event_type',
        'webhook_id',
        'raw_payload',
        'headers',
        'processed',
        'processed_at',
        'processing_attempts',
        'last_error',
        'signature_valid',
        'ip_address'
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'headers' => 'array',
        'processed' => 'boolean',
        'signature_valid' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }

    // Scopes
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('gateway_provider', $provider);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeWithSignatureValid($query)
    {
        return $query->where('signature_valid', true);
    }

    public function scopeFailedProcessing($query)
    {
        return $query->where('processed', false)
            ->where('processing_attempts', '>', 0);
    }

    // Métodos de ação
    public function markAsProcessed(): void
    {
        $this->update([
            'processed' => true,
            'processed_at' => now()
        ]);
    }

    public function markAsInvalid(string $reason): void
    {
        $this->update([
            'signature_valid' => false,
            'last_error' => $reason,
            'processed' => true,
            'processed_at' => now()
        ]);
    }

    public function incrementAttempts(): void
    {
        $this->increment('processing_attempts');
    }

    public function setError(string $error): void
    {
        $this->update([
            'last_error' => $error,
            'processing_attempts' => $this->processing_attempts + 1
        ]);
    }

    public function isValid(): bool
    {
        return $this->signature_valid === true;
    }

    public function canRetry(): bool
    {
        return !$this->processed && $this->processing_attempts < 5;
    }

    public function needsProcessing(): bool
    {
        return !$this->processed && $this->isValid();
    }

    // Getters
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->processed) {
            return 'Processado';
        }

        if ($this->signature_valid === false) {
            return 'Inválido';
        }

        if ($this->processing_attempts > 0) {
            return 'Erro';
        }

        return 'Pendente';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->processed) {
            return 'success';
        }

        if ($this->signature_valid === false) {
            return 'danger';
        }

        if ($this->processing_attempts > 0) {
            return 'warning';
        }

        return 'info';
    }
}
