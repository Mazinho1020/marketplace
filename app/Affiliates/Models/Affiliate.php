<?php

namespace App\Affiliates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $table = 'affiliates';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf_cnpj',
        'status',
        'commission_rate',
        'payment_method',
        'bank_details',
        'affiliate_code',
        'referral_link',
        'approved_at',
        'rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'bank_details' => 'array',
        'commission_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'bank_details'
    ];

    /**
     * Status possíveis do affiliate
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Métodos de pagamento disponíveis
     */
    const PAYMENT_PIX = 'pix';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_PAYPAL = 'paypal';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($affiliate) {
            if (empty($affiliate->affiliate_code)) {
                $affiliate->affiliate_code = $affiliate->generateUniqueCode();
            }
            if (empty($affiliate->referral_link)) {
                $affiliate->referral_link = $affiliate->generateReferralLink();
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AffiliatePayment::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Merchants\Models\Merchant::class,
            'affiliate_referrals',
            'affiliate_id',
            'merchant_id'
        )->withPivot('commission_rate', 'status', 'referred_at')
            ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByCommissionRate($query, $operator, $rate)
    {
        return $query->where('commission_rate', $operator, $rate);
    }

    /**
     * Métodos de negócio
     */

    /**
     * Aprovar affiliate
     */
    public function approve(float $commissionRate = null): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        $this->rejected_at = null;
        $this->rejection_reason = null;

        if ($commissionRate !== null) {
            $this->commission_rate = $commissionRate;
        }

        return $this->save();
    }

    /**
     * Rejeitar affiliate
     */
    public function reject(string $reason): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        $this->approved_at = null;

        return $this->save();
    }

    /**
     * Suspender affiliate
     */
    public function suspend(string $reason): bool
    {
        $this->status = self::STATUS_SUSPENDED;
        $this->rejection_reason = $reason;

        return $this->save();
    }

    /**
     * Reativar affiliate
     */
    public function reactivate(): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->rejection_reason = null;

        return $this->save();
    }

    /**
     * Verificar se affiliate está ativo
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Verificar se pode receber comissões
     */
    public function canEarnCommissions(): bool
    {
        return $this->isActive() && $this->commission_rate > 0;
    }

    /**
     * Obter total de comissões ganhas
     */
    public function getTotalCommissions(): float
    {
        return $this->commissions()
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Obter comissões pendentes
     */
    public function getPendingCommissions(): float
    {
        return $this->commissions()
            ->where('status', 'pending')
            ->sum('amount');
    }

    /**
     * Obter número de referrals ativos
     */
    public function getActiveReferralsCount(): int
    {
        return $this->referrals()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Obter estatísticas do affiliate
     */
    public function getStatistics(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(ar.id) as total_referrals,
                COUNT(CASE WHEN ar.status = 'active' THEN 1 END) as active_referrals,
                COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) as converted_referrals,
                COALESCE(SUM(ac.amount), 0) as total_commissions,
                COALESCE(SUM(CASE WHEN ac.status = 'pending' THEN ac.amount END), 0) as pending_commissions,
                COALESCE(SUM(CASE WHEN ac.status = 'paid' THEN ac.amount END), 0) as paid_commissions,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(ar.id), 0), 0
                    ), 2
                ) as conversion_rate
            FROM affiliates a
            LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id
            LEFT JOIN affiliate_commissions ac ON a.id = ac.affiliate_id
            WHERE a.id = ?
            GROUP BY a.id
        ", [$this->id]);

        return [
            'total_referrals' => (int) ($stats->total_referrals ?? 0),
            'active_referrals' => (int) ($stats->active_referrals ?? 0),
            'converted_referrals' => (int) ($stats->converted_referrals ?? 0),
            'conversion_rate' => (float) ($stats->conversion_rate ?? 0),
            'total_commissions' => (float) ($stats->total_commissions ?? 0),
            'pending_commissions' => (float) ($stats->pending_commissions ?? 0),
            'paid_commissions' => (float) ($stats->paid_commissions ?? 0),
            'commission_rate' => $this->commission_rate,
            'days_active' => $this->approved_at ? $this->approved_at->diffInDays(now()) : 0
        ];
    }

    /**
     * Obter histórico de performance mensal
     */
    public function getMonthlyPerformance(int $months = 12): array
    {
        $results = DB::select("
            SELECT 
                DATE_FORMAT(ar.created_at, '%Y-%m') as month,
                COUNT(ar.id) as referrals,
                COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) as conversions,
                COALESCE(SUM(ac.amount), 0) as commissions
            FROM affiliate_referrals ar
            LEFT JOIN affiliate_commissions ac ON ar.id = ac.referral_id
            WHERE ar.affiliate_id = ? 
            AND ar.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(ar.created_at, '%Y-%m')
            ORDER BY month DESC
        ", [$this->id, $months]);

        return collect($results)->map(function ($row) {
            return [
                'month' => $row->month,
                'referrals' => (int) $row->referrals,
                'conversions' => (int) $row->conversions,
                'commissions' => (float) $row->commissions,
                'conversion_rate' => $row->referrals > 0 ?
                    round(($row->conversions / $row->referrals) * 100, 2) : 0
            ];
        })->toArray();
    }

    /**
     * Calcular comissão para um valor
     */
    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->commission_rate / 100);
    }

    /**
     * Verificar se pode receber pagamento
     */
    public function canReceivePayment(): bool
    {
        return $this->isActive() &&
            $this->payment_method &&
            $this->getPendingCommissions() > 0;
    }

    /**
     * Validar dados bancários
     */
    public function validateBankDetails(): array
    {
        $errors = [];

        if ($this->payment_method === self::PAYMENT_BANK_TRANSFER) {
            $required = ['bank_code', 'agency', 'account', 'account_type', 'holder_name', 'holder_cpf'];

            foreach ($required as $field) {
                if (empty($this->bank_details[$field])) {
                    $errors[] = "Campo {$field} é obrigatório para transferência bancária";
                }
            }
        }

        if ($this->payment_method === self::PAYMENT_PIX) {
            if (empty($this->bank_details['pix_key'])) {
                $errors[] = "Chave PIX é obrigatória";
            }
        }

        return $errors;
    }

    /**
     * Atualizar dados bancários
     */
    public function updateBankDetails(array $details): bool
    {
        $this->bank_details = array_merge($this->bank_details ?? [], $details);
        return $this->save();
    }

    /**
     * Métodos auxiliares privados
     */

    /**
     * Gerar código único do affiliate
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'AFF' . strtoupper(Str::random(6));
        } while (self::where('affiliate_code', $code)->exists());

        return $code;
    }

    /**
     * Gerar link de referral
     */
    private function generateReferralLink(): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/ref/{$this->affiliate_code}";
    }

    /**
     * Atributos calculados
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_SUSPENDED => 'Suspenso',
            self::STATUS_INACTIVE => 'Inativo',
            default => 'Desconhecido'
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_PIX => 'PIX',
            self::PAYMENT_BANK_TRANSFER => 'Transferência Bancária',
            self::PAYMENT_PAYPAL => 'PayPal',
            default => 'Não definido'
        };
    }

    /**
     * Formatação para API
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        // Adicionar campos calculados
        $array['status_label'] = $this->status_label;
        $array['payment_method_label'] = $this->payment_method_label;
        $array['total_commissions'] = $this->getTotalCommissions();
        $array['pending_commissions'] = $this->getPendingCommissions();
        $array['active_referrals_count'] = $this->getActiveReferralsCount();
        $array['is_active'] = $this->isActive();
        $array['can_earn_commissions'] = $this->canEarnCommissions();

        // Remover dados sensíveis
        unset($array['bank_details']);

        return $array;
    }
}
