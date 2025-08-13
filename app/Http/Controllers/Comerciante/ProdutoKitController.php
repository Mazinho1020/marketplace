<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProdutoKitController extends Controller
{
    protected $empresaId;

    public function __construct()
    {
        $this->middleware('auth:comerciante');
        $this->middleware(function ($request, $next) {
            $this->empresaId = Auth::guard('comerciante')->user()->empresa_id;
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Produto::where('empresa_id', $this->empresaId)
            ->where('tipo', 'kit')
            ->with(['categoria', 'marca', 'kitsItens.produtoItem'])
            ->withCount('kitsItens');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('codigo_sistema', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->get('categoria_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $kits = $query->orderBy('nome')->paginate(15);

        // Para os filtros
        $categorias = \App\Models\ProdutoCategoria::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.kits.index', compact('kits', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Buscar produtos disponíveis para formar kits
        $produtos = Produto::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->where('tipo', '!=', 'kit') // Não incluir outros kits
            ->orderBy('nome')
            ->get();

        $categorias = \App\Models\ProdutoCategoria::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $marcas = \App\Models\ProdutoMarca::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.kits.create', compact('produtos', 'categorias', 'marcas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:produto_categorias,id',
            'marca_id' => 'nullable|exists:produto_marcas,id',
            'preco_venda' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'itens' => 'required|array|min:2', // Kit deve ter pelo menos 2 itens
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:0.001',
            'itens.*.preco_item' => 'nullable|numeric|min:0',
            'itens.*.desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'itens.*.obrigatorio' => 'boolean',
            'itens.*.substituivel' => 'boolean',
            'itens.*.ordem' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Criar o produto principal (kit)
            $kitData = [
                'empresa_id' => $this->empresaId,
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'tipo' => 'kit',
                'nome' => $request->nome,
                'slug' => Str::slug($request->nome),
                'sku' => $request->sku,
                'descricao' => $request->descricao,
                'preco_venda' => $request->preco_venda,
                'status' => $request->ativo ? 'disponivel' : 'indisponivel',
                'ativo' => $request->ativo ?? true,
                'controla_estoque' => false // Kits geralmente não controlam estoque próprio
            ];

            // Upload da imagem do kit
            if ($request->hasFile('imagem')) {
                $imagem = $request->file('imagem');
                $nomeArquivo = time() . '_kit_' . $imagem->getClientOriginalName();
                $caminhoImagem = $imagem->storeAs('produtos', $nomeArquivo, 'public');
                // Garante que não duplica 'storage/storage/'
                $kitData['imagem_principal'] = 'storage/produtos/' . $nomeArquivo;
            }

            $kit = Produto::create($kitData);

            // Criar os itens do kit
            foreach ($request->itens as $index => $itemData) {
                ProdutoKit::create([
                    'empresa_id' => $this->empresaId,
                    'produto_principal_id' => $kit->id,
                    'produto_item_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_item' => $itemData['preco_item'] ?? null,
                    'desconto_percentual' => $itemData['desconto_percentual'] ?? null,
                    'obrigatorio' => $itemData['obrigatorio'] ?? true,
                    'substituivel' => $itemData['substituivel'] ?? false,
                    'ordem' => $itemData['ordem'] ?? $index + 1,
                    'ativo' => true
                ]);
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.kits.show', $kit)
                ->with('success', 'Kit criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar kit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Produto $kit)
    {
        // Verificar se é da empresa e é um kit
        if ($kit->empresa_id !== $this->empresaId || $kit->tipo !== 'kit') {
            abort(404);
        }

        $kit->load([
            'categoria',
            'marca',
            'kitsItens' => function ($query) {
                $query->with('produtoItem')->ordenados();
            }
        ]);

        // Calcular valores
        $precoTotalItens = ProdutoKit::calcularPrecoTotalKit($kit->id, $this->empresaId);
        $economiaKit = $precoTotalItens - $kit->preco_venda;
        $percentualDesconto = $precoTotalItens > 0 ? (($economiaKit / $precoTotalItens) * 100) : 0;

        return view('comerciantes.produtos.kits.show', compact(
            'kit',
            'precoTotalItens',
            'economiaKit',
            'percentualDesconto'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $kit)
    {
        // Verificar se é da empresa e é um kit
        if ($kit->empresa_id !== $this->empresaId || $kit->tipo !== 'kit') {
            abort(404);
        }

        $kit->load(['kitsItens.produtoItem']);

        // Buscar produtos disponíveis para formar kits
        $produtos = Produto::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->where('tipo', '!=', 'kit')
            ->where('id', '!=', $kit->id) // Não incluir o próprio kit
            ->orderBy('nome')
            ->get();

        $categorias = \App\Models\ProdutoCategoria::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $marcas = \App\Models\ProdutoMarca::where('empresa_id', $this->empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.kits.edit', compact('kit', 'produtos', 'categorias', 'marcas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $kit)
    {
        // Verificar se é da empresa e é um kit
        if ($kit->empresa_id !== $this->empresaId || $kit->tipo !== 'kit') {
            abort(404);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:produto_categorias,id',
            'marca_id' => 'nullable|exists:produto_marcas,id',
            'preco_venda' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'itens' => 'required|array|min:2',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:0.001',
            'itens.*.preco_item' => 'nullable|numeric|min:0',
            'itens.*.desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'itens.*.obrigatorio' => 'boolean',
            'itens.*.substituivel' => 'boolean',
            'itens.*.ordem' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Atualizar o produto principal (kit)
            $kitData = [
                'categoria_id' => $request->categoria_id,
                'marca_id' => $request->marca_id,
                'nome' => $request->nome,
                'slug' => Str::slug($request->nome),
                'sku' => $request->sku,
                'descricao' => $request->descricao,
                'preco_venda' => $request->preco_venda,
                'status' => $request->ativo ? 'disponivel' : 'indisponivel',
                'ativo' => $request->ativo ?? true
            ];

            // Upload da nova imagem do kit
            if ($request->hasFile('imagem')) {
                // Remover imagem anterior se existir
                if ($kit->imagem_principal && file_exists(public_path($kit->imagem_principal))) {
                    unlink(public_path($kit->imagem_principal));
                }

                $imagem = $request->file('imagem');
                $nomeArquivo = time() . '_kit_' . $imagem->getClientOriginalName();
                $caminhoImagem = $imagem->storeAs('produtos', $nomeArquivo, 'public');
                // Garante que não duplica 'storage/storage/'
                $kitData['imagem_principal'] = 'storage/produtos/' . $nomeArquivo;
            }

            $kit->update($kitData);

            // Remover itens existentes
            ProdutoKit::where('produto_principal_id', $kit->id)->delete();

            // Criar novos itens
            foreach ($request->itens as $index => $itemData) {
                ProdutoKit::create([
                    'empresa_id' => $this->empresaId,
                    'produto_principal_id' => $kit->id,
                    'produto_item_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_item' => $itemData['preco_item'] ?? null,
                    'desconto_percentual' => $itemData['desconto_percentual'] ?? null,
                    'obrigatorio' => $itemData['obrigatorio'] ?? true,
                    'substituivel' => $itemData['substituivel'] ?? false,
                    'ordem' => $itemData['ordem'] ?? $index + 1,
                    'ativo' => true
                ]);
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.kits.show', $kit)
                ->with('success', 'Kit atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar kit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $kit)
    {
        // Verificar se é da empresa e é um kit
        if ($kit->empresa_id !== $this->empresaId || $kit->tipo !== 'kit') {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // Remover itens do kit
            ProdutoKit::where('produto_principal_id', $kit->id)->delete();

            // Remover o kit
            $kit->delete();

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.kits.index')
                ->with('success', 'Kit excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao excluir kit: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Buscar produtos para adicionar ao kit
     */
    public function buscarProduto(Request $request)
    {
        try {
            $term = $request->get('term');

            // Log para debug
            Log::info('ProdutoKitController::buscarProduto - Requisição recebida', [
                'term' => $term,
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token()
            ]);

            if (!$term || strlen($term) < 2) {
                return response()->json([]);
            }

            // Tentativa de autenticação múltipla
            $user = null;
            $empresaId = null;

            // Primeiro, verificar guard padrão
            if (Auth::check()) {
                $user = Auth::user();
                $empresaId = $user->empresa_id ?? null;
                Log::info('Usuário autenticado via guard padrão', ['user_id' => $user->id]);
            }

            // Segundo, verificar guard comerciante
            if (!$user && Auth::guard('comerciante')->check()) {
                $user = Auth::guard('comerciante')->user();
                $empresaId = $user->empresa_id ?? null;
                Log::info('Usuário autenticado via guard comerciante', ['user_id' => $user->id]);
            }

            // Terceiro, usar empresaId do controller se disponível
            if (!$empresaId && $this->empresaId) {
                $empresaId = $this->empresaId;
                Log::info('Usando empresa_id do controller', ['empresa_id' => $empresaId]);
            }

            // Se ainda não temos empresa_id, tentar buscar da sessão
            if (!$empresaId && session()->has('empresa_id')) {
                $empresaId = session('empresa_id');
                Log::info('Usando empresa_id da sessão', ['empresa_id' => $empresaId]);
            }

            // Se ainda não temos empresa_id, usar a primeira empresa como fallback (apenas para debug)
            if (!$empresaId) {
                $firstEmpresa = DB::table('empresas')->first();
                if ($firstEmpresa) {
                    $empresaId = $firstEmpresa->id;
                    Log::warning('Usando primeira empresa como fallback', ['empresa_id' => $empresaId]);
                }
            }

            if (!$empresaId) {
                Log::error('Nenhuma empresa_id disponível');
                return response()->json(['error' => 'Empresa não identificada'], 400);
            }

            $produtos = Produto::where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->where('tipo', '!=', 'kit') // Não incluir outros kits
                ->where(function ($query) use ($term) {
                    $query->where('nome', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('codigo_sistema', 'like', "%{$term}%");
                })
                ->select(['id', 'nome', 'sku', 'preco_venda', 'estoque_atual', 'controla_estoque', 'unidade_medida'])
                ->orderBy('nome')
                ->limit(20)
                ->get();

            Log::info('Produtos encontrados', [
                'count' => $produtos->count(),
                'empresa_id' => $empresaId,
                'term' => $term
            ]);

            return response()->json($produtos);
        } catch (\Exception $e) {
            Log::error('Erro na busca de produtos', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * AJAX: Calcular preço total do kit
     */
    public function calcularPrecoKit(Request $request)
    {
        $itens = $request->input('itens', []);
        $precoTotal = 0;

        foreach ($itens as $item) {
            if (!isset($item['produto_id']) || !isset($item['quantidade'])) {
                continue;
            }

            $produto = Produto::where('empresa_id', $this->empresaId)
                ->find($item['produto_id']);

            if (!$produto) {
                continue;
            }

            $precoItem = $item['preco_item'] ?? $produto->preco_venda;
            $quantidade = $item['quantidade'] ?? 1;
            $desconto = $item['desconto_percentual'] ?? 0;

            $precoComDesconto = $precoItem * (1 - ($desconto / 100));
            $precoTotal += $precoComDesconto * $quantidade;
        }

        return response()->json([
            'preco_total' => number_format($precoTotal, 2, '.', ''),
            'preco_total_formatado' => 'R$ ' . number_format($precoTotal, 2, ',', '.')
        ]);
    }
}
