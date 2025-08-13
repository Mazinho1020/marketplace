<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProdutoSubcategoria;
use App\Models\ProdutoCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProdutoSubcategoriaController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        $query = ProdutoSubcategoria::with(['categoria', 'parent', 'children'])
            ->porEmpresa($empresaId);

        // Filtros
        if ($request->filled('categoria_id')) {
            $query->porCategoria($request->categoria_id);
        }

        if ($request->filled('parent_id')) {
            if ($request->parent_id === '0') {
                $query->principais();
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        $subcategorias = $query->ordenado()->paginate(20);

        // Para os filtros
        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->ordenado()->get();
        $subcategoriasPai = ProdutoSubcategoria::porEmpresa($empresaId)
            ->principais()
            ->ativas()
            ->ordenado()
            ->get();

        return view('comerciantes.produtos.subcategorias.index', compact(
            'subcategorias',
            'categorias',
            'subcategoriasPai'
        ));
    }

    public function create(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->ordenado()->get();

        $subcategoriasPai = [];
        if ($request->filled('categoria_id')) {
            $subcategoriasPai = ProdutoSubcategoria::porEmpresa($empresaId)
                ->porCategoria($request->categoria_id)
                ->principais()
                ->ativas()
                ->ordenado()
                ->get();
        }

        return view('comerciantes.produtos.subcategorias.create', compact(
            'categorias',
            'subcategoriasPai'
        ));
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        $validated = $request->validate([
            'categoria_id' => 'required|exists:produto_categorias,id',
            'parent_id' => 'nullable|exists:produto_subcategorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('produto_subcategorias')->where(function ($query) use ($empresaId) {
                    return $query->where('empresa_id', $empresaId);
                })
            ],
            'icone' => 'nullable|string|max:100',
            'cor_fundo' => 'nullable|string|max:7',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ordem' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Validar categoria pertence à empresa
        $categoria = ProdutoCategoria::porEmpresa($empresaId)->findOrFail($validated['categoria_id']);

        // Validar parent pertence à empresa e categoria
        if (!empty($validated['parent_id'])) {
            ProdutoSubcategoria::porEmpresa($empresaId)
                ->porCategoria($validated['categoria_id'])
                ->findOrFail($validated['parent_id']);
        }

        $validated['empresa_id'] = $empresaId;

        // Upload da imagem
        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('produtos/subcategorias', 'public');
            $validated['imagem_url'] = $path;
        }

        $subcategoria = ProdutoSubcategoria::create($validated);

        return redirect()
            ->route('comerciantes.produtos.subcategorias.index')
            ->with('success', 'Subcategoria criada com sucesso!');
    }

    public function show(ProdutoSubcategoria $subcategoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($subcategoria->empresa_id !== $empresaId) {
            abort(404);
        }

        $subcategoria->load(['categoria', 'parent', 'children.children', 'produtos']);

        $estatisticas = [
            'total_produtos' => $subcategoria->produtos()->count(),
            'produtos_ativos' => $subcategoria->produtos()->ativos()->count(),
            'total_filhas' => $subcategoria->children()->count(),
            'filhas_ativas' => $subcategoria->children()->ativas()->count()
        ];

        return view('comerciantes.produtos.subcategorias.show', compact(
            'subcategoria',
            'estatisticas'
        ));
    }

    public function edit(ProdutoSubcategoria $subcategoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($subcategoria->empresa_id !== $empresaId) {
            abort(404);
        }

        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->ordenado()->get();

        $subcategoriasPai = ProdutoSubcategoria::porEmpresa($empresaId)
            ->porCategoria($subcategoria->categoria_id)
            ->principais()
            ->where('id', '!=', $subcategoria->id)
            ->ativas()
            ->ordenado()
            ->get();

        return view('comerciantes.produtos.subcategorias.edit', compact(
            'subcategoria',
            'categorias',
            'subcategoriasPai'
        ));
    }

    public function update(Request $request, ProdutoSubcategoria $subcategoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($subcategoria->empresa_id !== $empresaId) {
            abort(404);
        }

        $validated = $request->validate([
            'categoria_id' => 'required|exists:produto_categorias,id',
            'parent_id' => 'nullable|exists:produto_subcategorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('produto_subcategorias')->where(function ($query) use ($empresaId) {
                    return $query->where('empresa_id', $empresaId);
                })->ignore($subcategoria->id)
            ],
            'icone' => 'nullable|string|max:100',
            'cor_fundo' => 'nullable|string|max:7',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ordem' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255'
        ]);

        // Validar categoria pertence à empresa
        ProdutoCategoria::porEmpresa($empresaId)->findOrFail($validated['categoria_id']);

        // Validar parent pertence à empresa e categoria
        if (!empty($validated['parent_id'])) {
            ProdutoSubcategoria::porEmpresa($empresaId)
                ->porCategoria($validated['categoria_id'])
                ->where('id', '!=', $subcategoria->id)
                ->findOrFail($validated['parent_id']);

            // Evitar loop infinito
            if ($this->criariaLoop($subcategoria, $validated['parent_id'])) {
                return back()->withErrors(['parent_id' => 'Esta seleção criaria um loop hierárquico.']);
            }
        }

        // Upload da imagem
        if ($request->hasFile('imagem')) {
            // Deletar imagem anterior
            if ($subcategoria->imagem_url) {
                Storage::disk('public')->delete($subcategoria->imagem_url);
            }

            $path = $request->file('imagem')->store('produtos/subcategorias', 'public');
            $validated['imagem_url'] = $path;
        }

        $subcategoria->update($validated);

        return redirect()
            ->route('comerciantes.produtos.subcategorias.index')
            ->with('success', 'Subcategoria atualizada com sucesso!');
    }

    public function destroy(ProdutoSubcategoria $subcategoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($subcategoria->empresa_id !== $empresaId) {
            abort(404);
        }

        if (!$subcategoria->podeSerDeletada()) {
            return back()->withErrors(['erro' => 'Esta subcategoria não pode ser deletada pois possui produtos ou subcategorias filhas associadas.']);
        }

        // Deletar imagem se existir
        if ($subcategoria->imagem_url) {
            Storage::disk('public')->delete($subcategoria->imagem_url);
        }

        $subcategoria->delete();

        return redirect()
            ->route('comerciantes.produtos.subcategorias.index')
            ->with('success', 'Subcategoria deletada com sucesso!');
    }

    // AJAX: Buscar subcategorias por categoria
    public function porCategoria(Request $request)
    {
        $empresaId = session('empresa_id', 1);
        $categoriaId = $request->categoria_id;

        $subcategorias = ProdutoSubcategoria::porEmpresa($empresaId)
            ->porCategoria($categoriaId)
            ->ativas()
            ->ordenado()
            ->get(['id', 'nome', 'parent_id']);

        return response()->json($subcategorias);
    }

    // AJAX: Buscar subcategorias principais por categoria
    public function principaisPorCategoria(Request $request)
    {
        $empresaId = session('empresa_id', 1);
        $categoriaId = $request->categoria_id;

        $subcategorias = ProdutoSubcategoria::porEmpresa($empresaId)
            ->porCategoria($categoriaId)
            ->principais()
            ->ativas()
            ->ordenado()
            ->get(['id', 'nome']);

        return response()->json($subcategorias);
    }

    // AJAX: Atualizar ordem
    public function atualizarOrdem(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:produto_subcategorias,id',
            'items.*.ordem' => 'required|integer|min:1'
        ]);

        foreach ($validated['items'] as $item) {
            ProdutoSubcategoria::porEmpresa($empresaId)
                ->where('id', $item['id'])
                ->update(['ordem' => $item['ordem']]);
        }

        return response()->json(['success' => true]);
    }

    // AJAX: Toggle ativo
    public function toggleAtivo(ProdutoSubcategoria $subcategoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($subcategoria->empresa_id !== $empresaId) {
            abort(404);
        }

        $subcategoria->update(['ativo' => !$subcategoria->ativo]);

        return response()->json([
            'success' => true,
            'ativo' => $subcategoria->ativo
        ]);
    }

    // Método auxiliar para verificar loops hierárquicos
    private function criariaLoop($subcategoria, $novoParentId)
    {
        if (!$novoParentId) {
            return false;
        }

        // Se o novo pai é a própria subcategoria
        if ($novoParentId == $subcategoria->id) {
            return true;
        }

        // Se o novo pai é um filho da subcategoria atual
        $todosFilhos = $subcategoria->getTodosFilhos();
        return $todosFilhos->contains('id', $novoParentId);
    }
}
