<?php

namespace App\Affiliates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $table = 'affiliate_commissions';

    protected $fillable = [
        'affiliate_id',
        'referral_id',
        'merchant_id',
        'subscription_id',
        'transaction_id',
        'amount',
        'commission_rate',
        'status',
        'paid_at',
        'payment_id',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status possíveis da comissão
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Relacionamentos
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(AffiliateReferral::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(\App\Merchants\Models\Merchant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(\App\Merchants\Models\MerchantSubscription::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(AffiliatePayment::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeByAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
    }

    /**
     * Métodos de negócio
     */

    /**
     * Aprovar comissão
     */
    public function approve(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        return $this->save();
    }

    /**
     * Marcar como paga
     */
    public function markAsPaid($paymentId = null): bool
    {
        $this->status = self::STATUS_PAID;
        $this->paid_at = now();

        if ($paymentId) {
            $this->payment_id = $paymentId;
        }

        return $this->save();
    }

    /**
     * Cancelar comissão
     */
    public function cancel(string $reason = null): bool
    {
        $this->status = self::STATUS_CANCELLED;

        if ($reason) {
            $this->notes = $reason;
        }

        return $this->save();
    }

    /**
     * Disputar comissão
     */
    public function dispute(string $reason): bool
    {
        $this->status = self::STATUS_DISPUTED;
        $this->notes = $reason;

        return $this->save();
    }

    /**
     * Verificar se pode ser paga
     */
    public function canBePaid(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Obter status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Aprovada',
            self::STATUS_PAID => 'Paga',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_DISPUTED => 'Disputada',
            default => 'Desconhecido'
        };
    }

    /**
     * Calcular tempo até pagamento
     */
    public function getDaysUntilPaymentAttribute(): ?int
    {
        if ($this->status === self::STATUS_PAID) {
            return null;
        }

        // Assumindo que comissões são pagas 30 dias após aprovação
        $approvalDate = $this->status === self::STATUS_APPROVED ?
            $this->updated_at :
            $this->created_at->addDays(7); // 7 dias para aprovação

        $paymentDate = $approvalDate->addDays(30);

        return now()->diffInDays($paymentDate, false);
    }

    /**
     * Formatar para API
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        $array['status_label'] = $this->status_label;
        $array['days_until_payment'] = $this->days_until_payment;

        return $array;
    }
}
