<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\EmpresaUsuario;
use App\Comerciantes\Services\ComercianteService;

/**
 * Controller do dashboard principal dos comerciantes
 */
class DashboardController extends Controller
{
    public function __construct(
        private ComercianteService $comercianteService
    ) {}

    /**
     * Página principal do dashboard
     */
    public function index(Request $request)
    {
        // Busca o usuário autenticado usando o guard comerciante
        $user = Auth::guard('comerciante')->user();

        if (!$user) {
            return redirect()->route('comerciantes.login')
                ->withErrors(['error' => 'Usuário não encontrado.']);
        }

        // Busca dados estatísticos para o dashboard
        $dashboardData = $this->comercianteService->getDashboardData($user);

        // Busca sugestões de ações
        $sugestoes = $this->comercianteService->getSugestoesAcoes($user);

        return view('comerciantes.dashboard.index', compact('dashboardData', 'user', 'sugestoes'));
    }

    /**
     * Seleciona uma empresa específica para trabalhar
     * Salva na sessão para usar em outros controllers
     */
    public function selecionarEmpresa(Request $request, $empresaId)
    {
        // Busca o usuário autenticado usando o guard comerciante
        $user = Auth::guard('comerciante')->user();

        if (!$user) {
            return redirect()->route('comerciantes.login')
                ->withErrors(['error' => 'Usuário não encontrado.']);
        }

        // Verifica se o usuário tem permissão para acessar esta empresa
        if (!$this->comercianteService->podeAcessarEmpresa($user, $empresaId)) {
            abort(403, 'Acesso negado a esta empresa.');
        }

        // Salva o contexto da empresa na sessão
        session(['empresa_atual_id' => $empresaId]);

        return redirect()->route('comerciantes.dashboard')
            ->with('success', 'Empresa selecionada com sucesso!');
    }

    /**
     * Remove a seleção de empresa da sessão
     */
    public function limparSelecaoEmpresa(Request $request)
    {
        session()->forget('empresa_atual_id');

        return redirect()->route('comerciantes.dashboard')
            ->with('success', 'Seleção de empresa removida.');
    }

    /**
     * API endpoint para buscar estatísticas atualizadas
     * Para uso em atualizações via AJAX
     */
    public function estatisticas(Request $request)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        $dashboardData = $this->comercianteService->getDashboardData($user);

        return response()->json([
            'success' => true,
            'data' => $dashboardData['estatisticas']
        ]);
    }

    /**
     * Atualiza o progresso de configuração
     */
    public function atualizarProgresso(Request $request)
    {
        /** @var EmpresaUsuario $user */
        $user = Auth::guard('comerciante')->user();

        $progresso = $this->comercianteService->calcularProgressoConfiguracao($user);

        return response()->json([
            'success' => true,
            'progresso' => $progresso
        ]);
    }
}
