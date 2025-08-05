<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Models\Marca;

/**
 * Controller para gerenciar empresas
 */
class EmpresaController extends Controller
{
    /**
     * Lista todas as empresas do usuário logado
     */
    public function index(Request $request)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Base query - empresas que o usuário é proprietário
        $query = $user->empresasProprietario()->with(['marca']);

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('cnpj', 'like', "%{$busca}%")
                    ->orWhere('endereco_cidade', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('marca_id')) {
            $query->where('marca_id', $request->marca_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginação
        $empresas = $query->latest()->paginate(12);

        // Marcas para o filtro
        $marcas = $user->marcasProprietario()->where('status', 'ativa')->get();

        return view('comerciantes.empresas.index', compact('empresas', 'marcas'));
    }

    /**
     * Mostra o formulário de criação de empresa
     */
    public function create()
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Busca as marcas do usuário para selecionar
        $marcas = $user->marcasProprietario()->where('status', 'ativa')->get();

        return view('comerciantes.empresas.create', compact('marcas'));
    }

    /**
     * Salva uma nova empresa
     */
    public function store(Request $request)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        $request->validate([
            'nome' => 'required|string|max:200',
            'nome_fantasia' => 'nullable|string|max:200',
            'cnpj' => 'nullable|string|max:18|unique:empresas,cnpj',
            'marca_id' => 'nullable|exists:marcas,id',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'website' => 'nullable|url|max:300',

            // Endereço
            'endereco_cep' => 'nullable|string|max:9',
            'endereco_logradouro' => 'nullable|string|max:300',
            'endereco_numero' => 'nullable|string|max:20',
            'endereco_complemento' => 'nullable|string|max:100',
            'endereco_bairro' => 'nullable|string|max:100',
            'endereco_cidade' => 'nullable|string|max:100',
            'endereco_estado' => 'nullable|string|max:2',
        ]);

        // Verifica se a marca pertence ao usuário
        if ($request->marca_id) {
            $marca = $user->marcasProprietario()->find($request->marca_id);
            if (!$marca) {
                return back()->withErrors(['marca_id' => 'Marca não encontrada ou não pertence a você.']);
            }
        }

        $dadosEmpresa = $request->except(['_token']);
        $dadosEmpresa['proprietario_id'] = $user->id;

        $empresa = Empresa::create($dadosEmpresa);

        return redirect()->route('comerciantes.empresas.show', $empresa)
            ->with('success', 'Empresa criada com sucesso!');
    }

    /**
     * Mostra detalhes de uma empresa específica
     */
    public function show(Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica se o usuário tem permissão para acessar esta empresa
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado a esta empresa.');
        }

        $empresa->load(['marca', 'proprietario', 'usuariosVinculados']);

        return view('comerciantes.empresas.show', compact('empresa'));
    }

    /**
     * Mostra o formulário de edição de empresa
     */
    public function edit(Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica se o usuário tem permissão para editar esta empresa
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado a esta empresa.');
        }

        // Busca as marcas do usuário para selecionar
        $marcas = $user->marcasProprietario()->where('status', 'ativa')->get();

        return view('comerciantes.empresas.edit', compact('empresa', 'marcas'));
    }

    /**
     * Atualiza uma empresa
     */
    public function update(Request $request, Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica se o usuário tem permissão para editar esta empresa
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado a esta empresa.');
        }

        $request->validate([
            'nome' => 'required|string|max:200',
            'nome_fantasia' => 'nullable|string|max:200',
            'cnpj' => 'nullable|string|max:18|unique:empresas_marketplace,cnpj,' . $empresa->id,
            'slug' => 'required|string|max:200|unique:empresas_marketplace,slug,' . $empresa->id,
            'marca_id' => 'nullable|exists:marcas,id',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'website' => 'nullable|url|max:300',
            'status' => 'required|in:ativa,inativa,suspensa',

            // Endereço
            'endereco_cep' => 'nullable|string|max:9',
            'endereco_logradouro' => 'nullable|string|max:300',
            'endereco_numero' => 'nullable|string|max:20',
            'endereco_complemento' => 'nullable|string|max:100',
            'endereco_bairro' => 'nullable|string|max:100',
            'endereco_cidade' => 'nullable|string|max:100',
            'endereco_estado' => 'nullable|string|max:2',
        ]);

        // Verifica se a marca pertence ao usuário
        if ($request->marca_id) {
            $marca = $user->marcasProprietario()->find($request->marca_id);
            if (!$marca) {
                return back()->withErrors(['marca_id' => 'Marca não encontrada ou não pertence a você.']);
            }
        }

        $dadosEmpresa = $request->except(['_token', '_method', 'horario']);

        // Processa horário de funcionamento
        if ($request->has('horario')) {
            $horarioFuncionamento = [];
            foreach ($request->horario as $dia => $dados) {
                if (isset($dados['fechado']) && $dados['fechado']) {
                    $horarioFuncionamento[$dia] = ['fechado' => true];
                } elseif (!empty($dados['abertura']) && !empty($dados['fechamento'])) {
                    $horarioFuncionamento[$dia] = [
                        'abertura' => $dados['abertura'],
                        'fechamento' => $dados['fechamento'],
                        'fechado' => false
                    ];
                }
            }
            $dadosEmpresa['horario_funcionamento'] = json_encode($horarioFuncionamento);
        }

        $empresa->update($dadosEmpresa);

        return redirect()->route('comerciantes.empresas.show', $empresa)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Remove uma empresa
     */
    public function destroy(Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica se o usuário é proprietário da empresa
        if ($empresa->proprietario_id !== $user->id) {
            abort(403, 'Apenas o proprietário pode excluir a empresa.');
        }

        $empresa->delete();

        return redirect()->route('comerciantes.empresas.index')
            ->with('success', 'Empresa removida com sucesso!');
    }

    /**
     * Mostra a página de gerenciamento de usuários vinculados
     */
    public function usuarios(Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica se o usuário tem permissão para gerenciar usuários desta empresa
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado para gerenciar usuários desta empresa.');
        }

        $empresa->load(['usuariosVinculados', 'proprietario', 'marca']);

        return view('comerciantes.empresas.usuarios', compact('empresa'));
    }

    /**
     * Adiciona um usuário à empresa
     */
    public function adicionarUsuario(Request $request, Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica permissão
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'user_email' => 'required|email|exists:empresa_usuarios,email',
            'perfil' => 'required|in:administrador,gerente,colaborador',
            'permissoes' => 'nullable|array',
        ]);

        $usuarioParaVincular = EmpresaUsuario::where('email', $request->user_email)->first();

        // Verifica se já não está vinculado
        if ($empresa->usuariosVinculados()->where('user_id', $usuarioParaVincular->id)->exists()) {
            return back()->withErrors(['user_email' => 'Usuário já está vinculado a esta empresa.']);
        }

        // Cria o vínculo
        $empresa->usuariosVinculados()->attach($usuarioParaVincular->id, [
            'perfil' => $request->perfil,
            'permissoes' => json_encode($request->permissoes ?: []),
            'status' => 'ativo',
            'data_vinculo' => now(),
        ]);

        return back()->with('success', 'Usuário vinculado com sucesso!');
    }

    /**
     * Edita um usuário vinculado
     */
    public function editarUsuario(Request $request, Empresa $empresa, EmpresaUsuario $user)
    {
        // Implementar edição de usuário vinculado
        return back()->with('info', 'Funcionalidade em desenvolvimento.');
    }

    /**
     * Remove um usuário da empresa
     */
    public function removerUsuario(Request $request, Empresa $empresa, EmpresaUsuario $userParaRemover)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica permissão
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado.');
        }

        // Não pode remover o proprietário
        if ($userParaRemover->id === $empresa->proprietario_id) {
            return back()->withErrors(['error' => 'Não é possível remover o proprietário da empresa.']);
        }

        $empresa->usuariosVinculados()->detach($userParaRemover->id);

        return back()->with('success', 'Usuário removido da empresa com sucesso!');
    }
}
