<?php

namespace App\Services\Payment;

use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\PaymentWebhook;
use App\DTOs\Payment\WebhookDTO;
use App\Enums\Payment\PaymentStatus;
use App\Enums\Payment\GatewayProvider;
use App\Exceptions\Payment\GatewayException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebhookProcessor
{
    public function __construct(
        private GatewayManager $gatewayManager
    ) {}

    public function processWebhook(PaymentWebhook|WebhookDTO $webhookData): array
    {
        try {
            // Se for DTO, converte para modelo
            if ($webhookData instanceof WebhookDTO) {
                $webhook = $this->createWebhookFromDTO($webhookData);
            } else {
                $webhook = $webhookData;
            }

            return DB::transaction(function () use ($webhook) {
                // Processa o webhook baseado no provider
                $result = match ($webhook->gateway_provider) {
                    GatewayProvider::SAFE2PAY->value => $this->processSafe2PayWebhook($webhook),
                    default => throw new GatewayException("Provider não suportado: {$webhook->gateway_provider}", 'INVALID_PROVIDER')
                };

                // Marca como processado
                $webhook->update([
                    'processed' => true,
                    'processed_at' => now(),
                    'processing_response' => $result
                ]);

                return $result;
            });
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'webhook_id' => $webhook->id ?? 'novo',
                'error' => $e->getMessage()
            ]);

            // Se webhook existe, marca erro
            if (isset($webhook) && $webhook->exists) {
                $webhook->update([
                    'processed' => false,
                    'processing_error' => $e->getMessage(),
                    'processing_attempts' => ($webhook->processing_attempts ?? 0) + 1
                ]);
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function listWebhooks(array $filters = [], int $perPage = 15)
    {
        $query = PaymentWebhook::query()
            ->with(['transaction'])
            ->orderBy('created_at', 'desc');

        if (isset($filters['empresa_id'])) {
            $query->whereHas('transaction', function ($q) use ($filters) {
                $q->where('empresa_id', $filters['empresa_id']);
            });
        }

        if (isset($filters['provider'])) {
            $query->where('gateway_provider', $filters['provider']);
        }

        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['processed'])) {
            $query->where('processed', $filters['processed']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function getWebhookDetails(int $webhookId): ?array
    {
        $webhook = PaymentWebhook::with(['transaction'])->find($webhookId);

        if (!$webhook) {
            return null;
        }

        return [
            'id' => $webhook->id,
            'gateway_provider' => $webhook->gateway_provider,
            'event_type' => $webhook->event_type,
            'gateway_transaction_id' => $webhook->gateway_transaction_id,
            'processed' => $webhook->processed,
            'processing_attempts' => $webhook->processing_attempts,
            'processing_error' => $webhook->processing_error,
            'created_at' => $webhook->created_at->toISOString(),
            'processed_at' => $webhook->processed_at?->toISOString(),
            'payload' => $webhook->payload,
            'processing_response' => $webhook->processing_response,
            'transaction' => $webhook->transaction ? [
                'uuid' => $webhook->transaction->uuid,
                'transaction_code' => $webhook->transaction->transaction_code,
                'status' => $webhook->transaction->status->value,
                'amount_final' => $webhook->transaction->amount_final,
            ] : null
        ];
    }

    public function reprocessWebhook(int $webhookId): array
    {
        $webhook = PaymentWebhook::find($webhookId);

        if (!$webhook) {
            return [
                'success' => false,
                'message' => 'Webhook não encontrado'
            ];
        }

        try {
            $result = $this->processWebhook($webhook);

            return [
                'success' => $result['success'],
                'message' => $result['success'] ? 'Webhook reprocessado com sucesso' : $result['error']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Erro ao reprocessar webhook: {$e->getMessage()}"
            ];
        }
    }

    private function createWebhookFromDTO(WebhookDTO $dto): PaymentWebhook
    {
        // Extrai transaction_id do payload
        $transactionId = $this->extractTransactionIdFromPayload($dto->payload, $dto->gatewayProvider);

        // Busca a transação pelo gateway_transaction_id
        $transaction = PaymentTransaction::where('gateway_transaction_id', $transactionId)->first();

        return PaymentWebhook::create([
            'payment_transaction_id' => $transaction?->id,
            'gateway_provider' => $dto->gatewayProvider,
            'event_type' => $dto->eventType,
            'gateway_transaction_id' => $transactionId,
            'payload' => $dto->payload,
            'signature' => $dto->signature ?? '',
            'processed' => false,
            'processing_attempts' => 0,
            'received_at' => now(),
        ]);
    }

    private function extractTransactionIdFromPayload(array $payload, string $provider): string
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY->value => $payload['Transaction']['Id'] ?? $payload['transaction_id'] ?? '',
            default => $payload['transaction_id'] ?? $payload['id'] ?? ''
        };
    }

    private function processSafe2PayWebhook(PaymentWebhook $webhook): array
    {
        $payload = $webhook->payload;

        Log::info('Processando webhook Safe2Pay', [
            'event_type' => $webhook->event_type,
            'transaction_id' => $webhook->gateway_transaction_id
        ]);

        // Busca a transação
        $transaction = PaymentTransaction::where('gateway_transaction_id', $webhook->gateway_transaction_id)->first();

        if (!$transaction) {
            Log::warning('Transação não encontrada para webhook Safe2Pay', [
                'gateway_transaction_id' => $webhook->gateway_transaction_id
            ]);

            return [
                'success' => false,
                'error' => 'Transação não encontrada',
                'transaction_uuid' => null
            ];
        }

        // Processa baseado no tipo de evento
        $newStatus = $this->mapSafe2PayStatusToPaymentStatus($webhook->event_type);

        if (!$newStatus) {
            return [
                'success' => true,
                'message' => 'Evento ignorado',
                'transaction_uuid' => $transaction->uuid
            ];
        }

        // Atualiza o status da transação
        $this->updateTransactionStatus($transaction, $newStatus, $webhook);

        return [
            'success' => true,
            'message' => 'Webhook processado com sucesso',
            'transaction_uuid' => $transaction->uuid
        ];
    }

    private function mapSafe2PayStatusToPaymentStatus(string $eventType): ?PaymentStatus
    {
        return match ($eventType) {
            'Transaction.Waiting' => PaymentStatus::PENDING,
            'Transaction.InAnalysis' => PaymentStatus::PROCESSING,
            'Transaction.Approved' => PaymentStatus::APPROVED,
            'Transaction.Authorized' => PaymentStatus::APPROVED,
            'Transaction.Captured' => PaymentStatus::APPROVED,
            'Transaction.Denied' => PaymentStatus::DECLINED,
            'Transaction.Canceled' => PaymentStatus::CANCELLED,
            'Transaction.Refunded' => PaymentStatus::REFUNDED,
            'Transaction.ChargedBack' => PaymentStatus::REFUNDED,
            'Transaction.Expired' => PaymentStatus::EXPIRED,
            default => null
        };
    }

    private function updateTransactionStatus(PaymentTransaction $transaction, PaymentStatus $newStatus, PaymentWebhook $webhook): void
    {
        $oldStatus = $transaction->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        $transaction->update([
            'status' => $newStatus,
            'gateway_response' => $webhook->payload,
            'processed_at' => now()
        ]);

        $transaction->events()->create([
            'event_type' => 'status_changed',
            'description' => "Status alterado via webhook de {$oldStatus->label()} para {$newStatus->label()}",
            'previous_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'triggered_by' => 'webhook',
            'metadata' => [
                'webhook_id' => $webhook->id,
                'gateway_provider' => $webhook->gateway_provider,
                'event_type' => $webhook->event_type
            ]
        ]);

        if ($newStatus === PaymentStatus::APPROVED) {
            $transaction->update(['approved_at' => now()]);
        } elseif ($newStatus === PaymentStatus::CANCELLED) {
            $transaction->update(['cancelled_at' => now()]);
        }
    }

    public function processPendingWebhooks(int $limit = 50): int
    {
        $pendingWebhooks = PaymentWebhook::where('processed', false)
            ->where('processing_attempts', '<', 3)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $processed = 0;

        foreach ($pendingWebhooks as $webhook) {
            try {
                $this->processWebhook($webhook);
                $processed++;
            } catch (\Exception $e) {
                Log::error('Erro ao processar webhook pendente', [
                    'webhook_id' => $webhook->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $processed;
    }

    public function retryFailedWebhooks(int $limit = 10): int
    {
        $failedWebhooks = PaymentWebhook::where('processed', false)
            ->where('processing_attempts', '>=', 3)
            ->whereNotNull('processing_error')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        $retried = 0;

        foreach ($failedWebhooks as $webhook) {
            try {
                $webhook->update(['processing_attempts' => 0, 'processing_error' => null]);
                $this->processWebhook($webhook);
                $retried++;
            } catch (\Exception $e) {
                Log::error('Erro ao tentar reprocessar webhook falhado', [
                    'webhook_id' => $webhook->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $retried;
    }
}
