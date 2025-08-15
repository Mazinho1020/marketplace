<?php

namespace App\Http\Controllers\Comerciantes\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\Pagamento;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Empresa;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecebimentoController extends Controller
{
    /**
     * Store a newly created recebimento.
     */
    public function store(Request $request, Empresa $empresa, $id)
    {
        // Log da requisição para debug
        Log::info('RecebimentoController::store - Início', [
            'empresa_id' => $empresa->id,
            'lancamento_id' => $id,
            'request_data' => $request->all(),
            'auth_user' => Auth::id(),
            'auth_check' => Auth::check()
        ]);

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
            DB::beginTransaction();

            // Verificar se o lançamento existe e é de receber
            $lancamento = LancamentoFinanceiro::where('empresa_id', $empresa->id)
                ->where('id', $id)
                ->where('natureza_financeira', 'receber')
                ->firstOrFail();

            // Determinar o usuário
            $usuarioId = Auth::id() ?? 1;

            // Buscar próximo número de parcela
            $proximaParcela = Pagamento::where('lancamento_id', $id)
                ->where('tipo_id', 2) // 2 = recebimento
                ->max('numero_parcela_pagamento') + 1;

            // Criar o recebimento na tabela pagamentos com tipo_id = 2
            $recebimento = Pagamento::create([
                'lancamento_id' => $id,
                'numero_parcela_pagamento' => $proximaParcela,
                'tipo_id' => 2, // 2 = recebimento
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
                'usuario_id' => $usuarioId,
                'empresa_id' => $empresa->id,
                'status_pagamento' => 'confirmado',
            ]);

            // Atualizar situação do lançamento
            $this->atualizarSituacaoLancamento($lancamento);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Recebimento registrado com sucesso!',
                'recebimento' => $recebimento->load(['lancamento'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro ao registrar recebimento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar recebimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified recebimento.
     */
    public function show(Empresa $empresa, $id, $recebimentoId)
    {
        $recebimento = Pagamento::where('lancamento_id', $id)
            ->where('tipo_id', 2) // 2 = recebimento
            ->where('id', $recebimentoId)
            ->with(['lancamento'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'recebimento' => $recebimento
        ]);
    }

    /**
     * Update the specified recebimento.
     */
    public function update(Request $request, Empresa $empresa, $id, $recebimentoId)
    {
        $request->validate([
            'observacao' => 'nullable|string|max:1000',
            'comprovante_pagamento' => 'nullable|string',
            'data_compensacao' => 'nullable|date',
        ]);

        try {
            $recebimento = Pagamento::where('lancamento_id', $id)
                ->where('tipo_id', 2) // 2 = recebimento
                ->where('id', $recebimentoId)
                ->firstOrFail();

            $recebimento->update($request->only([
                'observacao',
                'comprovante_pagamento',
                'data_compensacao'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Recebimento atualizado com sucesso!',
                'recebimento' => $recebimento->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar recebimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel (estorno) the specified recebimento.
     */
    public function destroy(Request $request, Empresa $empresa, $id, $recebimentoId)
    {
        $request->validate([
            'motivo_estorno' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $recebimento = Pagamento::where('lancamento_id', $id)
                ->where('tipo_id', 2) // 2 = recebimento
                ->where('id', $recebimentoId)
                ->firstOrFail();

            // Estornar o recebimento
            $recebimento->update([
                'status_pagamento' => 'estornado',
                'observacao' => ($recebimento->observacao ?? '') . "\n[ESTORNO] " . $request->motivo_estorno
            ]);

            // Atualizar situação do lançamento
            $lancamento = LancamentoFinanceiro::findOrFail($id);
            $this->atualizarSituacaoLancamento($lancamento);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Recebimento estornado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar recebimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recebimento summary for a lancamento
     */
    public function getSummary(Empresa $empresa, $id)
    {
        $lancamento = LancamentoFinanceiro::findOrFail($id);

        // Buscar recebimentos (pagamentos com tipo_id = 2)
        $recebimentos = Pagamento::where('lancamento_id', $id)
            ->where('tipo_id', 2) // 2 = recebimento
            ->where('status_pagamento', 'confirmado')
            ->with(['formaPagamento', 'bandeira', 'contaBancaria'])
            ->orderBy('created_at', 'desc')
            ->get();

        $valorTotal = $lancamento->valor_final ?? $lancamento->valor;
        $valorRecebido = $recebimentos->sum('valor');
        $saldoDevedor = $valorTotal - $valorRecebido;

        return response()->json([
            'success' => true,
            'summary' => [
                'valor_total' => $valorTotal,
                'valor_recebido' => $valorRecebido,
                'saldo_devedor' => $saldoDevedor,
                'percentual_recebido' => $valorTotal > 0 ? round(($valorRecebido / $valorTotal) * 100, 2) : 0,
                'total_recebimentos' => $recebimentos->count(),
                'situacao_atual' => $saldoDevedor <= 0 ? 'Quitado' : 'Pendente'
            ],
            'recebimentos' => $recebimentos
        ]);
    }

    /**
     * Atualizar situação do lançamento baseado nos recebimentos
     */
    private function atualizarSituacaoLancamento(LancamentoFinanceiro $lancamento)
    {
        // Buscar recebimentos confirmados (pagamentos com tipo_id = 2)
        $valorRecebido = Pagamento::where('lancamento_id', $lancamento->id)
            ->where('tipo_id', 2) // 2 = recebimento
            ->where('status_pagamento', 'confirmado')
            ->sum('valor');

        $valorTotal = $lancamento->valor_final ?? $lancamento->valor;

        if ($valorRecebido >= $valorTotal) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PAGO;
        } elseif ($valorRecebido > 0) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PARCIALMENTE_PAGO;
        } else {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
        }

        $lancamento->save();
    }

    /**
     * Calcular resumo dos recebimentos
     */
    private function calcularResumoRecebimentos($contaReceber)
    {
        // Carregar recebimentos realizados
        $recebimentos = $contaReceber->recebimentos()
            ->where('status_pagamento', 'confirmado')
            ->with(['formaPagamento', 'bandeira', 'contaBancaria'])
            ->orderBy('data_pagamento', 'desc')
            ->get();

        // Calcular resumo dos recebimentos
        $valorTotal = $contaReceber->valor_final ?? $contaReceber->valor;
        $valorRecebido = $recebimentos->sum('valor');
        $saldoDevedor = $valorTotal - $valorRecebido;

        return [
            'valor_total' => $valorTotal,
            'valor_recebido' => $valorRecebido,
            'saldo_devedor' => $saldoDevedor,
            'percentual_recebido' => $valorTotal > 0 ? round(($valorRecebido / $valorTotal) * 100, 2) : 0,
            'total_recebimentos' => $recebimentos->count(),
            'situacao_atual' => $saldoDevedor <= 0 ? 'Quitado' : 'Pendente'
        ];
    }

    /**
     * Exibe página de pagamento separada
     */
    public function showPagamento(Empresa $empresa, $id)
    {
        try {
            // Buscar o lançamento específico
            $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresa->id)
                ->where('id', $id)
                ->where('natureza_financeira', 'receber')
                ->with(['pessoa', 'contaGerencial', 'pagamentos.formaPagamento', 'pagamentos.bandeira', 'pagamentos.contaBancaria'])
                ->firstOrFail();

            // Buscar todos os lançamentos pendentes da empresa para seleção
            $recebimentos = LancamentoFinanceiro::where('empresa_id', $empresa->id)
                ->where('natureza_financeira', 'receber')
                ->whereIn('situacao_financeira', ['pendente', 'parcialmente_pago'])
                ->with(['pessoa'])
                ->select([
                    'id',
                    'valor',
                    'valor_final',
                    'data_vencimento',
                    'pessoa_id',
                    'descricao'
                ])
                ->orderBy('data_vencimento')
                ->get()
                ->map(function ($lancamento) {
                    return [
                        'id' => $lancamento->id,
                        'valor_total' => $lancamento->valor_final ?? $lancamento->valor,
                        'data_vencimento' => $lancamento->data_vencimento,
                        'cliente_nome' => $lancamento->pessoa->nome ?? $lancamento->pessoa->razao_social ?? 'Cliente não informado',
                        'descricao' => $lancamento->descricao
                    ];
                });

            // Calcular resumo dos recebimentos
            $resumoRecebimentos = $this->calcularResumoRecebimentos($contaReceber);

            // Carregar formas de pagamento diretamente
            $formas_pagamento = DB::table('formas_pagamento')
                ->where('ativo', true)
                ->select('id', 'nome', 'tipo')
                ->orderBy('nome')
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                });

            // Carregar todas as bandeiras ativas
            $bandeiras = DB::table('forma_pag_bandeiras')
                ->where('ativo', true)
                ->select('id', 'nome')
                ->orderBy('nome')
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                });

            // Carregar contas bancárias da empresa
            $contas_bancarias = DB::table('conta_bancaria')
                ->where('empresa_id', $empresa->id)
                ->select('id', 'banco', 'agencia', 'numero_conta', 'nome_conta')
                ->orderBy('banco')
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                });

            return view('comerciantes.financeiro.contas-receber.pagamento', compact(
                'contaReceber',
                'empresa',
                'recebimentos',
                'resumoRecebimentos',
                'formas_pagamento',
                'bandeiras',
                'contas_bancarias'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir página de pagamento', [
                'empresa_id' => $empresa->id,
                'lancamento_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas-receber.show', [$empresa->id, $id])
                ->with('error', 'Erro ao carregar página de pagamento: ' . $e->getMessage());
        }
    }
}
