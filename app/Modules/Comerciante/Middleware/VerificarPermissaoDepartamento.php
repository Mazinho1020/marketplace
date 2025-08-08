<?php

namespace App\Modules\Comerciante\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificarPermissaoDepartamento
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $acao = 'visualizar')
    {
        // Para sistemas sem autenticação completa, vamos simular permissões básicas
        $empresaId = $request->get('empresa_id', $request->route('empresaId', 1));

        // Verificar se a empresa existe
        $empresaExiste = DB::table('empresas')->where('id', $empresaId)->exists();

        if (!$empresaExiste) {
            abort(403, 'Empresa não encontrada ou sem permissão de acesso');
        }

        // Simular verificação de permissões por ação
        $permissoesPermitidas = [
            'visualizar' => ['admin', 'gerente', 'usuario'],
            'criar' => ['admin', 'gerente'],
            'editar' => ['admin', 'gerente'],
            'excluir' => ['admin']
        ];

        // Simular perfil do usuário (em produção viria da sessão/auth)
        $perfilUsuario = $request->get('perfil', 'usuario'); // Padrão: usuario

        if (!in_array($perfilUsuario, $permissoesPermitidas[$acao] ?? [])) {
            abort(403, "Sem permissão para {$acao} departamentos");
        }

        return $next($request);
    }
}
