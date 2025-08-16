<?php

namespace App\Http\Controllers\Comerciantes\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\Cliente;
use App\Services\Vendas\VendaService;
use App\Http\Requests\Vendas\StoreVendaRequest;
use App\Http\Requests\Vendas\UpdateVendaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controller de Vendas para Comerciantes
 * 
 * Gerencia todas as operações de vendas do marketplace
 * seguindo os padrões estabelecidos no projeto
 */
class VendaController extends Controller
{
    protected $vendaService;

    public function __construct(VendaService $vendaService)
    {
        $this->vendaService = $vendaService;
    }

    /**
     * Display a listing of vendas.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            // Filtros
            $dataInicio = $request->get('data_inicio', now()->subDays(30)->format('Y-m-d'));
            $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
            $vendedorId = $request->get('vendedor_id');
            $clienteId = $request->get('cliente_id');
            $tipoVenda = $request->get('tipo_venda');
            $statusVenda = $request->get('status_venda');

            $filtros = array_filter([
                'vendedor_id' => $vendedorId,
                'cliente_id' => $clienteId,
                'tipo_venda' => $tipoVenda,
                'status_venda' => $statusVenda,
            ]);

            $vendasQuery = $this->vendaService->obterVendasPorPeriodo(
                $empresaId,
                Carbon::parse($dataInicio),
                Carbon::parse($dataFim),
                $filtros
            );

            $vendas = $vendasQuery->paginate(20);

            // Calcular métricas do período
            $metricas = $this->vendaService->calcularMetricasVendas(
                $empresaId,
                Carbon::parse($dataInicio),
                Carbon::parse($dataFim)
            );

            // Se for requisição AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'vendas' => $vendas,
                    'metricas' => $metricas
                ]);
            }

            return view('comerciantes.vendas.index', compact(
                'vendas',
                'metricas',
                'dataInicio',
                'dataFim',
                'vendedorId',
                'clienteId',
                'tipoVenda',
                'statusVenda'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao listar vendas', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'empresa_id' => $empresaId ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar vendas: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar vendas: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new venda.
     */
    public function create()
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            // Buscar dados necessários para o formulário
            $produtos = Produto::where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->where('status', 'disponivel')
                ->with(['categoria', 'marca', 'imagemPrincipal'])
                ->orderBy('nome')
                ->get();

            $clientes = Cliente::where('empresa_id', $empresaId)
                ->where('status', 'ativo')
                ->orderBy('nome')
                ->get();

            // Formas de pagamento (assumindo que existe uma tabela)
            $formasPagamento = DB::table('formas_pagamento')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            // Bandeiras de cartão
            $bandeiras = DB::table('forma_pag_bandeiras')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            return view('comerciantes.vendas.create', compact(
                'produtos',
                'clientes',
                'formasPagamento',
                'bandeiras'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de venda', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created venda.
     */
    public function store(StoreVendaRequest $request)
    {
        try {
            $venda = $this->vendaService->criarVenda($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda criada com sucesso!',
                    'venda' => $venda->load(['itens.produto', 'pagamentos.formaPagamento', 'cliente'])
                ]);
            }

            return redirect()
                ->route('comerciantes.vendas.show', $venda->id)
                ->with('success', 'Venda criada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar venda', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar venda: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao criar venda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified venda.
     */
    public function show(Request $request, Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            $venda->load([
                'cliente',
                'usuario',
                'itens.produto.imagemPrincipal',
                'pagamentos.formaPagamento',
                'pagamentos.bandeira'
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'venda' => $venda
                ]);
            }

            return view('comerciantes.vendas.show', compact('venda'));

        } catch (\Exception $e) {
            Log::error('Erro ao exibir venda', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar venda: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar venda: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified venda.
     */
    public function edit(Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            // Verificar se a venda pode ser editada
            if ($venda->isCancelada() || $venda->status_venda === Venda::STATUS_FINALIZADA) {
                return back()->with('error', 'Esta venda não pode ser editada.');
            }

            $empresaId = $user->empresa_id;

            // Buscar dados necessários para o formulário
            $produtos = Produto::where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->with(['categoria', 'marca', 'imagemPrincipal'])
                ->orderBy('nome')
                ->get();

            $clientes = Cliente::where('empresa_id', $empresaId)
                ->where('status', 'ativo')
                ->orderBy('nome')
                ->get();

            $formasPagamento = DB::table('formas_pagamento')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            $bandeiras = DB::table('forma_pag_bandeiras')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            $venda->load(['itens.produto', 'pagamentos.formaPagamento']);

            return view('comerciantes.vendas.edit', compact(
                'venda',
                'produtos',
                'clientes',
                'formasPagamento',
                'bandeiras'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de edição', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified venda.
     */
    public function update(UpdateVendaRequest $request, Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            $venda->update($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda atualizada com sucesso!',
                    'venda' => $venda->fresh(['itens.produto', 'pagamentos.formaPagamento', 'cliente'])
                ]);
            }

            return redirect()
                ->route('comerciantes.vendas.show', $venda->id)
                ->with('success', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar venda', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar venda: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified venda (soft delete).
     */
    public function destroy(Request $request, Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            // Verificar se a venda pode ser cancelada
            if ($venda->isCancelada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venda já está cancelada.'
                ], 400);
            }

            $this->vendaService->cancelarVenda($venda);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda cancelada com sucesso!'
                ]);
            }

            return redirect()
                ->route('comerciantes.vendas.index')
                ->with('success', 'Venda cancelada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar venda', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar venda: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar venda (baixar estoque)
     */
    public function confirmar(Request $request, Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            $this->vendaService->confirmarVenda($venda);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda confirmada com sucesso!',
                    'venda' => $venda->fresh()
                ]);
            }

            return back()->with('success', 'Venda confirmada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar venda', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'user_id' => auth()->id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao confirmar venda: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao confirmar venda: ' . $e->getMessage());
        }
    }

    /**
     * Imprimir cupom/comprovante da venda
     */
    public function imprimir(Venda $venda)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            // Verificar se a venda pertence à empresa do usuário
            if ($venda->empresa_id !== $user->empresa_id) {
                abort(403, 'Acesso negado.');
            }

            $venda->load([
                'cliente',
                'usuario',
                'empresa',
                'itens.produto',
                'pagamentos.formaPagamento'
            ]);

            return view('comerciantes.vendas.imprimir', compact('venda'));

        } catch (\Exception $e) {
            Log::error('Erro ao gerar comprovante', [
                'error' => $e->getMessage(),
                'venda_id' => $venda->id,
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Erro ao gerar comprovante: ' . $e->getMessage());
        }
    }
}