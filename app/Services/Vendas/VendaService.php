<?php

namespace App\Services\Vendas;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

/**
 * Service para gerenciamento de vendas
 * 
 * Centraliza toda a lógica de negócio relacionada às vendas
 * seguindo o padrão definido em PADRONIZACAO_COMPLETA.md
 */
class VendaService
{
    /**
     * Cria uma nova venda
     */
    public function criar(array $dados): Venda
    {
        return DB::transaction(function () use ($dados) {
            // Validações básicas
            $this->validarDadosVenda($dados);
            
            // Preparar dados da venda
            $dadosVenda = $this->prepararDadosVenda($dados);
            
            // Criar venda
            $venda = Venda::create($dadosVenda);
            
            // Adicionar itens se fornecidos
            if (!empty($dados['itens'])) {
                foreach ($dados['itens'] as $itemData) {
                    $this->adicionarItem($venda, $itemData);
                }
            }
            
            return $venda->fresh(['itens', 'cliente', 'vendedor']);
        });
    }

    /**
     * Atualiza uma venda existente
     */
    public function atualizar(Venda $venda, array $dados): Venda
    {
        return DB::transaction(function () use ($venda, $dados) {
            // Só permite atualizar vendas abertas
            if ($venda->status !== 'aberta') {
                throw new Exception('Não é possível alterar uma venda que não está aberta.');
            }
            
            // Validações
            $this->validarDadosVenda($dados, $venda->id);
            
            // Preparar dados
            $dadosVenda = $this->prepararDadosVenda($dados);
            
            // Atualizar venda
            $venda->update($dadosVenda);
            
            // Atualizar itens se fornecidos
            if (isset($dados['itens'])) {
                $this->atualizarItens($venda, $dados['itens']);
            }
            
            return $venda->fresh(['itens', 'cliente', 'vendedor']);
        });
    }

    /**
     * Adiciona um item à venda
     */
    public function adicionarItem(Venda $venda, array $dadosItem): VendaItem
    {
        return DB::transaction(function () use ($venda, $dadosItem) {
            // Só permite adicionar itens em vendas abertas
            if ($venda->status !== 'aberta') {
                throw new Exception('Não é possível adicionar itens a uma venda que não está aberta.');
            }
            
            // Buscar produto
            $produto = Produto::find($dadosItem['produto_id']);
            if (!$produto) {
                throw new Exception('Produto não encontrado.');
            }
            
            // Verificar se tem estoque
            if ($produto->controla_estoque && $produto->estoque_atual < $dadosItem['quantidade']) {
                throw new Exception('Estoque insuficiente para o produto: ' . $produto->nome);
            }
            
            // Preparar dados do item
            $dadosItemCompleto = $this->prepararDadosItem($dadosItem, $produto, $venda);
            
            // Criar item
            $item = $venda->itens()->create($dadosItemCompleto);
            
            // Recalcular totais da venda
            $venda->calcularTotal();
            $venda->save();
            
            return $item;
        });
    }

    /**
     * Remove um item da venda
     */
    public function removerItem(Venda $venda, int $itemId): bool
    {
        return DB::transaction(function () use ($venda, $itemId) {
            if ($venda->status !== 'aberta') {
                throw new Exception('Não é possível remover itens de uma venda que não está aberta.');
            }
            
            $item = $venda->itens()->find($itemId);
            if (!$item) {
                throw new Exception('Item não encontrado na venda.');
            }
            
            $item->delete();
            
            // Recalcular totais
            $venda->calcularTotal();
            $venda->save();
            
            return true;
        });
    }

    /**
     * Finaliza uma venda
     */
    public function finalizar(Venda $venda, array $opcoes = []): Venda
    {
        return DB::transaction(function () use ($venda, $opcoes) {
            if ($venda->status !== 'aberta') {
                throw new Exception('Só é possível finalizar vendas que estão abertas.');
            }
            
            if ($venda->itens()->count() === 0) {
                throw new Exception('Não é possível finalizar uma venda sem itens.');
            }
            
            // Verificar estoque de todos os itens
            foreach ($venda->itensAtivos as $item) {
                if ($item->produto && $item->controla_estoque) {
                    if ($item->produto->estoque_atual < $item->quantidade) {
                        throw new Exception("Estoque insuficiente para o produto: {$item->nome_produto}");
                    }
                }
            }
            
            // Finalizar venda
            $venda->finalizar();
            
            // Processar pagamentos se fornecidos
            if (!empty($opcoes['pagamentos'])) {
                $this->processarPagamentos($venda, $opcoes['pagamentos']);
            }
            
            return $venda->fresh(['itens', 'cliente', 'vendedor', 'pagamentos']);
        });
    }

    /**
     * Cancela uma venda
     */
    public function cancelar(Venda $venda, string $motivo, int $usuarioId): Venda
    {
        return DB::transaction(function () use ($venda, $motivo, $usuarioId) {
            if ($venda->status === 'cancelada') {
                throw new Exception('Esta venda já está cancelada.');
            }
            
            $venda->cancelar($motivo, $usuarioId);
            
            return $venda->fresh(['itens', 'cliente', 'vendedor']);
        });
    }

    /**
     * Busca vendas com filtros
     */
    public function buscar(array $filtros = [], int $perPage = 15): Collection
    {
        $query = Venda::query()->with(['cliente', 'vendedor', 'itens']);
        
        // Filtro por empresa
        if (!empty($filtros['empresa_id'])) {
            $query->porEmpresa($filtros['empresa_id']);
        }
        
        // Filtro por status
        if (!empty($filtros['status'])) {
            $query->comStatus($filtros['status']);
        }
        
        // Filtro por cliente
        if (!empty($filtros['cliente_id'])) {
            $query->porCliente($filtros['cliente_id']);
        }
        
        // Filtro por vendedor
        if (!empty($filtros['vendedor_id'])) {
            $query->porVendedor($filtros['vendedor_id']);
        }
        
        // Filtro por período
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $inicio = Carbon::parse($filtros['data_inicio'])->startOfDay();
            $fim = Carbon::parse($filtros['data_fim'])->endOfDay();
            $query->porPeriodo($inicio, $fim);
        }
        
        // Filtro por tipo de venda
        if (!empty($filtros['tipo_venda'])) {
            $query->porTipo($filtros['tipo_venda']);
        }
        
        // Filtro por número da venda
        if (!empty($filtros['numero_venda'])) {
            $query->where('numero_venda', 'like', '%' . $filtros['numero_venda'] . '%');
        }
        
        // Ordenação
        $query->orderBy('data_venda', 'desc');
        
        return $query->paginate($perPage);
    }

    /**
     * Obtém estatísticas de vendas
     */
    public function obterEstatisticas(int $empresaId, array $filtros = []): array
    {
        $query = Venda::porEmpresa($empresaId)->finalizadas();
        
        // Aplicar filtros de período se fornecidos
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $inicio = Carbon::parse($filtros['data_inicio'])->startOfDay();
            $fim = Carbon::parse($filtros['data_fim'])->endOfDay();
            $query->porPeriodo($inicio, $fim);
        } else {
            // Padrão: mês atual
            $query->mesAtual();
        }
        
        $vendas = $query->get();
        
        return [
            'total_vendas' => $vendas->count(),
            'valor_total' => $vendas->sum('valor_total'),
            'valor_liquido' => $vendas->sum('valor_liquido_vendedor'),
            'ticket_medio' => $vendas->count() > 0 ? $vendas->avg('valor_total') : 0,
            'comissao_marketplace' => $vendas->sum('valor_comissao_marketplace'),
            'total_itens' => $vendas->sum(function ($venda) {
                return $venda->itensAtivos->sum('quantidade');
            }),
            'produtos_mais_vendidos' => $this->obterProdutosMaisVendidos($empresaId, $filtros),
            'vendas_por_dia' => $this->obterVendasPorDia($empresaId, $filtros),
        ];
    }

    /**
     * Obtém produtos mais vendidos
     */
    public function obterProdutosMaisVendidos(int $empresaId, array $filtros = [], int $limite = 10): Collection
    {
        $query = VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
            ->where('vendas.empresa_id', $empresaId)
            ->where('vendas.status', 'finalizada')
            ->where('venda_itens.status_item', 'ativo');
        
        // Aplicar filtros de período
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $inicio = Carbon::parse($filtros['data_inicio'])->startOfDay();
            $fim = Carbon::parse($filtros['data_fim'])->endOfDay();
            $query->whereBetween('vendas.data_venda', [$inicio, $fim]);
        }
        
        return $query->select([
                'venda_itens.produto_id',
                'venda_itens.nome_produto',
                DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                DB::raw('SUM(venda_itens.valor_total_item) as valor_total'),
                DB::raw('COUNT(DISTINCT venda_itens.venda_id) as numero_vendas')
            ])
            ->groupBy('venda_itens.produto_id', 'venda_itens.nome_produto')
            ->orderBy('total_vendido', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtém vendas por dia para gráficos
     */
    public function obterVendasPorDia(int $empresaId, array $filtros = []): Collection
    {
        $query = Venda::porEmpresa($empresaId)->finalizadas();
        
        // Período padrão: últimos 30 dias
        $inicio = Carbon::now()->subDays(30)->startOfDay();
        $fim = Carbon::now()->endOfDay();
        
        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $inicio = Carbon::parse($filtros['data_inicio'])->startOfDay();
            $fim = Carbon::parse($filtros['data_fim'])->endOfDay();
        }
        
        return $query->whereBetween('data_venda', [$inicio, $fim])
            ->select([
                DB::raw('DATE(data_venda) as data'),
                DB::raw('COUNT(*) as total_vendas'),
                DB::raw('SUM(valor_total) as valor_total'),
                DB::raw('SUM(valor_liquido_vendedor) as valor_liquido')
            ])
            ->groupBy(DB::raw('DATE(data_venda)'))
            ->orderBy('data')
            ->get();
    }

    /**
     * Calcula comissão do marketplace
     */
    public function calcularComissao(Venda $venda, float $aliquota): float
    {
        return ($venda->valor_total * $aliquota) / 100;
    }

    // =========================================
    // MÉTODOS PRIVADOS
    // =========================================

    /**
     * Valida os dados da venda
     */
    private function validarDadosVenda(array $dados, ?int $vendaId = null): void
    {
        // Validar empresa
        if (empty($dados['empresa_id'])) {
            throw new Exception('Empresa é obrigatória.');
        }
        
        $empresa = Empresa::find($dados['empresa_id']);
        if (!$empresa) {
            throw new Exception('Empresa não encontrada.');
        }
        
        // Validar cliente se fornecido
        if (!empty($dados['cliente_id'])) {
            $cliente = Cliente::find($dados['cliente_id']);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado.');
            }
        }
        
        // Validar valores
        if (isset($dados['valor_total']) && $dados['valor_total'] < 0) {
            throw new Exception('Valor total não pode ser negativo.');
        }
    }

    /**
     * Prepara os dados da venda
     */
    private function prepararDadosVenda(array $dados): array
    {
        $dadosVenda = [
            'empresa_id' => $dados['empresa_id'],
            'cliente_id' => $dados['cliente_id'] ?? null,
            'vendedor_id' => $dados['vendedor_id'] ?? null,
            'tipo_venda' => $dados['tipo_venda'] ?? 'balcao',
            'origem_venda' => $dados['origem_venda'] ?? 'pdv',
            'observacoes' => $dados['observacoes'] ?? null,
            'observacoes_internas' => $dados['observacoes_internas'] ?? null,
            'tipo_entrega' => $dados['tipo_entrega'] ?? null,
            'dados_entrega' => $dados['dados_entrega'] ?? null,
            'aliquota_comissao' => $dados['aliquota_comissao'] ?? 0,
        ];
        
        // Campos opcionais
        foreach (['valor_desconto', 'valor_acrescimo', 'valor_frete', 'valor_taxa_servico'] as $campo) {
            if (isset($dados[$campo])) {
                $dadosVenda[$campo] = $dados[$campo];
            }
        }
        
        return $dadosVenda;
    }

    /**
     * Prepara os dados do item
     */
    private function prepararDadosItem(array $dadosItem, Produto $produto, Venda $venda): array
    {
        $valorUnitario = $dadosItem['valor_unitario'] ?? $produto->preco_venda;
        $quantidade = $dadosItem['quantidade'] ?? 1;
        
        return [
            'empresa_id' => $venda->empresa_id,
            'produto_id' => $produto->id,
            'produto_variacao_id' => $dadosItem['produto_variacao_id'] ?? null,
            'codigo_produto' => $produto->sku ?? $produto->codigo_sistema,
            'nome_produto' => $produto->nome,
            'descricao_produto' => $produto->descricao_curta,
            'unidade_medida' => $produto->unidade_medida ?? 'UN',
            'quantidade' => $quantidade,
            'valor_unitario' => $valorUnitario,
            'valor_unitario_original' => $valorUnitario,
            'valor_total_item' => $quantidade * $valorUnitario,
            'valor_custo_unitario' => $produto->preco_compra,
            'valor_custo_total' => $quantidade * ($produto->preco_compra ?? 0),
            'controla_estoque' => $produto->controla_estoque,
            'ncm' => $produto->ncm,
            'cfop' => $produto->cfop,
            'aliquota_icms' => $produto->aliquota_icms ?? 0,
            'aliquota_ipi' => $produto->aliquota_ipi ?? 0,
            'aliquota_pis' => $produto->aliquota_pis ?? 0,
            'aliquota_cofins' => $produto->aliquota_cofins ?? 0,
            'observacoes' => $dadosItem['observacoes'] ?? null,
            'ordem_item' => $venda->itens()->max('ordem_item') + 1,
        ];
    }

    /**
     * Atualiza os itens da venda
     */
    private function atualizarItens(Venda $venda, array $itens): void
    {
        // Remover itens não incluídos na atualização
        $idsItens = collect($itens)->pluck('id')->filter();
        $venda->itens()->whereNotIn('id', $idsItens)->delete();
        
        // Atualizar ou criar itens
        foreach ($itens as $itemData) {
            if (!empty($itemData['id'])) {
                // Atualizar item existente
                $item = $venda->itens()->find($itemData['id']);
                if ($item) {
                    $item->update([
                        'quantidade' => $itemData['quantidade'],
                        'valor_unitario' => $itemData['valor_unitario'],
                        'observacoes' => $itemData['observacoes'] ?? null,
                    ]);
                    $item->calcularTotalItem();
                    $item->save();
                }
            } else {
                // Criar novo item
                $this->adicionarItem($venda, $itemData);
            }
        }
    }

    /**
     * Processa os pagamentos da venda
     */
    private function processarPagamentos(Venda $venda, array $pagamentos): void
    {
        // Esta funcionalidade será implementada quando integrarmos com o sistema de pagamentos
        // Por enquanto, apenas marcamos como processado
    }
}