<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $query = $user->empresasProprietario();

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('razao_social', 'like', "%{$busca}%")
                    ->orWhere('nome_fantasia', 'like', "%{$busca}%")
                    ->orWhere('cnpj', 'like', "%{$busca}%")
                    ->orWhere('cidade', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Paginação
        $empresas = $query->latest()->paginate(12);

        return view('comerciantes.empresas.index', compact('empresas'));
    }

    /**
     * Mostra o formulário de criação de empresa
     */
    public function create()
    {
        // Buscar todas as marcas disponíveis
        $marcas = Marca::orderBy('nome')->get();

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
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:empresas,cnpj',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'site' => 'nullable|url|max:255',

            // Endereço
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'uf' => 'nullable|string|max:2',
        ]);

        $dadosEmpresa = $request->except(['_token']);

        $empresa = Empresa::create($dadosEmpresa);

        // Criar vínculo do usuário atual como proprietário da empresa
        $empresa->usuariosVinculados()->attach($user->id, [
            'perfil' => 'proprietario',
            'status' => 'ativo',
            'permissoes' => json_encode([]),
            'data_vinculo' => now(),
        ]);

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

        // Carrega relacionamentos disponíveis neste modelo
        $empresa->load(['usuariosVinculados']);

        // Estatísticas de pessoas por tipo
        $estatisticas = [
            'clientes' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('tipo', 'like', '%cliente%')
                ->count(),
            'funcionarios' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('tipo', 'like', '%funcionario%')
                ->count(),
            'fornecedores' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('tipo', 'like', '%fornecedor%')
                ->count(),
            'entregadores' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('tipo', 'like', '%entregador%')
                ->count(),
            'departamentos' => DB::table('pessoas_departamentos')
                ->where('empresa_id', $empresa->id)
                ->where('ativo', true)
                ->count(),
            'cargos' => DB::table('pessoas_cargos')
                ->where('empresa_id', $empresa->id)
                ->where('ativo', true)
                ->count()
        ];

        // Estatísticas de status
        $estatisticasStatus = [
            'pessoas_ativas' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('status', 'ativo')
                ->count(),
            'pessoas_inativas' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('status', '!=', 'ativo')
                ->count(),
            'funcionarios_ativos' => DB::table('pessoas')
                ->where('empresa_id', $empresa->id)
                ->where('tipo', 'like', '%funcionario%')
                ->where('status', 'ativo')
                ->count()
        ];

        return view('comerciantes.empresas.show', compact('empresa', 'estatisticas', 'estatisticasStatus'));
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

        // Buscar todas as marcas disponíveis
        $marcas = Marca::orderBy('nome')->get();

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
            'razao_social' => 'required|string|max:200',
            'nome_fantasia' => 'nullable|string|max:200',
            'cnpj' => 'nullable|string|max:18|unique:empresas,cnpj,' . $empresa->id,
            'inscricao_estadual' => 'nullable|string|max:50',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'site' => 'nullable|url|max:300',
            'status' => 'required|in:ativo,inativo,suspenso',

            // Endereço
            'cep' => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:300',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
        ]);

        $dadosEmpresa = $request->except(['_token', '_method']);

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

        // Carrega a relação com dados do pivot
        $empresa->load(['usuariosVinculados' => function ($query) {
            $query->withPivot(['perfil', 'status', 'permissoes', 'data_vinculo']);
        }, 'proprietario', 'marca']);

        // Debug: Se solicitado, mostrar informações detalhadas
        if (request()->has('debug')) {
            dd([
                'empresa_id' => $empresa->id,
                'empresa_nome' => $empresa->nome_fantasia,
                'usuariosVinculados_loaded' => $empresa->relationLoaded('usuariosVinculados'),
                'usuariosVinculados_count' => $empresa->usuariosVinculados ? $empresa->usuariosVinculados->count() : 0,
                'usuariosVinculados_data' => $empresa->usuariosVinculados ? $empresa->usuariosVinculados->toArray() : [],
                'raw_query' => $empresa->usuariosVinculados()->toSql(),
                'raw_bindings' => $empresa->usuariosVinculados()->getBindings(),
                'pivot_table_exists' => DB::select("SHOW TABLES LIKE 'empresa_user_vinculos'"),
                'raw_data' => DB::select("SELECT * FROM empresa_user_vinculos WHERE empresa_id = ?", [$empresa->id])
            ]);
        }

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
    public function editarUsuario(Request $request, Empresa $empresa, EmpresaUsuario $userVinculado)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica permissão
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'perfil' => 'required|in:administrador,gerente,colaborador',
            'permissoes' => 'nullable|array',
            'status' => 'required|in:ativo,inativo',
        ]);

        // Não pode alterar o perfil do proprietário
        $vinculo = $empresa->usuariosVinculados()->where('user_id', $userVinculado->id)->first();

        if (!$vinculo) {
            return back()->withErrors(['error' => 'Usuário não está vinculado a esta empresa.']);
        }

        if ($vinculo->pivot->perfil === 'proprietario') {
            return back()->withErrors(['error' => 'Não é possível alterar o perfil do proprietário.']);
        }

        // Atualizar o vínculo
        $empresa->usuariosVinculados()->updateExistingPivot($userVinculado->id, [
            'perfil' => $request->perfil,
            'permissoes' => json_encode($request->permissoes ?: []),
            'status' => $request->status,
        ]);

        return back()->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Mostra os dados de um usuário vinculado para edição (via AJAX)
     */
    public function mostrarUsuario(Empresa $empresa, EmpresaUsuario $userVinculado)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica permissão
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado.');
        }

        $vinculo = $empresa->usuariosVinculados()->where('user_id', $userVinculado->id)->first();

        if (!$vinculo) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        return response()->json([
            'id' => $userVinculado->id,
            'nome' => $userVinculado->nome ?? $userVinculado->username,
            'email' => $userVinculado->email,
            'perfil' => $vinculo->pivot->perfil,
            'status' => $vinculo->pivot->status,
            'permissoes' => json_decode($vinculo->pivot->permissoes ?? '[]', true),
            'data_vinculo' => $vinculo->pivot->data_vinculo,
        ]);
    }

    /**
     * Cria um novo usuário e o vincula à empresa
     */
    public function criarEVincularUsuario(Request $request, Empresa $empresa)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        // Verifica permissão
        if (!$user->temPermissaoEmpresa($empresa->id)) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:empresa_usuarios,email',
            'username' => 'required|string|max:100|unique:empresa_usuarios,username',
            'senha' => 'required|string|min:6|confirmed',
            'perfil' => 'required|in:administrador,gerente,colaborador',
            'permissoes' => 'nullable|array',
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
        ]);

        // Criar o usuário
        $novoUsuario = EmpresaUsuario::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'nome' => $request->nome,
            'username' => $request->username,
            'email' => $request->email,
            'senha' => bcrypt($request->senha),
            'telefone' => $request->telefone,
            'cargo' => $request->cargo,
            'status' => 'ativo',
        ]);

        // Vincular à empresa
        $empresa->usuariosVinculados()->attach($novoUsuario->id, [
            'perfil' => $request->perfil,
            'permissoes' => json_encode($request->permissoes ?: []),
            'status' => 'ativo',
            'data_vinculo' => now(),
        ]);

        return back()->with('success', 'Usuário criado e vinculado com sucesso!');
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
