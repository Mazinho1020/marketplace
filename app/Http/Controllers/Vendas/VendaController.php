<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Vendas\Venda;
use App\Models\Vendas\VendaStatusHistorico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controller principal para gestão de vendas
 * 
 * Gerencia todas as operações relacionadas a vendas/pedidos,
 * aproveitando 100% da infraestrutura existente do marketplace.
 */
class VendaController extends Controller
{
    /**
     * Lista vendas com filtros avançados
     */
    public function index(Request $request): View
    {
        $vendas = Venda::vendas()
            ->with(['cliente', 'pagamentos', 'historicoStatus'])
            ->empresa(auth()->user()->empresa_id ?? 1);

        // Aplicar filtros
        if ($request->filled('status')) {
            $vendas->porStatus($request->status);
        }

        if ($request->filled('canal')) {
            $vendas->porCanal($request->canal);
        }

        if ($request->filled('data_inicio')) {
            $vendas->where('data_emissao', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $vendas->where('data_emissao', '<=', $request->data_fim);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $vendas->where(function($query) use ($search) {
                $query->where('numero_venda', 'like', "%{$search}%")
                      ->orWhere('descricao', 'like', "%{$search}%")
                      ->orWhereHas('cliente', function($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%");
                      });
            });
        }

        $vendas = $vendas->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('vendas.index', compact('vendas'));
    }

    /**
     * Mostra formulário de criação de nova venda
     */
    public function create(): View
    {
        return view('vendas.create');
    }

    /**
     * Cria nova venda
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'valor_bruto' => 'required|numeric|min:0',
            'canal_venda' => 'required|in:pdv,online,delivery,telefone,whatsapp,presencial',
            'tipo_entrega' => 'required|in:balcao,entrega,correios,transportadora',
            'data_entrega_prevista' => 'nullable|date|after:today',
            'descricao' => 'required|string|max:500',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:1',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
        ]);

        try {
            \DB::beginTransaction();

            // Criar venda
            $venda = Venda::create([
                'empresa_id' => auth()->user()->empresa_id ?? 1,
                'usuario_id' => auth()->id(),
                'pessoa_id' => $request->pessoa_id,
                'pessoa_tipo' => 'cliente',
                'valor_bruto' => $request->valor_bruto,
                'valor_desconto' => $request->valor_desconto ?? 0,
                'canal_venda' => $request->canal_venda,
                'tipo_entrega' => $request->tipo_entrega,
                'data_entrega_prevista' => $request->data_entrega_prevista,
                'descricao' => $request->descricao,
                'data_emissao' => now(),
                'data_competencia' => now()->format('Y-m-d'),
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
                'situacao_financeira' => Venda::STATUS_RASCUNHO,
                'prioridade' => $request->prioridade ?? 'normal',
                'observacoes' => $request->observacoes,
            ]);

            // Adicionar itens da venda
            foreach ($request->itens as $item) {
                $venda->itens()->create([
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario' => $item['valor_unitario'],
                    'valor_total' => $item['quantidade'] * $item['valor_unitario'],
                    'descricao' => $item['descricao'] ?? '',
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda criada com sucesso!',
                'venda' => $venda->load(['cliente', 'itens']),
            ], 201);

        } catch (\Exception $e) {
            \DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostra detalhes da venda
     */
    public function show(int $id): View
    {
        $venda = Venda::vendas()
            ->with([
                'cliente', 
                'itens.produto', 
                'pagamentos', 
                'historicoStatus.usuario',
                'cancelamentos.usuario',
                'cupomFidelidade'
            ])
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        return view('vendas.show', compact('venda'));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(int $id): View
    {
        $venda = Venda::vendas()
            ->with(['cliente', 'itens'])
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        // Só permite edição se estiver em rascunho ou pendente
        if (!in_array($venda->situacao_financeira, [Venda::STATUS_RASCUNHO, Venda::STATUS_PENDENTE])) {
            abort(403, 'Venda não pode ser editada neste status.');
        }

        return view('vendas.edit', compact('venda'));
    }

    /**
     * Atualiza venda
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $venda = Venda::vendas()
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        // Verificar se pode ser editada
        if (!in_array($venda->situacao_financeira, [Venda::STATUS_RASCUNHO, Venda::STATUS_PENDENTE])) {
            return response()->json([
                'success' => false,
                'message' => 'Venda não pode ser editada neste status.',
            ], 403);
        }

        $request->validate([
            'valor_bruto' => 'sometimes|numeric|min:0',
            'data_entrega_prevista' => 'nullable|date|after:today',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        try {
            $venda->update($request->only([
                'valor_bruto',
                'valor_desconto', 
                'data_entrega_prevista',
                'observacoes',
                'prioridade'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Venda atualizada com sucesso!',
                'venda' => $venda->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Altera status da venda
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $venda = Venda::vendas()
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'motivo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        try {
            $sucesso = $venda->alterarStatus(
                $request->status,
                $request->motivo,
                $request->only(['observacoes', 'dados_adicionais'])
            );

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transição de status não permitida.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'venda' => $venda->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancela venda
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $venda = Venda::vendas()
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        $request->validate([
            'motivo_categoria' => 'required|in:cliente_desistiu,produto_indisponivel,erro_preco,problema_pagamento,outros',
            'motivo_detalhado' => 'required|string|max:500',
        ]);

        try {
            if (!$venda->podeSerCancelada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venda não pode ser cancelada neste status.',
                ], 400);
            }

            $cancelamento = $venda->cancelar(
                $request->motivo_detalhado,
                $request->motivo_categoria
            );

            return response()->json([
                'success' => true,
                'message' => 'Venda cancelada com sucesso!',
                'venda' => $venda->fresh(),
                'cancelamento' => $cancelamento,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplica venda para recorrência
     */
    public function duplicate(int $id): JsonResponse
    {
        $vendaOriginal = Venda::vendas()
            ->with(['itens'])
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($id);

        try {
            \DB::beginTransaction();

            $novaVenda = $vendaOriginal->replicate();
            $novaVenda->numero_venda = null; // Será gerado automaticamente
            $novaVenda->situacao_financeira = Venda::STATUS_RASCUNHO;
            $novaVenda->data_emissao = now();
            $novaVenda->data_competencia = now()->format('Y-m-d');
            $novaVenda->save();

            // Duplicar itens
            foreach ($vendaOriginal->itens as $item) {
                $novoItem = $item->replicate();
                $novoItem->lancamento_id = $novaVenda->id;
                $novoItem->save();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venda duplicada com sucesso!',
                'venda' => $novaVenda->load(['cliente', 'itens']),
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao duplicar venda: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Lista vendas para APIs externas
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $vendas = Venda::vendas()
            ->with(['cliente', 'itens'])
            ->empresa(auth()->user()->empresa_id ?? 1);

        // Aplicar filtros da API
        if ($request->filled('status')) {
            $vendas->porStatus($request->status);
        }

        if ($request->filled('canal')) {
            $vendas->porCanal($request->canal);
        }

        $vendas = $vendas->orderBy('created_at', 'desc')
                        ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $vendas,
        ]);
    }

    /**
     * API: Retorna métricas do dashboard
     */
    public function metrics(Request $request): JsonResponse
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $periodo = $request->periodo ?? 'hoje';

        $dataInicio = match($periodo) {
            'hoje' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'mes' => now()->startOfMonth(),
            'ano' => now()->startOfYear(),
            default => now()->startOfDay(),
        };

        $vendas = Venda::vendas()
            ->empresa($empresaId)
            ->where('data_emissao', '>=', $dataInicio);

        $metrics = [
            'total_vendas' => $vendas->count(),
            'valor_total' => $vendas->sum('valor_bruto'),
            'vendas_por_status' => $vendas->groupBy('situacao_financeira')
                                         ->selectRaw('situacao_financeira, count(*) as total')
                                         ->pluck('total', 'situacao_financeira'),
            'vendas_por_canal' => $vendas->groupBy('canal_venda')
                                        ->selectRaw('canal_venda, count(*) as total')
                                        ->pluck('total', 'canal_venda'),
            'ticket_medio' => $vendas->avg('valor_bruto'),
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }
}