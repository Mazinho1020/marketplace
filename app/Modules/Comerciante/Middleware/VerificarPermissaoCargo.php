<?php

namespace App\Modules\Comerciante\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VerificarPermissaoCargo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $action
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $action = 'visualizar')
    {
        // Verificar se usuário está autenticado
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Não autenticado'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar se empresa existe (se fornecida)
        $empresaId = $request->get('empresa_id') ?? $request->route('empresa_id');
        if ($empresaId) {
            $empresa = DB::table('empresas')->where('id', $empresaId)->first();
            if (!$empresa) {
                abort(404, 'Empresa não encontrada');
            }
        }

        // Definir permissões por ação
        $permissoes = [
            'visualizar' => ['admin', 'gerente', 'usuario'],
            'criar' => ['admin', 'gerente'],
            'editar' => ['admin', 'gerente'],
            'excluir' => ['admin']
        ];

        // Simular papel do usuário (em produção, buscar da base de dados)
        $papel = $this->obterPapelUsuario($user, $empresaId);

        // Verificar se o papel tem permissão para a ação
        if (!isset($permissoes[$action]) || !in_array($papel, $permissoes[$action])) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Você não tem permissão para ' . $action . ' cargos',
                    'required_roles' => $permissoes[$action] ?? []
                ], 403);
            }

            abort(403, 'Você não tem permissão para ' . $action . ' cargos');
        }

        return $next($request);
    }

    /**
     * Obter papel do usuário para a empresa específica
     * 
     * @param  mixed  $user
     * @param  int|null  $empresaId
     * @return string
     */
    private function obterPapelUsuario($user, $empresaId = null)
    {
        // Verificar se é administrador do sistema
        if ($user->is_admin ?? false) {
            return 'admin';
        }

        // Verificar na tabela de permissões de usuário para a empresa
        if ($empresaId) {
            $permissao = DB::table('usuario_empresas')
                ->where('usuario_id', $user->id)
                ->where('empresa_id', $empresaId)
                ->first();

            if ($permissao) {
                return $permissao->papel ?? 'usuario';
            }
        }

        // Verificar permissões gerais do usuário
        $permissaoGeral = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission', 'like', '%cargo%')
            ->orWhere('permission', 'comerciante.cargos%')
            ->first();

        if ($permissaoGeral) {
            if (
                str_contains($permissaoGeral->permission, 'gerenciar') ||
                str_contains($permissaoGeral->permission, 'admin')
            ) {
                return 'gerente';
            }
        }

        // Papel padrão baseado no tipo de usuário
        if (isset($user->tipo)) {
            switch ($user->tipo) {
                case 'administrador':
                case 'admin':
                    return 'admin';
                case 'gerente':
                case 'supervisor':
                    return 'gerente';
                default:
                    return 'usuario';
            }
        }

        // Para desenvolvimento/teste - papel baseado no ID do usuário
        if ($user->id == 1) {
            return 'admin';
        } elseif ($user->id <= 5) {
            return 'gerente';
        }

        return 'usuario';
    }
}
