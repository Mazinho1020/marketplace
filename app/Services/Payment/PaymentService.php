<?php

namespace App\Services\Payment;

use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\PaymentGateway;
use App\DTOs\Payment\PaymentRequestDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\Enums\Payment\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\SourceType;
use App\Enums\Payment\GatewayProvider;
use App\Exceptions\Payment\PaymentException;
use App\Exceptions\Payment\ConfigException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private GatewayManager $gatewayManager,
        private PaymentConfigService $configService
    ) {}

    public function createTransaction(PaymentRequestDTO $request): PaymentTransaction
    {
        // Validar dados da requisição
        $errors = $request->validate();
        if (!empty($errors)) {
            throw PaymentException::validationFailed($errors);
        }

        // Gerar código da transação
        $transactionCode = $this->generateTransactionCode($request->sourceType, $request->sourceId);

        // Calcular valor da parcela se parcelado
        $installmentAmount = $request->getInstallmentAmount();

        // Criar transação
        $transaction = PaymentTransaction::create([
            'uuid' => Str::uuid(),
            'transaction_code' => $transactionCode,
            'empresa_id' => $request->empresaId,
            'source_type' => $request->sourceType,
            'source_id' => $request->sourceId,
            'source_reference' => $request->sourceReference,
            'amount_original' => $request->amountOriginal,
            'amount_discount' => $request->amountDiscount,
            'amount_fees' => $request->amountFees,
            'amount_final' => $request->amountFinal,
            'currency_code' => $request->currencyCode,
            'payment_method' => $request->paymentMethod,
            'installments' => $request->installments,
            'installment_amount' => $installmentAmount,
            'customer_name' => $request->customerName,
            'customer_email' => $request->customerEmail,
            'customer_document' => $request->customerDocument,
            'customer_phone' => $request->customerPhone,
            'description' => $request->description,
            'success_url' => $request->successUrl,
            'cancel_url' => $request->cancelUrl,
            'notification_url' => $request->notificationUrl,
            'payment_data' => $request->paymentData,
            'metadata' => $request->metadata,
            'created_by_user_id' => $request->createdByUserId,
            'status' => PaymentStatus::DRAFT,
        ]);

        $transaction->logEvent('created', 'user');

        return $transaction;
    }

    public function processPayment(PaymentTransaction $transaction): PaymentResponseDTO
    {
        try {
            // Verificar se a transação pode ser processada
            if ($transaction->isFinal()) {
                throw PaymentException::alreadyProcessed($transaction->uuid);
            }

            // Atualizar status para processando
            $transaction->update(['status' => PaymentStatus::PROCESSING]);
            $transaction->logEvent('processing_started', 'system');

            // Se for método offline, aprovar imediatamente
            if ($transaction->payment_method->isOffline()) {
                return $this->processOfflinePayment($transaction);
            }

            // Processar via gateway
            return $this->processOnlinePayment($transaction);
        } catch (\Exception $e) {
            $transaction->update(['status' => PaymentStatus::DECLINED]);
            $transaction->logEvent('processing_failed', 'system', [
                'error' => $e->getMessage()
            ]);

            throw new PaymentException(
                'Falha no processamento do pagamento: ' . $e->getMessage(),
                500,
                ['original_error' => $e->getMessage()]
            );
        }
    }

    protected function processOfflinePayment(PaymentTransaction $transaction): PaymentResponseDTO
    {
        // Para pagamentos offline, apenas marcar como pendente
        $transaction->update(['status' => PaymentStatus::PENDING]);
        $transaction->logEvent('offline_payment_pending', 'system');

        return PaymentResponseDTO::pending(
            $transaction->uuid,
            null,
            null,
            null,
            null,
            null
        );
    }

    protected function processOnlinePayment(PaymentTransaction $transaction): PaymentResponseDTO
    {
        // Obter melhor gateway para o método
        $gatewayProvider = $this->configService->getBestGatewayForMethod(
            $transaction->payment_method->value
        );

        if (!$gatewayProvider) {
            throw new ConfigException(
                "Nenhum gateway configurado para {$transaction->payment_method->label()}"
            );
        }

        // Obter configuração do gateway
        $gatewayConfig = $this->configService->getGatewayConfig($gatewayProvider);

        // Validar configuração
        $configErrors = $this->configService->validateGatewayConfig($gatewayProvider);
        if (!empty($configErrors)) {
            throw new ConfigException(
                'Gateway mal configurado: ' . implode(', ', $configErrors)
            );
        }

        // Obter serviço do gateway
        $gatewayService = $this->gatewayManager->getService($gatewayProvider);

        // Processar pagamento
        $response = $gatewayService->processPayment($transaction, $gatewayConfig);

        // Atualizar transação com dados do gateway
        $transaction->update([
            'gateway_provider' => $gatewayProvider,
            'gateway_transaction_id' => $response->gatewayTransactionId,
            'gateway_raw_response' => $response->gatewayResponse,
            'status' => $response->status ?? PaymentStatus::PENDING,
            'processed_at' => now(),
        ]);

        // Salvar dados específicos do pagamento
        if ($response->qrCode || $response->barCode || $response->paymentUrl) {
            $paymentData = array_filter([
                'qr_code' => $response->qrCode,
                'qr_code_base64' => $response->qrCodeBase64,
                'bar_code' => $response->barCode,
                'digitable_line' => $response->digitableLine,
                'payment_url' => $response->paymentUrl,
            ]);

            $transaction->update(['payment_data' => $paymentData]);
        }

        // Definir expiração se aplicável
        if ($response->expiresAt) {
            $transaction->update(['expires_at' => $response->expiresAt]);
        }

        $transaction->logEvent('sent_to_gateway', 'system', [
            'gateway' => $gatewayProvider->value,
            'gateway_transaction_id' => $response->gatewayTransactionId,
        ]);

        return PaymentResponseDTO::success(
            $transaction->uuid,
            $response->gatewayTransactionId,
            $response->status,
            $response->message,
            [
                'paymentUrl' => $response->paymentUrl,
                'qrCode' => $response->qrCode,
                'qrCodeBase64' => $response->qrCodeBase64,
                'barCode' => $response->barCode,
                'digitableLine' => $response->digitableLine,
                'expiresAt' => $response->expiresAt,
            ]
        );
    }

    public function confirmPayment(PaymentTransaction $transaction): void
    {
        if (!$transaction->isPending()) {
            throw PaymentException::invalidStatus(
                $transaction->status->value,
                PaymentStatus::PENDING->value
            );
        }

        $transaction->approve();

        // Atualizar origem (lançamento, venda, etc.)
        $this->updateSourceAfterPayment($transaction);
    }

    public function cancelPayment(PaymentTransaction $transaction, string $reason = null): void
    {
        if ($transaction->isApproved()) {
            throw PaymentException::invalidStatus(
                $transaction->status->value,
                'not approved'
            );
        }

        $transaction->cancel($reason);
    }

    public function refundPayment(PaymentTransaction $transaction, float $amount = null): PaymentResponseDTO
    {
        if (!$transaction->isApproved()) {
            throw PaymentException::invalidStatus(
                $transaction->status->value,
                PaymentStatus::APPROVED->value
            );
        }

        if ($transaction->isOffline()) {
            // Para pagamentos offline, apenas marcar como estornado
            $transaction->refund($amount);
            return PaymentResponseDTO::success(
                $transaction->uuid,
                null,
                PaymentStatus::REFUNDED,
                'Pagamento estornado manualmente'
            );
        }

        // Estorno via gateway
        $gatewayService = $this->gatewayManager->getService($transaction->gateway_provider);
        $gatewayConfig = $this->configService->getGatewayConfig($transaction->gateway_provider);

        $response = $gatewayService->refundPayment($transaction, $gatewayConfig, $amount);

        if ($response->success) {
            $transaction->refund($amount);
        }

        return $response;
    }

    private function generateTransactionCode(SourceType $sourceType, int $sourceId): string
    {
        $prefix = match ($sourceType) {
            SourceType::PDV => 'PDV',
            SourceType::LANCAMENTO => 'LAN',
            SourceType::SITE_CLIENTE => 'CLI',
            SourceType::SITE_PLANOS => 'PLN',
            SourceType::MARKETPLACE => 'MKT',
            SourceType::DELIVERY => 'DEL',
            SourceType::API_EXTERNA => 'API',
            SourceType::MOBILE_APP => 'MOB',
            SourceType::WEBHOOK => 'WHK',
        };

        $date = now()->format('Ymd');
        $sequential = PaymentTransaction::whereDate('created_at', today())->count() + 1;

        return sprintf('%s_%s_%s_%03d', $prefix, $date, $sourceId, $sequential);
    }

    private function updateSourceAfterPayment(PaymentTransaction $transaction): void
    {
        match ($transaction->source_type) {
            SourceType::LANCAMENTO => $this->updateLancamento($transaction),
            SourceType::PDV => $this->updateVendaPDV($transaction),
            SourceType::SITE_PLANOS => $this->updatePlano($transaction),
            default => null, // Outros tipos não precisam de atualização automática
        };
    }

    private function updateLancamento(PaymentTransaction $transaction): void
    {
        // Atualizar tabela de lançamentos
        DB::table('lancamentos')
            ->where('id', $transaction->source_id)
            ->update([
                'status_pagamento' => 'paid',
                'data_pagamento' => now(),
                'forma_pagamento_usado' => $transaction->payment_method->label(),
                'valor_pago' => $transaction->amount_final,
                'updated_at' => now(),
            ]);
    }

    private function updateVendaPDV(PaymentTransaction $transaction): void
    {
        // Atualizar venda do PDV
        DB::table('vendas')
            ->where('id', $transaction->source_id)
            ->update([
                'status_pagamento' => 'paid',
                'data_pagamento' => now(),
                'updated_at' => now(),
            ]);
    }

    private function updatePlano(PaymentTransaction $transaction): void
    {
        // Atualizar assinatura do plano
        DB::table('planos_assinaturas')
            ->where('id', $transaction->source_id)
            ->update([
                'status' => 'active',
                'data_ativacao' => now(),
                'proxima_cobranca' => now()->addMonth(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Obter transação por UUID
     */
    public function getTransaction(string $uuid): PaymentTransaction
    {
        $transaction = PaymentTransaction::where('uuid', $uuid)->first();

        if (!$transaction) {
            throw PaymentException::transactionNotFound($uuid);
        }

        return $transaction;
    }

    /**
     * Listar transações com filtros
     */
    public function listTransactions(array $filters = [], int $perPage = 15)
    {
        $query = PaymentTransaction::query();

        if (isset($filters['empresa_id'])) {
            $query->byEmpresa($filters['empresa_id']);
        }

        if (isset($filters['status'])) {
            $query->byStatus(PaymentStatus::from($filters['status']));
        }

        if (isset($filters['source_type'])) {
            $query->bySource(SourceType::from($filters['source_type']));
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['gateway_provider'])) {
            $query->where('gateway_provider', $filters['gateway_provider']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->with(['events', 'webhooks'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Expirar transações pendentes
     */
    public function expireTransactions(): int
    {
        $expiredTransactions = PaymentTransaction::expired()->get();
        $count = 0;

        foreach ($expiredTransactions as $transaction) {
            $transaction->expire();
            $count++;
        }

        return $count;
    }
}
