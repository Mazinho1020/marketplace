<?php

namespace App\Services\Financial;

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;
use App\DTOs\Financial\ContaReceberDTO;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContasReceberService
{
    public function criar(ContaReceberDTO $dados): LancamentoFinanceiro
    {
        return DB::transaction(function () use ($dados) {
            $lancamento = LancamentoFinanceiro::create([
                'empresa_id' => $dados->empresaId,
                'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
                'pessoa_id' => $dados->pessoaId,
                'pessoa_tipo' => $dados->pessoaTipo,
                'conta_gerencial_id' => $dados->contaGerencialId,
                'numero_documento' => $dados->numeroDocumento,
                'descricao' => $dados->descricao,
                'observacoes' => $dados->observacoes,
                'valor' => $dados->valor,
                'valor_original' => $dados->valor,
                'valor_final' => $dados->valor,
                'data_emissao' => $dados->dataEmissao ?? now(),
                'data_competencia' => $dados->dataCompetencia ?? now(),
                'data_vencimento' => $dados->dataVencimento,
                'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
                'usuario_id' => auth()->id(),
                'config_alertas' => $dados->configAlertas,
                'juros_multa_config' => $dados->jurosMultaConfig,
            ]);

            return $lancamento->fresh();
        });
    }

    public function criarParcelado(ContaReceberDTO $dados, int $parcelas): Collection
    {
        return DB::transaction(function () use ($dados, $parcelas) {
            $grupoParcelas = Str::uuid();
            $valorParcela = round($dados->valor / $parcelas, 2);
            $valorUltimaParcela = $dados->valor - ($valorParcela * ($parcelas - 1));
            
            $lancamentos = collect();
            
            for ($i = 1; $i <= $parcelas; $i++) {
                $dataVencimento = Carbon::parse($dados->dataVencimento)->addMonths($i - 1);
                $valor = ($i === $parcelas) ? $valorUltimaParcela : $valorParcela;
                
                $lancamento = LancamentoFinanceiro::create([
                    'empresa_id' => $dados->empresaId,
                    'natureza_financeira' => NaturezaFinanceiraEnum::RECEBER,
                    'pessoa_id' => $dados->pessoaId,
                    'pessoa_tipo' => $dados->pessoaTipo,
                    'conta_gerencial_id' => $dados->contaGerencialId,
                    'numero_documento' => $dados->numeroDocumento,
                    'descricao' => $dados->descricao . " (Parcela {$i}/{$parcelas})",
                    'observacoes' => $dados->observacoes,
                    'valor' => $valor,
                    'valor_original' => $valor,
                    'valor_final' => $valor,
                    'data_emissao' => $dados->dataEmissao ?? now(),
                    'data_competencia' => $dados->dataCompetencia ?? now(),
                    'data_vencimento' => $dataVencimento,
                    'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
                    'parcela_atual' => $i,
                    'total_parcelas' => $parcelas,
                    'grupo_parcelas' => $grupoParcelas,
                    'usuario_id' => auth()->id(),
                    'config_alertas' => $dados->configAlertas,
                    'juros_multa_config' => $dados->jurosMultaConfig,
                ]);
                
                $lancamentos->push($lancamento);
            }
            
            return $lancamentos;
        });
    }

    public function receber(int $id, float $valor, array $dadosRecebimento): bool
    {
        return DB::transaction(function () use ($id, $valor, $dadosRecebimento) {
            $lancamento = LancamentoFinanceiro::findOrFail($id);
            
            // Verificar se é conta a receber
            if (!$lancamento->isContaReceber()) {
                throw new \Exception('Este lançamento não é uma conta a receber');
            }
            
            // Verificar se já não está pago
            if ($lancamento->isPaga()) {
                throw new \Exception('Esta conta já está recebida');
            }
            
            // Verificar se valor não excede o saldo
            $saldo = $lancamento->valor_saldo;
            if ($valor > $saldo) {
                throw new \Exception('Valor do recebimento não pode ser maior que o saldo devedor');
            }
            
            // Criar recebimento
            $recebimento = $lancamento->adicionarPagamento(array_merge($dadosRecebimento, [
                'valor' => $valor,
                'data_pagamento' => $dadosRecebimento['data_pagamento'] ?? now(),
                'status' => 'confirmado',
            ]));
            
            return $recebimento->exists;
        });
    }

    public function getDashboard(int $empresaId): array
    {
        $baseQuery = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);

        return [
            'total_pendente' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)->sum('valor_final'),
            'total_vencido' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->sum('valor_final'),
            'total_recebido_mes' => $baseQuery->clone()
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                ->whereMonth('data_pagamento', now()->month)
                ->whereYear('data_pagamento', now()->year)
                ->sum('valor_final'),
            'quantidade_pendente' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)->count(),
            'quantidade_vencida' => $baseQuery->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->count(),
            'proximos_vencimentos' => $this->getProximosVencimentos($empresaId, 7),
            'maiores_valores' => $this->getMaioresValoresPendentes($empresaId, 5),
            'inadimplencia' => $this->getRelatorioInadimplencia($empresaId),
        ];
    }

    public function getVencidas(int $empresaId): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
            ->with(['pessoa', 'contaGerencial', 'pagamentos'])
            ->orderBy('data_vencimento')
            ->get();
    }

    public function getProximosVencimentos(int $empresaId, int $dias = 30): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->whereBetween('data_vencimento', [now(), now()->addDays($dias)])
            ->with(['pessoa', 'contaGerencial'])
            ->orderBy('data_vencimento')
            ->get();
    }

    public function getMaioresValoresPendentes(int $empresaId, int $limit = 10): Collection
    {
        return LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->with(['pessoa', 'contaGerencial'])
            ->orderByDesc('valor_final')
            ->limit($limit)
            ->get();
    }

    public function getRelatorioInadimplencia(int $empresaId): array
    {
        $vencidas = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
            ->get();

        $inadimplentes = $vencidas->groupBy('pessoa_id')->map(function ($contas, $pessoaId) {
            $totalDevido = $contas->sum('valor_final');
            $diasMaximoAtraso = $contas->max('dias_atraso');
            $quantidadeContas = $contas->count();
            
            return [
                'pessoa_id' => $pessoaId,
                'pessoa' => $contas->first()->pessoa,
                'total_devido' => $totalDevido,
                'quantidade_contas' => $quantidadeContas,
                'dias_maximo_atraso' => $diasMaximoAtraso,
                'contas' => $contas
            ];
        });

        return [
            'total_inadimplencia' => $vencidas->sum('valor_final'),
            'quantidade_inadimplentes' => $inadimplentes->count(),
            'ticket_medio' => $inadimplentes->count() > 0 ? $vencidas->sum('valor_final') / $inadimplentes->count() : 0,
            'inadimplentes' => $inadimplentes->sortByDesc('total_devido')->values(),
            'por_faixa_atraso' => [
                '1_a_30' => $vencidas->filter(fn($c) => $c->dias_atraso <= 30)->sum('valor_final'),
                '31_a_60' => $vencidas->filter(fn($c) => $c->dias_atraso > 30 && $c->dias_atraso <= 60)->sum('valor_final'),
                '61_a_90' => $vencidas->filter(fn($c) => $c->dias_atraso > 60 && $c->dias_atraso <= 90)->sum('valor_final'),
                'acima_90' => $vencidas->filter(fn($c) => $c->dias_atraso > 90)->sum('valor_final'),
            ]
        ];
    }

    public function processarVencimentos(): int
    {
        $contasVencidas = LancamentoFinanceiro::where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->where('data_vencimento', '<', now()->startOfDay())
            ->get();

        foreach ($contasVencidas as $conta) {
            $conta->situacao_financeira = SituacaoFinanceiraEnum::VENCIDO;
            $conta->save();
        }

        return $contasVencidas->count();
    }

    public function buscar(int $empresaId, array $filtros = []): Collection
    {
        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
            ->with(['pessoa', 'contaGerencial', 'pagamentos']);

        if (isset($filtros['situacao'])) {
            $query->where('situacao_financeira', $filtros['situacao']);
        }

        if (isset($filtros['pessoa_id'])) {
            $query->where('pessoa_id', $filtros['pessoa_id']);
        }

        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $query->whereBetween('data_vencimento', [$filtros['data_inicio'], $filtros['data_fim']]);
        }

        if (isset($filtros['valor_min'])) {
            $query->where('valor_final', '>=', $filtros['valor_min']);
        }

        if (isset($filtros['valor_max'])) {
            $query->where('valor_final', '<=', $filtros['valor_max']);
        }

        return $query->orderBy('data_vencimento')->get();
    }
}