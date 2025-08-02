<?php

namespace App\Services\Payment;

use App\Models\Payment\PaymentTransaction;
use App\DTOs\Payment\PaymentResponseDTO;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\PaymentStatus;
use App\Exceptions\Payment\GatewayException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Safe2PayService implements GatewayServiceInterface
{
    private string $baseUrl;
    private int $timeout = 30;

    public function processPayment(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        $this->setupGateway($gatewayConfig);

        return match ($transaction->payment_method) {
            PaymentMethod::PIX => $this->processPix($transaction, $gatewayConfig),
            PaymentMethod::CREDIT_CARD => $this->processCreditCard($transaction, $gatewayConfig),
            PaymentMethod::DEBIT_CARD => $this->processDebitCard($transaction, $gatewayConfig),
            PaymentMethod::BANK_SLIP => $this->processBankSlip($transaction, $gatewayConfig),
            default => throw GatewayException::invalidResponse(
                'safe2pay',
                ['error' => 'Payment method not supported: ' . $transaction->payment_method->value]
            ),
        };
    }

    private function processPix(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        $credentials = $gatewayConfig['credentials'];

        $payload = [
            'Application' => 'PIX',
            'Vendor' => [
                'Name' => config('app.name', 'Sistema'),
                'DocumentNumber' => '12345678901', // TODO: Pegar do config
            ],
            'CallbackUrl' => $gatewayConfig['webhook_url'],
            'Reference' => $transaction->transaction_code,
            'Amount' => $transaction->amount_final,
            'Description' => $transaction->description ?? 'Pagamento via PIX',
            'Customer' => [
                'Name' => $transaction->customer_name,
                'Identity' => $transaction->customer_document ?? '12345678901',
                'Phone' => $transaction->customer_phone ?? '',
                'Email' => $transaction->customer_email ?? '',
            ],
            'Products' => [
                [
                    'Code' => $transaction->source_id,
                    'Description' => $transaction->description ?? 'Produto/Serviço',
                    'UnitPrice' => $transaction->amount_final,
                    'Quantity' => 1,
                ]
            ],
        ];

        // Configurar expiração do PIX
        $pixExpiration = $gatewayConfig['settings']['pix_expiracao_minutos'] ?? 30;
        if ($pixExpiration > 0) {
            $payload['DueDate'] = now()->addMinutes($pixExpiration)->format('Y-m-d\TH:i:s');
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $credentials['token'],
                ])
                ->post($this->baseUrl . '/Payment', $payload);

            if (!$response->successful()) {
                throw GatewayException::invalidResponse(
                    'safe2pay',
                    $response->json()
                );
            }

            $data = $response->json();

            if (!$data['IsSuccess']) {
                return PaymentResponseDTO::error(
                    $transaction->uuid,
                    $data['Message'] ?? 'Erro no processamento do PIX',
                    $data['Errors'] ?? [],
                    $data
                );
            }

            return PaymentResponseDTO::pending(
                $transaction->uuid,
                $data['TransactionId'] ?? null,
                null,
                $data['QrCode'] ?? null,
                null,
                isset($payload['DueDate']) ? new \DateTime($payload['DueDate']) : null
            );
        } catch (\Exception $e) {
            Log::error('Safe2Pay PIX Error', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            throw GatewayException::connectionFailed('safe2pay', $e->getMessage());
        }
    }

    private function processCreditCard(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        $credentials = $gatewayConfig['credentials'];

        // Para cartão de crédito, normalmente você precisaria dos dados do cartão
        // Aqui vou simular uma implementação básica
        $payload = [
            'Application' => 'CreditCard',
            'Vendor' => [
                'Name' => config('app.name', 'Sistema'),
                'DocumentNumber' => '12345678901',
            ],
            'CallbackUrl' => $gatewayConfig['webhook_url'],
            'Reference' => $transaction->transaction_code,
            'Amount' => $transaction->amount_final,
            'Description' => $transaction->description ?? 'Pagamento via Cartão de Crédito',
            'Customer' => [
                'Name' => $transaction->customer_name,
                'Identity' => $transaction->customer_document ?? '12345678901',
                'Phone' => $transaction->customer_phone ?? '',
                'Email' => $transaction->customer_email ?? '',
            ],
            'Products' => [
                [
                    'Code' => $transaction->source_id,
                    'Description' => $transaction->description ?? 'Produto/Serviço',
                    'UnitPrice' => $transaction->amount_final,
                    'Quantity' => 1,
                ]
            ],
        ];

        // Adicionar parcelamento se especificado
        if ($transaction->installments > 1) {
            $payload['Installments'] = $transaction->installments;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $credentials['token'],
                ])
                ->post($this->baseUrl . '/Payment', $payload);

            if (!$response->successful()) {
                throw GatewayException::invalidResponse('safe2pay', $response->json());
            }

            $data = $response->json();

            if (!$data['IsSuccess']) {
                return PaymentResponseDTO::error(
                    $transaction->uuid,
                    $data['Message'] ?? 'Erro no processamento do cartão',
                    $data['Errors'] ?? [],
                    $data
                );
            }

            // Para cartão, pode ser aprovado imediatamente ou ficar pendente
            $status = $this->mapSafe2PayStatus($data['Status'] ?? 1);

            return PaymentResponseDTO::success(
                $transaction->uuid,
                $data['TransactionId'] ?? null,
                $status,
                $data['Message'] ?? 'Cartão processado com sucesso'
            );
        } catch (\Exception $e) {
            Log::error('Safe2Pay Credit Card Error', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage()
            ]);

            throw GatewayException::connectionFailed('safe2pay', $e->getMessage());
        }
    }

    private function processDebitCard(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        // Implementação similar ao cartão de crédito, mas com Application = 'DebitCard'
        return $this->processCreditCard($transaction, $gatewayConfig);
    }

    private function processBankSlip(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        $credentials = $gatewayConfig['credentials'];

        $payload = [
            'Application' => 'BankSlip',
            'Vendor' => [
                'Name' => config('app.name', 'Sistema'),
                'DocumentNumber' => '12345678901',
            ],
            'CallbackUrl' => $gatewayConfig['webhook_url'],
            'Reference' => $transaction->transaction_code,
            'Amount' => $transaction->amount_final,
            'Description' => $transaction->description ?? 'Pagamento via Boleto',
            'Customer' => [
                'Name' => $transaction->customer_name,
                'Identity' => $transaction->customer_document ?? '12345678901',
                'Phone' => $transaction->customer_phone ?? '',
                'Email' => $transaction->customer_email ?? '',
            ],
            'Products' => [
                [
                    'Code' => $transaction->source_id,
                    'Description' => $transaction->description ?? 'Produto/Serviço',
                    'UnitPrice' => $transaction->amount_final,
                    'Quantity' => 1,
                ]
            ],
        ];

        // Configurar vencimento do boleto
        $boletoVencimento = $gatewayConfig['settings']['boleto_vencimento_dias'] ?? 7;
        $payload['DueDate'] = now()->addDays($boletoVencimento)->format('Y-m-d');

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $credentials['token'],
                ])
                ->post($this->baseUrl . '/Payment', $payload);

            if (!$response->successful()) {
                throw GatewayException::invalidResponse('safe2pay', $response->json());
            }

            $data = $response->json();

            if (!$data['IsSuccess']) {
                return PaymentResponseDTO::error(
                    $transaction->uuid,
                    $data['Message'] ?? 'Erro na geração do boleto',
                    $data['Errors'] ?? [],
                    $data
                );
            }

            return PaymentResponseDTO::pending(
                $transaction->uuid,
                $data['TransactionId'] ?? null,
                null,
                null,
                $data['BarcodeNumber'] ?? null,
                new \DateTime($payload['DueDate'])
            );
        } catch (\Exception $e) {
            Log::error('Safe2Pay Bank Slip Error', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage()
            ]);

            throw GatewayException::connectionFailed('safe2pay', $e->getMessage());
        }
    }

    public function refundPayment(PaymentTransaction $transaction, array $gatewayConfig, float $amount = null): PaymentResponseDTO
    {
        $this->setupGateway($gatewayConfig);
        $credentials = $gatewayConfig['credentials'];

        $payload = [
            'TransactionId' => $transaction->gateway_transaction_id,
            'Amount' => $amount ?? $transaction->amount_final,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => $credentials['token'],
                ])
                ->post($this->baseUrl . '/Refund', $payload);

            if (!$response->successful()) {
                throw GatewayException::invalidResponse('safe2pay', $response->json());
            }

            $data = $response->json();

            if (!$data['IsSuccess']) {
                return PaymentResponseDTO::error(
                    $transaction->uuid,
                    $data['Message'] ?? 'Erro no estorno',
                    $data['Errors'] ?? [],
                    $data
                );
            }

            return PaymentResponseDTO::success(
                $transaction->uuid,
                $data['TransactionId'] ?? null,
                PaymentStatus::REFUNDED,
                'Estorno processado com sucesso'
            );
        } catch (\Exception $e) {
            Log::error('Safe2Pay Refund Error', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage()
            ]);

            throw GatewayException::connectionFailed('safe2pay', $e->getMessage());
        }
    }

    public function checkPaymentStatus(PaymentTransaction $transaction, array $gatewayConfig): PaymentResponseDTO
    {
        $this->setupGateway($gatewayConfig);
        $credentials = $gatewayConfig['credentials'];

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-KEY' => $credentials['token'],
                ])
                ->get($this->baseUrl . '/Payment/' . $transaction->gateway_transaction_id);

            if (!$response->successful()) {
                throw GatewayException::invalidResponse('safe2pay', $response->json());
            }

            $data = $response->json();
            $status = $this->mapSafe2PayStatus($data['Status'] ?? 1);

            return PaymentResponseDTO::success(
                $transaction->uuid,
                $data['TransactionId'] ?? null,
                $status,
                'Status consultado com sucesso',
                ['gatewayResponse' => $data]
            );
        } catch (\Exception $e) {
            Log::error('Safe2Pay Status Check Error', [
                'transaction_id' => $transaction->uuid,
                'error' => $e->getMessage()
            ]);

            throw GatewayException::connectionFailed('safe2pay', $e->getMessage());
        }
    }

    public function validateWebhook(array $payload, string $signature, array $gatewayConfig): bool
    {
        $credentials = $gatewayConfig['credentials'];
        $secretKey = $credentials['secret_key'] ?? '';

        if (empty($secretKey)) {
            return false;
        }

        // Implementar validação de assinatura conforme documentação Safe2Pay
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $secretKey);

        return hash_equals($expectedSignature, $signature);
    }

    public function processWebhook(array $payload, array $gatewayConfig): ?PaymentResponseDTO
    {
        $transactionId = $payload['Reference'] ?? null;
        $status = $this->mapSafe2PayStatus($payload['Status'] ?? 1);

        if (!$transactionId) {
            return null;
        }

        return PaymentResponseDTO::success(
            $transactionId,
            $payload['TransactionId'] ?? null,
            $status,
            'Webhook processado com sucesso',
            ['gatewayResponse' => $payload]
        );
    }

    private function setupGateway(array $gatewayConfig): void
    {
        $this->baseUrl = $gatewayConfig['environment'] === 'production'
            ? 'https://api.safe2pay.com.br/v2'
            : 'https://sandbox.safe2pay.com.br/v2';

        $this->timeout = $gatewayConfig['settings']['timeout_segundos'] ?? 30;
    }

    private function mapSafe2PayStatus(int $status): PaymentStatus
    {
        return match ($status) {
            1 => PaymentStatus::PENDING,    // Aguardando
            2 => PaymentStatus::PROCESSING, // Processando
            3 => PaymentStatus::APPROVED,   // Aprovado
            4 => PaymentStatus::DECLINED,   // Negado
            5 => PaymentStatus::CANCELLED,  // Cancelado
            6 => PaymentStatus::REFUNDED,   // Estornado
            default => PaymentStatus::PENDING,
        };
    }
}
