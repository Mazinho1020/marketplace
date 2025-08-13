<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\ProdutoCodigoBarras;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProdutoCodigoBarrasController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $query = ProdutoCodigoBarras::with(['produto'])
            ->porEmpresa($empresaId);

        // Filtros
        if ($request->filled('produto_id')) {
            $query->porProduto($request->produto_id);
        }

        if ($request->filled('tipo')) {
            $query->porTipo($request->tipo);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('codigo', 'like', "%{$busca}%")
                    ->orWhereHas('produto', function ($subQ) use ($busca) {
                        $subQ->where('nome', 'like', "%{$busca}%")
                            ->orWhere('sku', 'like', "%{$busca}%");
                    });
            });
        }

        $codigosBarras = $query->orderBy('created_at', 'desc')->paginate(20);

        // Para os filtros
        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();
        $tipos = ProdutoCodigoBarras::getTipos();

        return view('comerciantes.produtos.codigos-barras.index', compact(
            'codigosBarras',
            'produtos',
            'tipos'
        ));
    }

    public function create(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();
        $tipos = ProdutoCodigoBarras::getTipos();

        // Produto pré-selecionado
        $produtoSelecionado = null;
        if ($request->filled('produto_id')) {
            $produtoSelecionado = Produto::porEmpresa($empresaId)
                ->find($request->produto_id);
        }

        return view('comerciantes.produtos.codigos-barras.create', compact(
            'produtos',
            'tipos',
            'produtoSelecionado'
        ));
    }

    public function store(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:ean13,ean8,code128,interno,outro',
            'codigo' => 'required|string|max:255',
            'principal' => 'boolean',
            'ativo' => 'boolean'
        ]);

        // Validar produto pertence à empresa
        $produto = Produto::porEmpresa($empresaId)->findOrFail($validated['produto_id']);

        // Verificar duplicação
        if (ProdutoCodigoBarras::verificarDuplicacao($empresaId, $validated['codigo'])) {
            return back()->withErrors(['codigo' => 'Este código de barras já está sendo usado por outro produto.'])->withInput();
        }

        // Validar formato do código
        $codigoTemp = new ProdutoCodigoBarras([
            'tipo' => $validated['tipo'],
            'codigo' => $validated['codigo']
        ]);

        if (!$codigoTemp->isValido()) {
            return back()->withErrors(['codigo' => 'Código de barras inválido para o tipo selecionado.'])->withInput();
        }

        $validated['empresa_id'] = $empresaId;

        // Se não tem nenhum código para este produto, definir como principal
        $temCodigos = ProdutoCodigoBarras::porProduto($validated['produto_id'])
            ->ativo()
            ->exists();

        if (!$temCodigos) {
            $validated['principal'] = true;
        }

        $codigoBarras = ProdutoCodigoBarras::create($validated);

        return redirect()
            ->route('comerciantes.produtos.codigos-barras.index')
            ->with('success', 'Código de barras criado com sucesso!');
    }

    public function show(ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        $codigoBarras->load(['produto', 'produto.categoria', 'produto.marca']);

        // Buscar outros códigos do mesmo produto
        $outrosCodigos = ProdutoCodigoBarras::where('produto_id', $codigoBarras->produto_id)
            ->where('id', '!=', $codigoBarras->id)
            ->ativo()
            ->orderBy('principal', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('comerciantes.produtos.codigos-barras.show', compact('codigoBarras', 'outrosCodigos'));
    }

    public function edit(ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();
        $tipos = ProdutoCodigoBarras::getTipos();

        return view('comerciantes.produtos.codigos-barras.edit', compact(
            'codigoBarras',
            'produtos',
            'tipos'
        ));
    }

    public function update(Request $request, ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'tipo' => 'required|in:ean13,ean8,code128,interno,outro',
            'codigo' => 'required|string|max:255',
            'principal' => 'boolean',
            'ativo' => 'boolean'
        ]);

        // Validar produto pertence à empresa
        $produto = Produto::porEmpresa($empresaId)->findOrFail($validated['produto_id']);

        // Verificar duplicação (excluindo o próprio registro)
        if (ProdutoCodigoBarras::verificarDuplicacao($empresaId, $validated['codigo'], $codigoBarras->id)) {
            return back()->withErrors(['codigo' => 'Este código de barras já está sendo usado por outro produto.'])->withInput();
        }

        // Validar formato do código
        $codigoTemp = new ProdutoCodigoBarras([
            'tipo' => $validated['tipo'],
            'codigo' => $validated['codigo']
        ]);

        if (!$codigoTemp->isValido()) {
            return back()->withErrors(['codigo' => 'Código de barras inválido para o tipo selecionado.'])->withInput();
        }

        $codigoBarras->update($validated);

        return redirect()
            ->route('comerciantes.produtos.codigos-barras.index')
            ->with('success', 'Código de barras atualizado com sucesso!');
    }

    public function destroy(ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        if (!$codigoBarras->podeSerDeletado()) {
            return back()->withErrors(['erro' => 'Este é o único código de barras do produto e não pode ser deletado.']);
        }

        $codigoBarras->delete();

        return redirect()
            ->route('comerciantes.produtos.codigos-barras.index')
            ->with('success', 'Código de barras deletado com sucesso!');
    }

    // AJAX: Buscar produto por código de barras
    public function buscarPorCodigo(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        $codigo = $request->codigo;

        $codigoBarras = ProdutoCodigoBarras::buscarPorCodigo($empresaId, $codigo);

        if ($codigoBarras) {
            return response()->json([
                'found' => true,
                'produto' => [
                    'id' => $codigoBarras->produto->id,
                    'nome' => $codigoBarras->produto->nome,
                    'sku' => $codigoBarras->produto->sku,
                    'preco_venda' => $codigoBarras->produto->preco_venda,
                    'estoque_atual' => $codigoBarras->produto->estoque_atual,
                    'categoria' => $codigoBarras->produto->categoria->nome ?? null,
                    'marca' => $codigoBarras->produto->marca->nome ?? null
                ],
                'codigo_barras' => [
                    'id' => $codigoBarras->id,
                    'tipo' => $codigoBarras->tipo,
                    'codigo' => $codigoBarras->codigo,
                    'principal' => $codigoBarras->principal
                ]
            ]);
        }

        return response()->json(['found' => false]);
    }

    // AJAX: Gerar código interno
    public function gerarCodigoInterno(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        $produtoId = $request->produto_id;

        $codigo = ProdutoCodigoBarras::gerarCodigoInterno($empresaId, $produtoId);

        return response()->json(['codigo' => $codigo]);
    }

    // AJAX: Validar código
    public function validarCodigo(Request $request)
    {
        $codigo = $request->codigo;
        $tipo = $request->tipo;

        $codigoTemp = new ProdutoCodigoBarras([
            'tipo' => $tipo,
            'codigo' => $codigo
        ]);

        $valido = $codigoTemp->isValido();

        return response()->json([
            'valido' => $valido,
            'formatado' => $codigoTemp->formatado,
            'mensagem' => $valido ? 'Código válido' : 'Código inválido para o tipo selecionado'
        ]);
    }

    // AJAX: Definir como principal
    public function definirPrincipal(ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        $codigoBarras->definirComoPrincipal();

        return response()->json([
            'success' => true,
            'message' => 'Código definido como principal'
        ]);
    }

    // AJAX: Toggle ativo
    public function toggleAtivo(ProdutoCodigoBarras $codigoBarras)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($codigoBarras->empresa_id !== $empresaId) {
            abort(404);
        }

        $codigoBarras->update(['ativo' => !$codigoBarras->ativo]);

        return response()->json([
            'success' => true,
            'ativo' => $codigoBarras->ativo
        ]);
    }

    // Página para scanner/leitura de códigos
    public function scanner()
    {
        return view('comerciantes.produtos.codigos-barras.scanner');
    }

    // Relatório de códigos duplicados
    public function relatorioDuplicados()
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $duplicados = ProdutoCodigoBarras::porEmpresa($empresaId)
            ->select('codigo')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('codigo')
            ->having('total', '>', 1)
            ->with(['produto'])
            ->get();

        return view('comerciantes.produtos.codigos-barras.relatorio-duplicados', compact('duplicados'));
    }
}
