<?php

namespace App\Services\Financial;

use App\Models\Financial\ContaGerencial;
use App\Models\Financial\ClassificacaoDre;
use App\DTOs\Financial\ContaGerencialDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContaGerencialService
{
    public function criar(ContaGerencialDTO $dados): ContaGerencial
    {
        return DB::transaction(function () use ($dados) {
            $conta = ContaGerencial::create($dados->toArray());
            
            // Vincular naturezas se fornecidas
            if (!empty($dados->naturezas)) {
                foreach ($dados->naturezas as $naturezaId) {
                    $conta->vincularNatureza($naturezaId);
                }
            }
            
            return $conta->load(['classificacaoDre', 'tipo', 'naturezas']);
        });
    }

    public function atualizar(int $contaId, ContaGerencialDTO $dados): ContaGerencial
    {
        return DB::transaction(function () use ($contaId, $dados) {
            $conta = ContaGerencial::findOrFail($contaId);
            $conta->update($dados->toArray());
            
            // Atualizar naturezas
            if (isset($dados->naturezas)) {
                $conta->naturezas()->sync(
                    collect($dados->naturezas)->mapWithKeys(fn($id) => [
                        $id => ['empresa_id' => $conta->empresa_id]
                    ])
                );
            }
            
            return $conta->fresh(['classificacaoDre', 'tipo', 'naturezas']);
        });
    }

    public function obterArvoreContas(int $empresaId, bool $apenasAtivas = true): Collection
    {
        $query = ContaGerencial::where('empresa_id', $empresaId)
            ->with([
                'contasFilhas' => function ($q) use ($apenasAtivas) {
                    if ($apenasAtivas) $q->where('ativo', true);
                    $q->orderBy('ordem_exibicao');
                },
                'classificacaoDre',
                'tipo',
                'naturezas'
            ]);

        if ($apenasAtivas) {
            $query->where('ativo', true);
        }

        return $query->raizes()
                    ->orderBy('ordem_exibicao')
                    ->get();
    }

    public function obterContasPorClassificacao(int $classificacaoId, bool $apenasAtivas = true): Collection
    {
        $query = ContaGerencial::where('classificacao_dre_id', $classificacaoId)
            ->with(['contasFilhas', 'naturezas']);

        if ($apenasAtivas) {
            $query->where('ativo', true);
        }

        return $query->orderBy('ordem_exibicao')->get();
    }

    public function obterContasParaLancamento(int $empresaId): Collection
    {
        return ContaGerencial::where('empresa_id', $empresaId)
            ->ativas()
            ->permiteLancamento()
            ->with(['classificacaoDre', 'tipo'])
            ->orderBy('nome')
            ->get()
            ->map(function ($conta) {
                return [
                    'id' => $conta->id,
                    'codigo' => $conta->codigo_completo,
                    'nome' => $conta->nome_completo,
                    'classificacao' => $conta->classificacaoDre?->nome,
                    'tipo' => $conta->tipo?->nome,
                    'natureza' => $conta->natureza_conta?->label(),
                ];
            });
    }

    public function criarPlanoContasPadrao(int $empresaId): void
    {
        $estruturaPadrao = $this->getEstruturaPadrao();
        
        DB::transaction(function () use ($estruturaPadrao, $empresaId) {
            $this->criarContasRecursivamente($estruturaPadrao, $empresaId);
        });
    }

    public function calcularResumoFinanceiro(int $empresaId, ?\DateTime $dataInicio = null, ?\DateTime $dataFim = null): array
    {
        $receitas = ContaGerencial::where('empresa_id', $empresaId)
            ->whereHas('tipo', fn($q) => $q->where('value', 'receita'))
            ->ativas()
            ->get()
            ->sum(fn($conta) => $conta->calcularSaldo($dataInicio, $dataFim));

        $despesas = ContaGerencial::where('empresa_id', $empresaId)
            ->whereHas('tipo', fn($q) => $q->where('value', 'despesa'))
            ->ativas()
            ->get()
            ->sum(fn($conta) => $conta->calcularSaldo($dataInicio, $dataFim));

        return [
            'receitas' => abs($receitas),
            'despesas' => abs($despesas),
            'resultado' => $receitas - $despesas,
            'margem' => $receitas > 0 ? (($receitas - $despesas) / $receitas) * 100 : 0,
        ];
    }

    private function getEstruturaPadrao(): array
    {
        return [
            [
                'nome' => 'RECEITAS OPERACIONAIS',
                'codigo' => '3',
                'nivel' => 1,
                'tipo' => 'receita',
                'filhas' => [
                    ['nome' => 'Vendas de Produtos', 'codigo' => '3.1'],
                    ['nome' => 'Prestação de Serviços', 'codigo' => '3.2'],
                    ['nome' => 'Outras Receitas', 'codigo' => '3.3'],
                ]
            ],
            [
                'nome' => 'DESPESAS OPERACIONAIS',
                'codigo' => '4',
                'nivel' => 1,
                'tipo' => 'despesa',
                'filhas' => [
                    ['nome' => 'Custo dos Produtos Vendidos', 'codigo' => '4.1'],
                    ['nome' => 'Despesas Administrativas', 'codigo' => '4.2'],
                    ['nome' => 'Despesas Comerciais', 'codigo' => '4.3'],
                ]
            ]
        ];
    }

    private function criarContasRecursivamente(array $contas, int $empresaId, ?ContaGerencial $contaPai = null): void
    {
        foreach ($contas as $dadosConta) {
            $filhas = $dadosConta['filhas'] ?? [];
            unset($dadosConta['filhas']);

            $dadosConta['empresa_id'] = $empresaId;
            $dadosConta['conta_pai_id'] = $contaPai?->id;
            $dadosConta['nivel'] = $contaPai ? $contaPai->nivel + 1 : 1;
            $dadosConta['permite_lancamento'] = empty($filhas);
            $dadosConta['ativo'] = true;

            $conta = ContaGerencial::create($dadosConta);

            if (!empty($filhas)) {
                $this->criarContasRecursivamente($filhas, $empresaId, $conta);
            }
        }
    }
}