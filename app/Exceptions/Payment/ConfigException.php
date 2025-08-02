<?php

namespace App\Exceptions\Payment;

use Exception;

class ConfigException extends Exception
{
    protected $configKey;

    public function __construct(
        string $message,
        ?string $configKey = null,
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->configKey = $configKey;
    }

    public function getConfigKey(): ?string
    {
        return $this->configKey;
    }

    public function toArray(): array
    {
        return [
            'error' => true,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'config_key' => $this->configKey,
        ];
    }

    public static function keyNotFound(string $key): self
    {
        return new self(
            "Configuração não encontrada: {$key}",
            $key,
            404
        );
    }

    public static function invalidValue(string $key, mixed $value): self
    {
        return new self(
            "Valor inválido para configuração {$key}: " . (is_scalar($value) ? $value : gettype($value)),
            $key,
            422
        );
    }

    public static function missingRequired(string $key): self
    {
        return new self(
            "Configuração obrigatória não definida: {$key}",
            $key,
            400
        );
    }

    public static function gatewayNotConfigured(string $provider, int $empresaId): self
    {
        return new self(
            "Gateway {$provider} não está configurado para a empresa {$empresaId}",
            "{$provider}_config",
            503
        );
    }
}
