<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/admin');
});

// ROTA DE TESTE - Layout
Route::get('/teste-layout', function () {
    return view('teste-layout');
})->name('teste.layout');

// ROTA DE TESTE - DEBUG HORÁRIOS (sem autenticação)
Route::get('/teste-horarios-debug/{empresa}', function ($empresa) {
    return response("
        <h1>TESTE DEBUG - HORÁRIOS</h1>
        <p>Empresa: $empresa</p>
        <p>Se você está vendo esta mensagem, a rota funciona!</p>
        <p>Timestamp: " . now() . "</p>
        <p><a href='/comerciantes/empresas/$empresa/horarios'>Tentar rota original</a></p>
    ");
})->name('teste.horarios.debug');

// ROTA DE TESTE - Gráfico
Route::get('/teste-grafico', function () {
    return view('admin.notificacoes.teste-grafico');
})->name('teste.grafico');

// ROTA DE TESTE - Estatísticas Simples
Route::get('/teste-estatisticas', function () {
    return view('admin.notificacoes.estatisticas-simples');
})->name('teste.estatisticas');

// ROTAS DO MÓDULO COMERCIANTE
require __DIR__ . '/comerciante.php';

// ROTA DE TESTE - Config Simples
Route::get('/teste-config', function () {
    $controller = new App\Http\Controllers\Admin\ConfigController();
    $request = request();
    $data = $controller->index($request);
    return view('admin.config.teste-simples', $data->getData());
})->name('teste.config');

// ROTA DE TESTE - Create Config Simples
Route::get('/teste-config-create', function () {
    $grupos = App\Models\Config\ConfigGroup::where('ativo', true)->orderBy('nome')->get();
    $sites = App\Models\Config\ConfigSite::where('ativo', true)->orderBy('nome')->get();
    $tipos = [
        'string' => 'Texto',
        'text' => 'Texto Longo',
        'integer' => 'Número Inteiro',
        'float' => 'Número Decimal',
        'boolean' => 'Verdadeiro/Falso',
        'json' => 'JSON',
        'array' => 'Array',
        'email' => 'Email',
        'url' => 'URL',
        'date' => 'Data',
        'datetime' => 'Data e Hora',
        'password' => 'Senha'
    ];

    return view('admin.config.create_simple', compact('grupos', 'sites', 'tipos'));
})->name('teste.config.create');

// ROTA DE TESTE - Sistema de Notificações
Route::get('/teste-notificacoes', function () {
    return view('admin.notificacoes.teste-simples');
})->name('teste.notificacoes');

// ROTAS DE TESTE - GET para evitar problemas de CSRF
Route::get('/admin/notificacoes/teste/conexao', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'ok', 'message' => 'Conexão ativa']);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/admin/notificacoes/teste/tabelas', function () {
    try {
        $tabelas = [
            'notificacao_aplicacoes',
            'notificacao_tipos_evento',
            'notificacao_templates',
            'notificacao_enviadas',
            'notificacao_templates_historico',
            'notificacao_agendamentos',
            'notificacao_preferencias_usuario',
            'notificacao_estatisticas'
        ];

        $existentes = [];
        foreach ($tabelas as $tabela) {
            if (DB::getSchemaBuilder()->hasTable($tabela)) {
                $existentes[] = $tabela;
            }
        }

        return response()->json(['status' => 'ok', 'tabelas' => $existentes]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/admin/notificacoes/teste/models', function () {
    try {
        $aplicacoes = \App\Models\Notificacao\NotificacaoAplicacao::count();
        $tipos_evento = \App\Models\Notificacao\NotificacaoTipoEvento::count();

        return response()->json([
            'status' => 'ok',
            'aplicacoes' => $aplicacoes,
            'tipos_evento' => $tipos_evento
        ]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/admin/notificacoes/teste/services', function () {
    try {
        $configService = new \App\Services\NotificacaoConfigService(1);
        $configs = $configService->getBehaviorConfig();

        return response()->json([
            'status' => 'ok',
            'configs' => count($configs)
        ]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/admin/notificacoes/teste/enviar', function () {
    try {
        $notificacaoService = new \App\Services\NotificacaoService(
            new \App\Services\NotificacaoConfigService(1),
            new \App\Services\NotificacaoTemplateService()
        );

        // Dados de teste mais variados
        $tiposEventos = ['pedido_criado', 'pagamento_aprovado', 'produto_baixo_estoque', 'cliente_novo'];
        $tipoEscolhido = $tiposEventos[array_rand($tiposEventos)];

        $dadosTest = [
            'pedido_criado' => [
                'pedido_id' => rand(10000, 99999),
                'cliente_nome' => 'João Silva',
                'valor_total' => 'R$ ' . number_format(rand(50, 500), 2, ',', '.'),
                'produtos' => rand(1, 5) . ' itens'
            ],
            'pagamento_aprovado' => [
                'transacao_id' => 'TXN' . rand(100000, 999999),
                'valor' => 'R$ ' . number_format(rand(25, 300), 2, ',', '.'),
                'metodo' => ['PIX', 'Cartão', 'Boleto'][rand(0, 2)]
            ],
            'produto_baixo_estoque' => [
                'produto_nome' => ['Pizza Margherita', 'Hambúrguer Especial', 'Lasanha'][rand(0, 2)],
                'estoque_atual' => rand(1, 5),
                'estoque_minimo' => 10
            ],
            'cliente_novo' => [
                'cliente_nome' => ['Maria Santos', 'Pedro Lima', 'Ana Costa'][rand(0, 2)],
                'email' => 'cliente' . rand(100, 999) . '@exemplo.com',
                'telefone' => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999)
            ]
        ];

        $resultado = $notificacaoService->sendEvent(
            $tipoEscolhido,
            $dadosTest[$tipoEscolhido],
            ['usuario_id' => 1, 'empresa_id' => 1]
        );

        return response()->json([
            'status' => 'ok',
            'enviado' => $resultado,
            'tipo_evento' => $tipoEscolhido,
            'dados' => $dadosTest[$tipoEscolhido],
            'message' => 'Notificação de teste processada com sucesso!'
        ]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

// Rota POST para envios personalizados de teste
Route::post('/admin/notificacoes/teste/enviar', function () {
    try {
        $dados = request()->all();

        // Validação básica
        if (empty($dados['destinatario']) || empty($dados['tipo']) || empty($dados['canal'])) {
            return response()->json([
                'success' => false,
                'message' => 'Dados obrigatórios não informados'
            ], 400);
        }

        // Prepara os dados para inserção direta no banco
        $dadosNotificacao = [
            'empresa_id' => 1,
            'aplicacao_id' => 1,
            'template_id' => 1,
            'email_destinatario' => $dados['canal'] === 'email' ? $dados['destinatario'] : null,
            'telefone_destinatario' => in_array($dados['canal'], ['sms', 'push']) ? $dados['destinatario'] : null,
            'canal' => $dados['canal'],
            'titulo' => $dados['titulo'] ?? 'Teste de Notificação',
            'mensagem' => $dados['mensagem'] ?? 'Esta é uma notificação de teste',
            'status' => 'enviado',
            'prioridade' => $dados['prioridade'] ?? 'media',
            'enviado_em' => now(),
            'entregue_em' => now(),
            'lido_em' => rand(1, 10) > 5 ? now() : null, // 50% chance de ser lida
            'mensagem_erro' => null,
            'tentativas' => 1,
            'id_externo' => 'test_' . uniqid(),
            'dados_processados' => json_encode([
                'tipo_teste' => $dados['tipo'],
                'prioridade' => $dados['prioridade'] ?? 'normal',
                'agendamento' => $dados['agendamento'] ?? false
            ]),
            'dados_evento_origem' => json_encode($dados),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Insere diretamente na tabela
        $id = DB::table('notificacao_enviadas')->insertGetId($dadosNotificacao);

        // Simula diferentes resultados baseado no canal
        if ($dados['canal'] === 'push') {
            // Push notifications sempre têm sucesso em testes
            // (remover simulação de falha para testes)

            // Opcional: Para simular falhas ocasionais, descomente a linha abaixo
            // $sucesso = rand(1, 10) > 1; // 90% de chance de sucesso
            // 
            // if (!$sucesso) {
            //     DB::table('notificacao_enviadas')
            //         ->where('id', $id)
            //         ->update([
            //             'status' => 'falhou',
            //             'mensagem_erro' => 'Token de push inválido ou expirado',
            //             'entregue_em' => null,
            //             'updated_at' => now()
            //         ]);
            //
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Falha no envio via push: Token inválido'
            //     ]);
            // }
        }

        // Adiciona log
        DB::table('notificacao_logs')->insert([
            'notificacao_id' => $id,
            'nivel' => 'info',
            'mensagem' => "Notificação de teste enviada via {$dados['canal']}",
            'dados' => json_encode($dados),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Notificação enviada com sucesso via {$dados['canal']}",
            'id' => $id,
            'dados' => $dadosNotificacao
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro interno: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/admin/notificacoes/teste/ultimas', function () {
    try {
        $notificacoes = \App\Models\Notificacao\NotificacaoEnviada::with(['aplicacao', 'tipoEvento'])
            ->where('empresa_id', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'ok',
            'notificacoes' => $notificacoes->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'tipo_evento' => $notif->tipoEvento->nome ?? $notif->tipo_evento_codigo,
                    'status' => $notif->status,
                    'canal' => $notif->canal,
                    'created_at' => $notif->created_at->format('d/m/Y H:i:s')
                ];
            })
        ]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}); // ROTA TEMPORÁRIA - Gerador de chave APP_KEY
Route::get('/gerar-key', function () {
    $key = base64_encode(random_bytes(32));
    $appKey = 'base64:' . $key;

    return response("<html><head><title>Gerador de Chave Laravel</title></head><body>
        <h1>🔑 Gerador de Chave APP_KEY</h1>
        <h2>Nova chave gerada:</h2>
        <div style='background:#f5f5f5; padding:15px; border:1px solid #ddd; font-family:monospace; font-size:16px;'>
            APP_KEY={$appKey}
        </div>
        <br>
        <h3>📋 Instruções:</h3>
        <ol>
            <li>Copie a linha APP_KEY= acima</li>
            <li>Abra o arquivo .env</li>
            <li>Substitua a linha APP_KEY existente</li>
            <li>Salve o arquivo</li>
            <li><a href='/login'>Teste o login aqui</a></li>
        </ol>
        <p>✅ Esta chave é válida para AES-256-CBC do Laravel 11!</p>
    </body></html>");
})->name('gerar.key');

// ROTA TEMPORÁRIA - Teste de sistema
Route::get('/teste-sistema', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        $hasUsers = DB::getSchemaBuilder()->hasTable('empresa_usuarios');
        $userCount = $hasUsers ? DB::table('empresa_usuarios')->count() : 0;

        return response("<html><head><title>Teste do Sistema</title></head><body>
            <h1>🔧 Teste do Sistema</h1>
            <h2>Status da Conexão:</h2>
            <ul>
                <li>✅ Laravel funcionando</li>
                <li>✅ Banco conectado: <strong>{$dbName}</strong></li>
                <li>" . ($hasUsers ? '✅' : '❌') . " Tabela empresa_usuarios: " . ($hasUsers ? 'Existe' : 'Não existe') . "</li>
                <li>👥 Usuários cadastrados: <strong>{$userCount}</strong></li>
            </ul>
            <h3>Ações:</h3>
            <p><a href='/gerar-key'>🔑 Gerar nova chave APP_KEY</a></p>
            <p><a href='/login'>🔐 Ir para Login</a></p>
            <p><a href='/'>🏠 Ir para Home</a></p>
        </body></html>");
    } catch (Exception $e) {
        return response("<html><head><title>Erro no Sistema</title></head><body>
            <h1>❌ Erro no Sistema</h1>
            <p><strong>Erro:</strong> {$e->getMessage()}</p>
            <p><strong>Linha:</strong> {$e->getLine()}</p>
            <p><strong>Arquivo:</strong> {$e->getFile()}</p>
            <h3>Ações:</h3>
            <p><a href='/gerar-key'>🔑 Gerar nova chave APP_KEY</a></p>
        </body></html>");
    }
})->name('teste.sistema');

// ROTA TEMPORÁRIA - Diagnóstico de Login
Route::get('/debug-login', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();

        // Verificar se tem usuários
        $users = DB::table('empresa_usuarios')->limit(5)->get();

        $html = "<html><head><title>Debug do Login</title></head><body>";
        $html .= "<h1>🔍 Diagnóstico do Sistema de Login</h1>";

        $html .= "<h2>Informações do Sistema:</h2>";
        $html .= "<ul>";
        $html .= "<li>✅ Laravel funcionando</li>";
        $html .= "<li>✅ Banco: <strong>{$dbName}</strong></li>";
        $html .= "<li>✅ APP_KEY configurada</li>";

        // Verificar criptografia
        try {
            $encrypted = encrypt('teste');
            $decrypted = decrypt($encrypted);
            $html .= "<li>✅ Criptografia funcionando</li>";
        } catch (Exception $e) {
            $html .= "<li>❌ Erro na criptografia: {$e->getMessage()}</li>";
        }

        $html .= "</ul>";

        $html .= "<h2>Usuários Disponíveis:</h2>";
        if ($users->count() > 0) {
            $html .= "<table border='1' style='border-collapse: collapse;'>";
            $html .= "<tr><th>Email</th><th>Status</th><th>Empresa ID</th><th>Senha Hash</th></tr>";
            foreach ($users as $user) {
                $passwordPreview = substr($user->password, 0, 20) . '...';
                $html .= "<tr>";
                $html .= "<td>{$user->email}</td>";
                $html .= "<td>{$user->status}</td>";
                $html .= "<td>{$user->empresa_id}</td>";
                $html .= "<td style='font-family:monospace; font-size:11px;'>{$passwordPreview}</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html .= "<p>❌ Nenhum usuário encontrado!</p>";
        }

        // Teste de autenticação manual
        $html .= "<h2>Teste de Login Manual:</h2>";
        $html .= "<form method='POST' action='/debug-login'>";
        $html .= csrf_field();
        $html .= "<p>Email: <input type='email' name='email' value='admin@teste.com' style='width:200px;'></p>";
        $html .= "<p>Senha: <input type='password' name='password' style='width:200px;'></p>";
        $html .= "<p><button type='submit'>Testar Login</button></p>";
        $html .= "</form>";

        $html .= "<h3>Links Úteis:</h3>";
        $html .= "<p><a href='/login'>🔐 Página de Login Oficial</a></p>";
        $html .= "<p><a href='/gerar-key'>🔑 Gerar Nova Chave</a></p>";

        $html .= "</body></html>";

        return response($html);
    } catch (Exception $e) {
        return response("<html><body>
            <h1>❌ Erro no Debug</h1>
            <p><strong>Erro:</strong> {$e->getMessage()}</p>
            <p><strong>Linha:</strong> {$e->getLine()}</p>
            <p><strong>Arquivo:</strong> {$e->getFile()}</p>
        </body></html>");
    }
})->name('debug.login');

// ROTA TEMPORÁRIA - Teste específico de senha
Route::get('/teste-senha', function () {
    return response("<html><head><title>Teste de Senha</title></head><body>
        <h1>🔐 Teste Específico de Senha</h1>
        <form method='POST'>
            " . csrf_field() . "
            <p>Email: <input type='email' name='email' value='admin@teste.com' style='width:250px;'></p>
            <p>Senha: <input type='text' name='password' placeholder='Digite a senha' style='width:250px;'></p>
            <p><button type='submit'>Testar Senha</button></p>
        </form>
    </body></html>");
});

Route::post('/teste-senha', function () {
    try {
        $email = request('email');
        $password = request('password');

        // Buscar usuário
        $user = DB::table('empresa_usuarios')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return response('<h1>❌ Usuário não encontrado</h1><a href="/teste-senha">← Voltar</a>');
        }

        $html = "<html><head><title>Resultado do Teste</title></head><body>";
        $html .= "<h1>🔍 Resultado do Teste de Senha</h1>";

        $html .= "<h3>Informações do Usuário:</h3>";
        $html .= "<p><strong>Email:</strong> {$user->email}</p>";
        $html .= "<p><strong>Status:</strong> {$user->status}</p>";
        $html .= "<p><strong>Empresa ID:</strong> {$user->empresa_id}</p>";

        $html .= "<h3>Informações da Senha:</h3>";
        $html .= "<p><strong>Senha digitada:</strong> {$password}</p>";
        $html .= "<p><strong>Tamanho da senha:</strong> " . strlen($password) . " caracteres</p>";
        $html .= "<p><strong>Hash no banco:</strong> " . substr($user->senha, 0, 40) . "...</p>";
        $html .= "<p><strong>Tamanho do hash:</strong> " . strlen($user->senha) . " caracteres</p>";

        $html .= "<h3>Testes de Verificação:</h3>";

        // Teste 1: Hash::check do Laravel
        $laravelCheck = Hash::check($password, $user->senha);
        $html .= "<p><strong>Hash::check (Laravel):</strong> " . ($laravelCheck ? '✅ TRUE' : '❌ FALSE') . "</p>";

        // Teste 2: password_verify do PHP nativo
        $phpCheck = password_verify($password, $user->senha);
        $html .= "<p><strong>password_verify (PHP):</strong> " . ($phpCheck ? '✅ TRUE' : '❌ FALSE') . "</p>";

        // Teste 3: Criar um novo hash da senha
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $html .= "<p><strong>Novo hash gerado:</strong> " . substr($newHash, 0, 40) . "...</p>";

        // Teste 4: Verificar se o novo hash funciona
        $newHashCheck = password_verify($password, $newHash);
        $html .= "<p><strong>Verificação do novo hash:</strong> " . ($newHashCheck ? '✅ TRUE' : '❌ FALSE') . "</p>";

        // Teste 5: Verificar algorítmo do hash existente
        $hashInfo = password_get_info($user->senha);
        $html .= "<p><strong>Algoritmo do hash atual:</strong> " . $hashInfo['algoName'] . "</p>";

        $html .= "<h3>Diagnóstico:</h3>";
        if ($laravelCheck || $phpCheck) {
            $html .= "<p>✅ <strong>SENHA CORRETA!</strong> O problema deve estar em outro lugar.</p>";
        } else {
            $html .= "<p>❌ <strong>SENHA INCORRETA</strong> ou hash corrompido.</p>";
            $html .= "<p>💡 <strong>Solução:</strong> Execute a query SQL abaixo para atualizar a senha:</p>";
            $html .= "<code style='background:#f5f5f5; padding:10px; display:block; margin:10px 0;'>";
            $html .= "UPDATE empresa_usuarios SET password = '{$newHash}' WHERE email = '{$email}';";
            $html .= "</code>";
        }

        $html .= "<p><a href='/teste-senha'>← Voltar ao teste</a></p>";
        $html .= "<p><a href='/login'>🔐 Ir para Login</a></p>";
        $html .= "</body></html>";

        return response($html);
    } catch (Exception $e) {
        return response("<h1>❌ Erro: {$e->getMessage()}</h1><a href='/teste-senha'>← Voltar</a>");
    }
});

Route::post('/debug-login', function () {
    try {
        $email = request('email');
        $password = request('password');

        $html = "<html><head><title>Resultado do Teste</title></head><body>";
        $html .= "<h1>🔍 Resultado do Teste de Login</h1>";

        // Buscar usuário
        $user = DB::table('empresa_usuarios')
            ->where('email', $email)
            ->where('status', 'ativo')
            ->first();

        if ($user) {
            $html .= "<p>✅ Usuário encontrado: {$user->email}</p>";
            $html .= "<p>Empresa ID: {$user->empresa_id}</p>";
            $html .= "<p>Status: {$user->status}</p>";

            // Verificar senha
            if (password_verify($password, $user->password)) {
                $html .= "<p>✅ <strong>Senha CORRETA!</strong></p>";
                $html .= "<p>🎉 O login deveria funcionar!</p>";

                // Verificar o que pode estar dando erro no LoginController
                $html .= "<h3>Possíveis problemas:</h3>";
                $html .= "<ul>";
                $html .= "<li>Verificar se o model EmpresaUsuario está funcionando</li>";
                $html .= "<li>Verificar se há middleware interferindo</li>";
                $html .= "<li>Verificar se a sessão está funcionando</li>";
                $html .= "</ul>";
            } else {
                $html .= "<p>❌ Senha incorreta</p>";

                // Testar com hash manual
                $testHash = password_hash($password, PASSWORD_DEFAULT);
                $html .= "<p>Hash da senha testada: " . substr($testHash, 0, 30) . "...</p>";
                $html .= "<p>Hash no banco: " . substr($user->password, 0, 30) . "...</p>";
            }
        } else {
            $html .= "<p>❌ Usuário não encontrado ou inativo</p>";

            // Verificar se existe com qualquer status
            $userAny = DB::table('empresa_usuarios')->where('email', $email)->first();
            if ($userAny) {
                $html .= "<p>ℹ️ Usuário existe mas com status: {$userAny->status}</p>";
            }
        }

        $html .= "<p><a href='/debug-login'>← Voltar ao Debug</a></p>";
        $html .= "</body></html>";

        return response($html);
    } catch (Exception $e) {
        return response("<html><body>
            <h1>❌ Erro no Teste</h1>
            <p>{$e->getMessage()}</p>
        </body></html>");
    }
});

// ============================================================================
// ROTAS DE AUTENTICAÇÃO - Sistema Simplificado
// ============================================================================

use App\Http\Controllers\Auth\LoginControllerSimplified;

// Login simplificado
Route::get('/login', [LoginControllerSimplified::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginControllerSimplified::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', [LoginControllerSimplified::class, 'logout'])->name('logout');
Route::get('/logout', [LoginControllerSimplified::class, 'logout']); // Para compatibilidade

// Reset de Senha
Route::get('/login/forgot', [LoginControllerSimplified::class, 'showForgotForm'])->name('password.request');
Route::post('/login/forgot', [LoginControllerSimplified::class, 'resetPasswordRequest'])->name('password.email');

// Rotas protegidas por autenticação simplificada
Route::middleware('auth.simple')->group(function () {

    // Dashboard geral (redireciona para admin)
    Route::get('/dashboard', function () {
        return redirect('/admin/dashboard');
    })->name('dashboard');

    // Admin (redireciona para admin/dashboard)
    Route::get('/admin', function () {
        return redirect('/admin/dashboard');
    })->middleware('auth.simple:60');

    // Admin Dashboard - requer nível 60 ou superior (supervisor+)
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware('auth.simple:60')
        ->name('admin.dashboard');

    // Página de acesso negado
    Route::get('/admin/access-denied', function () {
        return view('admin.access-denied');
    })->name('admin.access-denied');

    // Rotas básicas de administração
    Route::get('/admin/usuarios', function () {
        return view('admin.usuarios')->with('message', 'Área de Gerenciamento de Usuários - Em desenvolvimento');
    })->middleware('auth.simple:80')->name('admin.usuarios');

    Route::get('/admin/config', [App\Http\Controllers\Admin\ConfigController::class, 'index'])->middleware('auth.simple:80')->name('admin.config.index');
    Route::get('/admin/config/create', [App\Http\Controllers\Admin\ConfigController::class, 'create'])->middleware('auth.simple:80')->name('admin.config.create');
    Route::get('/admin/config/{id}/edit', [App\Http\Controllers\Admin\ConfigController::class, 'edit'])->middleware('auth.simple:80')->name('admin.config.edit');
    Route::post('/admin/config', [App\Http\Controllers\Admin\ConfigController::class, 'store'])->middleware('auth.simple:80')->name('admin.config.store');
    Route::put('/admin/config/{id}', [App\Http\Controllers\Admin\ConfigController::class, 'update'])->middleware('auth.simple:80')->name('admin.config.update');
    Route::delete('/admin/config/{id}', [App\Http\Controllers\Admin\ConfigController::class, 'destroy'])->middleware('auth.simple:80')->name('admin.config.destroy');
    Route::post('/admin/config/backup', [App\Http\Controllers\Admin\ConfigController::class, 'backup'])->middleware('auth.simple:80')->name('admin.config.backup');
    Route::post('/admin/config/test-email', [App\Http\Controllers\Admin\ConfigController::class, 'testEmail'])->middleware('auth.simple:80')->name('admin.config.test-email');
    // Rotas AJAX para configurações
    Route::post('/admin/config/set-value', [App\Http\Controllers\Admin\ConfigController::class, 'setValue'])->middleware('auth.simple:80')->name('admin.config.set-value');
    Route::post('/admin/config/clear-cache', [App\Http\Controllers\Admin\ConfigController::class, 'clearCache'])->middleware('auth.simple:80')->name('admin.config.clear-cache');

    Route::get('/admin/perfil', function () {
        return view('admin.perfil')->with('message', 'Perfil do Usuário - Em desenvolvimento');
    })->name('admin.perfil');

    Route::get('/admin/relatorios', function () {
        return view('admin.relatorios')->with('message', 'Relatórios - Em desenvolvimento');
    })->middleware('auth.simple:60')->name('admin.relatorios');

    // ============================================================================
    // ADMIN FIDELIDADE - Apenas visualização e relatórios (READ-ONLY)
    // ============================================================================
    Route::prefix('admin/fidelidade')->name('admin.fidelidade.')->middleware('auth.simple:60')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'dashboard'])->name('dashboard');
        Route::get('/index', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'dashboard'])->name('index');
        Route::get('/clientes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'clientes'])->name('clientes');
        Route::get('/transacoes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'transacoes'])->name('transacoes');
        Route::get('/cupons', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cupons'])->name('cupons');
        Route::get('/cashback', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cashback'])->name('cashback');
        Route::get('/relatorios', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'relatorios'])->name('relatorios');
        Route::get('/configuracoes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'configuracoes'])->name('configuracoes');
    });

    // ============================================================================
    // ROTAS DE TESTE SEM MIDDLEWARE (TEMPORÁRIO)
    // ============================================================================
    Route::prefix('test/fidelidade')->name('test.fidelidade.')->group(function () {
        Route::get('/clientes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'clientes'])->name('clientes');
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'dashboard'])->name('dashboard');
        Route::get('/transacoes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'transacoes'])->name('transacoes');
        Route::get('/cupons', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cupons'])->name('cupons');
        Route::get('/cashback', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'cashback'])->name('cashback');
        Route::get('/relatorios', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'relatorios'])->name('relatorios');
        Route::get('/configuracoes', [App\Http\Controllers\Admin\AdminFidelidadeController::class, 'configuracoes'])->name('configuracoes');
    });

    // ============================================================================
    // MANTER COMPATIBILIDADE TEMPORÁRIA (será removido após migração completa)
    // ============================================================================
    // Páginas antigas - redirecionamento temporário
    Route::get('/admin/fidelidade/programas', function () {
        return redirect()->route('admin.fidelidade.cashback');
    })->middleware('auth.simple:60')->name('admin.fidelidade.programas');

    Route::get('/admin/fidelidade/cartoes', function () {
        return redirect()->route('admin.fidelidade.clientes');
    })->middleware('auth.simple:60')->name('admin.fidelidade.cartoes');
    Route::patch('/admin/fidelidade/toggle/{tipo}/{id}', [App\Http\Controllers\FidelidadeController::class, 'toggleStatus'])->middleware('auth.simple:60')->name('admin.fidelidade.toggle');

    Route::get('/comerciante/dashboard', [DashboardController::class, 'comercianteDashboard'])->name('comerciante.dashboard');
    Route::get('/cliente/dashboard', [DashboardController::class, 'clienteDashboard'])->name('cliente.dashboard');
    Route::get('/entregador/dashboard', [DashboardController::class, 'entregadorDashboard'])->name('entregador.dashboard');
});

// ============================================================================
// ROTAS ESPECÍFICAS POR TIPO DE USUÁRIO (protegidas por autenticação)
// ============================================================================

// ADMIN - Rotas de administração
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // COMENTADO PARA EVITAR CONFLITO COM SISTEMA SIMPLIFICADO
    // Route::resource('config', App\Http\Controllers\Admin\ConfigController::class);

    // Rotas temporárias para módulos em desenvolvimento - COMENTADAS PARA EVITAR CONFLITO COM SISTEMA SIMPLIFICADO
    /*
    Route::get('usuarios', function () {
        return view('admin.temp', ['module' => 'Usuários']);
    })->name('usuarios.index');

    Route::get('empresas', function () {
        return view('admin.temp', ['module' => 'Empresas']);
    })->name('empresas.index');

    Route::get('financeiro', function () {
        return view('admin.temp', ['module' => 'Financeiro']);
    })->name('financeiro.index');

    Route::get('pdv', function () {
        return view('admin.temp', ['module' => 'PDV']);
    })->name('pdv.index');

    Route::get('delivery', function () {
        return view('admin.temp', ['module' => 'Delivery']);
    })->name('delivery.index');
    */

    Route::get('relatorios', function () {
        return view('admin.temp', ['module' => 'Relatórios']);
    })->name('relatorios.index');

    Route::get('sistema', function () {
        return view('admin.temp', ['module' => 'Sistema']);
    })->name('sistema.index');

    // Rotas adicionais para configurações
    Route::get('config/group/{group}', function ($group) {
        return redirect()->route('admin.config.index', ['grupo' => $group]);
    })->name('config.group');

    Route::get('config/export', function () {
        return response()->json(['message' => 'Export em desenvolvimento']);
    })->name('config.export');

    Route::post('config/set-value', function () {
        return response()->json(['success' => false, 'message' => 'Funcionalidade em desenvolvimento']);
    })->name('config.set-value');

    Route::post('config/clear-cache', function () {
        return response()->json(['success' => true, 'message' => 'Cache limpo com sucesso']);
    })->name('config.clear-cache');

    Route::get('config/{config}/history-detail/{history}', function ($config, $history) {
        return response()->json(['message' => 'Detalhes do histórico em desenvolvimento']);
    })->name('config.history-detail');
});

// ADMIN - Sistema de Dashboard e Gestão
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('index');

    // Merchants
    Route::get('/merchants', function () {
        return view('admin.temp', ['module' => 'Merchants']);
    })->name('merchants.index');
    Route::get('/merchants/create', function () {
        return view('admin.temp', ['module' => 'Criar Merchant']);
    })->name('merchants.create');

    // Affiliates  
    Route::get('/affiliates', function () {
        return view('admin.temp', ['module' => 'Afiliados']);
    })->name('affiliates.index');
    Route::get('/affiliates/programs', function () {
        return view('admin.temp', ['module' => 'Programas de Afiliados']);
    })->name('affiliates.programs');

    // Reports
    Route::get('/reports', function () {
        return view('admin.temp', ['module' => 'Relatórios']);
    })->name('reports.index');
    Route::get('/reports/revenue', function () {
        return view('admin.temp', ['module' => 'Relatório de Receita']);
    })->name('reports.revenue');

    // Subscriptions
    Route::get('/subscriptions', function () {
        return view('admin.temp', ['module' => 'Assinaturas']);
    })->name('subscriptions.index');

    // Settings
    Route::get('/settings', function () {
        return view('admin.temp', ['module' => 'Configurações']);
    })->name('settings');

    // Logs
    Route::get('/logs', function () {
        return view('admin.temp', ['module' => 'Logs do Sistema']);
    })->name('logs');

    // SISTEMA DE NOTIFICAÇÕES - PAINEL ADMINISTRATIVO
    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        // Página principal do painel
        Route::get('/', function () {
            $stats = [
                'hoje' => \App\Models\Notificacao\NotificacaoEnviada::whereDate('created_at', today())->count(),
                'pendentes' => \App\Models\Notificacao\NotificacaoAgendamento::where('ativo', true)->whereNull('ultima_execucao_em')->count(),
                'agendadas' => \App\Models\Notificacao\NotificacaoAgendamento::where('ativo', true)->count(),
                'taxa_sucesso' => '98.5',
                'templates_ativos' => \App\Models\Notificacao\NotificacaoTemplate::where('ativo', true)->count(),
                'total_templates' => \App\Models\Notificacao\NotificacaoTemplate::count(),
                'crescimento_hoje' => '12'
            ];
            return view('admin.notificacoes.index', compact('stats'));
        })->name('index');

        // Templates
        Route::get('/templates', function () {
            return view('admin.notificacoes.templates');
        })->name('templates');

        // Tipos de evento
        Route::get('/tipos', function () {
            return view('admin.notificacoes.tipos');
        })->name('tipos');

        // Aplicações
        Route::get('/aplicacoes', function () {
            return view('admin.notificacoes.aplicacoes');
        })->name('aplicacoes');

        // Notificações enviadas
        // Notificações enviadas
        Route::get('/enviadas', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'index'])->name('enviadas');
        Route::get('/enviadas/dados', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'dados'])->name('enviadas.dados');
        Route::get('/enviadas/estatisticas', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'estatisticas'])->name('enviadas.estatisticas');
        Route::get('/enviadas/{id}/detalhes', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'detalhes'])->name('enviadas.detalhes');
        Route::post('/enviadas/{id}/reenviar', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'reenviar'])->name('enviadas.reenviar');
        Route::get('/enviadas/{id}/logs', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'logs'])->name('enviadas.logs');
        Route::get('/enviadas/exportar', [App\Http\Controllers\Admin\NotificacoesEnviadasController::class, 'exportar'])->name('enviadas.exportar');

        // Estatísticas
        Route::get('/estatisticas', [\App\Http\Controllers\Admin\EstatisticasController::class, 'index'])->name('estatisticas');
        Route::get('/estatisticas/dados', [\App\Http\Controllers\Admin\EstatisticasController::class, 'dados'])->name('estatisticas.dados');

        // Logs específicos de notificações
        Route::get('/logs', function () {
            return view('admin.notificacoes.logs');
        })->name('logs');

        // APIs de Logs
        Route::get('/logs/api/dados', [App\Http\Controllers\Admin\LogsController::class, 'apiLogs'])->name('logs.api.dados');
        Route::get('/logs/api/estatisticas', [App\Http\Controllers\Admin\LogsController::class, 'apiEstatisticas'])->name('logs.api.estatisticas');
        Route::get('/logs/api/detalhes/{id}', [App\Http\Controllers\Admin\LogsController::class, 'apiDetalhes'])->name('logs.api.detalhes');

        // Página de teste
        Route::get('/teste', function () {
            return view('admin.notificacoes.teste');
        })->name('teste');

        // Diagnóstico
        Route::get('/diagnostico', function () {
            return view('admin.notificacoes.diagnostico');
        })->name('diagnostico');

        // Canais
        Route::get('/canais', function () {
            return view('admin.notificacoes.canais');
        })->name('canais');

        // Usuários
        Route::get('/usuarios', function () {
            return view('admin.notificacoes.usuarios');
        })->name('usuarios');

        // Configurações
        Route::get('/configuracoes', function () {
            return view('admin.notificacoes.configuracoes');
        })->name('configuracoes');

        // APIs para o painel
        Route::prefix('api')->group(function () {
            // Estatísticas
            Route::get('/estatisticas', function () {
                return response()->json([
                    'metricas' => [
                        'total_enviadas' => rand(1000, 5000),
                        'taxa_sucesso' => rand(95, 99),
                        'tempo_medio' => rand(150, 500) . 'ms',
                        'taxa_erro' => rand(1, 5),
                        'total_falhas' => rand(10, 50),
                        'crescimento' => rand(-5, 25)
                    ],
                    'graficos' => [
                        'volume' => [
                            'labels' => ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                            'enviadas' => [120, 190, 300, 500, 200, 300, 450],
                            'falharam' => [12, 19, 3, 15, 8, 12, 18]
                        ],
                        'canais' => [
                            'labels' => ['Email', 'SMS', 'Push', 'In-App'],
                            'values' => [45, 25, 20, 10]
                        ],
                        'tipos' => [
                            'labels' => ['Pedido', 'Pagamento', 'Estoque', 'Cliente'],
                            'values' => [150, 120, 80, 50]
                        ],
                        'horas' => [
                            'labels' => ['00h', '04h', '08h', '12h', '16h', '20h'],
                            'values' => [20, 15, 60, 120, 100, 80]
                        ]
                    ],
                    'tabelas' => [
                        'templates_top' => [
                            ['nome' => 'Pedido Criado', 'canal' => 'email', 'total' => 500, 'taxa_sucesso' => 98],
                            ['nome' => 'Pagamento Aprovado', 'canal' => 'sms', 'total' => 300, 'taxa_sucesso' => 96],
                            ['nome' => 'Baixo Estoque', 'canal' => 'push', 'total' => 200, 'taxa_sucesso' => 99]
                        ],
                        'erros_recentes' => [
                            ['created_at' => now(), 'canal' => 'email', 'erro_mensagem' => 'SMTP timeout', 'tentativas' => 2],
                            ['created_at' => now()->subMinutes(5), 'canal' => 'sms', 'erro_mensagem' => 'Invalid number', 'tentativas' => 1]
                        ]
                    ],
                    'comparativas' => [
                        'hoje_vs_ontem' => ['valor' => '+12%', 'percentual' => '+12%', 'positivo' => true],
                        'semana_vs_anterior' => ['valor' => '+8%', 'percentual' => '+8%', 'positivo' => true],
                        'mes_vs_anterior' => ['valor' => '-2%', 'percentual' => '-2%', 'positivo' => false],
                        'melhor_canal' => ['nome' => 'Email', 'taxa_sucesso' => 98]
                    ]
                ]);
            });

            // Notificações recentes
            Route::get('/recentes', function () {
                return response()->json([
                    [
                        'id' => 1,
                        'tipo' => 'pedido_criado',
                        'canal' => 'email',
                        'destinatario' => 'cliente@exemplo.com',
                        'titulo' => 'Pedido #12345 criado',
                        'status' => 'enviado',
                        'enviado_em' => now()
                    ],
                    [
                        'id' => 2,
                        'tipo' => 'pagamento_aprovado',
                        'canal' => 'sms',
                        'destinatario' => '+5511999999999',
                        'titulo' => 'Pagamento aprovado',
                        'status' => 'enviado',
                        'enviado_em' => now()->subMinutes(5)
                    ]
                ]);
            });

            // Templates
            Route::get('/templates', function () {
                return response()->json([
                    'data' => [
                        [
                            'id' => 1,
                            'nome' => 'Template Pedido Criado',
                            'canal' => 'email',
                            'tipo_evento_nome' => 'Pedido Criado',
                            'assunto' => 'Seu pedido #{pedido_numero} foi criado',
                            'ativo' => true,
                            'variaveis_count' => 5,
                            'created_at' => now()
                        ]
                    ],
                    'total' => 1,
                    'from' => 1,
                    'to' => 1
                ]);
            });

            // Tipos de evento
            Route::get('/tipos-evento', function () {
                return response()->json([
                    ['id' => 1, 'nome' => 'Pedido Criado'],
                    ['id' => 2, 'nome' => 'Pagamento Aprovado'],
                    ['id' => 3, 'nome' => 'Produto Baixo Estoque'],
                    ['id' => 4, 'nome' => 'Cliente Novo']
                ]);
            });

            // Estatísticas de tipos de evento
            Route::get('/tipos-evento/estatisticas', function () {
                return response()->json([
                    'total' => 12,
                    'ativos' => 8,
                    'mais_usado' => [
                        'nome' => 'Pedido Criado',
                        'uso_count' => 245
                    ],
                    'total_templates' => 24
                ]);
            });

            // Histórico de testes
            Route::get('/historico-testes', [App\Http\Controllers\Admin\NotificacaoController::class, 'historicoTestes']);

            // Detalhes de notificação
            Route::get('/detalhes/{id}', [App\Http\Controllers\Admin\NotificacaoController::class, 'detalhesNotificacao']);

            // Header notifications
            Route::get('/header-notifications', [App\Http\Controllers\Admin\NotificacaoController::class, 'getHeaderNotifications']);

            // Marcar como lida
            Route::post('/marcar-lida/{id}', [App\Http\Controllers\Admin\NotificacaoController::class, 'marcarComoLida']);

            // Aplicações data
            Route::get('/aplicacoes-data', [App\Http\Controllers\Admin\NotificacaoController::class, 'getAplicacoesData']);

            // APIs de Usuários
            Route::get('/usuarios', [App\Http\Controllers\Admin\UsuariosController::class, 'apiUsuarios']);
            Route::get('/usuarios/estatisticas', [App\Http\Controllers\Admin\UsuariosController::class, 'apiEstatisticas']);
            Route::get('/usuarios/detalhes/{id}', [App\Http\Controllers\Admin\UsuariosController::class, 'apiDetalhes']);

            // Atividade recente
            Route::get('/atividade-recente', [App\Http\Controllers\Admin\NotificacaoController::class, 'getAtividadeRecente']);

            // Diagnóstico - Status geral
            Route::get('/diagnostico/status-geral', function () {
                return response()->json([
                    ['nome' => 'Database', 'status' => true, 'mensagem' => 'Conectado'],
                    ['nome' => 'Email', 'status' => true, 'mensagem' => 'Configurado'],
                    ['nome' => 'SMS', 'status' => false, 'mensagem' => 'Não configurado'],
                    ['nome' => 'Push', 'status' => true, 'mensagem' => 'Ativo']
                ]);
            });

            // Diagnóstico - Tabelas
            Route::get('/diagnostico/tabelas', function () {
                $tabelas = [
                    'notificacao_aplicacoes',
                    'notificacao_tipos_evento',
                    'notificacao_templates',
                    'notificacao_enviadas',
                    'notificacao_templates_historico',
                    'notificacao_agendamentos',
                    'notificacao_preferencias_usuario',
                    'notificacao_estatisticas'
                ];

                $resultado = [];
                foreach ($tabelas as $tabela) {
                    $resultado[] = [
                        'nome' => $tabela,
                        'existe' => true,
                        'registros' => rand(0, 1000)
                    ];
                }

                return response()->json($resultado);
            });

            // Diagnóstico - Services
            Route::get('/diagnostico/services', function () {
                return response()->json([
                    ['nome' => 'NotificacaoService', 'funcionando' => true, 'ultimo_uso' => 'Agora'],
                    ['nome' => 'NotificacaoConfigService', 'funcionando' => true, 'ultimo_uso' => '5 min atrás'],
                    ['nome' => 'NotificacaoTemplateService', 'funcionando' => true, 'ultimo_uso' => '2 min atrás']
                ]);
            });

            // Diagnóstico - Testes de conectividade
            Route::get('/diagnostico/teste-database', function () {
                try {
                    DB::connection()->getPdo();
                    return response()->json(['success' => true, 'tempo' => rand(50, 200)]);
                } catch (Exception $e) {
                    return response()->json(['success' => false, 'erro' => $e->getMessage()]);
                }
            });

            Route::get('/diagnostico/teste-email', function () {
                return response()->json(['success' => true]);
            });

            Route::get('/diagnostico/teste-sms', function () {
                return response()->json(['success' => false]);
            });

            Route::get('/diagnostico/teste-push', function () {
                return response()->json(['success' => true]);
            });

            // Diagnóstico - Métricas
            Route::get('/diagnostico/metricas', function () {
                return response()->json([
                    'memoria' => rand(30, 80),
                    'cpu' => rand(10, 60),
                    'disco' => rand(20, 70),
                    'tempo_resposta' => rand(100, 500),
                    'notificacoes_por_minuto' => rand(10, 100)
                ]);
            });
        });

        // Teste de envio
        Route::post('/teste/enviar', [App\Http\Controllers\Admin\NotificacaoController::class, 'enviarTestePersonalizado']);
    });

    // Payments Analytics (route diferente para evitar conflito)
    Route::get('/payment-analytics', function () {
        return view('admin.temp', ['module' => 'Analytics de Pagamentos']);
    })->name('payment.analytics');
});

// ADMIN - Configurações Simplificadas
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('config-simple', [App\Http\Controllers\Admin\ConfigSimpleController::class, 'index'])->name('config.simple');
    Route::post('config-simple/set-value', [App\Http\Controllers\Admin\ConfigSimpleController::class, 'setValue'])->name('config.simple.set-value');
    Route::post('config-simple/clear-cache', [App\Http\Controllers\Admin\ConfigSimpleController::class, 'clearCache'])->name('config.simple.clear-cache');

    Route::post('config/{config}/restore-value', function ($config) {
        return response()->json(['success' => false, 'message' => 'Restauração em desenvolvimento']);
    })->name('config.restore-value');
});

// Rota de teste para verificar configurações
Route::get('/test-config', function () {
    try {
        $count = App\Models\Config\ConfigDefinition::count();
        $configs = App\Models\Config\ConfigDefinition::limit(5)->get();

        $html = "<h1>Teste de Configurações</h1>";
        $html .= "<p>Total de configurações: {$count}</p>";

        if ($configs->count() > 0) {
            $html .= "<h3>Primeiras 5 configurações:</h3><ul>";
            foreach ($configs as $config) {
                $html .= "<li><strong>{$config->chave}</strong> - {$config->tipo_dado} (Grupo: {$config->grupo_id})</li>";
            }
            $html .= "</ul>";
        } else {
            $html .= "<p>❌ Nenhuma configuração encontrada</p>";
        }

        return response($html);
    } catch (Exception $e) {
        return response("<h1>❌ Erro</h1><p>{$e->getMessage()}</p><p>Linha: {$e->getLine()}</p><p>Arquivo: {$e->getFile()}</p>");
    }
});

// Debug específico para config simple
Route::get('/debug-config-simple', function () {
    try {
        $html = "<h1>Debug Config Simple</h1>";

        // Teste 1: Verificar se a classe existe
        if (class_exists('App\Models\Config\ConfigDefinition')) {
            $html .= "<p>✅ Classe ConfigDefinition encontrada</p>";
        } else {
            $html .= "<p>❌ Classe ConfigDefinition não encontrada</p>";
            return response($html);
        }

        // Teste 2: Testar conexão
        $count = App\Models\Config\ConfigDefinition::count();
        $html .= "<p>Total de configurações: {$count}</p>";

        // Teste 3: Testar query com limit
        $configs = App\Models\Config\ConfigDefinition::orderBy('id')->limit(3)->get();
        $html .= "<p>Configurações carregadas: " . $configs->count() . "</p>";

        if ($configs->count() > 0) {
            $html .= "<h3>Primeiras configurações:</h3><ul>";
            foreach ($configs as $config) {
                $html .= "<li>ID: {$config->id} | Chave: {$config->chave} | Tipo: {$config->tipo_dado}</li>";
            }
            $html .= "</ul>";
        }

        // Teste 4: Verificar view
        $html .= "<p><a href='/admin/config-simple'>Testar Config Simple</a></p>";

        return response($html);
    } catch (Exception $e) {
        return response("<h1>❌ Erro no Debug</h1><p><strong>Mensagem:</strong> {$e->getMessage()}</p><p><strong>Linha:</strong> {$e->getLine()}</p><p><strong>Arquivo:</strong> {$e->getFile()}</p>");
    }
}); // ADMIN - Produtos, Pedidos, etc.
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('config/{config}/history', [App\Http\Controllers\Admin\ConfigController::class, 'history'])->name('config.history');

    // COMENTADO: Rotas de resource conflitantes - usando rotas específicas do AdminFidelidadeController
    // Route::resource('fidelidade', App\Http\Controllers\Admin\FidelidadeAdminController::class);
    // Route::get('fidelidade/deletados/{tipo?}', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'deletados'])->name('fidelidade.deletados');
    // Route::post('fidelidade/restaurar', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'restaurar'])->name('fidelidade.restaurar');
    // Route::delete('fidelidade/deletar-permanente', [App\Http\Controllers\Admin\FidelidadeAdminController::class, 'deletarPermanente'])->name('fidelidade.deletar-permanente');
});

// COMERCIANTE - Rotas para comerciantes
Route::prefix('comerciante')->name('comerciante.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('comerciante.dashboard');
    })->name('dashboard');

    // Rotas temporárias
    Route::get('produtos', function () {
        return view('admin.temp', ['module' => 'Produtos - Comerciante']);
    })->name('produtos.index');

    Route::get('pedidos', function () {
        return view('admin.temp', ['module' => 'Pedidos - Comerciante']);
    })->name('pedidos.index');
});

// CLIENTE - Rotas para clientes
Route::prefix('cliente')->name('cliente.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('cliente.dashboard');
    })->name('dashboard');

    // Rotas temporárias
    Route::get('compras', function () {
        return view('admin.temp', ['module' => 'Minhas Compras']);
    })->name('compras.index');

    Route::get('fidelidade', function () {
        return view('admin.temp', ['module' => 'Programa Fidelidade']);
    })->name('fidelidade.index');
});

// ENTREGADOR - Rotas para entregadores
Route::prefix('entregador')->name('entregador.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('entregador.dashboard');
    })->name('dashboard');

    // Rotas temporárias
    Route::get('entregas', function () {
        return view('admin.temp', ['module' => 'Minhas Entregas']);
    })->name('entregas.index');
});

// Rotas do Sistema de Configurações Multi-Empresa
Route::prefix('config')->name('config.')->group(function () {
    Route::get('/', [App\Http\Controllers\ConfigAdminController::class, 'index'])->name('index');
    Route::get('/system-status', [App\Http\Controllers\ConfigAdminController::class, 'systemStatus'])->name('system-status');
    Route::get('/manage-client/{clientId}', [App\Http\Controllers\ConfigAdminController::class, 'manageClient'])->name('manage-client');
    Route::post('/update-client/{clientId}', [App\Http\Controllers\ConfigAdminController::class, 'updateClient'])->name('update-client');
});

// Incluir rotas do módulo de fidelidade
// require __DIR__ . '/fidelidade/web.php';

// ROTA DE TESTE DEBUGBAR
Route::get('/test-debugbar', function () {
    // Forçar o carregamento do Debugbar
    if (app()->bound('debugbar')) {
        app('debugbar')->info('Debugbar está funcionando!');
        app('debugbar')->warning('Este é um teste do Debugbar');
        app('debugbar')->error('Mensagem de erro de teste');
    }

    return '<h1>Teste Debugbar</h1>
            <p>Status do Debugbar: ' . (app()->bound('debugbar') ? 'Carregado' : 'Não carregado') . '</p>
            <p>APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false') . '</p>
            <p>DEBUGBAR_ENABLED: ' . (config('debugbar.enabled') ? 'true' : 'false') . '</p>
            <p>Veja a barra de debug no final da página se estiver funcionando.</p>';
});
