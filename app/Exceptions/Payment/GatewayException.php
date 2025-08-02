<?php

namespace App\Exceptions\Payment;

use Exception;

class GatewayException extends Exception
{
    protected $gatewayProvider;
    protected $gatewayResponse;
    protected $requestData;

    public function __construct(
        string $message,
        string $gatewayProvider,
        ?array $gatewayResponse = null,
        ?array $requestData = null,
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->gatewayProvider = $gatewayProvider;
        $this->gatewayResponse = $gatewayResponse;
        $this->requestData = $requestData;
    }

    public function getGatewayProvider(): string
    {
        return $this->gatewayProvider;
    }

    public function getGatewayResponse(): ?array
    {
        return $this->gatewayResponse;
    }

    public function getRequestData(): ?array
    {
        return $this->requestData;
    }

    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'gateway' => $this->gatewayProvider,
            'gateway_response' => $this->gatewayResponse,
        ];
    }

    public static function connectionFailed(string $provider, string $reason = ''): self
    {
        return new self(
            "Falha na conexão com o gateway {$provider}. {$reason}",
            $provider,
            null,
            null,
            503
        );
    }

    public static function invalidCredentials(string $provider): self
    {
        return new self(
            "Credenciais inválidas para o gateway {$provider}",
            $provider,
            null,
            null,
            401
        );
    }

    public static function invalidResponse(string $provider, ?array $response = null): self
    {
        return new self(
            "Resposta inválida do gateway {$provider}",
            $provider,
            $response,
            null,
            502
        );
    }

    public static function timeout(string $provider): self
    {
        return new self(
            "Timeout na comunicação com o gateway {$provider}",
            $provider,
            null,
            null,
            504
        );
    }

    public static function rateLimitExceeded(string $provider): self
    {
        return new self(
            "Limite de requisições excedido para o gateway {$provider}",
            $provider,
            null,
            null,
            429
        );
    }

    public static function webhookValidationFailed(string $provider, string $reason = ''): self
    {
        return new self(
            "Falha na validação do webhook do {$provider}. {$reason}",
            $provider,
            null,
            null,
            400
        );
    }
}
