<?php

namespace App\DTOs\Payment;

use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\SourceType;

class PaymentRequestDTO
{
    public function __construct(
        public int $empresaId,
        public SourceType $sourceType,
        public int $sourceId,
        public float $amountFinal,
        public PaymentMethod $paymentMethod,
        public string $customerName,
        public ?string $customerEmail = null,
        public ?string $customerDocument = null,
        public ?string $customerPhone = null,
        public ?string $description = null,
        public int $installments = 1,
        public ?float $amountOriginal = null,
        public ?float $amountDiscount = null,
        public ?float $amountFees = null,
        public string $currencyCode = 'BRL',
        public ?string $sourceReference = null,
        public ?string $successUrl = null,
        public ?string $cancelUrl = null,
        public ?string $notificationUrl = null,
        public ?array $metadata = null,
        public ?array $paymentData = null,
        public ?int $createdByUserId = null
    ) {
        // Calcular valores se não fornecidos
        $this->amountOriginal = $amountOriginal ?? $amountFinal;
        $this->amountDiscount = $amountDiscount ?? 0;
        $this->amountFees = $amountFees ?? 0;
    }

    public function toArray(): array
    {
        return [
            'empresa_id' => $this->empresaId,
            'source_type' => $this->sourceType->value,
            'source_id' => $this->sourceId,
            'source_reference' => $this->sourceReference,
            'amount_original' => $this->amountOriginal,
            'amount_discount' => $this->amountDiscount,
            'amount_fees' => $this->amountFees,
            'amount_final' => $this->amountFinal,
            'currency_code' => $this->currencyCode,
            'payment_method' => $this->paymentMethod->value,
            'installments' => $this->installments,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_document' => $this->customerDocument,
            'customer_phone' => $this->customerPhone,
            'description' => $this->description,
            'success_url' => $this->successUrl,
            'cancel_url' => $this->cancelUrl,
            'notification_url' => $this->notificationUrl,
            'metadata' => $this->metadata,
            'payment_data' => $this->paymentData,
            'created_by_user_id' => $this->createdByUserId,
        ];
    }

    public function getInstallmentAmount(): float
    {
        return $this->installments > 1 ?
            round($this->amountFinal / $this->installments, 2) :
            $this->amountFinal;
    }

    public function isValid(): bool
    {
        return !empty($this->customerName) &&
            $this->amountFinal > 0 &&
            $this->installments >= 1 &&
            ($this->paymentMethod->allowsInstallments() || $this->installments === 1);
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->customerName)) {
            $errors[] = 'Nome do cliente é obrigatório';
        }

        if ($this->amountFinal <= 0) {
            $errors[] = 'Valor final deve ser maior que zero';
        }

        if ($this->installments < 1) {
            $errors[] = 'Número de parcelas deve ser maior que zero';
        }

        if (!$this->paymentMethod->allowsInstallments() && $this->installments > 1) {
            $errors[] = 'Método de pagamento não permite parcelamento';
        }

        if ($this->paymentMethod->requiresGateway() && $this->sourceType->allowsOfflinePayment() === false) {
            // Validações específicas para pagamentos online
            if (empty($this->customerEmail)) {
                $errors[] = 'Email é obrigatório para pagamentos online';
            }
        }

        return $errors;
    }
}
