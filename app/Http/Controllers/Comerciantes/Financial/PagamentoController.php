<?php

namespace App\Http\Controllers\Comerciantes\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\Pagamento;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Empresa;
use App\Services\Financial\ContasPagarService;
use App\Enums\SituacaoFinanceiraEnum;
use App\Enums\NaturezaFinanceiraEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PagamentoController extends Controller
{
    protected $contasPagarService;

    public function __construct(ContasPagarService $contasPagarService)
    {
        $this->contasPagarService = $contasPagarService;
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Empresa $empresa, $id)
    {
        $contaPagar = LancamentoFinanceiro::where('empresa_id', $empresa->id)
            ->where('id', $id)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->firstOrFail();

        // Buscar formas de pagamento da empresa
        $formasPagamento = DB::table('formas_pagamento')
            ->where('empresa_id', $empresa->id)
            ->where('ativo', true)
            ->where('tipo', 'pagamento')
            ->get(['id', 'nome', 'gateway_method']);

        // Buscar contas bancárias da empresa
        $contasBancarias = DB::table('contas_bancarias')
            ->where('empresa_id', $empresa->id)
            ->where('ativo', true)
            ->get(['id', 'nome', 'banco', 'agencia', 'conta']);

        return view('comerciantes.financeiro.contas-pagar.pagamento', compact(
            'empresa',
            'contaPagar',
            'formasPagamento',
            'contasBancarias'
        ));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request, Empresa $empresa, $id)
    {
        $request->validate([
            'forma_pagamento_id' => 'required|exists:formas_pagamento,id',
            'bandeira_id' => 'nullable|exists:forma_pag_bandeiras,id',
            'conta_bancaria_id' => 'required|integer',
            'valor' => 'required|numeric|min:0.01',
            'valor_principal' => 'nullable|numeric|min:0',
            'valor_juros' => 'nullable|numeric|min:0',
            'valor_multa' => 'nullable|numeric|min:0',
            'valor_desconto' => 'nullable|numeric|min:0',
            'data_pagamento' => 'required|date',
            'data_compensacao' => 'nullable|date',
            'observacao' => 'nullable|string|max:1000',
            'comprovante_pagamento' => 'nullable|string',
            'taxa' => 'nullable|numeric|min:0|max:100',
            'valor_taxa' => 'nullable|numeric|min:0',
            'referencia_externa' => 'nullable|string|max:100',
        ]);

        try {
            // Usar o Service para processar o pagamento
            $pagamento = $this->contasPagarService->pagar($id, [
                'forma_pagamento_id' => $request->forma_pagamento_id,
                'bandeira_id' => $request->bandeira_id,
                'conta_bancaria_id' => $request->conta_bancaria_id,
                'valor' => $request->valor,
                'valor_principal' => $request->valor_principal ?? $request->valor,
                'valor_juros' => $request->valor_juros ?? 0,
                'valor_multa' => $request->valor_multa ?? 0,
                'valor_desconto' => $request->valor_desconto ?? 0,
                'data_pagamento' => $request->data_pagamento,
                'data_compensacao' => $request->data_compensacao,
                'observacao' => $request->observacao,
                'comprovante_pagamento' => $request->comprovante_pagamento,
                'taxa' => $request->taxa ?? 0,
                'valor_taxa' => $request->valor_taxa ?? 0,
                'referencia_externa' => $request->referencia_externa,
                'usuario_id' => Auth::id(),
            ]);

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas-pagar.show', ['empresa' => $empresa, 'id' => $id])
                ->with('success', 'Pagamento registrado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Empresa $empresa, $id, Pagamento $pagamento)
    {
        $pagamento->load(['lancamento']);

        return response()->json([
            'success' => true,
            'pagamento' => $pagamento
        ]);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Empresa $empresa, $id, Pagamento $pagamento)
    {
        $request->validate([
            'observacao' => 'nullable|string|max:1000',
            'comprovante_pagamento' => 'nullable|string',
            'data_compensacao' => 'nullable|date',
        ]);

        try {
            $pagamento->update($request->only([
                'observacao',
                'comprovante_pagamento',
                'data_compensacao'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Pagamento atualizado com sucesso!',
                'pagamento' => $pagamento->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel (estorno) the specified payment.
     */
    public function destroy(Request $request, Empresa $empresa, $id, Pagamento $pagamento)
    {
        $request->validate([
            'motivo_estorno' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Estornar o pagamento
            $pagamento->estornar($request->motivo_estorno);

            // Atualizar situação do lançamento
            $lancamento = LancamentoFinanceiro::findOrFail($id);
            $this->atualizarSituacaoLancamento($lancamento);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pagamento estornado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment summary for a lancamento
     */
    public function getSummary(Empresa $empresa, $id)
    {
        $lancamento = LancamentoFinanceiro::findOrFail($id);

        $pagamentos = $lancamento->pagamentos()
            ->where('status_pagamento', 'confirmado')
            ->orderBy('created_at', 'desc')
            ->get();

        $valorTotal = $lancamento->valor_final;
        $valorPago = $pagamentos->sum('valor');
        $saldoDevedor = $valorTotal - $valorPago;

        return response()->json([
            'success' => true,
            'summary' => [
                'valor_total' => $valorTotal,
                'valor_pago' => $valorPago,
                'saldo_devedor' => $saldoDevedor,
                'percentual_pago' => $valorTotal > 0 ? round(($valorPago / $valorTotal) * 100, 2) : 0,
                'total_pagamentos' => $pagamentos->count(),
                'situacao_atual' => $saldoDevedor <= 0 ? 'Quitado' : 'Pendente'
            ],
            'pagamentos' => $pagamentos
        ]);
    }

    /**
     * Atualizar situação do lançamento baseado nos pagamentos
     */
    private function atualizarSituacaoLancamento(LancamentoFinanceiro $lancamento)
    {
        $valorPago = $lancamento->pagamentos()
            ->where('status_pagamento', 'confirmado')
            ->sum('valor');

        $valorTotal = $lancamento->valor_final;

        if ($valorPago >= $valorTotal) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PAGO;
        } elseif ($valorPago > 0) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PARCIALMENTE_PAGO;
        } else {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
        }

        $lancamento->save();
    }
}
