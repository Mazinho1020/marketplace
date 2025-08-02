<?php

namespace App\Affiliates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliateReferral extends Model
{
    use HasFactory;

    protected $table = 'affiliate_referrals';

    protected $fillable = [
        'affiliate_id',
        'merchant_id',
        'commission_rate',
        'status',
        'referred_at',
        'converted_at',
        'first_payment_at',
        'total_commission_earned',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referrer_url',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_commission_earned' => 'decimal:2',
        'referred_at' => 'datetime',
        'converted_at' => 'datetime',
        'first_payment_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status possíveis do referral
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CONVERTED = 'converted';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relacionamentos
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(\App\Merchants\Models\Merchant::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'referral_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    public function scopeByAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Métodos de negócio
     */

    /**
     * Ativar referral (quando merchant se cadastra)
     */
    public function activate(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        $this->referred_at = now();

        return $this->save();
    }

    /**
     * Converter referral (quando merchant faz primeira compra)
     */
    public function convert(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $this->status = self::STATUS_CONVERTED;
        $this->converted_at = now();

        return $this->save();
    }

    /**
     * Cancelar referral
     */
    public function cancel(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save();
    }

    /**
     * Registrar primeiro pagamento
     */
    public function recordFirstPayment(): bool
    {
        if (!$this->first_payment_at) {
            $this->first_payment_at = now();
            return $this->save();
        }

        return true;
    }

    /**
     * Verificar se está ativo
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Verificar se foi convertido
     */
    public function isConverted(): bool
    {
        return $this->status === self::STATUS_CONVERTED;
    }

    /**
     * Obter tempo até conversão
     */
    public function getTimeToConversion(): ?int
    {
        if (!$this->converted_at || !$this->referred_at) {
            return null;
        }

        return $this->referred_at->diffInDays($this->converted_at);
    }

    /**
     * Atualizar total de comissão ganha
     */
    public function updateTotalCommission(): bool
    {
        $total = $this->commissions()->where('status', 'paid')->sum('amount');
        $this->total_commission_earned = $total;

        return $this->save();
    }

    /**
     * Obter origem da conversão formatada
     */
    public function getSourceAttribute(): string
    {
        if ($this->utm_source && $this->utm_medium) {
            return "{$this->utm_source} / {$this->utm_medium}";
        }

        if ($this->utm_source) {
            return $this->utm_source;
        }

        if ($this->referrer_url) {
            $parsed = parse_url($this->referrer_url);
            return $parsed['host'] ?? 'Direct';
        }

        return 'Direct';
    }

    /**
     * Obter status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_CONVERTED => 'Convertido',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    /**
     * Formatar para API
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        $array['status_label'] = $this->status_label;
        $array['source'] = $this->source;
        $array['time_to_conversion'] = $this->getTimeToConversion();
        $array['is_active'] = $this->isActive();
        $array['is_converted'] = $this->isConverted();

        return $array;
    }
}
