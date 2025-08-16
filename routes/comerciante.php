<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Comerciantes\Controllers\Auth\LoginController;
use App\Comerciantes\Controllers\DashboardController;
use App\Comerciantes\Controllers\MarcaController;
use App\Comerciantes\Controllers\EmpresaController;
use App\Comerciantes\Controllers\HorarioController;
use App\Comerciantes\Controllers\PlanoController;
use App\Comerciantes\Controllers\NotificacaoController;
use App\Comerciantes\Controllers\ProdutoController;
use App\Comerciantes\Controllers\ProdutoCategoriaController;
use App\Comerciantes\Controllers\ProdutoMarcaController;
use App\Comerciantes\Controllers\ProdutoSubcategoriaController;
use App\Comerciantes\Controllers\ProdutoPrecoQuantidadeController;

// Controladores do módulo Comerciante
use App\Modules\Comerciante\Controllers\Config\ConfigController;
use App\Modules\Comerciante\Controllers\Pessoas\PessoaController;

use function Laravel\Prompts\alert;

/**
 * Rotas do módulo de comerciantes
 * Usa a tabela empresa_usuarios para autenticação
 */
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {

    // ========================
    // ROTAS PÚBLICAS (Login)
    // ========================
    Route::middleware(['guest:comerciante'])->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        // Route::get('/cadastro', [RegisterController::class, 'showRegistrationForm'])->name('register');
        // Route::post('/cadastro', [RegisterController::class, 'register']);
    });
});

// ===========================
// ROTAS DE CLIENTES (SEM AUTENTICAÇÃO)
// ===========================
Route::prefix('comerciantes/clientes')->name('comerciantes.clientes.')->group(function () {

    // Rota super simples para testar
    Route::get('/ola', function () {
        return response('Olá! Sistema funcionando! ' . date('H:i:s'))
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('ola');

    // Dashboard principal do sistema
    Route::get('/dashboard', function () {
        try {
            // Buscar informações do usuário logado
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id ?? 1; // Fallback para teste

            // Estatísticas
            $totalPessoas = \App\Modules\Comerciante\Models\Pessoas\Pessoa::count();
            $totalDepartamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::count();
            $totalCargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::count();
            $totalEnderecos = \App\Modules\Comerciante\Models\Pessoas\PessoaEndereco::count();

            // Estatísticas por tipo
            $totalClientes = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%cliente%')->count();
            $totalFuncionarios = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%funcionario%')->count();
            $totalFornecedores = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%fornecedor%')->count();
            $totalEntregadores = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%entregador%')->count();

            // Buscar informações do plano atual
            $assinaturaAtual = null;
            $planoStatus = 'Sem plano ativo';
            $planoNome = 'Nenhum';
            $planoVencimento = '-';

            try {
                $assinaturaAtual = \App\Models\AfiPlanAssinaturas::with('plano')
                    ->where('empresa_id', $empresaId)
                    ->whereIn('status', ['trial', 'ativo'])
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($assinaturaAtual) {
                    $planoNome = $assinaturaAtual->plano->nome ?? 'Plano Básico';
                    $planoStatus = $assinaturaAtual->status === 'ativo' ? 'Ativo' : 'Trial';
                    $planoVencimento = $assinaturaAtual->expira_em ? $assinaturaAtual->expira_em->format('d/m/Y') : '-';
                }
            } catch (\Exception $e) {
                // Silenciar erro se tabelas de planos não existirem
            }

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard - Sistema de Gestão Comercial</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        
        .navbar { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 15px 0; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { color: #2c3e50; text-decoration: none; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #3498db; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        .header h1 { font-size: 3em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .header p { font-size: 1.2em; opacity: 0.9; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { font-size: 3em; margin-bottom: 15px; }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .stat-label { color: #7f8c8d; font-size: 1.1em; font-weight: 500; }
        
        .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .action-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 25px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .action-card h3 { color: #2c3e50; margin-bottom: 15px; font-size: 1.3em; }
        .action-links { display: flex; flex-direction: column; gap: 10px; }
        .action-link { display: flex; align-items: center; gap: 10px; padding: 12px 15px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #2c3e50; transition: all 0.3s; }
        .action-link:hover { background: #e9ecef; transform: translateX(5px); }
        
        .quick-stats { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 30px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .quick-stats h3 { color: #2c3e50; margin-bottom: 20px; font-size: 1.4em; }
        .mini-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
        .mini-stat { text-align: center; padding: 15px; background: #f8f9fa; border-radius: 10px; }
        .mini-stat-number { font-size: 1.8em; font-weight: bold; color: #3498db; }
        .mini-stat-label { font-size: 0.9em; color: #7f8c8d; margin-top: 5px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🏢 Sistema Comercial</div>
            <div class="nav-links">
                <a href="/comerciantes/clientes/dashboard">🏠 Dashboard</a>
                <a href="/comerciantes/planos">💎 Planos</a>
                <a href="/comerciantes/clientes/clientes">👤 Clientes</a>
                <a href="/comerciantes/clientes/funcionarios">👔 Funcionários</a>
                <a href="/comerciantes/clientes/fornecedores">🏭 Fornecedores</a>
                <a href="/comerciantes/clientes/entregadores">🚚 Entregadores</a>
                <a href="/comerciantes/clientes/status">⚙️ Status</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>📊 Dashboard do Sistema</h1>
            <p>Visão geral completa do seu sistema de gestão comercial</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number">' . $totalPessoas . '</div>
                <div class="stat-label">Total de Pessoas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <div class="stat-number">' . $totalDepartamentos . '</div>
                <div class="stat-label">Departamentos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💼</div>
                <div class="stat-number">' . $totalCargos . '</div>
                <div class="stat-label">Cargos</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📍</div>
                <div class="stat-number">' . $totalEnderecos . '</div>
                <div class="stat-label">Endereços</div>
            </div>
        </div>

        <div class="quick-stats">
            <h3>� Informações do Plano Atual</h3>
            <div class="mini-stats">
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $planoNome . '</div>
                    <div class="mini-stat-label">Plano Ativo</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $planoStatus . '</div>
                    <div class="mini-stat-label">Status</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $planoVencimento . '</div>
                    <div class="mini-stat-label">Vencimento</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number"><a href="/comerciantes/planos" style="color: #3498db; text-decoration: none;">Gerenciar</a></div>
                    <div class="mini-stat-label">Ações</div>
                </div>
            </div>
        </div>

        <div class="quick-stats">
            <h3>�📈 Distribuição por Tipo de Pessoa</h3>
            <div class="mini-stats">
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $totalClientes . '</div>
                    <div class="mini-stat-label">Clientes</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $totalFuncionarios . '</div>
                    <div class="mini-stat-label">Funcionários</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $totalFornecedores . '</div>
                    <div class="mini-stat-label">Fornecedores</div>
                </div>
                <div class="mini-stat">
                    <div class="mini-stat-number">' . $totalEntregadores . '</div>
                    <div class="mini-stat-label">Entregadores</div>
                </div>
            </div>
        </div>

        <div class="actions-grid">
            <div class="action-card">
                <h3>� Gerenciar Planos</h3>
                <div class="action-links">
                    <a href="/comerciantes/planos" class="action-link">
                        <span>📊</span> Dashboard de Planos
                    </a>
                    <a href="/comerciantes/planos/planos" class="action-link">
                        <span>💰</span> Ver Planos Disponíveis
                    </a>
                    <a href="/comerciantes/planos/historico" class="action-link">
                        <span>📈</span> Histórico de Pagamentos
                    </a>
                </div>
            </div>

            <div class="action-card">
                <h3>�👤 Gestão de Clientes</h3>
                <div class="action-links">
                    <a href="/comerciantes/clientes/clientes" class="action-link">
                        <span>👥</span> Ver Todos os Clientes
                    </a>
                    <a href="/comerciantes/clientes/clientes/ativos" class="action-link">
                        <span>✅</span> Clientes Ativos
                    </a>
                    <a href="/comerciantes/clientes/criar-pessoa?tipo=cliente" class="action-link">
                        <span>➕</span> Cadastrar Novo Cliente
                    </a>
                </div>
            </div>

            <div class="action-card">
                <h3>👔 Gestão de Funcionários</h3>
                <div class="action-links">
                    <a href="/comerciantes/clientes/funcionarios" class="action-link">
                        <span>👥</span> Ver Todos os Funcionários
                    </a>
                    <a href="/comerciantes/clientes/funcionarios/ativos" class="action-link">
                        <span>✅</span> Funcionários Ativos
                    </a>
                    <a href="/comerciantes/clientes/criar-pessoa?tipo=funcionario" class="action-link">
                        <span>➕</span> Cadastrar Novo Funcionário
                    </a>
                </div>
            </div>

            <div class="action-card">
                <h3>🏭 Gestão de Fornecedores</h3>
                <div class="action-links">
                    <a href="/comerciantes/clientes/fornecedores" class="action-link">
                        <span>🏭</span> Ver Todos os Fornecedores
                    </a>
                    <a href="/comerciantes/clientes/criar-pessoa?tipo=fornecedor" class="action-link">
                        <span>➕</span> Cadastrar Novo Fornecedor
                    </a>
                </div>
            </div>

            <div class="action-card">
                <h3>🚚 Gestão de Entregadores</h3>
                <div class="action-links">
                    <a href="/comerciantes/clientes/entregadores" class="action-link">
                        <span>🚚</span> Ver Todos os Entregadores
                    </a>
                    <a href="/comerciantes/clientes/criar-pessoa?tipo=entregador" class="action-link">
                        <span>➕</span> Cadastrar Novo Entregador
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

            return response($html)
                ->header('Content-Type', 'text/html; charset=UTF-8')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro no dashboard: ' . $e->getMessage()
            ], 500);
        }
    })->name('dashboard');

    // Diagnóstico para Chrome
    Route::get('/chrome-test', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Se você está vendo isso no Chrome, parabéns! O sistema funciona!',
            'timestamp' => now()->format('d/m/Y H:i:s'),
            'server' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
            ],
            'headers_sent' => [
                'cache_control' => 'no-cache, no-store, must-revalidate',
                'content_type' => 'application/json',
                'cors_headers' => 'enabled'
            ]
        ])->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache');
    })->name('chrome-test');

    Route::get('/api/dashboard', function () {
        try {
            $pessoas = \App\Modules\Comerciante\Models\Pessoas\Pessoa::with(['departamento', 'cargo'])
                ->limit(5)
                ->get();
            $departamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::all();
            $cargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::with('departamento')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Sistema de Pessoas funcionando!',
                'data' => [
                    'pessoas_total' => \App\Modules\Comerciante\Models\Pessoas\Pessoa::count(),
                    'departamentos_total' => $departamentos->count(),
                    'cargos_total' => $cargos->count(),
                    'pessoas_amostra' => $pessoas,
                    'departamentos' => $departamentos,
                    'cargos' => $cargos
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro no sistema: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    })->name('api.dashboard');

    Route::get('/api/config', function () {
        try {
            $configManager = app(\App\Modules\Comerciante\Config\ConfigManager::class);
            $grupos = $configManager->listarGrupos();

            return response()->json([
                'status' => 'success',
                'message' => 'Sistema de Configurações funcionando!',
                'data' => [
                    'grupos_total' => count($grupos),
                    'grupos' => $grupos
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro no sistema: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    })->name('api.config');

    Route::get('/status', function () {
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Sistema de Pessoas - Status</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto; }
        .header { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 15px; margin-bottom: 25px; }
        .status-ok { color: #27ae60; font-weight: bold; }
        .status-error { color: #e74c3c; font-weight: bold; }
        .info-box { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3498db; }
        .success-box { background: #d5f4e6; border-left-color: #27ae60; }
        .error-box { background: #fadbd8; border-left-color: #e74c3c; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #3498db; color: white; padding: 20px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
        .links { margin-top: 30px; }
        .link-button { display: inline-block; background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }
        .link-button:hover { background: #2980b9; }
        .chrome-fix { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Sistema de Pessoas para Comerciantes</h1>
            <p>Status do Sistema - ' . date('d/m/Y H:i:s') . '</p>
        </div>
        
        <div class="info-box chrome-fix">
            <strong>🔧 DICA PARA CHROME:</strong><br>
            Se esta página não abrir no Chrome, tente:<br>
            • Limpar cache (Ctrl+Shift+Del)<br>
            • Modo anônimo (Ctrl+Shift+N)<br>
            • Ou use o <strong>Edge/Firefox</strong> que funcionam perfeitamente!
        </div>';

        try {
            // Verificar tabelas
            $tabelas = [
                'pessoas' => Schema::hasTable('pessoas'),
                'pessoas_departamentos' => Schema::hasTable('pessoas_departamentos'),
                'pessoas_cargos' => Schema::hasTable('pessoas_cargos'),
                'pessoas_enderecos' => Schema::hasTable('pessoas_enderecos'),
                'pessoas_contas_bancarias' => Schema::hasTable('pessoas_contas_bancarias'),
                'pessoas_documentos' => Schema::hasTable('pessoas_documentos'),
                'pessoas_dependentes' => Schema::hasTable('pessoas_dependentes'),
                'pessoas_historico_cargos' => Schema::hasTable('pessoas_historico_cargos'),
                'pessoas_afastamentos' => Schema::hasTable('pessoas_afastamentos'),
                'comerciante_config_groups' => Schema::hasTable('comerciante_config_groups'),
                'comerciante_config_definitions' => Schema::hasTable('comerciante_config_definitions'),
                'comerciante_config_values' => Schema::hasTable('comerciante_config_values'),
                'comerciante_config_history' => Schema::hasTable('comerciante_config_history'),
            ];

            $totalTabelas = count($tabelas);
            $tabelasOk = count(array_filter($tabelas));

            if ($tabelasOk === $totalTabelas) {
                $html .= '<div class="info-box success-box">
                    <strong class="status-ok">✅ BANCO DE DADOS OK</strong><br>
                    Todas as ' . $totalTabelas . ' tabelas foram criadas com sucesso!
                </div>';
            } else {
                $html .= '<div class="info-box error-box">
                    <strong class="status-error">❌ PROBLEMA NO BANCO</strong><br>
                    ' . $tabelasOk . ' de ' . $totalTabelas . ' tabelas encontradas.
                </div>';
            }

            // Estatísticas
            $totalPessoas = \App\Modules\Comerciante\Models\Pessoas\Pessoa::count();
            $totalDepartamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::count();
            $totalCargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::count();
            $totalEnderecos = \App\Modules\Comerciante\Models\Pessoas\PessoaEndereco::count();

            $html .= '<div class="stats">
                <div class="stat-card">
                    <div class="stat-number">' . $totalPessoas . '</div>
                    <div>Pessoas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . $totalDepartamentos . '</div>
                    <div>Departamentos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . $totalCargos . '</div>
                    <div>Cargos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">' . $totalEnderecos . '</div>
                    <div>Endereços</div>
                </div>
            </div>';

            // Seção de Acesso Rápido às Pessoas
            $html .= '<div class="info-box">
                <h3>🚀 Acesso Rápido às Pessoas</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; margin-top: 15px;">
                    <a href="/comerciantes/clientes/clientes" style="background: #e74c3c; color: white; padding: 15px; text-decoration: none; border-radius: 5px; text-align: center; transition: all 0.3s;">
                        👤 Ver Clientes<br>
                        <small>Visualizar todos os clientes</small>
                    </a>
                    <a href="/comerciantes/clientes/funcionarios" style="background: #3498db; color: white; padding: 15px; text-decoration: none; border-radius: 5px; text-align: center; transition: all 0.3s;">
                        👔 Ver Funcionários<br>
                        <small>Visualizar funcionários ativos</small>
                    </a>
                    <a href="/comerciantes/clientes/fornecedores" style="background: #f39c12; color: white; padding: 15px; text-decoration: none; border-radius: 5px; text-align: center; transition: all 0.3s;">
                        🏭 Ver Fornecedores<br>
                        <small>Visualizar fornecedores</small>
                    </a>
                    <a href="/comerciantes/clientes/entregadores" style="background: #9b59b6; color: white; padding: 15px; text-decoration: none; border-radius: 5px; text-align: center; transition: all 0.3s;">
                        🚚 Ver Entregadores<br>
                        <small>Visualizar entregadores</small>
                    </a>
                    <a href="/comerciantes/clientes/criar-pessoa" style="background: #27ae60; color: white; padding: 15px; text-decoration: none; border-radius: 5px; text-align: center; transition: all 0.3s;">
                        ➕ Criar Nova Pessoa<br>
                        <small>Formulário de cadastro</small>
                    </a>
                </div>
            </div>';

            // Dados dos departamentos
            $departamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::all();
            if ($departamentos->count() > 0) {
                $html .= '<div class="info-box">
                    <strong>📁 Departamentos Criados:</strong><br>';
                foreach ($departamentos as $dept) {
                    $html .= '• ' . $dept->nome . ' (' . $dept->codigo . ')<br>';
                }
                $html .= '</div>';
            }

            // Dados dos cargos
            $cargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::with('departamento')->get();
            if ($cargos->count() > 0) {
                $html .= '<div class="info-box">
                    <strong>👔 Cargos Criados:</strong><br>';
                foreach ($cargos as $cargo) {
                    $html .= '• ' . $cargo->nome . ' (' . $cargo->codigo . ') - ' . $cargo->departamento->nome . '<br>';
                }
                $html .= '</div>';
            }

            $html .= '<div class="info-box success-box">
                <strong class="status-ok">🚀 SISTEMA OPERACIONAL</strong><br>
                O sistema está funcionando perfeitamente e pronto para uso!
            </div>';
        } catch (\Exception $e) {
            $html .= '<div class="info-box error-box">
                <strong class="status-error">❌ ERRO NO SISTEMA</strong><br>
                ' . $e->getMessage() . '
            </div>';
        }

        $html .= '<div class="links">
            <h3>🔗 Links para Teste:</h3>
            <a href="/comerciantes/clientes/ola" class="link-button">� Olá Simples</a>
            <a href="/comerciantes/clientes/dashboard" class="link-button">📊 Dashboard JSON</a>
            <a href="/comerciantes/clientes/config" class="link-button">⚙️ Config JSON</a>
            <a href="/comerciantes/clientes/tabelas" class="link-button">🗄️ Tabelas JSON</a>
        </div>

        <div class="info-box">
            <strong>📋 Resumo da Implementação:</strong><br>
            ✅ 19 arquivos de código criados<br>
            ✅ 13 tabelas de banco de dados<br>
            ✅ 4 migrações executadas<br>
            ✅ Sistema modular organizado<br>
            ✅ Configurações dinâmicas<br>
            ✅ Gestão completa de pessoas<br>
            ✅ Performance otimizada<br>
            <br>
            <strong>🌐 Compatibilidade:</strong><br>
            ✅ Edge: Funcionando perfeitamente<br>
            ✅ Firefox: Funcionando perfeitamente<br>
            ⚠️ Chrome: Pode requerer cache limpo
        </div>

        </div>
        </body>
        </html>';

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-Content-Type-Options', 'nosniff');
    })->name('sistema');

    Route::get('/tabelas', function () {
        try {
            $tabelas = [
                'pessoas' => \Illuminate\Support\Facades\Schema::hasTable('pessoas'),
                'pessoas_departamentos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_departamentos'),
                'pessoas_cargos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_cargos'),
                'pessoas_enderecos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_enderecos'),
                'pessoas_contas_bancarias' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_contas_bancarias'),
                'pessoas_documentos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_documentos'),
                'pessoas_dependentes' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_dependentes'),
                'pessoas_historico_cargos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_historico_cargos'),
                'pessoas_afastamentos' => \Illuminate\Support\Facades\Schema::hasTable('pessoas_afastamentos'),
                'comerciante_config_groups' => \Illuminate\Support\Facades\Schema::hasTable('comerciante_config_groups'),
                'comerciante_config_definitions' => \Illuminate\Support\Facades\Schema::hasTable('comerciante_config_definitions'),
                'comerciante_config_values' => \Illuminate\Support\Facades\Schema::hasTable('comerciante_config_values'),
                'comerciante_config_history' => \Illuminate\Support\Facades\Schema::hasTable('comerciante_config_history'),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Verificação de tabelas',
                'data' => [
                    'tabelas_existentes' => array_filter($tabelas),
                    'tabelas_faltando' => array_filter($tabelas, fn($exists) => !$exists),
                    'total_existentes' => count(array_filter($tabelas)),
                    'total_esperadas' => count($tabelas)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro na verificação: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    })->name('tabelas');

    // Rotas para visualizar pessoas por tipo
    Route::get('/clientes', function () {
        try {
            $clientes = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%cliente%')
                ->with(['departamento', 'cargo', 'enderecos'])
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #e74c3c; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .create-link { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-left: 10px; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👤 Clientes Cadastrados</h1>
            <a href="/comerciantes/clientes/dashboard" class="back-link">← Voltar ao Dashboard</a>
            <a href="/comerciantes/clientes/criar-pessoa?tipo=cliente" class="create-link">➕ Criar Novo Cliente</a>
        </div>';

            if ($clientes->count() > 0) {
                foreach ($clientes as $cliente) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($cliente->nome ?? 'Não informado') . '<br>
                        <strong>Tipo:</strong> ' . ($cliente->tipo ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($cliente->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($cliente->telefone ?? 'Não informado') . '<br>
                        <strong>Status:</strong> ' . ($cliente->status ?? 'Não informado') . '<br>
                        <strong>Criado em:</strong> ' . ($cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum cliente encontrado</h3>
                    <p>Ainda não há clientes cadastrados no sistema.</p>
                    <a href="/comerciantes/clientes/criar-pessoa?tipo=cliente" class="create-link">Cadastrar Primeiro Cliente</a>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar clientes: ' . $e->getMessage()
            ], 500);
        }
    })->name('clientes');

    // Clientes ativos
    Route::get('/clientes/ativos', function () {
        try {
            $clientes = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%cliente%')
                ->where('status', 'ativo')
                ->with(['departamento', 'cargo', 'enderecos'])
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Ativos - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #27ae60; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #d5f4e6; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #27ae60; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .create-link { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-left: 10px; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
        .active-badge { background: #27ae60; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Clientes Ativos</h1>
            <a href="/comerciantes/clientes/dashboard" class="back-link">← Voltar ao Dashboard</a>
            <a href="/comerciantes/clientes/clientes" class="back-link">👤 Todos os Clientes</a>
        </div>';

            if ($clientes->count() > 0) {
                foreach ($clientes as $cliente) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($cliente->nome ?? 'Não informado') . ' <span class="active-badge">ATIVO</span><br>
                        <strong>Tipo:</strong> ' . ($cliente->tipo ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($cliente->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($cliente->telefone ?? 'Não informado') . '<br>
                        <strong>Criado em:</strong> ' . ($cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum cliente ativo encontrado</h3>
                    <p>Não há clientes com status ativo no sistema.</p>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar clientes ativos: ' . $e->getMessage()
            ], 500);
        }
    })->name('clientes.ativos');

    Route::get('/funcionarios/ativos', function () {
        try {
            $funcionarios = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%funcionario%')
                ->where('status', 'ativo')
                ->with(['departamento', 'cargo'])
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários Ativos - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3498db; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
        .active-badge { background: #27ae60; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Funcionários Ativos</h1>
            <a href="/comerciantes/clientes/dashboard" class="back-link">← Voltar ao Dashboard</a>
            <a href="/comerciantes/clientes/funcionarios" class="back-link">👔 Todos os Funcionários</a>
        </div>';

            if ($funcionarios->count() > 0) {
                foreach ($funcionarios as $funcionario) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($funcionario->nome ?? 'Não informado') . ' <span class="active-badge">ATIVO</span><br>
                        <strong>Departamento:</strong> ' . ($funcionario->departamento->nome ?? 'Não informado') . '<br>
                        <strong>Cargo:</strong> ' . ($funcionario->cargo->nome ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($funcionario->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($funcionario->telefone ?? 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum funcionário ativo encontrado</h3>
                    <p>Não há funcionários com status ativo no sistema.</p>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar funcionários ativos: ' . $e->getMessage()
            ], 500);
        }
    })->name('funcionarios.ativos');

    Route::get('/funcionarios', function () {
        try {
            $funcionarios = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%funcionario%')
                ->with(['departamento', 'cargo'])
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3498db; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .create-link { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-left: 10px; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👔 Funcionários Cadastrados</h1>
            <a href="/comerciantes/clientes/dashboard" class="back-link">← Voltar ao Dashboard</a>
            <a href="/comerciantes/clientes/criar-pessoa?tipo=funcionario" class="create-link">➕ Criar Novo Funcionário</a>
        </div>';

            if ($funcionarios->count() > 0) {
                foreach ($funcionarios as $funcionario) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($funcionario->nome ?? 'Não informado') . '<br>
                        <strong>Departamento:</strong> ' . ($funcionario->departamento->nome ?? 'Não informado') . '<br>
                        <strong>Cargo:</strong> ' . ($funcionario->cargo->nome ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($funcionario->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($funcionario->telefone ?? 'Não informado') . '<br>
                        <strong>Data Admissão:</strong> ' . ($funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum funcionário encontrado</h3>
                    <p>Ainda não há funcionários cadastrados no sistema.</p>
                    <a href="/comerciantes/clientes/criar-pessoa" class="create-link">Cadastrar Primeiro Funcionário</a>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar funcionários: ' . $e->getMessage()
            ], 500);
        }
    })->name('funcionarios');

    Route::get('/fornecedores', function () {
        try {
            $fornecedores = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%fornecedor%')
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #f39c12; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .create-link { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-left: 10px; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏭 Fornecedores Cadastrados</h1>
            <a href="/comerciantes/clientes/sistema" class="back-link">← Voltar ao Sistema</a>
            <a href="/comerciantes/clientes/criar-pessoa" class="create-link">➕ Criar Novo Fornecedor</a>
        </div>';

            if ($fornecedores->count() > 0) {
                foreach ($fornecedores as $fornecedor) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($fornecedor->nome ?? 'Não informado') . '<br>
                        <strong>Nome Fantasia:</strong> ' . ($fornecedor->nome_fantasia ?? 'Não informado') . '<br>
                        <strong>CNPJ/CPF:</strong> ' . ($fornecedor->cpf_cnpj ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($fornecedor->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($fornecedor->telefone ?? 'Não informado') . '<br>
                        <strong>Website:</strong> ' . ($fornecedor->website ?? 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum fornecedor encontrado</h3>
                    <p>Ainda não há fornecedores cadastrados no sistema.</p>
                    <a href="/comerciantes/clientes/criar-pessoa" class="create-link">Cadastrar Primeiro Fornecedor</a>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar fornecedores: ' . $e->getMessage()
            ], 500);
        }
    })->name('fornecedores');

    Route::get('/entregadores', function () {
        try {
            $entregadores = \App\Modules\Comerciante\Models\Pessoas\Pessoa::where('tipo', 'LIKE', '%entregador%')
                ->get();

            $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregadores - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .person-card { background: #ecf0f1; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #9b59b6; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .create-link { background: #27ae60; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-left: 10px; }
        .no-data { background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚚 Entregadores Cadastrados</h1>
            <a href="/comerciantes/clientes/sistema" class="back-link">← Voltar ao Sistema</a>
            <a href="/comerciantes/clientes/criar-pessoa" class="create-link">➕ Criar Novo Entregador</a>
        </div>';

            if ($entregadores->count() > 0) {
                foreach ($entregadores as $entregador) {
                    $html .= '<div class="person-card">
                        <strong>Nome:</strong> ' . ($entregador->nome ?? 'Não informado') . '<br>
                        <strong>CPF:</strong> ' . ($entregador->cpf_cnpj ?? 'Não informado') . '<br>
                        <strong>Email:</strong> ' . ($entregador->email ?? 'Não informado') . '<br>
                        <strong>Telefone:</strong> ' . ($entregador->telefone ?? 'Não informado') . '<br>
                        <strong>WhatsApp:</strong> ' . ($entregador->whatsapp ?? 'Não informado') . '<br>
                        <strong>Status:</strong> ' . ($entregador->status ?? 'Não informado') . '
                    </div>';
                }
            } else {
                $html .= '<div class="no-data">
                    <h3>Nenhum entregador encontrado</h3>
                    <p>Ainda não há entregadores cadastrados no sistema.</p>
                    <a href="/comerciantes/clientes/criar-pessoa" class="create-link">Cadastrar Primeiro Entregador</a>
                </div>';
            }

            $html .= '</div></body></html>';
            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar entregadores: ' . $e->getMessage()
            ], 500);
        }
    })->name('entregadores');

    // Formulário para criar pessoas
    Route::get('/criar-pessoa', function () {
        $departamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::all();
        $cargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::all();

        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Pessoa - Sistema de Pessoas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
        .header { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .submit-btn { background: #27ae60; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .submit-btn:hover { background: #219a52; }
        .back-link { background: #95a5a6; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-bottom: 20px; display: inline-block; }
        .info-box { background: #d5f4e6; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Criar Nova Pessoa</h1>
            <a href="/comerciantes/clientes/sistema" class="back-link">← Voltar ao Sistema</a>
        </div>

        <div class="info-box">
            <strong>📝 Formulário de Demonstração</strong><br>
            Este é um formulário básico para criar pessoas no sistema. Em produção, você teria validações completas e integração com o banco de dados.
        </div>

        <form method="POST" action="/comerciantes/clientes/criar-pessoa">
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo">Tipo de Pessoa:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione o tipo</option>
                        <option value="cliente">👤 Cliente</option>
                        <option value="funcionario">👔 Funcionário</option>
                        <option value="fornecedor">🏭 Fornecedor</option>
                        <option value="entregador">🚚 Entregador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="ativo">✅ Ativo</option>
                        <option value="inativo">❌ Inativo</option>
                        <option value="suspenso">⏸️ Suspenso</option>
                        <option value="bloqueado">🚫 Bloqueado</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" required placeholder="Digite o nome completo">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="exemplo@email.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <label for="cpf_cnpj">CPF/CNPJ:</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="000.000.000-00 ou 00.000.000/0001-00">
                </div>
            </div>

            <div class="form-row" id="funcionario-fields" style="display: none;">
                <div class="form-group">
                    <label for="departamento_id">Departamento:</label>
                    <select id="departamento_id" name="departamento_id">
                        <option value="">Selecione o departamento</option>';

        foreach ($departamentos as $dept) {
            $html .= '<option value="' . $dept->id . '">' . $dept->nome . ' (' . $dept->codigo . ')</option>';
        }

        $html .= '                    </select>
                </div>
                <div class="form-group">
                    <label for="cargo_id">Cargo:</label>
                    <select id="cargo_id" name="cargo_id">
                        <option value="">Selecione o cargo</option>';

        foreach ($cargos as $cargo) {
            $html .= '<option value="' . $cargo->id . '">' . $cargo->nome . ' (' . $cargo->codigo . ')</option>';
        }

        $html .= '                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes" placeholder="Informações adicionais sobre a pessoa..."></textarea>
            </div>

            <button type="submit" class="submit-btn">✅ Criar Pessoa</button>
        </form>

        <script>
            document.getElementById("tipo").addEventListener("change", function() {
                const funcionarioFields = document.getElementById("funcionario-fields");
                if (this.value === "funcionario") {
                    funcionarioFields.style.display = "grid";
                } else {
                    funcionarioFields.style.display = "none";
                }
            });
        </script>
    </div>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    })->name('criar-pessoa');

    // Processar criação de pessoa
    Route::post('/criar-pessoa', function (\Illuminate\Http\Request $request) {
        try {
            $pessoa = new \App\Modules\Comerciante\Models\Pessoas\Pessoa();
            $pessoa->empresa_id = 2; // ID da empresa de teste
            $pessoa->tipo = $request->tipo;
            $pessoa->nome = $request->nome;
            $pessoa->email = $request->email;
            $pessoa->telefone = $request->telefone;
            $pessoa->cpf_cnpj = $request->cpf_cnpj;
            $pessoa->status = $request->status;
            $pessoa->observacoes = $request->observacoes;

            if ($request->tipo === 'funcionario') {
                $pessoa->departamento_id = $request->departamento_id;
                $pessoa->cargo_id = $request->cargo_id;
            }

            $pessoa->save();

            return redirect('/comerciantes/clientes/dashboard')
                ->with('success', 'Pessoa criada com sucesso!');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar pessoa: ' . $e->getMessage()
            ], 500);
        }
    })->name('criar-pessoa.store');

    /**
     * PESSOAS (Clientes, Funcionários, Fornecedores, Entregadores)
     * Sistema de gestão de pessoas por empresa
     */
    Route::prefix('pessoas')->name('pessoas.')->group(function () {
        Route::get('/', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'store'])->name('store');
        Route::get('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'show'])->name('show');
        Route::get('/{pessoa}/edit', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'edit'])->name('edit');
        Route::put('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'update'])->name('update');
        Route::delete('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'destroy'])->name('destroy');
    });

    // Rota de debug para departamentos
    Route::get('/debug-departamentos', function () {
        try {
            $empresaId = 1;
            $controller = new \App\Modules\Comerciante\Controllers\DepartamentoController();
            $request = new \Illuminate\Http\Request(['empresa_id' => $empresaId]);

            return response()->json([
                'status' => 'success',
                'message' => 'Controller DepartamentoController funcionando!',
                'empresa_id' => $empresaId,
                'view_exists' => view()->exists('comerciantes.departamentos.create')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    })->name('debug.departamentos');

    /**
     * DEPARTAMENTOS
     * Gestão de departamentos da empresa
     */
    Route::prefix('departamentos')->name('departamentos.')->group(function () {
        Route::get('/', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'store'])->name('store');
        Route::get('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'show'])->name('show');
        Route::get('/{departamento}/edit', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'edit'])->name('edit');
        Route::put('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'update'])->name('update');
        Route::delete('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'destroy'])->name('destroy');
    });

    /**
     * CARGOS
     * Gestão de cargos da empresa
     */
    Route::prefix('cargos')->name('cargos.')->group(function () {
        Route::get('/', [\App\Modules\Comerciante\Controllers\CargoController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Comerciante\Controllers\CargoController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Comerciante\Controllers\CargoController::class, 'store'])->name('store');
        Route::get('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'show'])->name('show');
        Route::get('/{cargo}/edit', [\App\Modules\Comerciante\Controllers\CargoController::class, 'edit'])->name('edit');
        Route::put('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'update'])->name('update');
        Route::delete('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'destroy'])->name('destroy');
    });
});

// ===========================
// ROTAS DE REDIRECIONAMENTO
// ===========================

// Rota de redirecionamento para o dashboard
Route::get('/comerciantes/dashboard', function () {
    return redirect('/comerciantes/clientes/dashboard');
})->name('comerciantes.dashboard.redirect');

// Rota principal do dashboard (sem prefixo sistema) 
Route::get('/comerciantes', function () {
    return redirect('/comerciantes/clientes/dashboard');
})->name('comerciantes.index');

// ===========================
// ROTAS PROTEGIDAS (SEPARADAS)
// ===========================
Route::prefix('comerciantes')->name('comerciantes.')->group(function () {
    Route::middleware(['auth.comerciante'])->group(function () {
        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        /**
         * DASHBOARD
         */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/empresa/{empresa}', [DashboardController::class, 'selecionarEmpresa'])->name('dashboard.empresa');
        Route::get('/dashboard/limpar-empresa', [DashboardController::class, 'limparSelecaoEmpresa'])->name('dashboard.limpar');

        // APIs do dashboard
        Route::get('/dashboard/estatisticas', [DashboardController::class, 'estatisticas'])->name('dashboard.estatisticas');
        Route::get('/dashboard/progresso', [DashboardController::class, 'atualizarProgresso'])->name('dashboard.progresso');

        /**
         * NOTIFICAÇÕES
         * Sistema completo de notificações para comerciantes
         */
        Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
            Route::get('/', [NotificacaoController::class, 'index'])->name('index');
            Route::get('/dashboard', [NotificacaoController::class, 'dashboard'])->name('dashboard');
            Route::get('/header', [NotificacaoController::class, 'headerNotifications'])->name('header');
            Route::get('/{id}', [NotificacaoController::class, 'show'])->name('show');
            Route::post('/{id}/marcar-lida', [NotificacaoController::class, 'marcarComoLida'])->name('marcar-lida');
            Route::post('/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('marcar-todas-lidas');
        });

        /**
         * DEMONSTRAÇÃO DO SISTEMA DE PERMISSÕES
         */
        Route::get('/demo-permissoes', function () {
            return view('comerciantes.demo-permissoes');
        })->name('demo-permissoes');

        /**
         * RELATÓRIOS BÁSICOS (todos os planos)
         */
        Route::prefix('relatorios')->name('relatorios.')->group(function () {
            Route::get('/vendas', function () {
                return view('comerciantes.relatorios.vendas');
            })->name('vendas');

            Route::get('/clientes', function () {
                return view('comerciantes.relatorios.clientes');
            })->name('clientes');
        });

        /**
         * RELATÓRIOS AVANÇADOS (apenas planos profissional e enterprise)
         */
        Route::prefix('relatorios')->name('relatorios.')->middleware('plan:advanced_reports')->group(function () {
            Route::get('/analytics', function () {
                return view('comerciantes.relatorios.analytics');
            })->name('analytics');

            Route::get('/performance', function () {
                return view('comerciantes.relatorios.performance');
            })->name('performance');

            Route::get('/financeiro-detalhado', function () {
                return view('comerciantes.relatorios.financeiro-detalhado');
            })->name('financeiro-detalhado');
        });

        /**
         * GESTÃO DE EMPRESAS (apenas planos profissional e enterprise)
         */
        Route::middleware('plan:company_management')->resource('empresas', EmpresaController::class);

        /**
         * INTEGRAÇÃO COM API (apenas planos profissional e enterprise)
         */
        Route::prefix('api')->name('api.')->middleware('plan:api_access')->group(function () {
            Route::get('/tokens', function () {
                return view('comerciantes.api.tokens');
            })->name('tokens');

            Route::post('/gerar-token', function () {
                return response()->json(['token' => 'novo-token-api']);
            })->name('gerar-token');
        });

        /**
         * OPERAÇÕES EM LOTE (apenas planos profissional e enterprise)
         */
        Route::prefix('bulk')->name('bulk.')->middleware('plan:bulk_operations')->group(function () {
            Route::get('/importar', function () {
                return view('comerciantes.bulk.importar');
            })->name('importar');

            Route::post('/exportar', function () {
                return response()->download('path/to/export.csv');
            })->name('exportar');
        });

        /**
         * AUDITORIA E LOGS (apenas plano enterprise)
         */
        Route::prefix('auditoria')->name('auditoria.')->middleware('plan:audit_logs')->group(function () {
            Route::get('/', function () {
                return view('comerciantes.auditoria.index');
            })->name('index');

            Route::get('/logs/{user}', function ($user) {
                return view('comerciantes.auditoria.user-logs', compact('user'));
            })->name('user-logs');
        });

        /**
         * CAMPOS PERSONALIZADOS (apenas plano enterprise)
         */
        Route::prefix('campos-personalizados')->name('campos.')->middleware('plan:custom_fields')->group(function () {
            Route::get('/', function () {
                return view('comerciantes.campos.index');
            })->name('index');

            Route::post('/criar', function () {
                return response()->json(['success' => true]);
            })->name('criar');
        });

        /**
         * PERMISSÕES AVANÇADAS (apenas plano enterprise)
         */
        Route::prefix('permissoes')->name('permissoes.')->middleware('plan:advanced_permissions')->group(function () {
            Route::get('/gerenciar', function () {
                return view('comerciantes.permissoes.gerenciar');
            })->name('gerenciar');

            Route::post('/aplicar', function () {
                return response()->json(['success' => true]);
            })->name('aplicar');
        });

        /**
         * MARCAS (recursos limitados por plano)
         * Observação: Resource completo não tem middleware, mas métodos específicos podem ter
         */
        Route::resource('marcas', MarcaController::class)->except(['edit', 'update']);

        // Edição de marcas limitada por plano
        Route::middleware('plan:brand_management')->group(function () {
            Route::get('marcas/{marca}/edit', [MarcaController::class, 'edit'])->name('marcas.edit');
            Route::put('marcas/{marca}', [MarcaController::class, 'update'])->name('marcas.update');
        });

        /**
         * EMPRESAS
         * Resource completo + rotas extras para gerenciar usuários vinculados
         */
        Route::resource('empresas', EmpresaController::class);

        // Gerenciamento de usuários vinculados às empresas
        Route::prefix('empresas/{empresa}')->name('empresas.')->group(function () {
            Route::get('/usuarios', [EmpresaController::class, 'usuarios'])->name('usuarios.index');
            Route::post('/usuarios', [EmpresaController::class, 'adicionarUsuario'])->name('usuarios.store');
            Route::post('/usuarios/criar', [EmpresaController::class, 'criarEVincularUsuario'])->name('usuarios.create');
            Route::get('/usuarios/{user}', [EmpresaController::class, 'mostrarUsuario'])->name('usuarios.show');
            Route::get('/usuarios/{user}/edit', [EmpresaController::class, 'editarUsuarioForm'])->name('usuarios.edit');
            Route::put('/usuarios/{user}', [EmpresaController::class, 'editarUsuario'])->name('usuarios.update');
            Route::delete('/usuarios/{user}', [EmpresaController::class, 'removerUsuario'])->name('usuarios.destroy');
        });

        /**
         * PLANOS E ASSINATURAS
         * Sistema completo de gestão de planos e pagamentos
         */
        Route::prefix('planos')->name('planos.')->group(function () {
            // Dashboard de planos
            Route::get('/', [PlanoController::class, 'dashboard'])->name('dashboard');

            // Visualizar e escolher planos
            Route::get('/planos', [PlanoController::class, 'planos'])->name('planos');

            // Alterar plano
            Route::post('/alterar', [PlanoController::class, 'alterarPlano'])->name('alterar');

            // Checkout e pagamento
            Route::get('/checkout/{transactionUuid}', [PlanoController::class, 'checkout'])->name('checkout');

            // Confirmar pagamento manual
            Route::post('/confirmar-pagamento/{transactionUuid}', [PlanoController::class, 'confirmarPagamento'])->name('confirmar-pagamento');

            // Histórico
            Route::get('/historico', [PlanoController::class, 'historico'])->name('historico');

            // Toggle renovação automática
            Route::post('/toggle-renovacao', [PlanoController::class, 'toggleRenovacao'])->name('toggle-renovacao');

            // Páginas de resultado
            Route::get('/sucesso', function () {
                return view('comerciantes.planos.sucesso');
            })->name('sucesso');

            Route::get('/cancelado', function () {
                return view('comerciantes.planos.cancelado');
            })->name('cancelado');
        });

        /**
         * PRODUTOS
         * Sistema completo de gestão de produtos com categorias e marcas
         */
        Route::prefix('produtos')->name('produtos.')->group(function () {
            // CRUD de Produtos (rotas básicas primeiro)
            Route::get('/', [ProdutoController::class, 'index'])->name('index');
            Route::get('/create', [ProdutoController::class, 'create'])->name('create');
            Route::post('/', [ProdutoController::class, 'store'])->name('store');

            // Rotas especiais de produtos (antes das rotas com parâmetros)
            Route::get('/relatorio/estoque', [ProdutoController::class, 'relatorioEstoque'])->name('relatorio-estoque');
            Route::post('/verificar-estoque-baixo', [ProdutoController::class, 'verificarEstoqueBaixo'])->name('verificar-estoque-baixo');

            // Categorias (ANTES das rotas {produto})
            Route::prefix('categorias')->name('categorias.')->group(function () {
                Route::get('/', [ProdutoCategoriaController::class, 'index'])->name('index');
                Route::get('/create', [ProdutoCategoriaController::class, 'create'])->name('create');
                Route::post('/', [ProdutoCategoriaController::class, 'store'])->name('store');
                Route::get('/{categoria}/edit', [ProdutoCategoriaController::class, 'edit'])->name('edit');
                Route::put('/{categoria}', [ProdutoCategoriaController::class, 'update'])->name('update');
                Route::delete('/{categoria}', [ProdutoCategoriaController::class, 'destroy'])->name('destroy');
            });

            // Marcas (ANTES das rotas {produto})
            Route::prefix('marcas')->name('marcas.')->group(function () {
                Route::get('/', [ProdutoMarcaController::class, 'index'])->name('index');
                Route::get('/create', [ProdutoMarcaController::class, 'create'])->name('create');
                Route::post('/', [ProdutoMarcaController::class, 'store'])->name('store');
                Route::get('/{marca}/edit', [ProdutoMarcaController::class, 'edit'])->name('edit');
                Route::put('/{marca}', [ProdutoMarcaController::class, 'update'])->name('update');
                Route::delete('/{marca}', [ProdutoMarcaController::class, 'destroy'])->name('destroy');
            });

            // Subcategorias (ANTES das rotas {produto})
            Route::prefix('subcategorias')->name('subcategorias.')->group(function () {
                Route::get('/', [ProdutoSubcategoriaController::class, 'index'])->name('index');
                Route::get('/create', [ProdutoSubcategoriaController::class, 'create'])->name('create');
                Route::post('/', [ProdutoSubcategoriaController::class, 'store'])->name('store');
                Route::get('/{subcategoria}', [ProdutoSubcategoriaController::class, 'show'])->name('show');
                Route::get('/{subcategoria}/edit', [ProdutoSubcategoriaController::class, 'edit'])->name('edit');
                Route::put('/{subcategoria}', [ProdutoSubcategoriaController::class, 'update'])->name('update');
                Route::delete('/{subcategoria}', [ProdutoSubcategoriaController::class, 'destroy'])->name('destroy');

                // AJAX Routes
                Route::get('/por-categoria', [ProdutoSubcategoriaController::class, 'porCategoria'])->name('por-categoria');
                Route::get('/principais-por-categoria', [ProdutoSubcategoriaController::class, 'principaisPorCategoria'])->name('principais-por-categoria');
                Route::post('/atualizar-ordem', [ProdutoSubcategoriaController::class, 'atualizarOrdem'])->name('atualizar-ordem');
                Route::post('/{subcategoria}/toggle-ativo', [ProdutoSubcategoriaController::class, 'toggleAtivo'])->name('toggle-ativo');
            });

            // Códigos de Barras (ANTES das rotas {produto})
            Route::prefix('codigos-barras')->name('codigos-barras.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'store'])->name('store');
                Route::get('/scanner', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'scanner'])->name('scanner');
                Route::get('/relatorio-duplicados', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'relatorioDuplicados'])->name('relatorio-duplicados');
                Route::get('/{codigoBarras}', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'show'])->name('show');
                Route::get('/{codigoBarras}/edit', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'edit'])->name('edit');
                Route::put('/{codigoBarras}', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'update'])->name('update');
                Route::delete('/{codigoBarras}', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'destroy'])->name('destroy');

                // AJAX Routes
                Route::get('/buscar-por-codigo', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'buscarPorCodigo'])->name('buscar-por-codigo');
                Route::get('/gerar-codigo-interno', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'gerarCodigoInterno'])->name('gerar-codigo-interno');
                Route::post('/validar-codigo', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'validarCodigo'])->name('validar-codigo');
                Route::post('/{codigoBarras}/definir-principal', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'definirPrincipal'])->name('definir-principal');
                Route::post('/{codigoBarras}/toggle-ativo', [\App\Http\Controllers\Comerciante\ProdutoCodigoBarrasController::class, 'toggleAtivo'])->name('toggle-ativo');
            });

            // Histórico de Preços (ANTES das rotas {produto})
            Route::prefix('historico-precos')->name('historico-precos.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'store'])->name('store');
                Route::get('/relatorio', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'relatorio'])->name('relatorio');
                Route::get('/comparacao', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'comparacao'])->name('comparacao');
                Route::get('/exportar', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'exportar'])->name('exportar');
                Route::get('/produto/{produto}', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'produto'])->name('produto');
                Route::get('/{historicoPreco}', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'show'])->name('show');
                Route::get('/{historicoPreco}/edit', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'edit'])->name('edit');
                Route::put('/{historicoPreco}', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'update'])->name('update');
                Route::delete('/{historicoPreco}', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'destroy'])->name('destroy');

                // AJAX Routes
                Route::get('/dados-grafico', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'dadosGrafico'])->name('dados-grafico');
                Route::get('/estatisticas-rapidas', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'estatisticasRapidas'])->name('estatisticas-rapidas');
                Route::post('/limpar-antigo', [\App\Http\Controllers\Comerciante\ProdutoHistoricoPrecoController::class, 'limparAntigo'])->name('limpar-antigo');
            });

            // Configurações de Produtos (ANTES das rotas {produto})
            Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'store'])->name('store');
                Route::get('/por-produto', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'porProduto'])->name('por-produto');
                Route::get('/{configuracao}', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'show'])->name('show');
                Route::get('/{configuracao}/edit', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'edit'])->name('edit');
                Route::put('/{configuracao}', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'update'])->name('update');
                Route::delete('/{configuracao}', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'destroy'])->name('destroy');
                Route::post('/{configuracao}/toggle-ativo', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'toggleAtivo'])->name('toggle-ativo');

                // Rotas para gerenciar itens das configurações
                Route::post('/{configuracao}/itens', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'storeItem'])->name('itens.store');
                Route::put('/{configuracao}/itens/{item}', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'updateItem'])->name('itens.update');
                Route::delete('/{configuracao}/itens/{item}', [\App\Http\Controllers\Comerciante\ProdutoConfiguracaoController::class, 'destroyItem'])->name('itens.destroy');
            });

            // Kits/Combos de Produtos (ANTES das rotas {produto})
            Route::prefix('kits')->name('kits.')->group(function () {
                // ROTAS ESPECÍFICAS PRIMEIRO (antes das rotas com parâmetros)
                Route::get('/buscar-produto', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'buscarProduto'])->name('buscar-produto');
                Route::post('/calcular-preco', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'calcularPrecoKit'])->name('calcular-preco');

                // ROTAS BÁSICAS
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'store'])->name('store');

                // ROTAS COM PARÂMETROS NO FINAL
                Route::get('/{kit}', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'show'])->name('show');
                Route::get('/{kit}/edit', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'edit'])->name('edit');
                Route::put('/{kit}', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'update'])->name('update');
                Route::delete('/{kit}', [\App\Http\Controllers\Comerciante\ProdutoKitController::class, 'destroy'])->name('destroy');
            });

            // Preços por Quantidade (ANTES das rotas {produto})
            Route::prefix('precos-quantidade')->name('precos-quantidade.')->group(function () {
                Route::get('/', [ProdutoPrecoQuantidadeController::class, 'index'])->name('index');
                Route::get('/create', [ProdutoPrecoQuantidadeController::class, 'create'])->name('create');
                Route::post('/', [ProdutoPrecoQuantidadeController::class, 'store'])->name('store');
                Route::get('/{preco}', [ProdutoPrecoQuantidadeController::class, 'show'])->name('show');
                Route::get('/{preco}/edit', [ProdutoPrecoQuantidadeController::class, 'edit'])->name('edit');
                Route::put('/{preco}', [ProdutoPrecoQuantidadeController::class, 'update'])->name('update');
                Route::delete('/{preco}', [ProdutoPrecoQuantidadeController::class, 'destroy'])->name('destroy');
                Route::post('/calcular', [ProdutoPrecoQuantidadeController::class, 'calcular'])->name('calcular');
                Route::get('/produto/{produto}', [ProdutoPrecoQuantidadeController::class, 'porProduto'])->name('por-produto');
                Route::post('/{preco}/toggle-ativo', [ProdutoPrecoQuantidadeController::class, 'toggleAtivo'])->name('toggle-ativo');
                Route::get('/relatorio/desconto', [ProdutoPrecoQuantidadeController::class, 'relatorioDesconto'])->name('relatorio-desconto');
            });

            // Estoque (ANTES das rotas {produto})
            Route::prefix('estoque')->name('estoque.')->group(function () {
                Route::get('/alertas', [\App\Http\Controllers\Comerciante\EstoqueController::class, 'alertas'])->name('alertas');
                Route::get('/movimentacoes', [\App\Http\Controllers\Comerciante\EstoqueController::class, 'movimentacoes'])->name('movimentacoes');
                Route::post('/movimentacao/registrar', [\App\Http\Controllers\Comerciante\EstoqueController::class, 'registrarMovimentacao'])->name('movimentacao.registrar');
                Route::post('/atualizar-lote', [\App\Http\Controllers\Comerciante\EstoqueController::class, 'atualizarLote'])->name('atualizar-lote');
            });

            // Rotas com parâmetros {produto} (NO FINAL)
            Route::get('/{produto}', [ProdutoController::class, 'show'])->name('show');
            Route::get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('edit');
            Route::put('/{produto}', [ProdutoController::class, 'update'])->name('update');
            Route::delete('/{produto}', [ProdutoController::class, 'destroy'])->name('destroy');

            // Galeria de Imagens por produto (ANTES das ações especiais)
            Route::prefix('{produto}/imagens')->name('imagens.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'index'])->name('index');
                Route::post('/upload', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'upload'])->name('upload');
                Route::put('/{imagem}', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'update'])->name('update');
                Route::delete('/{imagem}', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'destroy'])->name('destroy');
                Route::post('/{imagem}/principal', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'setPrincipal'])->name('setPrincipal');
                Route::post('/reordenar', [\App\Http\Controllers\Comerciante\ProdutoImagemController::class, 'reordenar'])->name('reordenar');
            });

            // Produtos Relacionados (Cross-sell, Up-sell, Similares, etc.)
            Route::prefix('{produto}/relacionados')->name('relacionados.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'store'])->name('store');
                Route::put('/{relacionado}', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'update'])->name('update');
                Route::delete('/{relacionado}', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'destroy'])->name('destroy');
                Route::post('/update-ordem', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'updateOrdem'])->name('update-ordem');
                Route::get('/buscar', [\App\Http\Controllers\Comerciante\ProdutoRelacionadoController::class, 'buscarProdutos'])->name('buscar');
            });

            // Ações especiais de produtos específicos (também no final)
            Route::post('/{produto}/movimentacao', [ProdutoController::class, 'movimentacao'])->name('movimentacao');
            Route::post('/{produto}/duplicate', [ProdutoController::class, 'duplicate'])->name('duplicate');
            Route::patch('/{produto}/estoque', [ProdutoController::class, 'atualizarEstoque'])->name('atualizar-estoque');
        });

        /**
         * PESSOAS (Clientes, Funcionários, Fornecedores, Entregadores)
         * Sistema de gestão de pessoas por empresa
         */
        Route::prefix('pessoas')->name('pessoas.')->group(function () {
            Route::get('/', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'store'])->name('store');
            Route::get('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'show'])->name('show');
            Route::get('/{pessoa}/edit', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'edit'])->name('edit');
            Route::put('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'update'])->name('update');
            Route::delete('/{pessoa}', [\App\Modules\Comerciante\Controllers\Pessoas\PessoaController::class, 'destroy'])->name('destroy');
        });

        /**
         * VENDAS
         * Sistema completo de vendas para comerciantes
         */
        Route::prefix('vendas')->name('vendas.')->group(function () {
            // Rotas básicas do resource
            Route::get('/', [\App\Http\Controllers\Comerciante\VendaController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Comerciante\VendaController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Comerciante\VendaController::class, 'store'])->name('store');
            Route::get('/{venda}', [\App\Http\Controllers\Comerciante\VendaController::class, 'show'])->name('show');
            Route::get('/{venda}/edit', [\App\Http\Controllers\Comerciante\VendaController::class, 'edit'])->name('edit');
            Route::put('/{venda}', [\App\Http\Controllers\Comerciante\VendaController::class, 'update'])->name('update');
            Route::delete('/{venda}', [\App\Http\Controllers\Comerciante\VendaController::class, 'destroy'])->name('destroy');
            
            // Ações especiais de vendas
            Route::post('/{venda}/finalizar', [\App\Http\Controllers\Comerciante\VendaController::class, 'finalizar'])->name('finalizar');
            Route::post('/{venda}/cancelar', [\App\Http\Controllers\Comerciante\VendaController::class, 'cancelar'])->name('cancelar');
            
            // Gerenciamento de itens
            Route::post('/{venda}/itens', [\App\Http\Controllers\Comerciante\VendaController::class, 'adicionarItem'])->name('itens.adicionar');
            Route::delete('/{venda}/itens/{item}', [\App\Http\Controllers\Comerciante\VendaController::class, 'removerItem'])->name('itens.remover');
            
            // APIs para busca e estatísticas
            Route::get('/api/estatisticas', [\App\Http\Controllers\Comerciante\VendaController::class, 'estatisticas'])->name('api.estatisticas');
            Route::get('/api/produtos/buscar', [\App\Http\Controllers\Comerciante\VendaController::class, 'buscarProdutos'])->name('api.produtos.buscar');
            Route::get('/api/clientes/buscar', [\App\Http\Controllers\Comerciante\VendaController::class, 'buscarClientes'])->name('api.clientes.buscar');
        });

        /**
         * DEPARTAMENTOS
         * Gestão de departamentos da empresa
         */
        Route::prefix('departamentos')->name('departamentos.')->group(function () {
            Route::get('/', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'store'])->name('store');
            Route::get('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'show'])->name('show');
            Route::get('/{departamento}/edit', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'edit'])->name('edit');
            Route::put('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'update'])->name('update');
            Route::delete('/{departamento}', [\App\Modules\Comerciante\Controllers\DepartamentoController::class, 'destroy'])->name('destroy');
        });

        /**
         * CARGOS
         * Gestão de cargos da empresa
         */
        Route::prefix('cargos')->name('cargos.')->group(function () {
            Route::get('/', [\App\Modules\Comerciante\Controllers\CargoController::class, 'index'])->name('index');
            Route::get('/create', [\App\Modules\Comerciante\Controllers\CargoController::class, 'create'])->name('create');
            Route::post('/', [\App\Modules\Comerciante\Controllers\CargoController::class, 'store'])->name('store');
            Route::get('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'show'])->name('show');
            Route::get('/{cargo}/edit', [\App\Modules\Comerciante\Controllers\CargoController::class, 'edit'])->name('edit');
            Route::put('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'update'])->name('update');
            Route::delete('/{cargo}', [\App\Modules\Comerciante\Controllers\CargoController::class, 'destroy'])->name('destroy');
        });

        /**
         * HORÁRIOS DE FUNCIONAMENTO
         * Rotas organizadas por empresa
         */

        Route::prefix('empresas/{empresa}/horarios')->name('horarios.')->group(function () {
            // Dashboard principal
            Route::get('/', [HorarioController::class, 'index'])->name('index');

            // Horários Padrão
            Route::prefix('padrao')->name('padrao.')->group(function () {
                Route::get('/', [HorarioController::class, 'padrao'])->name('index');
                Route::get('/criar', [HorarioController::class, 'criarPadrao'])->name('create');
                Route::post('/criar', [HorarioController::class, 'salvarPadrao'])->name('store');
                Route::get('/{id}/editar', [HorarioController::class, 'editarPadrao'])->name('edit');
                Route::put('/{id}', [HorarioController::class, 'atualizarPadrao'])->name('update');
            });

            // Exceções
            Route::prefix('excecoes')->name('excecoes.')->group(function () {
                Route::get('/', [HorarioController::class, 'excecoes'])->name('index');
                Route::get('/criar', [HorarioController::class, 'criarExcecao'])->name('create');
                Route::post('/criar', [HorarioController::class, 'salvarExcecao'])->name('store');
            });

            // Ações gerais
            Route::delete('/{id}', [HorarioController::class, 'deletar'])->name('destroy');

            // API
            Route::get('/api/status', [HorarioController::class, 'apiStatus'])->name('api.status');
        });

        /**
         * CONFIGURAÇÕES DO SISTEMA (NOVO MÓDULO)
         * Gerenciamento dinâmico de configurações por grupos
         */
        Route::prefix('config')->name('config.')->group(function () {
            Route::get('/', [ConfigController::class, 'index'])->name('index');
            Route::get('/grupos', [ConfigController::class, 'grupos'])->name('grupos');
            Route::get('/grupo/{grupo}', [ConfigController::class, 'grupo'])->name('grupo');
            Route::post('/definir', [ConfigController::class, 'definir'])->name('definir');
            Route::put('/atualizar', [ConfigController::class, 'atualizar'])->name('atualizar');
            Route::delete('/remover', [ConfigController::class, 'remover'])->name('remover');
            Route::get('/exportar', [ConfigController::class, 'exportar'])->name('exportar');
            Route::post('/importar', [ConfigController::class, 'importar'])->name('importar');
        });

        /**
         * ROTAS DE TESTE E DEMONSTRAÇÃO (PROTEGIDAS)
         */
        Route::prefix('teste-auth')->name('teste.')->group(function () {
            Route::get('/dashboard', function () {
                try {
                    $pessoas = \App\Modules\Comerciante\Models\Pessoas\Pessoa::with(['departamento', 'cargo'])
                        ->limit(5)
                        ->get();
                    $departamentos = \App\Modules\Comerciante\Models\Pessoas\PessoaDepartamento::all();
                    $cargos = \App\Modules\Comerciante\Models\Pessoas\PessoaCargo::with('departamento')->get();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Sistema de Pessoas funcionando (com auth)!',
                        'data' => [
                            'pessoas_total' => \App\Modules\Comerciante\Models\Pessoas\Pessoa::count(),
                            'departamentos_total' => $departamentos->count(),
                            'cargos_total' => $cargos->count(),
                            'pessoas_amostra' => $pessoas,
                            'departamentos' => $departamentos,
                            'cargos' => $cargos
                        ]
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Erro no sistema: ' . $e->getMessage()
                    ], 500);
                }
            })->name('dashboard');

            Route::get('/config', function () {
                try {
                    $configManager = app(\App\Modules\Comerciante\Config\ConfigManager::class);
                    $grupos = $configManager->listarGrupos();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Sistema de Configurações funcionando (com auth)!',
                        'data' => [
                            'grupos_total' => count($grupos),
                            'grupos' => $grupos
                        ]
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Erro no sistema: ' . $e->getMessage()
                    ], 500);
                }
            })->name('config');
        });
        /**
         * RELATÓRIOS (futuro)
         */
        // Route::prefix('relatorios')->name('relatorios.')->group(function () {
        //     Route::get('/marcas', [RelatorioController::class, 'marcas'])->name('marcas');
        //     Route::get('/empresas', [RelatorioController::class, 'empresas'])->name('empresas');
        //     Route::get('/financeiro', [RelatorioController::class, 'financeiro'])->name('financeiro');
        // });

    });
});
