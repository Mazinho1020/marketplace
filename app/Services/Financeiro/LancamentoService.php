<?php

namespace App\Services\Financeiro;

use App\Models\Financeiro\Lancamento;
use App\Models\Financeiro\Pagamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LancamentoService
{
    /**
     * Criar lançamento (pagar ou receber)
     */
    public function criar(array $dados): Lancamento
    {
        return DB::transaction(function () use ($dados) {
            $lancamento = Lancamento::create($dados);
            
            // Gerar número documento se não fornecido
            if (empty($lancamento->numero_documento)) {
                $prefixo = $lancamento->isContaPagar() ? 'PAG' : 'REC';
                $numero = str_pad($lancamento->id, 8, '0', STR_PAD_LEFT);
                $lancamento->numero_documento = "{$prefixo}-{$numero}";
                $lancamento->save();
            }
            
            // Criar itens se fornecidos
            if (isset($dados['itens']) && is_array($dados['itens'])) {
                foreach ($dados['itens'] as $item) {
                    $lancamento->itens()->create(array_merge($item, [
                        'empresa_id' => $lancamento->empresa_id,
                    ]));
                }
            }
            
            return $lancamento->fresh(['pagamentos', 'itens']);
        });
    }

    /**
     * Criar conta a pagar
     */
    public function criarContaPagar(array $dados): Lancamento
    {
        $dados['natureza_financeira'] = Lancamento::NATUREZA_SAIDA;
        $dados['categoria_operacao'] = $dados['categoria_operacao'] ?? 'compra';
        
        return $this->criar($dados);
    }

    /**
     * Criar conta a receber
     */
    public function criarContaReceber(array $dados): Lancamento
    {
        $dados['natureza_financeira'] = Lancamento::NATUREZA_ENTRADA;
        $dados['categoria_operacao'] = $dados['categoria_operacao'] ?? 'venda';
        
        return $this->criar($dados);
    }

    /**
     * Criar parcelado
     */
    public function criarParcelado(array $dados, int $numeroParcelas): Collection
    {
        return DB::transaction(function () use ($dados, $numeroParcelas) {
            $grupoParcelas = Str::uuid();
            $valorParcela = $dados['valor_bruto'] / $numeroParcelas;
            $dataVencimento = \Carbon\Carbon::parse($dados['data_vencimento']);
            $lancamentos = collect();
            
            for ($i = 1; $i <= $numeroParcelas; $i++) {
                $dadosParcela = array_merge($dados, [
                    'valor_bruto' => $valorParcela,
                    'data_vencimento' => $dataVencimento->copy(),
                    'e_parcelado' => true,
                    'parcela_atual' => $i,
                    'total_parcelas' => $numeroParcelas,
                    'grupo_parcelas' => $grupoParcelas,
                    'descricao' => $dados['descricao'] . " (Parcela {$i}/{$numeroParcelas})",
                ]);
                
                $lancamentos->push($this->criar($dadosParcela));
                $dataVencimento->addDays(30);
            }
            
            return $lancamentos;
        });
    }

    /**
     * Processar pagamento/recebimento - INTEGRADO COM TABELA PAGAMENTOS
     */
    public function processarPagamento(Lancamento $lancamento, array $dadosPagamento): Pagamento
    {
        return DB::transaction(function () use ($lancamento, $dadosPagamento) {
            return $lancamento->adicionarPagamento(
                $dadosPagamento['valor'],
                $dadosPagamento
            );
        });
    }

    /**
     * Listar contas a pagar
     */
    public function listarContasPagar(int $empresaId, array $filtros = [])
    {
        return $this->aplicarFiltros(
            Lancamento::empresa($empresaId)->saidas(),
            $filtros
        )->with(['contaGerencial', 'pagamentos'])->paginate(20);
    }

    /**
     * Listar contas a receber
     */
    public function listarContasReceber(int $empresaId, array $filtros = [])
    {
        return $this->aplicarFiltros(
            Lancamento::empresa($empresaId)->entradas(),
            $filtros
        )->with(['contaGerencial', 'pagamentos'])->paginate(20);
    }

    /**
     * Aplicar filtros
     */
    private function aplicarFiltros($query, array $filtros)
    {
        if (isset($filtros['situacao_financeira'])) {
            $query->where('situacao_financeira', $filtros['situacao_financeira']);
        }

        if (isset($filtros['data_inicio'])) {
            $query->where('data_vencimento', '>=', $filtros['data_inicio']);
        }

        if (isset($filtros['data_fim'])) {
            $query->where('data_vencimento', '<=', $filtros['data_fim']);
        }

        if (isset($filtros['pessoa_id'])) {
            $query->where('pessoa_id', $filtros['pessoa_id']);
        }

        return $query->orderBy('data_vencimento');
    }
}