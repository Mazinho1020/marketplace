<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Controller;
use App\Models\Permission\EmpresaPermissao;
use App\Services\Permission\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Lista todas as permissões
     */
    public function index(Request $request)
    {
        $empresaId = Auth::user()->empresa_id;

        $query = EmpresaPermissao::empresa($empresaId)
            ->with(['empresa']);

        // Filtros
        if ($request->filled('categoria')) {
            $query->categoria($request->categoria);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $permissoes = $query->paginate(20);
        $categorias = EmpresaPermissao::getCategorias($empresaId);

        return view('admin.permissions.index', compact('permissoes', 'categorias'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create()
    {
        $categorias = EmpresaPermissao::getCategorias(Auth::user()->empresa_id);

        return view('admin.permissions.create', compact('categorias'));
    }

    /**
     * Armazena nova permissão
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'codigo' => 'required|string|max:100|regex:/^[a-z0-9_.]+$/',
            'descricao' => 'nullable|string|max:1000',
            'categoria' => 'nullable|string|max:50'
        ]);

        $empresaId = Auth::user()->empresa_id;

        // Verificar se código já existe
        $exists = EmpresaPermissao::where('codigo', $request->codigo)
            ->where(function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId)
                    ->orWhere('is_sistema', true);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['codigo' => 'Este código já está em uso.']);
        }

        EmpresaPermissao::create([
            'nome' => $request->nome,
            'codigo' => $request->codigo,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'is_sistema' => false,
            'empresa_id' => $empresaId,
            'sync_status' => 'pendente'
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissão criada com sucesso!');
    }

    /**
     * Mostra detalhes da permissão
     */
    public function show(EmpresaPermissao $permission)
    {
        $permission->load(['empresa', 'papelPermissoes.papel', 'usuarioPermissoes.usuario']);

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * API: Lista permissões do usuário logado
     */
    public function myPermissions()
    {
        $user = Auth::user();
        $permissions = $this->permissionService->getUserPermissions($user);

        return response()->json([
            'permissions' => $permissions->pluck('codigo'),
            'roles' => $this->permissionService->getUserRoles($user)->pluck('codigo')
        ]);
    }
}
