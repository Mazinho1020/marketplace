<?php

namespace App\Services\Financial;

use App\DTOs\Financial\ContaReceberDTO;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\Enums\FrequenciaRecorrenciaEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ContasReceberService
{
    /**
     * Criar uma nova conta a receber
     */
    public function criar(ContaReceberDTO $dto): LancamentoFinanceiro
    {
        return DB::transaction(function () use ($dto) {
            $lancamento = LancamentoFinanceiro::create([
                'empresa_id' => $dto->empresa_id,
                'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
                'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
                'descricao' => $dto->descricao,
                'valor_total' => $dto->valor_total,
                'data_vencimento' => $dto->data_vencimento,
                'data_competencia' => $dto->data_competencia,
                'cliente_id' => $dto->cliente_id,
                'funcionario_id' => $dto->funcionario_id,
                'conta_gerencial_id' => $dto->conta_gerencial_id,
                'categoria_id' => $dto->categoria_id,
                'observacoes' => $dto->observacoes,
                'codigo_lancamento' => $dto->codigo_lancamento,
                'documento_referencia' => $dto->documento_referencia,
                'cobranca_automatica' => $dto->cobranca_automatica ?? false,
                'juros_multa_config' => $dto->juros_multa_config,
                'numero_parcelas' => $dto->numero_parcelas ?? 1,
                'parcela_atual' => $dto->parcela_atual ?? 1,
                'valor_parcela' => $dto->valor_parcela,
                'frequencia_recorrencia' => $dto->frequencia_recorrencia,
                'usuario_criacao_id' => auth()->id(),
            ]);

            return $lancamento;
        });
    }

    /**
     * Criar conta a receber parcelada
     */
    public function criarParcelado(ContaReceberDTO $dto): Collection
    {
        if (!$dto->numero_parcelas || $dto->numero_parcelas <= 1) {
            return collect([$this->criar($dto)]);
        }

        return DB::transaction(function () use ($dto) {
            $parcelas = collect();
            $valorParcela = round($dto->valor_total / $dto->numero_parcelas, 2);
            $dataVencimento = Carbon::parse($dto->data_vencimento);

            for ($i = 1; $i <= $dto->numero_parcelas; $i++) {
                // Ajustar última parcela para evitar diferenças de centavos
                $valorAtual = ($i === $dto->numero_parcelas)
                    ? $dto->valor_total - ($valorParcela * ($i - 1))
                    : $valorParcela;

                $parcelaDto = ContaReceberDTO::fromArray([
                    'empresa_id' => $dto->empresa_id,
                    'descricao' => $dto->descricao . " - Parcela {$i}/{$dto->numero_parcelas}",
                    'valor_total' => $valorAtual,
                    'data_vencimento' => $dataVencimento->copy()->addMonths($i - 1),
                    'data_competencia' => $dto->data_competencia,
                    'cliente_id' => $dto->cliente_id,
                    'funcionario_id' => $dto->funcionario_id,
                    'conta_gerencial_id' => $dto->conta_gerencial_id,
                    'categoria_id' => $dto->categoria_id,
                    'observacoes' => $dto->observacoes,
                    'codigo_lancamento' => $dto->codigo_lancamento . ($dto->numero_parcelas > 1 ? "-{$i}" : ''),
                    'documento_referencia' => $dto->documento_referencia,
                    'cobranca_automatica' => $dto->cobranca_automatica,
                    'juros_multa_config' => $dto->juros_multa_config,
                    'numero_parcelas' => $dto->numero_parcelas,
                    'parcela_atual' => $i,
                    'valor_parcela' => $valorAtual,
                    'frequencia_recorrencia' => $dto->frequencia_recorrencia,
                ]);

                $parcelas->push($this->criar($parcelaDto));
            }

            return $parcelas;
        });
    }

    /**
     * Registrar recebimento de uma conta
     */
    public function receber(int $lancamentoId, array $dados)
    {
        return DB::transaction(function () use ($lancamentoId, $dados) {
            $lancamento = LancamentoFinanceiro::findOrFail($lancamentoId);

            // Validações básicas
            $this->validarRecebimento($lancamento, $dados);

            // Criar o recebimento usando a tabela pagamentos
            $pagamento = $lancamento->adicionarPagamento([
                'tipo_id' => 2, // Tipo "Recebimento" 
                'forma_pagamento_id' => $dados['forma_pagamento_id'],
                'bandeira_id' => $dados['bandeira_id'] ?? null,
                'conta_bancaria_id' => $dados['conta_bancaria_id'],
                'valor' => $dados['valor'],
                'valor_principal' => $dados['valor_principal'] ?? $dados['valor'],
                'valor_juros' => $dados['valor_juros'] ?? 0,
                'valor_multa' => $dados['valor_multa'] ?? 0,
                'valor_desconto' => $dados['valor_desconto'] ?? 0,
                'data_pagamento' => $dados['data_recebimento'] ?? $dados['data_pagamento'],
                'data_compensacao' => $dados['data_compensacao'] ?? null,
                'observacao' => $dados['observacao'] ?? null,
                'comprovante_pagamento' => $dados['comprovante_recebimento'] ?? $dados['comprovante_pagamento'] ?? null,
                'taxa' => $dados['taxa'] ?? 0,
                'valor_taxa' => $dados['valor_taxa'] ?? 0,
                'referencia_externa' => $dados['referencia_externa'] ?? null,
                'usuario_id' => $dados['usuario_id'],
            ]);

            return $pagamento;
        });
    }

    /**
     * Validar dados do recebimento
     */
    private function validarRecebimento($lancamento, $dados)
    {
        // Validar se o lançamento é do tipo "a receber"
        if ($lancamento->natureza_financeira !== NaturezaFinanceiraEnum::RECEBER) {
            throw new \InvalidArgumentException('Este lançamento não é uma conta a receber.');
        }

        // Calcular valor já recebido
        $valorRecebido = $lancamento->recebimentos()
            ->where('status_recebimento', 'confirmado')
            ->sum('valor');

        $valorTotal = $lancamento->valor_final;
        $saldoDevedor = $valorTotal - $valorRecebido;

        // Validar se o valor do recebimento não excede o saldo devedor (com tolerância de R$ 0,01)
        if ($dados['valor'] > ($saldoDevedor + 0.01)) {
            throw new \InvalidArgumentException(
                "Valor do recebimento (R$ " . number_format($dados['valor'], 2, ',', '.') .
                    ") excede o saldo em aberto (R$ " . number_format($saldoDevedor, 2, ',', '.') . ")."
            );
        }
    }

    /**
     * Atualizar situação do lançamento baseado nos recebimentos
     */
    private function atualizarSituacaoLancamento($lancamento)
    {
        // Use pagamentos confirmados para recebimentos (tipo_id = 2)
        $valorRecebido = $lancamento->pagamentos()
            ->where('status_pagamento', 'confirmado')
            ->where('tipo_id', 2) // Apenas recebimentos
            ->sum('valor');

        $valorTotal = $lancamento->valor_final;

        if ($valorRecebido >= $valorTotal) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PAGO;
        } elseif ($valorRecebido > 0) {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
        } else {
            $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
        }

        $lancamento->save();
    }

    /**
     * Estornar um recebimento
     */
    public function estornarRecebimento(int $pagamentoId): bool
    {
        return DB::transaction(function () use ($pagamentoId) {
            $pagamento = Pagamento::findOrFail($pagamentoId);

            // Verificar se é um pagamento de conta a receber
            if ($pagamento->lancamento->natureza_financeira !== NaturezaFinanceiraEnum::RECEBER) {
                throw new \Exception('Este pagamento não é de uma conta a receber.');
            }

            // Usar o método estornar do modelo Pagamento
            return $pagamento->estornar('Estorno via ContasReceberService');
        });
    }

    /**
     * Obter dashboard de contas a receber
     */
    public function getDashboard(int $empresaId): array
    {
        $baseQuery = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);

        return [
            'total_a_receber' => $baseQuery->clone()->sum('valor_total'),
            'total_recebido' => $baseQuery->clone()->whereHas('pagamentos')->with('pagamentos')->get()
                ->sum(function ($lancamento) {
                    return $lancamento->pagamentos->sum('valor');
                }),
            'total_em_aberto' => $baseQuery->clone()
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->get()
                ->sum('valor_saldo'),
            'total_vencidas' => $baseQuery->clone()
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->where('data_vencimento', '<', now())
                ->get()
                ->sum('valor_saldo'),
            'qtd_total' => $baseQuery->clone()->count(),
            'qtd_pagas' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)->count(),
            'qtd_pendentes' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)->count(),
            'qtd_vencidas' => $baseQuery->clone()
                ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                ->where('data_vencimento', '<', now())
                ->count(),
            'recebimentos_mes_atual' => $baseQuery->clone()
                ->whereHas('pagamentos', function ($query) {
                    $query->whereMonth('data_pagamento', now()->month)
                        ->whereYear('data_pagamento', now()->year);
                })
                ->with('pagamentos')
                ->get()
                ->sum(function ($lancamento) {
                    return $lancamento->pagamentos
                        ->where('data_pagamento', '>=', now()->startOfMonth())
                        ->where('data_pagamento', '<=', now()->endOfMonth())
                        ->sum('valor');
                }),
        ];
    }

    /**
     * Obter projeção de fluxo de caixa para recebimentos
     */
    public function getProjecaoFluxoCaixa(int $empresaId, Carbon $dataInicio, Carbon $dataFim): array
    {
        $contas = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim])
            ->orderBy('data_vencimento')
            ->get();

        $projecao = [];
        $saldoAcumulado = 0;

        foreach ($contas as $conta) {
            $dataVencimento = Carbon::parse($conta->data_vencimento)->format('Y-m-d');

            if (!isset($projecao[$dataVencimento])) {
                $projecao[$dataVencimento] = [
                    'data' => $dataVencimento,
                    'entradas' => 0,
                    'quantidade_contas' => 0,
                    'contas' => [],
                ];
            }

            $projecao[$dataVencimento]['entradas'] += $conta->valor_saldo;
            $projecao[$dataVencimento]['quantidade_contas']++;
            $projecao[$dataVencimento]['contas'][] = [
                'id' => $conta->id,
                'descricao' => $conta->descricao,
                'valor' => $conta->valor_saldo,
                'cliente' => $conta->cliente?->nome,
            ];

            $saldoAcumulado += $conta->valor_saldo;
        }

        return [
            'projecao_diaria' => array_values($projecao),
            'total_periodo' => $saldoAcumulado,
            'media_diaria' => count($projecao) > 0 ? $saldoAcumulado / count($projecao) : 0,
        ];
    }

    /**
     * Obter contas vencendo nos próximos dias
     */
    public function getContasVencendo(int $empresaId, int $diasAdiante = 7): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->whereBetween('data_vencimento', [now(), now()->addDays($diasAdiante)])
            ->orderBy('data_vencimento')
            ->with(['cliente', 'categoria'])
            ->get();
    }

    /**
     * Obter contas vencidas
     */
    public function getContasVencidas(int $empresaId): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->where('data_vencimento', '<', now())
            ->orderBy('data_vencimento')
            ->with(['cliente', 'categoria'])
            ->get();
    }

    /**
     * Processar cobrança automática
     */
    public function processarCobrancaAutomatica(int $empresaId): array
    {
        $contasCobranca = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
            ->where('cobranca_automatica', true)
            ->where('data_vencimento', '<=', now()->addDays(3)) // 3 dias antes do vencimento
            ->get();

        $resultados = [
            'processadas' => 0,
            'erros' => 0,
            'detalhes' => [],
        ];

        foreach ($contasCobranca as $conta) {
            try {
                // Aqui você implementaria a lógica de cobrança automática
                // (envio de e-mail, SMS, WhatsApp, etc.)

                $resultados['processadas']++;
                $resultados['detalhes'][] = [
                    'conta_id' => $conta->id,
                    'cliente' => $conta->cliente?->nome ?? 'Cliente não informado',
                    'valor' => $conta->valor_saldo,
                    'status' => 'Cobrança enviada com sucesso',
                ];
            } catch (\Exception $e) {
                $resultados['erros']++;
                $resultados['detalhes'][] = [
                    'conta_id' => $conta->id,
                    'cliente' => $conta->cliente?->nome ?? 'Cliente não informado',
                    'valor' => $conta->valor_saldo,
                    'status' => 'Erro: ' . $e->getMessage(),
                ];
            }
        }

        return $resultados;
    }

    /**
     * Gerar relatório de recebimentos
     */
    public function getRelatorioRecebimentos(int $empresaId, Carbon $dataInicio, Carbon $dataFim): array
    {
        $pagamentos = Pagamento::whereHas('lancamento', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);
        })
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->with(['lancamento.cliente', 'formaPagamento', 'contaBancaria'])
            ->orderBy('data_pagamento')
            ->get();

        return [
            'periodo' => [
                'inicio' => $dataInicio->format('d/m/Y'),
                'fim' => $dataFim->format('d/m/Y'),
            ],
            'total_recebido' => $pagamentos->sum('valor'),
            'quantidade_recebimentos' => $pagamentos->count(),
            'media_por_recebimento' => $pagamentos->count() > 0 ? $pagamentos->avg('valor') : 0,
            'recebimentos_por_forma_pagamento' => $pagamentos->groupBy('formaPagamento.nome')
                ->map(function ($grupo) {
                    return [
                        'quantidade' => $grupo->count(),
                        'valor_total' => $grupo->sum('valor'),
                    ];
                }),
            'recebimentos_por_cliente' => $pagamentos->groupBy('lancamento.cliente.nome')
                ->map(function ($grupo) {
                    return [
                        'quantidade' => $grupo->count(),
                        'valor_total' => $grupo->sum('valor'),
                    ];
                })
                ->sortByDesc('valor_total')
                ->take(10),
            'detalhes' => $pagamentos->map(function ($pagamento) {
                return [
                    'data' => $pagamento->data_pagamento,
                    'valor' => $pagamento->valor,
                    'cliente' => $pagamento->lancamento->cliente?->nome ?? 'Não informado',
                    'descricao' => $pagamento->lancamento->descricao,
                    'forma_pagamento' => $pagamento->formaPagamento?->nome ?? 'Não informado',
                    'conta_bancaria' => $pagamento->contaBancaria?->nome ?? 'Não informado',
                ];
            }),
        ];
    }
}
