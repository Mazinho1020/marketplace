<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstoqueController extends Controller
{
    public function __construct()
    {
        // Middleware será aplicado nas rotas
    }

    /**
     * Alertas de estoque baixo
     */
    public function alertas()
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        // Produtos com estoque baixo
        $produtosEstoqueBaixo = Produto::where('empresa_id', $empresaId)
            ->where('controla_estoque', true)
            ->where('ativo', true)
            ->whereRaw('estoque_atual <= estoque_minimo')
            ->with(['categoria', 'marca'])
            ->orderBy('estoque_atual', 'asc')
            ->get();

        // Produtos sem estoque
        $produtosSemEstoque = Produto::where('empresa_id', $empresaId)
            ->where('controla_estoque', true)
            ->where('ativo', true)
            ->where('estoque_atual', '<=', 0)
            ->with(['categoria', 'marca'])
            ->orderBy('nome')
            ->get();

        // Produtos com estoque alto (acima do máximo)
        $produtosEstoqueAlto = Produto::where('empresa_id', $empresaId)
            ->where('controla_estoque', true)
            ->where('ativo', true)
            ->whereRaw('estoque_atual >= estoque_maximo')
            ->where('estoque_maximo', '>', 0)
            ->with(['categoria', 'marca'])
            ->orderBy('estoque_atual', 'desc')
            ->get();

        return view('comerciantes.produtos.estoque.alertas', compact(
            'produtosEstoqueBaixo',
            'produtosSemEstoque',
            'produtosEstoqueAlto'
        ));
    }

    /**
     * Movimentações de estoque
     */
    public function movimentacoes(Request $request)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $query = Produto::where('empresa_id', $empresaId)
            ->with(['movimentacoes' => function ($q) use ($request) {
                $q->orderBy('created_at', 'desc');

                // Filtros opcionais
                if ($request->filled('data_inicio')) {
                    $q->whereDate('created_at', '>=', $request->data_inicio);
                }

                if ($request->filled('data_fim')) {
                    $q->whereDate('created_at', '<=', $request->data_fim);
                }

                if ($request->filled('tipo')) {
                    $q->where('tipo', $request->tipo);
                }
            }, 'categoria', 'marca']);

        // Filtro por produto específico
        if ($request->filled('produto_id')) {
            $query->where('id', $request->produto_id);
        }

        $produtos = $query->get();

        // Lista de produtos para o filtro
        $listaProdutos = Produto::where('empresa_id', $empresaId)
            ->where('controla_estoque', true)
            ->orderBy('nome')
            ->pluck('nome', 'id');

        return view('comerciantes.produtos.estoque.movimentacoes', compact(
            'produtos',
            'listaProdutos'
        ));
    }

    /**
     * Registrar movimentação manual
     */
    public function registrarMovimentacao(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:entrada,saida,ajuste',
            'quantidade' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'observacoes' => 'nullable|string'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $produto = Produto::where('id', $request->produto_id)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        // Registrar movimentação usando o método do model
        $produto->registrarMovimentacao(
            $request->tipo,
            $request->quantidade,
            $request->motivo,
            $request->observacoes
        );

        return redirect()->back()->with('success', 'Movimentação registrada com sucesso!');
    }

    /**
     * Atualizar estoque em lote
     */
    public function atualizarLote(Request $request)
    {
        $request->validate([
            'produtos' => 'required|array',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.estoque_atual' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;
        $atualizados = 0;

        foreach ($request->produtos as $produtoData) {
            $produto = Produto::where('id', $produtoData['id'])
                ->where('empresa_id', $empresaId)
                ->first();

            if ($produto && $produto->estoque_atual != $produtoData['estoque_atual']) {
                $quantidade = abs($produtoData['estoque_atual'] - $produto->estoque_atual);
                $tipo = $produtoData['estoque_atual'] > $produto->estoque_atual ? 'entrada' : 'saida';

                $produto->registrarMovimentacao(
                    $tipo,
                    $quantidade,
                    $request->motivo,
                    "Atualização em lote"
                );

                $atualizados++;
            }
        }

        return redirect()->back()->with('success', "{$atualizados} produtos atualizados com sucesso!");
    }
}
