<?php

namespace App\Services\Payment;

use App\Models\Payment\PaymentTransaction;
use App\DTOs\Payment\PaymentResponseDTO;

interface GatewayServiceInterface
{
    /**
     * Processar um pagamento
     */
    public function processPayment(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO;

    /**
     * Estornar um pagamento
     */
    public function refundPayment(PaymentTransaction $transaction, array $gatewayConfig, ?float $amount = null): PaymentResponseDTO;

    /**
     * Consultar status de um pagamento
     */
    public function checkPaymentStatus(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO;

    /**
     * Validar webhook
     */
    public function validateWebhook(array $payload, string $signature, array $gatewayConfig): bool;

    /**
     * Processar webhook
     */
    public function processWebhook(array $payload, array $gatewayConfig): ?PaymentResponseDTO;
}
