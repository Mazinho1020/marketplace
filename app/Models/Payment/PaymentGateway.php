<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Payment\GatewayProvider;

class PaymentGateway extends Model
{
    protected $fillable = [
        'empresa_id',
        'provider',
        'name',
        'environment',
        'is_active',
        'priority',
        'credentials',
        'supported_methods',
        'webhook_url',
        'success_url',
        'cancel_url',
        'min_amount',
        'max_amount',
        'fees_config'
    ];

    protected $casts = [
        'provider' => GatewayProvider::class,
        'is_active' => 'boolean',
        'credentials' => 'array',
        'supported_methods' => 'array',
        'fees_config' => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProvider($query, GatewayProvider $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeByEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    // Métodos de verificação
    public function supportsMethod(string $method): bool
    {
        return in_array($method, $this->supported_methods);
    }

    public function isInProduction(): bool
    {
        return $this->environment === 'production';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function canProcessAmount(float $amount): bool
    {
        return $amount >= $this->min_amount && $amount <= $this->max_amount;
    }

    // Métodos de credenciais
    public function getDecryptedCredentials(): array
    {
        // TODO: Implementar descriptografia das credenciais
        // Por enquanto retorna as credenciais como estão
        return $this->credentials;
    }

    public function setCredentials(array $credentials): void
    {
        // TODO: Implementar criptografia das credenciais
        // Por enquanto salva as credenciais como estão
        $this->credentials = $credentials;
    }

    public function hasValidCredentials(): bool
    {
        $credentials = $this->getDecryptedCredentials();

        return match ($this->provider) {
            GatewayProvider::SAFE2PAY =>
            !empty($credentials['token']) && !empty($credentials['secret_key']),
            GatewayProvider::MERCADOPAGO =>
            !empty($credentials['access_token']),
            GatewayProvider::PAGSEGURO =>
            !empty($credentials['email']) && !empty($credentials['token']),
            GatewayProvider::STRIPE =>
            !empty($credentials['secret_key']) && !empty($credentials['publishable_key']),
            default => false,
        };
    }

    // Getters
    public function getProviderLabelAttribute(): string
    {
        return $this->provider->label();
    }

    public function getEnvironmentLabelAttribute(): string
    {
        return match ($this->environment) {
            'sandbox', 'test' => 'Teste',
            'production', 'live' => 'Produção',
            default => ucfirst($this->environment),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    // Métodos de configuração
    public function getFeeForMethod(string $method): float
    {
        return $this->fees_config[$method] ?? 0.0;
    }

    public function setFeeForMethod(string $method, float $fee): void
    {
        $fees = $this->fees_config;
        $fees[$method] = $fee;
        $this->fees_config = $fees;
    }

    public function addSupportedMethod(string $method): void
    {
        $methods = $this->supported_methods;
        if (!in_array($method, $methods)) {
            $methods[] = $method;
            $this->supported_methods = $methods;
        }
    }

    public function removeSupportedMethod(string $method): void
    {
        $methods = $this->supported_methods;
        $key = array_search($method, $methods);
        if ($key !== false) {
            unset($methods[$key]);
            $this->supported_methods = array_values($methods);
        }
    }
}
