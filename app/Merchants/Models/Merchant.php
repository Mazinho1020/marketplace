<?php

namespace App\Merchants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Core\Config\ConfigManager;

class Merchant extends Model
{
    protected $fillable = [
        'company_name',
        'trading_name',
        'document',
        'email',
        'phone',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
        'address_zipcode',
        'status',
        'license_key',
        'api_key'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com assinaturas
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class);
    }

    /**
     * Assinatura ativa atual
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(MerchantSubscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest();
    }

    /**
     * Verificar se tem assinatura ativa
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Verificar se tem uma feature específica
     */
    public function hasFeature(string $feature): bool
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            // Sem assinatura, só permite recursos básicos
            return in_array($feature, ['financeiro', 'relatorios_basicos']);
        }

        $features = is_array($subscription->features)
            ? $subscription->features
            : json_decode($subscription->features, true);

        return in_array($feature, $features ?? []);
    }

    /**
     * Verificar se está dentro dos limites
     */
    public function isWithinLimits(): bool
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return false;
        }

        $limits = is_array($subscription->limits)
            ? $subscription->limits
            : json_decode($subscription->limits, true);

        // Verificar limite de usuários
        if (isset($limits['users'])) {
            $currentUsers = $this->users()->count();
            if ($currentUsers > $limits['users']) {
                return false;
            }
        }

        // Verificar limite de transações mensais
        if (isset($limits['transactions_per_month'])) {
            $monthlyTransactions = $this->getMonthlyTransactionCount();
            if ($monthlyTransactions > $limits['transactions_per_month']) {
                return false;
            }
        }

        // Verificar limite de empresas
        if (isset($limits['companies'])) {
            $currentCompanies = $this->companies()->count();
            if ($currentCompanies > $limits['companies']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obter features ativas
     */
    public function getActiveFeatures(): array
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return ['financeiro', 'relatorios_basicos'];
        }

        $features = is_array($subscription->features)
            ? $subscription->features
            : json_decode($subscription->features, true);

        return $features ?? [];
    }

    /**
     * Obter limites ativos
     */
    public function getActiveLimits(): array
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            $config = app(ConfigManager::class);
            return [
                'users' => 1,
                'companies' => 1,
                'transactions_per_month' => 100
            ];
        }

        $limits = is_array($subscription->limits)
            ? $subscription->limits
            : json_decode($subscription->limits, true);

        return $limits ?? [];
    }

    /**
     * Obter uso atual vs limites
     */
    public function getUsageInfo(): array
    {
        $limits = $this->getActiveLimits();

        return [
            'users' => [
                'current' => $this->users()->count(),
                'limit' => $limits['users'] ?? 0,
                'percentage' => $this->calculatePercentage($this->users()->count(), $limits['users'] ?? 1)
            ],
            'companies' => [
                'current' => $this->companies()->count(),
                'limit' => $limits['companies'] ?? 0,
                'percentage' => $this->calculatePercentage($this->companies()->count(), $limits['companies'] ?? 1)
            ],
            'transactions' => [
                'current' => $this->getMonthlyTransactionCount(),
                'limit' => $limits['transactions_per_month'] ?? 0,
                'percentage' => $this->calculatePercentage($this->getMonthlyTransactionCount(), $limits['transactions_per_month'] ?? 1)
            ]
        ];
    }

    /**
     * Verificar se pode fazer upgrade
     */
    public function canUpgrade(): bool
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return true; // Sem assinatura pode fazer upgrade
        }

        // Pode fazer upgrade se não estiver no plano mais alto
        return !in_array($subscription->plan_code, ['enterprise']);
    }

    /**
     * Verificar se pode fazer downgrade
     */
    public function canDowngrade(): bool
    {
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return false;
        }

        // Pode fazer downgrade se não estiver no plano mais baixo
        return !in_array($subscription->plan_code, ['basic']);
    }

    /**
     * Gerar license key única
     */
    public static function generateLicenseKey(string $planCode): string
    {
        $prefix = strtoupper(substr($planCode, 0, 3));
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));

        return "PRD-{$prefix}-{$year}{$month}-{$random}";
    }

    /**
     * Scopers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Métodos auxiliares
     */
    private function getMonthlyTransactionCount(): int
    {
        $currentMonth = now()->format('Y-m');

        return $this->transactions()
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->count();
    }

    private function calculatePercentage(int $current, int $limit): float
    {
        if ($limit === 0) return 0;
        return round(($current / $limit) * 100, 2);
    }

    /**
     * Relacionamentos adicionais (assumindo que existem)
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    /**
     * Mutators
     */
    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Accessors
     */
    public function getFormattedDocumentAttribute(): string
    {
        $doc = $this->document;
        if (strlen($doc) === 14) {
            return substr($doc, 0, 2) . '.' . substr($doc, 2, 3) . '.' . substr($doc, 5, 3) . '/' . substr($doc, 8, 4) . '-' . substr($doc, 12, 2);
        }
        return $doc;
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        }
        return $phone;
    }
}
