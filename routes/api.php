<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ROTAS DE TESTE - API para testes de notificação (sem CSRF)
Route::prefix('notificacoes/teste')->group(function () {
    Route::post('/conexao', function () {
        try {
            DB::connection()->getPdo();
            return response()->json(['status' => 'ok', 'message' => 'Conexão ativa']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    });

    Route::post('/tabelas', function () {
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

    Route::post('/models', function () {
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

    Route::post('/services', function () {
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

    Route::post('/enviar', function () {
        try {
            $request = request();
            $notificacaoService = new \App\Services\NotificacaoService(
                new \App\Services\NotificacaoConfigService(1),
                new \App\Services\NotificacaoTemplateService()
            );

            $resultado = $notificacaoService->sendEvent(
                $request->tipo_evento,
                $request->dados ?? [],
                ['usuario_id' => $request->usuario_id, 'empresa_id' => 1]
            );

            return response()->json([
                'status' => 'ok',
                'enviado' => $resultado,
                'message' => 'Notificação processada'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    });

    Route::post('/ultimas', function () {
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
    });
});
