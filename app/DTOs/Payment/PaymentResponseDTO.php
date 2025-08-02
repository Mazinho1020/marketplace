<?php

namespace App\DTOs\Payment;

use App\Enums\Payment\PaymentStatus;

class PaymentResponseDTO
{
    public function __construct(
        public bool $success,
        public string $transactionId,
        public ?string $gatewayTransactionId = null,
        public ?PaymentStatus $status = null,
        public ?string $message = null,
        public ?array $gatewayResponse = null,
        public ?string $paymentUrl = null,
        public ?string $qrCode = null,
        public ?string $qrCodeBase64 = null,
        public ?string $barCode = null,
        public ?string $digitableLine = null,
        public ?\DateTime $expiresAt = null,
        public ?array $errors = null,
        public ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'gateway_transaction_id' => $this->gatewayTransactionId,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'message' => $this->message,
            'payment_url' => $this->paymentUrl,
            'qr_code' => $this->qrCode,
            'qr_code_base64' => $this->qrCodeBase64,
            'bar_code' => $this->barCode,
            'digitable_line' => $this->digitableLine,
            'expires_at' => $this->expiresAt?->format('Y-m-d H:i:s'),
            'errors' => $this->errors,
            'metadata' => $this->metadata,
        ];
    }

    public static function success(
        string $transactionId,
        ?string $gatewayTransactionId = null,
        ?PaymentStatus $status = null,
        ?string $message = null,
        ?array $additionalData = []
    ): self {
        $response = new self(
            success: true,
            transactionId: $transactionId,
            gatewayTransactionId: $gatewayTransactionId,
            status: $status,
            message: $message
        );

        // Aplicar dados adicionais se fornecidos
        foreach ($additionalData as $key => $value) {
            if (property_exists($response, $key)) {
                $response->$key = $value;
            }
        }

        return $response;
    }

    public static function error(
        string $transactionId,
        string $message,
        ?array $errors = null,
        ?array $gatewayResponse = null
    ): self {
        return new self(
            success: false,
            transactionId: $transactionId,
            message: $message,
            errors: $errors,
            gatewayResponse: $gatewayResponse,
            status: PaymentStatus::DECLINED
        );
    }

    public static function pending(
        string $transactionId,
        ?string $gatewayTransactionId = null,
        ?string $paymentUrl = null,
        ?string $qrCode = null,
        ?string $barCode = null,
        ?\DateTime $expiresAt = null
    ): self {
        return new self(
            success: true,
            transactionId: $transactionId,
            gatewayTransactionId: $gatewayTransactionId,
            status: PaymentStatus::PENDING,
            paymentUrl: $paymentUrl,
            qrCode: $qrCode,
            barCode: $barCode,
            expiresAt: $expiresAt
        );
    }

    public function hasPaymentUrl(): bool
    {
        return !empty($this->paymentUrl);
    }

    public function hasQrCode(): bool
    {
        return !empty($this->qrCode) || !empty($this->qrCodeBase64);
    }

    public function hasBarCode(): bool
    {
        return !empty($this->barCode) || !empty($this->digitableLine);
    }

    public function isExpired(): bool
    {
        return $this->expiresAt && $this->expiresAt < new \DateTime();
    }
}
