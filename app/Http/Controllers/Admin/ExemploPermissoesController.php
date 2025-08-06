<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User\EmpresaUsuario;
use App\Services\Permission\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * EXEMPLO PRÁTICO DE USO DO SISTEMA DE PERMISSÕES
 * 
 * Este controller demonstra como usar o sistema de permissões
 * em diferentes cenários do marketplace.
 */
class ExemploPermissoesController extends Controller
{
    protected PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * EXEMPLO 1: Dashboard com permissões
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Verificar permissão básica
        if (!$user->hasPermission('dashboard.visualizar')) {
            abort(403, 'Sem permissão para acessar o dashboard');
        }

        $data = [];

        // Carregar dados baseado nas permissões do usuário
        if ($user->hasPermission('vendas.relatorios')) {
            $data['vendas'] = $this->getVendasData();
        }

        if ($user->hasPermission('financeiro.relatorios')) {
            $data['financeiro'] = $this->getFinanceiroData();
        }

        if ($user->hasPermission('estoque.visualizar')) {
            $data['estoque'] = $this->getEstoqueData();
        }

        // Verificar se é gerente ou admin para mostrar dados gerenciais
        if ($user->hasAnyPermission(['usuarios.listar', 'configuracoes.empresa'])) {
            $data['gerencial'] = $this->getDadosGerenciais();
        }

        return view('admin.dashboard', compact('data'));
    }

    /**
     * EXEMPLO 2: Gestão de usuários com diferentes níveis
     */
    public function gerenciarUsuarios(Request $request)
    {
        $user = Auth::user();

        // Verificar permissão básica para listar usuários
        if (!$user->hasPermission('usuarios.listar')) {
            return redirect()->back()->with('error', 'Sem permissão para gerenciar usuários');
        }

        $query = EmpresaUsuario::where('empresa_id', $user->empresa_id);

        // Se não é admin, só pode ver usuários do mesmo nível ou inferior
        if (!$user->hasRole('admin')) {
            $userLevel = $user->getTipoPrincipal()?->nivel_acesso ?? 0;
            $query->whereHas('tipoPrincipal', function ($q) use ($userLevel) {
                $q->where('nivel_acesso', '<=', $userLevel);
            });
        }

        $usuarios = $query->with(['tipoPrincipal', 'empresa'])->paginate(15);

        // Verificar quais ações o usuário pode fazer
        $permissions = [
            'can_create' => $user->hasPermission('usuarios.criar'),
            'can_edit' => $user->hasPermission('usuarios.editar'),
            'can_delete' => $user->hasPermission('usuarios.excluir'),
            'can_manage_permissions' => $user->hasPermission('usuarios.gerenciar_permissoes'),
            'can_manage_roles' => $user->hasPermission('usuarios.gerenciar_papeis'),
        ];

        return view('admin.usuarios.index', compact('usuarios', 'permissions'));
    }

    /**
     * EXEMPLO 3: Atribuir permissões (apenas para quem pode)
     */
    public function atribuirPermissao(Request $request, EmpresaUsuario $usuario)
    {
        $currentUser = Auth::user();

        // Verificar se pode gerenciar permissões
        if (!$currentUser->hasPermission('usuarios.gerenciar_permissoes')) {
            return response()->json(['error' => 'Sem permissão para gerenciar permissões'], 403);
        }

        // Verificar se pode alterar usuário de nível igual ou superior
        $currentLevel = $currentUser->getTipoPrincipal()?->nivel_acesso ?? 0;
        $targetLevel = $usuario->getTipoPrincipal()?->nivel_acesso ?? 0;

        if ($targetLevel >= $currentLevel && !$currentUser->hasRole('super_admin')) {
            return response()->json(['error' => 'Não pode alterar usuário de nível igual ou superior'], 403);
        }

        $request->validate([
            'permission_code' => 'required|string',
            'action' => 'required|in:grant,revoke'
        ]);

        try {
            if ($request->action === 'grant') {
                $success = $this->permissionService->grantPermission(
                    $usuario,
                    $request->permission_code,
                    $currentUser
                );
            } else {
                $success = $this->permissionService->revokePermission(
                    $usuario,
                    $request->permission_code,
                    $currentUser
                );
            }

            if ($success) {
                return response()->json(['success' => 'Permissão atualizada com sucesso']);
            } else {
                return response()->json(['error' => 'Falha ao atualizar permissão'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * EXEMPLO 4: PDV com verificações específicas
     */
    public function acessarPDV()
    {
        $user = Auth::user();

        // Verificar acesso básico ao PDV
        if (!$user->hasPermission('pdv.acessar')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Sem permissão para acessar o PDV');
        }

        // Verificar permissões específicas do PDV
        $pdvPermissions = [
            'can_start_sale' => $user->hasPermission('pdv.iniciar_venda'),
            'can_finish_sale' => $user->hasPermission('pdv.finalizar_venda'),
            'can_cancel_sale' => $user->hasPermission('pdv.cancelar_venda'),
            'can_apply_discount' => $user->hasPermission('pdv.aplicar_desconto'),
        ];

        // Se não pode iniciar vendas, redirecionar
        if (!$pdvPermissions['can_start_sale']) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Sem permissão para iniciar vendas');
        }

        return view('admin.pdv.index', compact('pdvPermissions'));
    }

    /**
     * EXEMPLO 5: Relatórios com permissões granulares
     */
    public function relatorios(Request $request)
    {
        $user = Auth::user();
        $availableReports = [];

        // Verificar cada tipo de relatório
        if ($user->hasPermission('vendas.relatorios')) {
            $availableReports['vendas'] = [
                'title' => 'Relatórios de Vendas',
                'reports' => ['vendas_diarias', 'vendas_mensais', 'top_produtos']
            ];
        }

        if ($user->hasPermission('financeiro.relatorios')) {
            $availableReports['financeiro'] = [
                'title' => 'Relatórios Financeiros',
                'reports' => ['fluxo_caixa', 'contas_receber', 'contas_pagar']
            ];
        }

        if ($user->hasPermission('estoque.relatorios')) {
            $availableReports['estoque'] = [
                'title' => 'Relatórios de Estoque',
                'reports' => ['produtos_baixo_estoque', 'movimentacao_estoque']
            ];
        }

        // Se não tem nenhuma permissão de relatório
        if (empty($availableReports)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Sem permissão para acessar relatórios');
        }

        return view('admin.relatorios.index', compact('availableReports'));
    }

    /**
     * EXEMPLO 6: API para verificar permissões do usuário
     */
    public function minhasPermissoes()
    {
        $user = Auth::user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'email' => $user->email,
                'empresa_id' => $user->empresa_id
            ],
            'permissions' => $this->permissionService->getUserPermissions($user)->pluck('codigo'),
            'roles' => $this->permissionService->getUserRoles($user)->pluck('codigo'),
            'effective_permissions' => [
                'dashboard' => $user->hasPermission('dashboard.visualizar'),
                'users' => [
                    'list' => $user->hasPermission('usuarios.listar'),
                    'create' => $user->hasPermission('usuarios.criar'),
                    'edit' => $user->hasPermission('usuarios.editar'),
                    'delete' => $user->hasPermission('usuarios.excluir'),
                ],
                'pdv' => [
                    'access' => $user->hasPermission('pdv.acessar'),
                    'sell' => $user->hasPermission('pdv.iniciar_venda'),
                    'discount' => $user->hasPermission('pdv.aplicar_desconto'),
                ],
                'reports' => [
                    'sales' => $user->hasPermission('vendas.relatorios'),
                    'financial' => $user->hasPermission('financeiro.relatorios'),
                    'inventory' => $user->hasPermission('estoque.relatorios'),
                ],
                'admin' => [
                    'system' => $user->hasPermission('sistema.admin'),
                    'company_settings' => $user->hasPermission('configuracoes.empresa'),
                    'security' => $user->hasPermission('configuracoes.seguranca'),
                ]
            ]
        ]);
    }

    // Métodos auxiliares para os dados
    private function getVendasData()
    {
        return [
            'total_hoje' => 1250.50,
            'total_mes' => 35000.00,
            'vendas_pendentes' => 5
        ];
    }

    private function getFinanceiroData()
    {
        return [
            'saldo_caixa' => 15000.00,
            'contas_receber' => 8500.00,
            'contas_pagar' => 3200.00
        ];
    }

    private function getEstoqueData()
    {
        return [
            'produtos_baixo_estoque' => 12,
            'valor_total_estoque' => 75000.00,
            'movimentacoes_hoje' => 25
        ];
    }

    private function getDadosGerenciais()
    {
        return [
            'usuarios_ativos' => 45,
            'vendas_mes_anterior' => 42000.00,
            'crescimento' => 15.5
        ];
    }
}
