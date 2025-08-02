<?php

namespace App\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\WebhookProcessor;
use App\Services\Payment\PaymentService;
use App\DTOs\Payment\WebhookDTO;
use App\Enums\Payment\GatewayProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private WebhookProcessor $webhookProcessor,
        private PaymentService $paymentService
    ) {}

    public function handle(Request $request, string $provider): Response
    {
        try {
            // Valida se o provider é suportado
            $gatewayProvider = GatewayProvider::tryFrom($provider);
            if (!$gatewayProvider) {
                Log::warning("Webhook recebido de provider não suportado: {$provider}");
                return response('Provider não suportado', 400);
            }

            // Log do webhook recebido
            Log::info("Webhook recebido do provider: {$provider}", [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Valida a assinatura (para Safe2Pay)
            if ($gatewayProvider === GatewayProvider::SAFE2PAY) {
                $this->validateSafe2PaySignature($request);
            }

            // Cria o DTO do webhook
            $webhookData = $this->createWebhookDTO($request, $gatewayProvider);

            // Processa o webhook
            $result = $this->webhookProcessor->processWebhook($webhookData);

            if ($result['success']) {
                Log::info("Webhook processado com sucesso", [
                    'provider' => $provider,
                    'transaction_uuid' => $result['transaction_uuid'] ?? null,
                    'event_type' => $webhookData->eventType
                ]);

                return response('OK', 200);
            } else {
                Log::error("Erro ao processar webhook", [
                    'provider' => $provider,
                    'error' => $result['error'],
                    'webhook_data' => $webhookData->toArray()
                ]);

                return response('Erro interno', 500);
            }
        } catch (\Exception $e) {
            Log::error("Erro crítico no webhook", [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response('Erro interno', 500);
        }
    }

    public function safe2pay(Request $request): Response
    {
        return $this->handle($request, 'safe2pay');
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'provider' => 'nullable|string',
            'event_type' => 'nullable|string',
            'processed' => 'nullable|boolean',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $filters = $request->only([
                'empresa_id',
                'provider',
                'event_type',
                'processed',
                'date_from',
                'date_to'
            ]);

            $perPage = $request->input('per_page', 15);

            $webhooks = $this->webhookProcessor->listWebhooks($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $webhooks->items(),
                'pagination' => [
                    'current_page' => $webhooks->currentPage(),
                    'last_page' => $webhooks->lastPage(),
                    'per_page' => $webhooks->perPage(),
                    'total' => $webhooks->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(int $webhookId): JsonResponse
    {
        try {
            $webhook = $this->webhookProcessor->getWebhookDetails($webhookId);

            if (!$webhook) {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $webhook
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function reprocess(Request $request, int $webhookId): JsonResponse
    {
        try {
            $result = $this->webhookProcessor->reprocessWebhook($webhookId);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function test(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string',
            'event_type' => 'required|string',
            'transaction_id' => 'required|string',
            'payload' => 'required|array',
        ]);

        try {
            $gatewayProvider = GatewayProvider::from($request->input('provider'));

            $webhookData = new WebhookDTO(
                $gatewayProvider->value,
                $request->input('event_type'),
                $request->input('payload'),
                null,
                null,
                'test-signature'
            );

            $result = $this->webhookProcessor->processWebhook($webhookData);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Webhook de teste processado com sucesso' : $result['error'],
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function validateSafe2PaySignature(Request $request): void
    {
        $signature = $request->header('X-Safe2Pay-Signature');
        $timestamp = $request->header('X-Safe2Pay-Timestamp');

        if (!$signature || !$timestamp) {
            throw new \Exception('Headers de autenticação Safe2Pay ausentes');
        }

        $payload = $request->getContent();
        $webhookSecret = config('payment.gateways.safe2pay.webhook_secret');

        if (!$webhookSecret) {
            throw new \Exception('Webhook secret não configurado para Safe2Pay');
        }

        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $payload, $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Assinatura do webhook Safe2Pay inválida');
        }

        // Verifica se o timestamp não é muito antigo (5 minutos)
        $maxAge = 300; // 5 minutos
        if (abs(time() - $timestamp) > $maxAge) {
            throw new \Exception('Webhook Safe2Pay expirado');
        }
    }

    private function createWebhookDTO(Request $request, GatewayProvider $provider): WebhookDTO
    {
        $payload = $request->all();

        // Extrai dados específicos do provider
        $eventType = $this->extractEventType($payload, $provider);
        $transactionId = $this->extractTransactionId($payload, $provider);

        return new WebhookDTO(
            $provider->value,
            $eventType,
            $payload,
            null,
            $request->headers->all(),
            $request->header('X-Safe2Pay-Signature') ?? '',
            $request->ip()
        );
    }

    private function extractEventType(array $payload, GatewayProvider $provider): string
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY => $payload['EventType'] ?? $payload['event_type'] ?? 'unknown',
            default => $payload['event_type'] ?? $payload['type'] ?? 'unknown'
        };
    }

    private function extractTransactionId(array $payload, GatewayProvider $provider): string
    {
        return match ($provider) {
            GatewayProvider::SAFE2PAY => $payload['Transaction']['Id'] ?? $payload['transaction_id'] ?? '',
            default => $payload['transaction_id'] ?? $payload['id'] ?? ''
        };
    }
}
