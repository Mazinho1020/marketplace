<?php

namespace App\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PaymentConfigService;
use App\DTOs\Payment\PaymentRequestDTO;
use App\Models\Payment\PaymentTransaction;
use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\SourceType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private PaymentConfigService $configService
    ) {}

    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'source_type' => ['required', Rule::in(array_map(fn($e) => $e->value, SourceType::cases()))],
            'source_id' => 'required|integer',
            'amount_final' => 'required|numeric|min:0.01',
            'payment_method' => ['required', Rule::in(array_map(fn($e) => $e->value, PaymentMethod::cases()))],
            'customer_name' => 'required|string|max:200',
            'customer_email' => 'nullable|email',
            'customer_document' => 'nullable|string|max:20',
            'customer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'installments' => 'nullable|integer|min:1|max:12',
            'source_reference' => 'nullable|string|max:100',
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
            'notification_url' => 'nullable|url',
            'metadata' => 'nullable|array',
            'payment_data' => 'nullable|array',
        ]);

        try {
            $dto = new PaymentRequestDTO(
                empresaId: $request->input('empresa_id'),
                sourceType: SourceType::from($request->input('source_type')),
                sourceId: $request->input('source_id'),
                amountFinal: (float) $request->input('amount_final'),
                paymentMethod: PaymentMethod::from($request->input('payment_method')),
                customerName: $request->input('customer_name'),
                customerEmail: $request->input('customer_email'),
                customerDocument: $request->input('customer_document'),
                customerPhone: $request->input('customer_phone'),
                description: $request->input('description'),
                installments: $request->input('installments', 1),
                amountOriginal: $request->input('amount_original'),
                amountDiscount: $request->input('amount_discount'),
                amountFees: $request->input('amount_fees'),
                sourceReference: $request->input('source_reference'),
                successUrl: $request->input('success_url'),
                cancelUrl: $request->input('cancel_url'),
                notificationUrl: $request->input('notification_url'),
                metadata: $request->input('metadata'),
                paymentData: $request->input('payment_data'),
                createdByUserId: $request->user()?->id
            );

            $transaction = $this->paymentService->createTransaction($dto);

            return response()->json([
                'success' => true,
                'message' => 'Transação criada com sucesso',
                'data' => [
                    'uuid' => $transaction->uuid,
                    'transaction_code' => $transaction->transaction_code,
                    'status' => $transaction->status->value,
                    'status_label' => $transaction->status->label(),
                    'amount_final' => $transaction->amount_final,
                    'payment_method' => $transaction->payment_method->value,
                    'payment_method_label' => $transaction->payment_method->label(),
                    'created_at' => $transaction->created_at->toISOString(),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function process(Request $request, string $transactionId): JsonResponse
    {
        try {
            $transaction = PaymentTransaction::where('uuid', $transactionId)->firstOrFail();

            $response = $this->paymentService->processPayment($transaction);

            return response()->json([
                'success' => $response->success,
                'message' => $response->message,
                'data' => $response->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(string $transactionId): JsonResponse
    {
        try {
            $transaction = PaymentTransaction::with(['events', 'webhooks'])
                ->where('uuid', $transactionId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'uuid' => $transaction->uuid,
                    'transaction_code' => $transaction->transaction_code,
                    'status' => $transaction->status->value,
                    'status_label' => $transaction->status->label(),
                    'status_color' => $transaction->status->color(),
                    'amount_final' => $transaction->amount_final,
                    'formatted_amount' => $transaction->formatted_amount,
                    'payment_method' => $transaction->payment_method->value,
                    'payment_method_label' => $transaction->payment_method->label(),
                    'source_type' => $transaction->source_type->value,
                    'source_type_label' => $transaction->source_type->label(),
                    'customer_name' => $transaction->customer_name,
                    'customer_email' => $transaction->customer_email,
                    'description' => $transaction->description,
                    'installments' => $transaction->installments,
                    'gateway_provider' => $transaction->gateway_provider?->value,
                    'gateway_transaction_id' => $transaction->gateway_transaction_id,
                    'qr_code' => $transaction->qr_code,
                    'bar_code' => $transaction->bar_code,
                    'digitable_line' => $transaction->digitable_line,
                    'payment_url' => $transaction->payment_url,
                    'expires_at' => $transaction->expires_at?->toISOString(),
                    'created_at' => $transaction->created_at->toISOString(),
                    'processed_at' => $transaction->processed_at?->toISOString(),
                    'approved_at' => $transaction->approved_at?->toISOString(),
                    'cancelled_at' => $transaction->cancelled_at?->toISOString(),
                    'events' => $transaction->events->map(function ($event) {
                        return [
                            'id' => $event->id,
                            'event_type' => $event->event_type,
                            'description' => $event->description,
                            'previous_status' => $event->previous_status,
                            'new_status' => $event->new_status,
                            'triggered_by' => $event->triggered_by,
                            'created_at' => $event->created_at->toISOString(),
                        ];
                    }),
                    'webhooks' => $transaction->webhooks->map(function ($webhook) {
                        return [
                            'id' => $webhook->id,
                            'gateway_provider' => $webhook->gateway_provider,
                            'event_type' => $webhook->event_type,
                            'processed' => $webhook->processed,
                            'status_label' => $webhook->status_label,
                            'status_color' => $webhook->status_color,
                            'created_at' => $webhook->created_at->toISOString(),
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada'
            ], 404);
        }
    }

    public function confirm(Request $request, string $transactionId): JsonResponse
    {
        try {
            $transaction = PaymentTransaction::where('uuid', $transactionId)->firstOrFail();

            $this->paymentService->confirmPayment($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Pagamento confirmado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel(Request $request, string $transactionId): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $transaction = PaymentTransaction::where('uuid', $transactionId)->firstOrFail();

            $this->paymentService->cancelPayment(
                $transaction,
                $request->input('reason')
            );

            return response()->json([
                'success' => true,
                'message' => 'Pagamento cancelado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function refund(Request $request, string $transactionId): JsonResponse
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01'
        ]);

        try {
            $transaction = PaymentTransaction::where('uuid', $transactionId)->firstOrFail();

            $response = $this->paymentService->refundPayment(
                $transaction,
                $request->input('amount')
            );

            return response()->json([
                'success' => $response->success,
                'message' => $response->message,
                'data' => $response->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'status' => 'nullable|string',
            'source_type' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'gateway_provider' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $filters = $request->only([
                'empresa_id',
                'status',
                'source_type',
                'payment_method',
                'gateway_provider',
                'date_from',
                'date_to'
            ]);

            $perPage = $request->input('per_page', 15);

            $transactions = $this->paymentService->listTransactions($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function paymentMethods(Request $request): JsonResponse
    {
        $empresaId = $request->query('empresa_id');

        if (!$empresaId) {
            return response()->json([
                'success' => false,
                'message' => 'ID da empresa é obrigatório'
            ], 400);
        }

        try {
            $configService = app(\App\Services\Payment\PaymentConfigService::class)
                ->setEmpresaId((int) $empresaId);

            $methods = $configService->getAvailablePaymentMethods();

            return response()->json([
                'success' => true,
                'data' => $methods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
