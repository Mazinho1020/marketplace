<?php

namespace App\Http\Controllers\Comerciantes\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Vendas\Venda;
use App\Models\Vendas\VendaItem;
use App\Models\Produto;
use App\Models\ProdutoCategoria;
use App\Models\ProdutoMarca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VendaController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request, int $empresa)
    {
        try {
            $query = Venda::porEmpresa($empresa)
                ->with(['usuario', 'cliente', 'itens.produto'])
                ->orderBy('created_at', 'desc');

            // Filtros
            if ($request->filled('status')) {
                $query->porStatus($request->status);
            }

            if ($request->filled('data_inicio') && $request->filled('data_fim')) {
                $query->porPeriodo($request->data_inicio, $request->data_fim);
            }

            if ($request->filled('numero_venda')) {
                $query->where('numero_venda', 'like', '%' . $request->numero_venda . '%');
            }

            $vendas = $query->paginate(20);

            // Estatísticas rápidas
            $estatisticas = [
                'total_vendas_hoje' => Venda::porEmpresa($empresa)->hoje()->count(),
                'total_vendas_mes' => Venda::porEmpresa($empresa)->esteMes()->count(),
                'valor_vendas_hoje' => Venda::porEmpresa($empresa)->hoje()->sum('valor_liquido'),
                'valor_vendas_mes' => Venda::porEmpresa($empresa)->esteMes()->sum('valor_liquido'),
            ];

            return view('comerciantes.vendas.index', compact('vendas', 'empresa', 'estatisticas'));
        } catch (\Exception $e) {
            Log::error('Erro ao listar vendas: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar vendas.');
        }
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create(int $empresa)
    {
        try {
            // Buscar produtos ativos da empresa
            $produtos = Produto::where('empresa_id', $empresa)
                ->where('ativo', true)
                ->where('status_venda', 'ativo')
                ->with(['categoria', 'marca', 'imagens'])
                ->orderBy('nome')
                ->get();

            // Buscar categorias para filtros
            $categorias = ProdutoCategoria::where('empresa_id', $empresa)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            // Buscar marcas para filtros
            $marcas = ProdutoMarca::where('empresa_id', $empresa)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            // Buscar clientes (pessoas do tipo cliente)
            $clientes = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'like', '%cliente%')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            return view('comerciantes.vendas.create', compact('empresa', 'produtos', 'categorias', 'marcas', 'clientes'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de venda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar formulário de venda.');
        }
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request, int $empresa)
    {
        $request->validate([
            'tipo_venda' => 'required|in:balcao,delivery,online,telefone',
            'cliente_id' => 'nullable|exists:pessoas,id',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $usuario = Auth::user();

            // Criar a venda
            $venda = Venda::create([
                'empresa_id' => $empresa,
                'usuario_id' => $usuario->id,
                'cliente_id' => $request->cliente_id,
                'tipo_venda' => $request->tipo_venda,
                'observacoes' => $request->observacoes,
                'valor_desconto' => $request->valor_desconto ?? 0,
            ]);

            // Adicionar itens
            foreach ($request->itens as $itemData) {
                $produto = Produto::findOrFail($itemData['produto_id']);
                
                // Verificar estoque se necessário
                if ($produto->controla_estoque && $produto->estoque_atual < $itemData['quantidade']) {
                    throw new \Exception("Estoque insuficiente para o produto: {$produto->nome}");
                }

                $venda->adicionarItem(
                    $produto,
                    $itemData['quantidade'],
                    $itemData['valor_unitario'],
                    [
                        'observacoes' => $itemData['observacoes'] ?? null,
                    ]
                );
            }

            // Confirmar a venda se solicitado
            if ($request->confirmar_venda) {
                $venda->confirmar();
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.empresas.vendas.show', ['empresa' => $empresa, 'venda' => $venda->id])
                ->with('success', 'Venda criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar venda: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar venda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sale.
     */
    public function show(int $empresa, int $venda)
    {
        try {
            $venda = Venda::porEmpresa($empresa)
                ->with(['usuario', 'cliente', 'itens.produto', 'lancamento', 'pagamentos'])
                ->findOrFail($venda);

            return view('comerciantes.vendas.show', compact('venda', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao exibir venda: ' . $e->getMessage());
            return back()->with('error', 'Venda não encontrada.');
        }
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(int $empresa, int $venda)
    {
        try {
            $venda = Venda::porEmpresa($empresa)
                ->with(['itens.produto'])
                ->findOrFail($venda);

            // Só permitir edição de vendas pendentes
            if ($venda->status !== Venda::STATUS_PENDENTE) {
                return back()->with('error', 'Apenas vendas pendentes podem ser editadas.');
            }

            // Buscar produtos ativos da empresa
            $produtos = Produto::where('empresa_id', $empresa)
                ->where('ativo', true)
                ->where('status_venda', 'ativo')
                ->with(['categoria', 'marca'])
                ->orderBy('nome')
                ->get();

            // Buscar clientes
            $clientes = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'like', '%cliente%')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();

            return view('comerciantes.vendas.edit', compact('venda', 'empresa', 'produtos', 'clientes'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar edição de venda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar venda para edição.');
        }
    }

    /**
     * Update the specified sale in storage.
     */
    public function update(Request $request, int $empresa, int $venda)
    {
        $request->validate([
            'tipo_venda' => 'required|in:balcao,delivery,online,telefone',
            'cliente_id' => 'nullable|exists:pessoas,id',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $venda = Venda::porEmpresa($empresa)->findOrFail($venda);

            // Só permitir edição de vendas pendentes
            if ($venda->status !== Venda::STATUS_PENDENTE) {
                return back()->with('error', 'Apenas vendas pendentes podem ser editadas.');
            }

            $venda->update([
                'tipo_venda' => $request->tipo_venda,
                'cliente_id' => $request->cliente_id,
                'observacoes' => $request->observacoes,
                'valor_desconto' => $request->valor_desconto ?? 0,
            ]);

            $venda->recalcularTotais();

            DB::commit();

            return redirect()
                ->route('comerciantes.empresas.vendas.show', ['empresa' => $empresa, 'venda' => $venda->id])
                ->with('success', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar venda: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy(int $empresa, int $venda)
    {
        DB::beginTransaction();
        try {
            $venda = Venda::porEmpresa($empresa)->findOrFail($venda);

            // Só permitir exclusão de vendas pendentes
            if ($venda->status !== Venda::STATUS_PENDENTE) {
                return back()->with('error', 'Apenas vendas pendentes podem ser excluídas.');
            }

            $venda->cancelar('Venda excluída pelo usuário');

            DB::commit();

            return redirect()
                ->route('comerciantes.empresas.vendas.index', ['empresa' => $empresa])
                ->with('success', 'Venda cancelada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir venda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao excluir venda.');
        }
    }

    /**
     * Confirm a sale
     */
    public function confirmar(int $empresa, int $venda)
    {
        DB::beginTransaction();
        try {
            $venda = Venda::porEmpresa($empresa)->findOrFail($venda);

            if ($venda->status !== Venda::STATUS_PENDENTE) {
                return back()->with('error', 'Apenas vendas pendentes podem ser confirmadas.');
            }

            $venda->confirmar();

            DB::commit();

            return back()->with('success', 'Venda confirmada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao confirmar venda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao confirmar venda.');
        }
    }

    /**
     * Cancel a sale
     */
    public function cancelar(Request $request, int $empresa, int $venda)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $venda = Venda::porEmpresa($empresa)->findOrFail($venda);

            if ($venda->status === Venda::STATUS_CANCELADA) {
                return back()->with('error', 'Esta venda já foi cancelada.');
            }

            $venda->cancelar($request->motivo);

            DB::commit();

            return back()->with('success', 'Venda cancelada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar venda: ' . $e->getMessage());
            return back()->with('error', 'Erro ao cancelar venda.');
        }
    }

    /**
     * Search products for sale
     */
    public function buscarProdutos(Request $request, int $empresa)
    {
        try {
            $termo = $request->get('q', '');
            
            $produtos = Produto::where('empresa_id', $empresa)
                ->where('ativo', true)
                ->where('status_venda', 'ativo')
                ->where(function ($query) use ($termo) {
                    $query->where('nome', 'like', "%{$termo}%")
                          ->orWhere('codigo_sistema', 'like', "%{$termo}%")
                          ->orWhere('codigo_barras', 'like', "%{$termo}%")
                          ->orWhere('sku', 'like', "%{$termo}%");
                })
                ->with(['categoria', 'marca'])
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'produtos' => $produtos->map(function ($produto) {
                    return [
                        'id' => $produto->id,
                        'nome' => $produto->nome,
                        'codigo' => $produto->codigo_sistema,
                        'preco_venda' => $produto->preco_venda,
                        'estoque_atual' => $produto->estoque_atual,
                        'controla_estoque' => $produto->controla_estoque,
                        'categoria' => $produto->categoria ? $produto->categoria->nome : null,
                        'marca' => $produto->marca ? $produto->marca->nome : null,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar produtos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produtos.'
            ], 500);
        }
    }
}