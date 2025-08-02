<?php

namespace App\Services\Payment;

use App\Services\ConfigService;
use App\Exceptions\Payment\ConfigException;
use App\Enums\Payment\GatewayProvider;
use Illuminate\Support\Facades\Cache;

class PaymentConfigService
{
    protected ConfigService $configService;
    protected ?int $empresaId = null;
    protected string $cachePrefix = 'payment_config_';
    protected int $cacheTtl = 3600; // 1 hora

    public function __construct(ConfigService $configService, ?int $empresaId = null)
    {
        $this->configService = $configService;
        if ($empresaId) {
            $this->configService->setEmpresaId($empresaId);
            $this->empresaId = $empresaId;
        }
    }

    /**
     * Define a empresa para as configurações
     */
    public function setEmpresaId(int $empresaId): self
    {
        $this->empresaId = $empresaId;
        $this->configService->setEmpresaId($empresaId);
        return $this;
    }

    /**
     * Obter configuração de um gateway específico
     */
    public function getGatewayConfig(GatewayProvider $provider): array
    {
        $this->validateEmpresaId();

        $cacheKey = $this->cachePrefix . "gateway_{$provider->value}_{$this->empresaId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($provider) {
            $config = [];

            // Configurações básicas do gateway
            $config['provider'] = $provider->value;
            $config['active'] = $this->get("{$provider->value}_ativo", false);
            $config['environment'] = $this->get("{$provider->value}_environment", 'sandbox');
            $config['debug'] = $this->get("{$provider->value}_modo_debug", false);

            // Credenciais
            $config['credentials'] = $this->getGatewayCredentials($provider);

            // URLs
            $config['webhook_url'] = $this->get("{$provider->value}_webhook_url");
            $config['success_url'] = $this->get("{$provider->value}_success_url");
            $config['cancel_url'] = $this->get("{$provider->value}_cancel_url");

            // Métodos suportados
            $config['methods'] = $this->getGatewaySupportedMethods($provider);

            // Configurações específicas
            $config['settings'] = $this->getGatewaySpecificSettings($provider);

            // Taxas
            $config['fees'] = $this->getGatewayFees($provider);

            return $config;
        });
    }

    /**
     * Obter credenciais do gateway
     */
    protected function getGatewayCredentials(GatewayProvider $provider): array
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY => [
                'token' => $this->get('safe2pay_token'),
                'secret_key' => $this->get('safe2pay_secret_key'),
            ],
            GatewayProvider::MERCADOPAGO => [
                'access_token' => $this->get('mercadopago_access_token'),
                'public_key' => $this->get('mercadopago_public_key'),
            ],
            GatewayProvider::PAGSEGURO => [
                'email' => $this->get('pagseguro_email'),
                'token' => $this->get('pagseguro_token'),
            ],
            GatewayProvider::STRIPE => [
                'secret_key' => $this->get('stripe_secret_key'),
                'publishable_key' => $this->get('stripe_publishable_key'),
            ],
            default => [],
        };
    }

    /**
     * Obter métodos suportados pelo gateway
     */
    protected function getGatewaySupportedMethods(GatewayProvider $provider): array
    {
        $methods = [];

        foreach ($provider->supportedMethods() as $method) {
            $isActive = $this->get("{$provider->value}_{$method->value}_ativo", true);
            if ($isActive) {
                $methods[] = $method->value;
            }
        }

        return $methods;
    }

    /**
     * Obter configurações específicas do gateway
     */
    protected function getGatewaySpecificSettings(GatewayProvider $provider): array
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY => [
                'pix_expiracao_minutos' => (int)$this->get('safe2pay_pix_expiracao_minutos', 30),
                'boleto_vencimento_dias' => (int)$this->get('safe2pay_boleto_vencimento_dias', 7),
                'timeout_segundos' => (int)$this->get('safe2pay_timeout_segundos', 30),
                'retry_attempts' => (int)$this->get('safe2pay_retry_attempts', 3),
            ],
            GatewayProvider::MERCADOPAGO => [
                'pix_expiracao_minutos' => (int)$this->get('mercadopago_pix_expiracao_minutos', 30),
                'boleto_vencimento_dias' => (int)$this->get('mercadopago_boleto_vencimento_dias', 7),
            ],
            default => [],
        };
    }

    /**
     * Obter taxas do gateway
     */
    protected function getGatewayFees(GatewayProvider $provider): array
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY => [
                'pix' => (float)$this->get('safe2pay_taxa_pix', 0.99),
                'credit_card' => (float)$this->get('safe2pay_taxa_cartao_credito', 3.49),
                'debit_card' => (float)$this->get('safe2pay_taxa_cartao_debito', 2.49),
                'bank_slip' => (float)$this->get('safe2pay_taxa_boleto', 2.50),
            ],
            default => [],
        };
    }

    /**
     * Verificar se um gateway está ativo
     */
    public function isGatewayActive(GatewayProvider $provider): bool
    {
        return (bool)$this->get("{$provider->value}_ativo", false);
    }

    /**
     * Verificar se um método de pagamento está ativo para um gateway
     */
    public function isMethodActive(GatewayProvider $provider, string $method): bool
    {
        return (bool)$this->get("{$provider->value}_{$method}_ativo", true);
    }

    /**
     * Obter o melhor gateway para um método de pagamento
     */
    public function getBestGatewayForMethod(string $method): ?GatewayProvider
    {
        $activeProviders = [];

        foreach (GatewayProvider::cases() as $provider) {
            if (
                $this->isGatewayActive($provider) &&
                in_array($method, array_map(fn($m) => $m->value, $provider->supportedMethods())) &&
                $this->isMethodActive($provider, $method)
            ) {

                $activeProviders[] = $provider;
            }
        }

        if (empty($activeProviders)) {
            return null;
        }

        // Por enquanto retorna o primeiro ativo
        // TODO: Implementar lógica de prioridade baseada em taxas, disponibilidade, etc.
        return $activeProviders[0];
    }

    /**
     * Obter todas as formas de pagamento disponíveis
     */
    public function getAvailablePaymentMethods(): array
    {
        $methods = [];

        foreach (GatewayProvider::cases() as $provider) {
            if (!$this->isGatewayActive($provider)) {
                continue;
            }

            foreach ($provider->supportedMethods() as $method) {
                if ($this->isMethodActive($provider, $method->value)) {
                    $methods[] = [
                        'method' => $method->value,
                        'label' => $method->label(),
                        'icon' => $method->icon(),
                        'provider' => $provider->value,
                        'provider_label' => $provider->label(),
                        'allows_installments' => $method->allowsInstallments(),
                        'is_online' => $method->isOnline(),
                    ];
                }
            }
        }

        // Adicionar métodos offline (dinheiro, etc.)
        $methods[] = [
            'method' => 'cash',
            'label' => 'Dinheiro',
            'icon' => 'fas fa-money-bill',
            'provider' => null,
            'provider_label' => 'Offline',
            'allows_installments' => false,
            'is_online' => false,
        ];

        return $methods;
    }

    /**
     * Configurar um gateway
     */
    public function configureGateway(GatewayProvider $provider, array $config): void
    {
        foreach ($config as $key => $value) {
            $configKey = "{$provider->value}_{$key}";
            $this->configService->set($configKey, $value);
        }

        // Limpar cache
        $this->clearGatewayCache($provider);
    }

    /**
     * Limpar cache de um gateway específico
     */
    public function clearGatewayCache(GatewayProvider $provider): void
    {
        $this->validateEmpresaId();

        $cacheKey = $this->cachePrefix . "gateway_{$provider->value}_{$this->empresaId}";
        Cache::forget($cacheKey);
    }

    /**
     * Limpar todo o cache de configurações
     */
    public function clearAllCache(): void
    {
        foreach (GatewayProvider::cases() as $provider) {
            $this->clearGatewayCache($provider);
        }
    }

    /**
     * Wrapper para o ConfigService
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        try {
            return $this->configService->get($key, $default);
        } catch (\Exception $e) {
            if ($default === null) {
                throw new ConfigException("Configuração '{$key}' não encontrada", $key);
            }
            return $default;
        }
    }

    /**
     * Validar configurações de um gateway
     */
    public function validateGatewayConfig(GatewayProvider $provider): array
    {
        $errors = [];
        $config = $this->getGatewayConfig($provider);

        if (!$config['active']) {
            return $errors; // Gateway inativo, não precisa validar
        }

        // Validar credenciais
        $credentials = $config['credentials'];
        $requiredCredentials = match ($provider) {
            GatewayProvider::SAFE2PAY => ['token', 'secret_key'],
            GatewayProvider::MERCADOPAGO => ['access_token'],
            GatewayProvider::PAGSEGURO => ['email', 'token'],
            GatewayProvider::STRIPE => ['secret_key', 'publishable_key'],
            default => [],
        };

        foreach ($requiredCredentials as $credential) {
            if (empty($credentials[$credential])) {
                $errors[] = "Credencial '{$credential}' é obrigatória para {$provider->label()}";
            }
        }

        // Validar URLs obrigatórias
        if (empty($config['webhook_url'])) {
            $errors[] = 'URL de webhook é obrigatória';
        }

        if (empty($config['success_url'])) {
            $errors[] = 'URL de sucesso é obrigatória';
        }

        return $errors;
    }

    /**
     * Valida se a empresa foi definida
     */
    protected function validateEmpresaId(): void
    {
        if (!isset($this->empresaId)) {
            throw new ConfigException('ID da empresa não foi definido', 'EMPRESA_ID_REQUIRED');
        }
    }
}
