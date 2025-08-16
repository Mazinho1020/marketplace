<?php

namespace App\Services\Vendas;

use App\Models\Venda;
use App\Models\VendaItem;
use App\Models\VendaPagamento;
use App\Models\Produto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VendaService
{
    /**
     * Criar uma nova venda com itens e pagamentos
     */
    public function criarVenda(array $dados): Venda
    {
        return DB::transaction(function () use ($dados) {
            // Criar a venda principal
            $venda = Venda::create([
                'empresa_id' => $dados['empresa_id'],
                'usuario_id' => $dados['usuario_id'],
                'cliente_id' => $dados['cliente_id'] ?? null,
                'caixa_id' => $dados['caixa_id'] ?? null,
                'mesa_id' => $dados['mesa_id'] ?? null,
                'tipo_venda' => $dados['tipo_venda'],
                'origem' => $dados['origem'],
                'desconto_percentual' => $dados['desconto_percentual'] ?? 0,
                'desconto_valor' => $dados['desconto_valor'] ?? 0,
                'acrescimo_percentual' => $dados['acrescimo_percentual'] ?? 0,
                'acrescimo_valor' => $dados['acrescimo_valor'] ?? 0,
                'observacoes' => $dados['observacoes'] ?? null,
                'observacoes_internas' => $dados['observacoes_internas'] ?? null,
                'cupom_desconto' => $dados['cupom_desconto'] ?? null,
                'data_venda' => $dados['data_venda'] ?? now(),
                'data_entrega_prevista' => $dados['data_entrega_prevista'] ?? null,
                'dados_entrega' => $dados['dados_entrega'] ?? null,
                'metadados' => $dados['metadados'] ?? null,
                'status_venda' => $dados['status_venda'] ?? Venda::STATUS_PENDENTE,
                'status_pagamento' => Venda::PAGAMENTO_PENDENTE,
                'status_entrega' => Venda::ENTREGA_PENDENTE,
            ]);

            // Adicionar itens
            if (isset($dados['itens']) && is_array($dados['itens'])) {
                foreach ($dados['itens'] as $itemData) {
                    $this->adicionarItemVenda($venda, $itemData);
                }
            }

            // Adicionar pagamentos se fornecidos
            if (isset($dados['pagamentos']) && is_array($dados['pagamentos'])) {
                foreach ($dados['pagamentos'] as $pagamentoData) {
                    $this->adicionarPagamentoVenda($venda, $pagamentoData);
                }
            }

            // Recalcular totais
            $venda->calcularTotais();

            Log::info('Venda criada com sucesso', [
                'venda_id' => $venda->id,
                'numero_venda' => $venda->numero_venda,
                'valor_total' => $venda->valor_total,
                'empresa_id' => $venda->empresa_id
            ]);

            return $venda;
        });
    }

    /**
     * Adicionar item à venda
     */
    public function adicionarItemVenda(Venda $venda, array $itemData): VendaItem
    {
        // Verificar se o produto existe e está ativo
        $produto = Produto::where('id', $itemData['produto_id'])
            ->where('empresa_id', $venda->empresa_id)
            ->where('ativo', true)
            ->first();

        if (!$produto) {
            throw new \Exception('Produto não encontrado ou inativo.');
        }

        // Verificar estoque se o produto controla estoque
        if ($produto->controla_estoque && $produto->estoque_atual < $itemData['quantidade']) {
            throw new \Exception("Estoque insuficiente para o produto {$produto->nome}. Disponível: {$produto->estoque_atual}");
        }

        $vendaItem = VendaItem::create([
            'venda_id' => $venda->id,
            'produto_id' => $itemData['produto_id'],
            'produto_variacao_id' => $itemData['produto_variacao_id'] ?? null,
            'quantidade' => $itemData['quantidade'],
            'valor_unitario' => $itemData['valor_unitario'] ?? $produto->preco_venda,
            'desconto_percentual' => $itemData['desconto_percentual'] ?? 0,
            'desconto_valor' => $itemData['desconto_valor'] ?? 0,
            'observacoes' => $itemData['observacoes'] ?? null,
            'configuracoes' => $itemData['configuracoes'] ?? null,
            'personalizacoes' => $itemData['personalizacoes'] ?? null,
            'percentual_comissao_vendedor' => $itemData['percentual_comissao_vendedor'] ?? 0,
            'empresa_id' => $venda->empresa_id,
        ]);

        return $vendaItem;
    }

    /**
     * Adicionar pagamento à venda
     */
    public function adicionarPagamentoVenda(Venda $venda, array $pagamentoData): VendaPagamento
    {
        $vendaPagamento = VendaPagamento::create([
            'venda_id' => $venda->id,
            'forma_pagamento_id' => $pagamentoData['forma_pagamento_id'],
            'bandeira_id' => $pagamentoData['bandeira_id'] ?? null,
            'valor_pagamento' => $pagamentoData['valor_pagamento'],
            'parcelas' => $pagamentoData['parcelas'] ?? 1,
            'data_pagamento' => $pagamentoData['data_pagamento'] ?? now(),
            'observacoes' => $pagamentoData['observacoes'] ?? null,
            'empresa_id' => $venda->empresa_id,
            'usuario_id' => $venda->usuario_id,
            'status_pagamento' => VendaPagamento::STATUS_CONFIRMADO,
        ]);

        return $vendaPagamento;
    }

    /**
     * Confirmar venda (baixar estoque)
     */
    public function confirmarVenda(Venda $venda): bool
    {
        if ($venda->status_venda !== Venda::STATUS_PENDENTE) {
            throw new \Exception('Venda não pode ser confirmada. Status atual: ' . $venda->status_venda);
        }

        return DB::transaction(function () use ($venda) {
            $venda->confirmarVenda();

            Log::info('Venda confirmada', [
                'venda_id' => $venda->id,
                'numero_venda' => $venda->numero_venda,
                'empresa_id' => $venda->empresa_id
            ]);

            return true;
        });
    }

    /**
     * Cancelar venda (devolver estoque)
     */
    public function cancelarVenda(Venda $venda): bool
    {
        if ($venda->isCancelada()) {
            throw new \Exception('Venda já está cancelada.');
        }

        return DB::transaction(function () use ($venda) {
            $venda->cancelarVenda();

            Log::info('Venda cancelada', [
                'venda_id' => $venda->id,
                'numero_venda' => $venda->numero_venda,
                'empresa_id' => $venda->empresa_id
            ]);

            return true;
        });
    }

    /**
     * Obter vendas por período
     */
    public function obterVendasPorPeriodo(int $empresaId, Carbon $dataInicio, Carbon $dataFim, array $filtros = [])
    {
        $query = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->with(['cliente', 'usuario', 'itens.produto', 'pagamentos.formaPagamento']);

        // Aplicar filtros adicionais
        if (isset($filtros['vendedor_id'])) {
            $query->where('usuario_id', $filtros['vendedor_id']);
        }

        if (isset($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        if (isset($filtros['tipo_venda'])) {
            $query->where('tipo_venda', $filtros['tipo_venda']);
        }

        if (isset($filtros['status_venda'])) {
            $query->where('status_venda', $filtros['status_venda']);
        }

        return $query->orderBy('data_venda', 'desc');
    }

    /**
     * Calcular métricas de vendas
     */
    public function calcularMetricasVendas(int $empresaId, Carbon $dataInicio, Carbon $dataFim): array
    {
        $vendas = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->confirmadas()
            ->get();

        $totalVendas = $vendas->count();
        $valorTotalVendas = $vendas->sum('valor_total');
        $ticketMedio = $totalVendas > 0 ? $valorTotalVendas / $totalVendas : 0;

        // Vendas por tipo
        $vendasPorTipo = $vendas->groupBy('tipo_venda')->map(function ($grupo) {
            return [
                'quantidade' => $grupo->count(),
                'valor_total' => $grupo->sum('valor_total')
            ];
        });

        // Vendas por vendedor
        $vendasPorVendedor = $vendas->groupBy('usuario_id')->map(function ($grupo) {
            return [
                'quantidade' => $grupo->count(),
                'valor_total' => $grupo->sum('valor_total'),
                'vendedor_nome' => $grupo->first()->usuario->name ?? 'N/A'
            ];
        });

        // Produtos mais vendidos
        $produtosMaisVendidos = VendaItem::whereIn('venda_id', $vendas->pluck('id'))
            ->select('produto_id', 'nome_produto')
            ->selectRaw('SUM(quantidade) as total_quantidade')
            ->selectRaw('SUM(valor_total_item) as total_valor')
            ->groupBy('produto_id', 'nome_produto')
            ->orderBy('total_quantidade', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_vendas' => $totalVendas,
            'valor_total_vendas' => $valorTotalVendas,
            'ticket_medio' => $ticketMedio,
            'vendas_por_tipo' => $vendasPorTipo,
            'vendas_por_vendedor' => $vendasPorVendedor,
            'produtos_mais_vendidos' => $produtosMaisVendidos,
        ];
    }

    /**
     * Obter relatório de comissões
     */
    public function obterRelatorioComissoes(int $empresaId, Carbon $dataInicio, Carbon $dataFim): array
    {
        $vendas = Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->confirmadas()
            ->with(['usuario', 'itens'])
            ->get();

        $comissoesPorVendedor = [];

        foreach ($vendas as $venda) {
            $vendedorId = $venda->usuario_id;
            
            if (!isset($comissoesPorVendedor[$vendedorId])) {
                $comissoesPorVendedor[$vendedorId] = [
                    'vendedor_nome' => $venda->usuario->name ?? 'N/A',
                    'total_vendas' => 0,
                    'valor_vendas' => 0,
                    'comissao_marketplace' => 0,
                    'comissao_vendedor' => 0,
                ];
            }

            $comissoesPorVendedor[$vendedorId]['total_vendas']++;
            $comissoesPorVendedor[$vendedorId]['valor_vendas'] += $venda->valor_total;
            $comissoesPorVendedor[$vendedorId]['comissao_marketplace'] += $venda->valor_comissao_marketplace;
            $comissoesPorVendedor[$vendedorId]['comissao_vendedor'] += $venda->itens->sum('valor_comissao_vendedor');
        }

        return $comissoesPorVendedor;
    }
}