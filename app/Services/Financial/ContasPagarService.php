<?php

namespace App\Services\Financial;

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\DTOs\Financial\ContaPagarDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContasPagarService
{
    /**
     * Criar nova conta a pagar
     */
    public function criar(ContaPagarDTO $dados): LancamentoFinanceiro
    {
        return DB::transaction(function () use ($dados) {
            $lancamento = LancamentoFinanceiro::create([
                'empresa_id' => $dados->empresa_id,
                'natureza_financeira' => NaturezaFinanceiraEnum::PAGAR,
                'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
                'pessoa_id' => $dados->pessoa_id,
                'pessoa_tipo' => $dados->pessoa_tipo ?? 'fornecedor',
                'conta_gerencial_id' => $dados->conta_gerencial_id,
                'descricao' => $dados->descricao,
                'numero_documento' => $dados->numero_documento,
                'valor' => $dados->valor_final,
                'valor_original' => $dados->valor_original,
                'valor_desconto' => $dados->valor_desconto,
                'valor_acrescimo' => $dados->valor_acrescimo,
                'valor_juros' => $dados->valor_juros,
                'valor_multa' => $dados->valor_multa,
                'valor_final' => $dados->valor_final,
                'valor_saldo' => $dados->valor_final, // Inicialmente todo o valor é saldo
                'data_emissao' => $dados->data_emissao,
                'data_competencia' => $dados->data_competencia,
                'data_vencimento' => $dados->data_vencimento,
                'observacoes' => $dados->observacoes,
                'e_recorrente' => $dados->e_recorrente,
                'frequencia_recorrencia' => $dados->frequencia_recorrencia,
                'cobranca_automatica' => $dados->cobranca_automatica,
                'usuario_id' => $dados->usuario_id ?? auth()->id(),
            ]);

            return $lancamento;
        });
    }

    /**
     * Criar contas parceladas
     */
    public function criarParcelado(ContaPagarDTO $dados, int $parcelas): Collection
    {
        return DB::transaction(function () use ($dados, $parcelas) {
            $lancamentos = collect();
            $valorParcela = $dados->valor_final / $parcelas;
            $dataVencimento = Carbon::parse($dados->data_vencimento);
            $grupoParcelas = uniqid('CP_' . $dados->empresa_id . '_');

            for ($i = 1; $i <= $parcelas; $i++) {
                $dadosParcela = clone $dados;
                $dadosParcela->descricao = $dados->descricao . " (Parcela {$i}/{$parcelas})";
                $dadosParcela->valor_original = round($dados->valor_original / $parcelas, 2);
                $dadosParcela->valor_final = round($valorParcela, 2);
                $dadosParcela->data_vencimento = $dataVencimento->toDateString();

                $lancamento = $this->criar($dadosParcela);

                $lancamento->update([
                    'parcela_atual' => $i,
                    'total_parcelas' => $parcelas,
                    'grupo_parcelas' => $grupoParcelas,
                    'valor_saldo' => round($valorParcela, 2),
                ]);

                $lancamentos->push($lancamento);

                // Próxima data de vencimento (30 dias padrão)
                $dataVencimento->addDays($dados->intervalo_parcelas ?? 30);
            }

            return $lancamentos;
        });
    }

    /**
     * Efetuar pagamento de uma conta
     */
    public function pagar(int $lancamentoId, array $dadosPagamento): Pagamento
    {
        return DB::transaction(function () use ($lancamentoId, $dadosPagamento) {
            $lancamento = LancamentoFinanceiro::findOrFail($lancamentoId);

            if ($lancamento->natureza_financeira !== NaturezaFinanceiraEnum::PAGAR) {
                throw new \InvalidArgumentException('Este lançamento não é uma conta a pagar');
            }

            // Validar se o valor não excede o saldo devedor (calculado dinamicamente)
            $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
            $saldoDevedor = $lancamento->valor_final - $valorPago;
            $valorPagamento = $dadosPagamento['valor'];

            if ($valorPagamento > ($saldoDevedor + 0.01)) {
                throw new \InvalidArgumentException('O valor do pagamento não pode ser maior que o saldo devedor');
            }

            // Criar o pagamento
            $pagamento = $lancamento->adicionarPagamento([
                'tipo_id' => 1, // Tipo padrão
                'forma_pagamento_id' => $dadosPagamento['forma_pagamento_id'],
                'bandeira_id' => $dadosPagamento['bandeira_id'] ?? null,
                'conta_bancaria_id' => $dadosPagamento['conta_bancaria_id'] ?? null,
                'valor' => $valorPagamento,
                'valor_principal' => $dadosPagamento['valor_principal'] ?? $valorPagamento,
                'valor_juros' => $dadosPagamento['valor_juros'] ?? 0,
                'valor_multa' => $dadosPagamento['valor_multa'] ?? 0,
                'valor_desconto' => $dadosPagamento['valor_desconto'] ?? 0,
                'data_pagamento' => $dadosPagamento['data_pagamento'],
                'data_compensacao' => $dadosPagamento['data_compensacao'] ?? null,
                'observacao' => $dadosPagamento['observacao'] ?? null,
                'comprovante_pagamento' => $dadosPagamento['comprovante_pagamento'] ?? null,
                'usuario_id' => $dadosPagamento['usuario_id'] ?? (\Illuminate\Support\Facades\Auth::id() ?? 1),
                'taxa' => $dadosPagamento['taxa'] ?? 0,
                'valor_taxa' => $dadosPagamento['valor_taxa'] ?? 0,
                'referencia_externa' => $dadosPagamento['referencia_externa'] ?? null,
            ]);

            return $pagamento;
        });
    }

    /**
     * Obter dashboard de contas a pagar
     */
    public function getDashboard(int $empresaId): array
    {
        $hoje = now();
        $inicioMes = $hoje->copy()->startOfMonth();
        $fimMes = $hoje->copy()->endOfMonth();

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR);

        return [
            'total_aberto' => $query->clone()->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->sum('valor_saldo'),
            'total_pago_mes' => $query->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                ->whereBetween('data_ultimo_pagamento', [$inicioMes, $fimMes])
                ->sum('valor_pago'),
            'vencendo_hoje' => $query->clone()->whereDate('data_vencimento', $hoje)
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->sum('valor_saldo'),
            'em_atraso' => $query->clone()->where('data_vencimento', '<', $hoje->toDateString())
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->sum('valor_saldo'),
            'proximos_7_dias' => $query->clone()
                ->whereBetween('data_vencimento', [$hoje->toDateString(), $hoje->copy()->addDays(7)])
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->sum('valor_saldo'),
            'quantidade_pendente' => $query->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->count(),
            'quantidade_vencidas' => $query->clone()->where('data_vencimento', '<', $hoje->toDateString())
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->count(),
        ];
    }

    /**
     * Obter contas vencidas
     */
    public function getVencidas(int $empresaId): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->where('data_vencimento', '<', now()->toDateString())
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->with(['pessoa', 'contaGerencial'])
            ->orderBy('data_vencimento')
            ->get();
    }

    /**
     * Calcular projeção de fluxo de caixa
     */
    public function getProjecaoFluxoCaixa(int $empresaId, int $dias = 30): array
    {
        $hoje = now();
        $fim = $hoje->copy()->addDays($dias);

        $contas = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->whereBetween('data_vencimento', [$hoje->toDateString(), $fim->toDateString()])
            ->get()
            ->groupBy(function ($conta) {
                return Carbon::parse($conta->data_vencimento)->format('Y-m-d');
            });

        $projecao = [];
        $current = $hoje->copy();

        while ($current <= $fim) {
            $data = $current->format('Y-m-d');
            $contasDoDia = $contas->get($data, collect());

            $projecao[] = [
                'data' => $data,
                'data_formatada' => $current->format('d/m/Y'),
                'valor_total' => $contasDoDia->sum('valor_saldo'),
                'quantidade' => $contasDoDia->count(),
                'contas' => $contasDoDia->map(function ($conta) {
                    return [
                        'id' => $conta->id,
                        'descricao' => $conta->descricao,
                        'valor' => $conta->valor_saldo,
                        'pessoa_nome' => $conta->pessoa?->nome ?? 'Não informado',
                    ];
                }),
            ];

            $current->addDay();
        }

        return $projecao;
    }

    /**
     * Processar pagamentos em lote
     */
    public function pagarEmLote(array $lancamentoIds, array $dadosPagamento): array
    {
        return DB::transaction(function () use ($lancamentoIds, $dadosPagamento) {
            $resultados = [];

            foreach ($lancamentoIds as $lancamentoId) {
                try {
                    $pagamento = $this->pagar($lancamentoId, $dadosPagamento);
                    $resultados[] = [
                        'lancamento_id' => $lancamentoId,
                        'status' => 'success',
                        'pagamento_id' => $pagamento->id,
                    ];
                } catch (\Exception $e) {
                    $resultados[] = [
                        'lancamento_id' => $lancamentoId,
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $resultados;
        });
    }

    /**
     * Estornar pagamento
     */
    public function estornarPagamento(int $pagamentoId, string $motivo = ''): bool
    {
        return DB::transaction(function () use ($pagamentoId, $motivo) {
            $pagamento = Pagamento::findOrFail($pagamentoId);

            if ($pagamento->lancamento->natureza_financeira !== NaturezaFinanceiraEnum::PAGAR) {
                throw new \InvalidArgumentException('Este pagamento não é de uma conta a pagar');
            }

            return $pagamento->estornar($motivo);
        });
    }

    /**
     * Atualizar situação de todos os lançamentos em atraso
     */
    public function atualizarSituacoesVencidas(int $empresaId): int
    {
        $contasVencidas = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
            ->where('data_vencimento', '<', now()->toDateString())
            ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->get();

        foreach ($contasVencidas as $conta) {
            $conta->update(['situacao_financeira' => SituacaoFinanceiraEnum::VENCIDO]);
        }

        return $contasVencidas->count();
    }
}
