<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProdutoMarca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoMarcaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_id', 1);

        $marcas = ProdutoMarca::porEmpresa($empresaId)
            ->orderBy('nome')
            ->paginate(20);

        return view('comerciantes.produtos.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('comerciantes.produtos.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'site' => 'nullable|url',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024'
        ]);

        $empresaId = session('empresa_id', 1);

        $dadosMarca = [
            'empresa_id' => $empresaId,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'site' => $request->site,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'ativo' => true,
            'sync_status' => 'pendente'
        ];

        // Upload do logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $nomeArquivo = time() . '_' . $logo->getClientOriginalName();
            $caminho = $logo->storeAs('marcas', $nomeArquivo, 'public');
            $dadosMarca['logo'] = $nomeArquivo;
        }

        $marca = ProdutoMarca::create($dadosMarca);

        return redirect()
            ->route('comerciantes.produtos.marcas.index')
            ->with('success', 'Marca criada com sucesso!');
    }

    public function edit(ProdutoMarca $marca)
    {
        $this->verificarEmpresaMarca($marca);

        return view('comerciantes.produtos.marcas.edit', compact('marca'));
    }

    public function update(Request $request, ProdutoMarca $marca)
    {
        $this->verificarEmpresaMarca($marca);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'site' => 'nullable|url',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024'
        ]);

        $dadosMarca = [
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'site' => $request->site,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'ativo' => $request->has('ativo'),
            'sync_status' => 'pendente'
        ];

        // Upload do novo logo
        if ($request->hasFile('logo')) {
            // Remover logo anterior
            if ($marca->logo) {
                Storage::disk('public')->delete('marcas/' . $marca->logo);
            }

            $logo = $request->file('logo');
            $nomeArquivo = time() . '_' . $logo->getClientOriginalName();
            $caminho = $logo->storeAs('marcas', $nomeArquivo, 'public');
            $dadosMarca['logo'] = $nomeArquivo;
        }

        $marca->update($dadosMarca);

        return redirect()
            ->route('comerciantes.produtos.marcas.index')
            ->with('success', 'Marca atualizada com sucesso!');
    }

    public function destroy(ProdutoMarca $marca)
    {
        $this->verificarEmpresaMarca($marca);

        if ($marca->produtos()->count() > 0) {
            return back()->withErrors(['erro' => 'Não é possível excluir uma marca que possui produtos.']);
        }

        // Remover logo se existir
        if ($marca->logo) {
            Storage::disk('public')->delete('marcas/' . $marca->logo);
        }

        $marca->delete();

        return redirect()
            ->route('comerciantes.produtos.marcas.index')
            ->with('success', 'Marca excluída com sucesso!');
    }

    private function verificarEmpresaMarca(ProdutoMarca $marca)
    {
        $empresaId = session('empresa_id', 1);

        if ($marca->empresa_id !== $empresaId) {
            abort(403, 'Marca não pertence à sua empresa.');
        }
    }
}
