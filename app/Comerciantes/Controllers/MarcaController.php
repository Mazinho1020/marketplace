<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\Marca;
use App\Comerciantes\Models\EmpresaUsuario;

/**
 * Controller para gerenciar marcas
 */
class MarcaController extends Controller
{
    /**
     * Lista todas as marcas do usuário logado
     */
    public function index()
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        $marcas = $user->marcasProprietario()
            ->latest()
            ->paginate(12);

        return view('comerciantes.marcas.index', compact('marcas'));
    }

    /**
     * Mostra o formulário de criação de marca
     */
    public function create()
    {
        return view('comerciantes.marcas.create');
    }

    /**
     * Salva uma nova marca
     */
    public function store(Request $request)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        $request->validate([
            'nome' => 'required|string|max:200',
            'descricao' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cor_primaria' => 'nullable|string|max:7',
            'cor_secundaria' => 'nullable|string|max:7',
        ]);

        $dadosMarca = $request->only(['nome', 'descricao']);
        $dadosMarca['pessoa_fisica_id'] = $user->id;

        // Monta identidade visual
        if ($request->cor_primaria || $request->cor_secundaria) {
            $dadosMarca['identidade_visual'] = [
                'cor_primaria' => $request->cor_primaria ?: '#2ECC71',
                'cor_secundaria' => $request->cor_secundaria ?: '#27AE60',
            ];
        }

        // Upload do logo se fornecido
        if ($request->hasFile('logo')) {
            $dadosMarca['logo_url'] = $request->file('logo')->store('marcas/logos', 'public');
        }

        $marca = Marca::create($dadosMarca);

        return redirect()->route('comerciantes.marcas.show', $marca)
            ->with('success', 'Marca criada com sucesso!');
    }

    /**
     * Mostra detalhes de uma marca específica
     */
    public function show(Marca $marca)
    {
        // Verifica se o usuário é proprietário da marca
        if ($marca->pessoa_fisica_id !== Auth::guard('comerciante')->id()) {
            abort(403, 'Acesso negado a esta marca.');
        }

        $marca->load(['proprietario']);

        return view('comerciantes.marcas.show', compact('marca'));
    }

    /**
     * Mostra o formulário de edição de marca
     */
    public function edit(Marca $marca)
    {
        // Verifica se o usuário é proprietário da marca
        if ($marca->pessoa_fisica_id !== Auth::guard('comerciante')->id()) {
            abort(403, 'Acesso negado a esta marca.');
        }

        return view('comerciantes.marcas.edit', compact('marca'));
    }

    /**
     * Atualiza uma marca
     */
    public function update(Request $request, Marca $marca)
    {
        // Verifica se o usuário é proprietário da marca
        if ($marca->pessoa_fisica_id !== Auth::guard('comerciante')->id()) {
            abort(403, 'Acesso negado a esta marca.');
        }

        $request->validate([
            'nome' => 'required|string|max:200',
            'descricao' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cor_primaria' => 'nullable|string|max:7',
            'cor_secundaria' => 'nullable|string|max:7',
            'status' => 'required|in:ativa,inativa,suspensa',
        ]);

        $dadosMarca = $request->only(['nome', 'descricao', 'status']);

        // Monta identidade visual
        if ($request->cor_primaria || $request->cor_secundaria) {
            $dadosMarca['identidade_visual'] = [
                'cor_primaria' => $request->cor_primaria ?: '#2ECC71',
                'cor_secundaria' => $request->cor_secundaria ?: '#27AE60',
            ];
        }

        // Upload do logo se fornecido
        if ($request->hasFile('logo')) {
            $dadosMarca['logo_url'] = $request->file('logo')->store('marcas/logos', 'public');
        }

        $marca->update($dadosMarca);

        return redirect()->route('comerciantes.marcas.show', $marca)
            ->with('success', 'Marca atualizada com sucesso!');
    }

    /**
     * Remove uma marca (só se não tiver empresas)
     */
    public function destroy(Marca $marca)
    {
        // Verifica se o usuário é proprietário da marca
        if ($marca->pessoa_fisica_id !== Auth::guard('comerciante')->id()) {
            abort(403, 'Acesso negado a esta marca.');
        }

        // TODO: Verificar se existem empresas vinculadas quando implementar relação

        $marca->delete();

        return redirect()->route('comerciantes.marcas.index')
            ->with('success', 'Marca removida com sucesso!');
    }
}
