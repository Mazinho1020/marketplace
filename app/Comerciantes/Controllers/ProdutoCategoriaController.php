<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProdutoCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdutoCategoriaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_id', 1);

        $categorias = ProdutoCategoria::with(['subcategorias'])
            ->porEmpresa($empresaId)
            ->principais()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->paginate(20);

        return view('comerciantes.produtos.categorias.index', compact('categorias'));
    }

    public function create()
    {
        $empresaId = session('empresa_id', 1);
        $categoriasPai = ProdutoCategoria::porEmpresa($empresaId)->principais()->ativas()->orderBy('nome')->get();

        return view('comerciantes.produtos.categorias.create', compact('categoriasPai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string|max:100',
            'cor' => 'nullable|string|max:7',
            'categoria_pai_id' => 'nullable|exists:produto_categorias,id'
        ]);

        $empresaId = session('empresa_id', 1);

        $categoria = ProdutoCategoria::create([
            'empresa_id' => $empresaId,
            'categoria_pai_id' => $request->categoria_pai_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'slug' => Str::slug($request->nome),
            'icone' => $request->icone ?: 'fas fa-tag',
            'cor' => $request->cor ?: '#007bff',
            'ordem' => $this->getProximaOrdem($empresaId, $request->categoria_pai_id),
            'ativo' => true,
            'sync_status' => 'pendente'
        ]);

        return redirect()
            ->route('comerciantes.produtos.categorias.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(ProdutoCategoria $categoria)
    {
        $this->verificarEmpresaCategoria($categoria);

        $empresaId = session('empresa_id', 1);
        $categoriasPai = ProdutoCategoria::porEmpresa($empresaId)
            ->principais()
            ->ativas()
            ->where('id', '!=', $categoria->id)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.categorias.edit', compact('categoria', 'categoriasPai'));
    }

    public function update(Request $request, ProdutoCategoria $categoria)
    {
        $this->verificarEmpresaCategoria($categoria);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'icone' => 'nullable|string|max:100',
            'cor' => 'nullable|string|max:7',
            'categoria_pai_id' => 'nullable|exists:produto_categorias,id'
        ]);

        $categoria->update([
            'categoria_pai_id' => $request->categoria_pai_id,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'slug' => Str::slug($request->nome),
            'icone' => $request->icone ?: $categoria->icone,
            'cor' => $request->cor ?: $categoria->cor,
            'ativo' => $request->has('ativo'),
            'sync_status' => 'pendente'
        ]);

        return redirect()
            ->route('comerciantes.produtos.categorias.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(ProdutoCategoria $categoria)
    {
        $this->verificarEmpresaCategoria($categoria);

        if ($categoria->produtos()->count() > 0) {
            return back()->withErrors(['erro' => 'Não é possível excluir uma categoria que possui produtos.']);
        }

        $categoria->delete();

        return redirect()
            ->route('comerciantes.produtos.categorias.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }

    private function verificarEmpresaCategoria(ProdutoCategoria $categoria)
    {
        $empresaId = session('empresa_id', 1);

        if ($categoria->empresa_id !== $empresaId) {
            abort(403, 'Categoria não pertence à sua empresa.');
        }
    }

    private function getProximaOrdem($empresaId, $categoriaPaiId = null)
    {
        return ProdutoCategoria::porEmpresa($empresaId)
            ->where('categoria_pai_id', $categoriaPaiId)
            ->max('ordem') + 1;
    }
}
