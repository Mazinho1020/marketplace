<?php

namespace App\DTOs\Payment;

class WebhookDTO
{
    public function __construct(
        public string $gatewayProvider,
        public string $eventType,
        public array $payload,
        public ?string $webhookId = null,
        public ?array $headers = null,
        public ?string $signature = null,
        public ?string $ipAddress = null
    ) {}

    public function toArray(): array
    {
        return [
            'gateway_provider' => $this->gatewayProvider,
            'event_type' => $this->eventType,
            'webhook_id' => $this->webhookId,
            'raw_payload' => $this->payload,
            'headers' => $this->headers,
            'signature_valid' => null, // Será definido após validação
            'ip_address' => $this->ipAddress,
        ];
    }

    public function getTransactionId(): ?string
    {
        // Implementar extração do ID da transação baseado no provider
        return match ($this->gatewayProvider) {
            'safe2pay' => $this->payload['TransactionId'] ?? $this->payload['Reference'] ?? null,
            'mercadopago' => $this->payload['data']['id'] ?? null,
            'pagseguro' => $this->payload['notificationCode'] ?? null,
            default => null,
        };
    }

    public function getPaymentStatus(): ?string
    {
        // Implementar mapeamento de status baseado no provider
        return match ($this->gatewayProvider) {
            'safe2pay' => $this->mapSafe2PayStatus(),
            'mercadopago' => $this->mapMercadoPagoStatus(),
            'pagseguro' => $this->mapPagSeguroStatus(),
            default => null,
        };
    }

    private function mapSafe2PayStatus(): ?string
    {
        $status = $this->payload['Status'] ?? null;

        return match ($status) {
            1 => 'pending',    // Aguardando
            2 => 'processing', // Processando
            3 => 'approved',   // Aprovado
            4 => 'declined',   // Negado
            5 => 'cancelled',  // Cancelado
            6 => 'refunded',   // Estornado
            default => null,
        };
    }

    private function mapMercadoPagoStatus(): ?string
    {
        $status = $this->payload['status'] ?? null;

        return match ($status) {
            'pending' => 'pending',
            'approved' => 'approved',
            'authorized' => 'processing',
            'in_process' => 'processing',
            'in_mediation' => 'processing',
            'rejected' => 'declined',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
            default => null,
        };
    }

    private function mapPagSeguroStatus(): ?string
    {
        $status = $this->payload['status'] ?? null;

        return match ($status) {
            '1' => 'pending',    // Aguardando pagamento
            '2' => 'processing', // Em análise
            '3' => 'approved',   // Paga
            '4' => 'approved',   // Disponível
            '5' => 'processing', // Em disputa
            '6' => 'refunded',   // Devolvida
            '7' => 'cancelled',  // Cancelada
            default => null,
        };
    }

    public function isValid(): bool
    {
        return !empty($this->gatewayProvider) &&
            !empty($this->eventType) &&
            !empty($this->payload);
    }

    public function getAmount(): ?float
    {
        return match ($this->gatewayProvider) {
            'safe2pay' => $this->payload['Amount'] ?? null,
            'mercadopago' => $this->payload['transaction_amount'] ?? null,
            'pagseguro' => $this->payload['grossAmount'] ?? null,
            default => null,
        };
    }
}
