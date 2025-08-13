<?php

namespace App\Services\Financial;

use App\Models\Financial\ClassificacaoDre;
use App\DTOs\Financial\ClassificacaoDreDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ClassificacaoDreService
{
    public function criar(ClassificacaoDreDTO $dados): ClassificacaoDre
    {
        return ClassificacaoDre::create($dados->toArray());
    }

    public function atualizar(int $classificacaoId, ClassificacaoDreDTO $dados): ClassificacaoDre
    {
        $classificacao = ClassificacaoDre::findOrFail($classificacaoId);
        $classificacao->update($dados->toArray());
        
        return $classificacao->fresh();
    }

    public function obterArvoreClassificacoes(int $empresaId, bool $apenasAtivas = true): Collection
    {
        $query = ClassificacaoDre::where('empresa_id', $empresaId)
            ->with([
                'classificacoesFilhas' => function ($q) use ($apenasAtivas) {
                    if ($apenasAtivas) $q->where('ativo', true);
                    $q->orderBy('ordem_exibicao');
                },
                'tipo'
            ]);

        if ($apenasAtivas) {
            $query->where('ativo', true);
        }

        return $query->raizes()
                    ->orderBy('ordem_exibicao')
                    ->get();
    }

    public function obterPorTipo(int $empresaId, int $tipoId): Collection
    {
        return ClassificacaoDre::where('empresa_id', $empresaId)
            ->where('tipo_id', $tipoId)
            ->ativas()
            ->orderBy('ordem_exibicao')
            ->get();
    }

    public function gerarRelatorioDre(int $empresaId, ?\DateTime $dataInicio = null, ?\DateTime $dataFim = null): array
    {
        $classificacoes = $this->obterArvoreClassificacoes($empresaId);
        $relatorio = [];

        foreach ($classificacoes as $classificacao) {
            $total = $classificacao->calcularTotalLancamentos($dataInicio, $dataFim);
            
            $relatorio[] = [
                'id' => $classificacao->id,
                'nome' => $classificacao->nome,
                'tipo' => $classificacao->tipo->nome,
                'total' => $total,
                'filhas' => $this->processarFilhas($classificacao->classificacoesFilhas, $dataInicio, $dataFim)
            ];
        }

        return $relatorio;
    }

    private function processarFilhas(Collection $filhas, ?\DateTime $dataInicio, ?\DateTime $dataFim): array
    {
        $resultado = [];

        foreach ($filhas as $filha) {
            $total = $filha->calcularTotalLancamentos($dataInicio, $dataFim);
            
            $resultado[] = [
                'id' => $filha->id,
                'nome' => $filha->nome,
                'total' => $total,
                'filhas' => $this->processarFilhas($filha->classificacoesFilhas, $dataInicio, $dataFim)
            ];
        }

        return $resultado;
    }

    public function duplicarEstrutura(int $empresaOrigemId, int $empresaDestinoId): void
    {
        DB::transaction(function () use ($empresaOrigemId, $empresaDestinoId) {
            $classificacoes = ClassificacaoDre::where('empresa_id', $empresaOrigemId)
                ->ativas()
                ->raizes()
                ->with('classificacoesFilhas')
                ->get();

            foreach ($classificacoes as $classificacao) {
                $this->duplicarClassificacaoRecursivamente($classificacao, $empresaDestinoId);
            }
        });
    }

    private function duplicarClassificacaoRecursivamente(ClassificacaoDre $original, int $empresaDestinoId, ?int $paiId = null): void
    {
        $nova = ClassificacaoDre::create([
            'codigo' => $original->codigo,
            'nivel' => $original->nivel,
            'classificacao_pai_id' => $paiId,
            'nome' => $original->nome,
            'descricao' => $original->descricao,
            'tipo_id' => $original->tipo_id,
            'ativo' => true,
            'ordem_exibicao' => $original->ordem_exibicao,
            'empresa_id' => $empresaDestinoId,
        ]);

        foreach ($original->classificacoesFilhas as $filha) {
            $this->duplicarClassificacaoRecursivamente($filha, $empresaDestinoId, $nova->id);
        }
    }
}