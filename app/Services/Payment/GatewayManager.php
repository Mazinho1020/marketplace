<?php

namespace App\Services\Payment;

use App\Enums\Payment\GatewayProvider;
use App\Exceptions\Payment\GatewayException;
use Illuminate\Support\Facades\App;

class GatewayManager
{
    protected array $services = [];

    public function __construct()
    {
        $this->registerServices();
    }

    /**
     * Registrar serviços de gateway disponíveis
     */
    protected function registerServices(): void
    {
        $this->services = [
            GatewayProvider::SAFE2PAY->value => Safe2PayService::class,
            // Adicionar outros gateways aqui conforme implementados
            // GatewayProvider::MERCADOPAGO->value => MercadoPagoService::class,
            // GatewayProvider::PAGSEGURO->value => PagSeguroService::class,
            // GatewayProvider::STRIPE->value => StripeService::class,
        ];
    }

    /**
     * Obter serviço de um gateway específico
     */
    public function getService(GatewayProvider $provider): GatewayServiceInterface
    {
        $serviceClass = $this->services[$provider->value] ?? null;

        if (!$serviceClass) {
            throw GatewayException::invalidResponse(
                $provider->value,
                ['error' => 'Gateway service not implemented']
            );
        }

        if (!class_exists($serviceClass)) {
            throw GatewayException::invalidResponse(
                $provider->value,
                ['error' => 'Gateway service class not found']
            );
        }

        return App::make($serviceClass);
    }

    /**
     * Verificar se um gateway está disponível
     */
    public function isAvailable(GatewayProvider $provider): bool
    {
        return isset($this->services[$provider->value]) &&
            class_exists($this->services[$provider->value]);
    }

    /**
     * Listar gateways disponíveis
     */
    public function getAvailableGateways(): array
    {
        $available = [];

        foreach (GatewayProvider::cases() as $provider) {
            if ($this->isAvailable($provider)) {
                $available[] = [
                    'provider' => $provider->value,
                    'label' => $provider->label(),
                    'supported_methods' => array_map(
                        fn($method) => $method->value,
                        $provider->supportedMethods()
                    ),
                    'has_webhook_support' => $provider->hasWebhookSupport(),
                ];
            }
        }

        return $available;
    }

    /**
     * Registrar um novo serviço de gateway
     */
    public function registerService(GatewayProvider $provider, string $serviceClass): void
    {
        if (!class_exists($serviceClass)) {
            throw new \InvalidArgumentException("Service class {$serviceClass} does not exist");
        }

        if (!in_array(GatewayServiceInterface::class, class_implements($serviceClass))) {
            throw new \InvalidArgumentException(
                "Service class {$serviceClass} must implement GatewayServiceInterface"
            );
        }

        $this->services[$provider->value] = $serviceClass;
    }
}
