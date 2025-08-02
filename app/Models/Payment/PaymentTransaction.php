<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Enums\Payment\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\SourceType;
use App\Enums\Payment\GatewayProvider;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'uuid',
        'transaction_code',
        'empresa_id',
        'tenant_id',
        'source_type',
        'source_id',
        'source_reference',
        'amount_original',
        'amount_discount',
        'amount_fees',
        'amount_final',
        'currency_code',
        'payment_method',
        'installments',
        'installment_amount',
        'gateway_provider',
        'gateway_transaction_id',
        'gateway_status',
        'gateway_raw_response',
        'status',
        'expires_at',
        'processed_at',
        'approved_at',
        'cancelled_at',
        'customer_name',
        'customer_email',
        'customer_document',
        'customer_phone',
        'success_url',
        'cancel_url',
        'notification_url',
        'payment_data',
        'description',
        'internal_notes',
        'metadata',
        'created_by_user_id',
        'version'
    ];

    protected $casts = [
        'source_type' => SourceType::class,
        'payment_method' => PaymentMethod::class,
        'gateway_provider' => GatewayProvider::class,
        'status' => PaymentStatus::class,
        'gateway_raw_response' => 'array',
        'payment_data' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_original' => 'decimal:2',
        'amount_discount' => 'decimal:2',
        'amount_fees' => 'decimal:2',
        'amount_final' => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->uuid)) {
                $transaction->uuid = Str::uuid();
            }
            if (empty($transaction->version)) {
                $transaction->version = 1;
            }
        });
    }

    // Relacionamentos
    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class, 'transaction_id');
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(PaymentWebhook::class, 'transaction_id');
    }

    // Scopes
    public function scopeByEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeByStatus($query, PaymentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySource($query, SourceType $sourceType, $sourceId = null)
    {
        $query = $query->where('source_type', $sourceType);
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        return $query;
    }

    public function scopeOnline($query)
    {
        return $query->whereNotNull('gateway_provider');
    }

    public function scopeOffline($query)
    {
        return $query->whereNull('gateway_provider');
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::PROCESSING]);
    }

    // Métodos de estado
    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === PaymentStatus::APPROVED;
    }

    public function isDeclined(): bool
    {
        return $this->status === PaymentStatus::DECLINED;
    }

    public function isCancelled(): bool
    {
        return $this->status === PaymentStatus::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::EXPIRED ||
            ($this->expires_at && $this->expires_at->isPast());
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentStatus::REFUNDED;
    }

    public function isOnline(): bool
    {
        return !is_null($this->gateway_provider);
    }

    public function isOffline(): bool
    {
        return is_null($this->gateway_provider);
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    // Métodos de ação
    public function approve(): void
    {
        if ($this->isFinal() && !$this->isPending()) {
            throw new \InvalidArgumentException('Transação não pode ser aprovada no status atual: ' . $this->status->value);
        }

        $this->update([
            'status' => PaymentStatus::APPROVED,
            'approved_at' => now(),
            'processed_at' => now(),
        ]);

        $this->logEvent('payment_approved', 'system');
    }

    public function decline(string $reason = null): void
    {
        if ($this->isFinal() && !$this->isPending()) {
            throw new \InvalidArgumentException('Transação não pode ser negada no status atual: ' . $this->status->value);
        }

        $this->update([
            'status' => PaymentStatus::DECLINED,
            'processed_at' => now(),
            'internal_notes' => $reason,
        ]);

        $this->logEvent('payment_declined', 'system', ['reason' => $reason]);
    }

    public function cancel(string $reason = null): void
    {
        if ($this->isApproved()) {
            throw new \InvalidArgumentException('Transação aprovada não pode ser cancelada. Use estorno.');
        }

        $this->update([
            'status' => PaymentStatus::CANCELLED,
            'cancelled_at' => now(),
            'internal_notes' => $reason,
        ]);

        $this->logEvent('payment_cancelled', 'system', ['reason' => $reason]);
    }

    public function expire(): void
    {
        if (!$this->isPending()) {
            throw new \InvalidArgumentException('Apenas transações pendentes podem expirar');
        }

        $this->update([
            'status' => PaymentStatus::EXPIRED,
        ]);

        $this->logEvent('payment_expired', 'system');
    }

    public function refund(float $amount = null): void
    {
        if (!$this->isApproved()) {
            throw new \InvalidArgumentException('Apenas transações aprovadas podem ser estornadas');
        }

        $this->update([
            'status' => PaymentStatus::REFUNDED,
        ]);

        $this->logEvent('payment_refunded', 'system', ['amount' => $amount ?? $this->amount_final]);
    }

    public function updateFromGateway(array $response): void
    {
        $this->update([
            'gateway_raw_response' => $response,
            'gateway_status' => $response['status'] ?? null,
            'processed_at' => now(),
            'version' => $this->version + 1,
        ]);

        $this->logEvent('gateway_response', 'gateway', $response);
    }

    public function logEvent(string $eventType, string $triggeredBy, array $data = []): void
    {
        $this->events()->create([
            'event_type' => $eventType,
            'event_data' => $data,
            'previous_status' => $this->getOriginal('status')?->value,
            'new_status' => $this->status->value,
            'triggered_by' => $triggeredBy,
            'user_id' => auth()->id() ?? null,
        ]);
    }

    // Getters customizados
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method->label();
    }

    public function getSourceTypeLabelAttribute(): string
    {
        return $this->source_type->label();
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->amount_final, 2, ',', '.');
    }

    public function getQrCodeAttribute(): ?string
    {
        return $this->payment_data['qr_code'] ?? null;
    }

    public function getBarCodeAttribute(): ?string
    {
        return $this->payment_data['bar_code'] ?? null;
    }

    public function getDigitableLineAttribute(): ?string
    {
        return $this->payment_data['digitable_line'] ?? null;
    }

    public function getPaymentUrlAttribute(): ?string
    {
        return $this->payment_data['payment_url'] ?? null;
    }
}
