<?php

namespace App\Exceptions\Payment;

use Exception;

class PaymentException extends Exception
{
    protected $code = 400;
    protected $details;

    public function __construct(
        string $message = 'Erro no processamento do pagamento',
        int $code = 400,
        ?array $details = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'details' => $this->getDetails(),
        ];
    }

    public static function transactionNotFound(string $transactionId): self
    {
        return new self("Transação não encontrada: {$transactionId}", 404);
    }

    public static function invalidStatus(string $currentStatus, string $expectedStatus): self
    {
        return new self(
            "Status inválido. Atual: {$currentStatus}, Esperado: {$expectedStatus}",
            422
        );
    }

    public static function invalidAmount(float $amount): self
    {
        return new self(
            "Valor inválido: R$ " . number_format($amount, 2, ',', '.'),
            422
        );
    }

    public static function gatewayNotConfigured(string $provider): self
    {
        return new self(
            "Gateway {$provider} não está configurado para esta empresa",
            503
        );
    }

    public static function methodNotSupported(string $method, string $provider): self
    {
        return new self(
            "Método {$method} não é suportado pelo gateway {$provider}",
            422
        );
    }

    public static function validationFailed(array $errors): self
    {
        return new self(
            'Dados de pagamento inválidos',
            422,
            $errors
        );
    }

    public static function alreadyProcessed(string $transactionId): self
    {
        return new self(
            "Transação {$transactionId} já foi processada",
            409
        );
    }
}
