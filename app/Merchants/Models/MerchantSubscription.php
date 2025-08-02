<?php

namespace App\Merchants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Config\ConfigManager;

class MerchantSubscription extends Model
{
    protected $fillable = [
        'merchant_id',
        'plan_id',
        'plan_code',
        'plan_name',
        'features',
        'limits',
        'billing_cycle',
        'amount',
        'currency',
        'status',
        'started_at',
        'expires_at',
        'cancelled_at',
        'trial_ends_at',
        'auto_renewal',
        'last_payment_at',
        'next_payment_at',
        'gateway_customer_id',
        'gateway_subscription_id'
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'amount' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_at' => 'datetime',
        'auto_renewal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamentos
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Verificar se a assinatura está ativa
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
            ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Verificar se está em período trial
     */
    public function isInTrial(): bool
    {
        return $this->status === 'trial' &&
            $this->trial_ends_at &&
            $this->trial_ends_at->isFuture();
    }

    /**
     * Verificar se está vencida
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Verificar se precisa de renovação
     */
    public function needsRenewal(): bool
    {
        if (!$this->auto_renewal || $this->billing_cycle === 'lifetime') {
            return false;
        }

        return $this->next_payment_at && $this->next_payment_at->isPast();
    }

    /**
     * Calcular dias para vencimento
     */
    public function getDaysToExpiration(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Ativar assinatura
     */
    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
            'last_payment_at' => now(),
            'next_payment_at' => $this->calculateNextPaymentDate()
        ]);
    }

    /**
     * Suspender assinatura
     */
    public function suspend(string $reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'auto_renewal' => false,
            'metadata' => array_merge($this->metadata ?? [], [
                'suspension_reason' => $reason,
                'suspended_at' => now()->toISOString()
            ])
        ]);
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renewal' => false,
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason
            ])
        ]);
    }

    /**
     * Renovar assinatura
     */
    public function renew(): void
    {
        $nextExpiration = $this->calculateNextExpirationDate();
        $nextPayment = $this->calculateNextPaymentDate();

        $this->update([
            'status' => 'active',
            'expires_at' => $nextExpiration,
            'last_payment_at' => now(),
            'next_payment_at' => $nextPayment
        ]);
    }

    /**
     * Fazer upgrade para um plano superior
     */
    public function upgradeTo(SubscriptionPlan $newPlan, array $options = []): void
    {
        $proRatedAmount = $this->calculateProRatedAmount($newPlan);

        $this->update([
            'plan_id' => $newPlan->id,
            'plan_code' => $newPlan->code,
            'plan_name' => $newPlan->name,
            'features' => $newPlan->features,
            'limits' => $newPlan->limits,
            'amount' => $options['billing_cycle'] === 'yearly'
                ? $newPlan->price_yearly
                : $newPlan->price_monthly,
            'billing_cycle' => $options['billing_cycle'] ?? $this->billing_cycle,
            'metadata' => array_merge($this->metadata ?? [], [
                'upgrade_date' => now()->toISOString(),
                'previous_plan' => $this->plan_code,
                'pro_rated_amount' => $proRatedAmount
            ])
        ]);
    }

    /**
     * Fazer downgrade (agendado para próxima renovação)
     */
    public function scheduleDowngradeTo(SubscriptionPlan $newPlan): void
    {
        $this->update([
            'metadata' => array_merge($this->metadata ?? [], [
                'scheduled_downgrade' => [
                    'plan_id' => $newPlan->id,
                    'plan_code' => $newPlan->code,
                    'effective_date' => $this->expires_at->toISOString()
                ]
            ])
        ]);
    }

    /**
     * Obter valor mensal equivalente (MRR)
     */
    public function getMonthlyValue(): float
    {
        return match ($this->billing_cycle) {
            'monthly' => $this->amount,
            'yearly' => $this->amount / 12,
            'lifetime' => 0, // Lifetime não contribui para MRR
            default => 0
        };
    }

    /**
     * Calcular próxima data de pagamento
     */
    private function calculateNextPaymentDate(): ?\Carbon\Carbon
    {
        if ($this->billing_cycle === 'lifetime') {
            return null;
        }

        $base = $this->last_payment_at ?? $this->started_at ?? now();

        return match ($this->billing_cycle) {
            'monthly' => $base->copy()->addMonth(),
            'yearly' => $base->copy()->addYear(),
            default => null
        };
    }

    /**
     * Calcular próxima data de expiração
     */
    private function calculateNextExpirationDate(): ?\Carbon\Carbon
    {
        if ($this->billing_cycle === 'lifetime') {
            return null;
        }

        $base = $this->expires_at ?? now();

        return match ($this->billing_cycle) {
            'monthly' => $base->copy()->addMonth(),
            'yearly' => $base->copy()->addYear(),
            default => null
        };
    }

    /**
     * Calcular valor proporcional para upgrade
     */
    private function calculateProRatedAmount(SubscriptionPlan $newPlan): float
    {
        if (!$this->expires_at) {
            return 0;
        }

        $daysRemaining = now()->diffInDays($this->expires_at);
        $totalDays = match ($this->billing_cycle) {
            'monthly' => 30,
            'yearly' => 365,
            default => 30
        };

        $unusedValue = ($this->amount / $totalDays) * $daysRemaining;

        $newAmount = $this->billing_cycle === 'yearly'
            ? $newPlan->price_yearly
            : $newPlan->price_monthly;

        $newValue = ($newAmount / $totalDays) * $daysRemaining;

        return max(0, $newValue - $unusedValue);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', now());
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>', now());
    }

    public function scopeNeedsRenewal($query)
    {
        return $query->where('auto_renewal', true)
            ->where('next_payment_at', '<=', now())
            ->where('status', 'active');
    }

    /**
     * Atributos calculados
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->isExpired();
    }

    public function getDaysToExpirationAttribute(): ?int
    {
        return $this->getDaysToExpiration();
    }

    public function getMonthlyValueAttribute(): float
    {
        return $this->getMonthlyValue();
    }
}
