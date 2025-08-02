<?php

namespace App\Affiliates\Controllers;

use App\Http\Controllers\Controller;
use App\Affiliates\Models\Affiliate;
use App\Affiliates\Models\AffiliateCommission;
use App\Affiliates\Models\AffiliatePayment;
use App\Affiliates\Services\AffiliateService;
use App\Core\Config\ConfigManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{
    private $affiliateService;
    private $config;

    public function __construct(AffiliateService $affiliateService, ConfigManager $config)
    {
        $this->affiliateService = $affiliateService;
        $this->config = $config;
    }

    /**
     * Listar affiliates (admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Affiliate::query();

            // Filtros
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('affiliate_code', 'like', "%{$search}%");
                });
            }

            // Ordenação
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginação
            $perPage = min($request->get('per_page', 15), 100);
            $affiliates = $query->paginate($perPage);

            return response()->json([
                'affiliates' => $affiliates->items(),
                'pagination' => [
                    'current_page' => $affiliates->currentPage(),
                    'last_page' => $affiliates->lastPage(),
                    'per_page' => $affiliates->perPage(),
                    'total' => $affiliates->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar affiliates: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Criar novo affiliate
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliates,email',
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'required|string|max:18|unique:affiliates,cpf_cnpj',
            'payment_method' => 'required|in:pix,bank_transfer,paypal',
            'bank_details' => 'required|array',
            'commission_rate' => 'nullable|numeric|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $affiliate = Affiliate::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'cpf_cnpj' => $request->cpf_cnpj,
                'payment_method' => $request->payment_method,
                'bank_details' => $request->bank_details,
                'commission_rate' => $request->commission_rate ?? $this->config->get('affiliate.default_commission_rate', 10),
                'status' => Affiliate::STATUS_PENDING
            ]);

            Log::info("Novo affiliate criado", [
                'affiliate_id' => $affiliate->id,
                'email' => $affiliate->email
            ]);

            return response()->json([
                'message' => 'Affiliate criado com sucesso',
                'affiliate' => $affiliate->toArray()
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar affiliate'], 500);
        }
    }

    /**
     * Mostrar affiliate específico
     */
    public function show(int $id): JsonResponse
    {
        try {
            $affiliate = Affiliate::findOrFail($id);
            $statistics = $affiliate->getStatistics();
            $monthlyPerformance = $affiliate->getMonthlyPerformance();

            return response()->json([
                'affiliate' => $affiliate->toArray(),
                'statistics' => $statistics,
                'monthly_performance' => $monthlyPerformance
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Affiliate não encontrado'], 404);
        }
    }

    /**
     * Atualizar affiliate
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => "email|unique:affiliates,email,{$id}",
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => "string|max:18|unique:affiliates,cpf_cnpj,{$id}",
            'payment_method' => 'in:pix,bank_transfer,paypal',
            'bank_details' => 'array',
            'commission_rate' => 'numeric|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $affiliate = Affiliate::findOrFail($id);
            $affiliate->update($request->only([
                'name',
                'email',
                'phone',
                'cpf_cnpj',
                'payment_method',
                'bank_details',
                'commission_rate'
            ]));

            Log::info("Affiliate atualizado", [
                'affiliate_id' => $affiliate->id,
                'changes' => $request->only([
                    'name',
                    'email',
                    'phone',
                    'commission_rate'
                ])
            ]);

            return response()->json([
                'message' => 'Affiliate atualizado com sucesso',
                'affiliate' => $affiliate->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar affiliate'], 500);
        }
    }

    /**
     * Aprovar affiliate
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'commission_rate' => 'nullable|numeric|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $success = $this->affiliateService->approveAffiliate($id, $request->commission_rate);

            if ($success) {
                return response()->json(['message' => 'Affiliate aprovado com sucesso']);
            } else {
                return response()->json(['error' => 'Não foi possível aprovar o affiliate'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao aprovar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao aprovar affiliate'], 500);
        }
    }

    /**
     * Rejeitar affiliate
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $affiliate = Affiliate::findOrFail($id);
            $affiliate->reject($request->reason);

            Log::info("Affiliate rejeitado", [
                'affiliate_id' => $affiliate->id,
                'reason' => $request->reason
            ]);

            return response()->json(['message' => 'Affiliate rejeitado']);
        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao rejeitar affiliate'], 500);
        }
    }

    /**
     * Suspender affiliate
     */
    public function suspend(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $affiliate = Affiliate::findOrFail($id);
            $affiliate->suspend($request->reason);

            Log::info("Affiliate suspenso", [
                'affiliate_id' => $affiliate->id,
                'reason' => $request->reason
            ]);

            return response()->json(['message' => 'Affiliate suspenso']);
        } catch (\Exception $e) {
            Log::error('Erro ao suspender affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao suspender affiliate'], 500);
        }
    }

    /**
     * Reativar affiliate
     */
    public function reactivate(int $id): JsonResponse
    {
        try {
            $affiliate = Affiliate::findOrFail($id);
            $affiliate->reactivate();

            Log::info("Affiliate reativado", ['affiliate_id' => $affiliate->id]);

            return response()->json(['message' => 'Affiliate reativado']);
        } catch (\Exception $e) {
            Log::error('Erro ao reativar affiliate: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao reativar affiliate'], 500);
        }
    }

    /**
     * Obter comissões do affiliate
     */
    public function commissions(Request $request, int $id): JsonResponse
    {
        try {
            $query = AffiliateCommission::where('affiliate_id', $id)
                ->with(['referral', 'merchant', 'subscription']);

            // Filtros
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Ordenação
            $query->orderBy('created_at', 'desc');

            // Paginação
            $perPage = min($request->get('per_page', 15), 100);
            $commissions = $query->paginate($perPage);

            return response()->json([
                'commissions' => $commissions->items(),
                'pagination' => [
                    'current_page' => $commissions->currentPage(),
                    'last_page' => $commissions->lastPage(),
                    'per_page' => $commissions->perPage(),
                    'total' => $commissions->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar comissões: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar comissões'], 500);
        }
    }

    /**
     * Obter pagamentos do affiliate
     */
    public function payments(Request $request, int $id): JsonResponse
    {
        try {
            $query = AffiliatePayment::where('affiliate_id', $id);

            // Filtros
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Ordenação
            $query->orderBy('created_at', 'desc');

            // Paginação
            $perPage = min($request->get('per_page', 15), 100);
            $payments = $query->paginate($perPage);

            return response()->json([
                'payments' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar pagamentos: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar pagamentos'], 500);
        }
    }

    /**
     * Obter estatísticas do programa de afiliados
     */
    public function programStatistics(): JsonResponse
    {
        try {
            $statistics = $this->affiliateService->getProgramStatistics();
            $topPerformers = $this->affiliateService->getTopPerformers();

            return response()->json([
                'statistics' => $statistics,
                'top_performers' => $topPerformers
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar estatísticas: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar estatísticas'], 500);
        }
    }

    /**
     * Processar pagamentos pendentes
     */
    public function processPayments(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'min_amount' => 'nullable|numeric|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $minAmount = $request->get('min_amount', 50);
            $results = $this->affiliateService->processPayments($minAmount);

            Log::info("Processamento de pagamentos concluído", $results);

            return response()->json([
                'message' => 'Processamento de pagamentos concluído',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamentos: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar pagamentos'], 500);
        }
    }

    /**
     * Gerar relatório de performance
     */
    public function performanceReport(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => 'in:7d,30d,90d,1y'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $period = $request->get('period', '30d');
            $report = $this->affiliateService->generatePerformanceReport($id, $period);

            return response()->json($report);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao gerar relatório'], 500);
        }
    }

    /**
     * Validar código de referral (endpoint público)
     */
    public function validateReferralCode(string $code): JsonResponse
    {
        try {
            $affiliate = $this->affiliateService->validateReferralCode($code);

            if ($affiliate) {
                return response()->json([
                    'valid' => true,
                    'affiliate' => [
                        'name' => $affiliate->name,
                        'code' => $affiliate->affiliate_code,
                        'commission_rate' => $affiliate->commission_rate
                    ]
                ]);
            } else {
                return response()->json(['valid' => false]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao validar código: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao validar código'], 500);
        }
    }
}
