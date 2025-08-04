<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsuariosController extends Controller
{
    public function index()
    {
        return view('admin.notificacoes.usuarios');
    }

    public function apiUsuarios(Request $request)
    {
        try {
            $query = DB::table('empresa_usuarios')
                ->select([
                    'id',
                    'nome',
                    'email',
                    'telefone',
                    'status',
                    'last_login',
                    'cargo',
                    'created_at',
                    'empresa_id'
                ])
                ->whereNull('deleted_at');

            // Filtros
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            if ($request->has('busca') && $request->busca != '') {
                $busca = $request->busca;
                $query->where(function ($q) use ($busca) {
                    $q->where('nome', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%")
                        ->orWhere('telefone', 'like', "%{$busca}%");
                });
            }

            // Paginação
            $perPage = 10;
            $page = $request->get('pagina', 1);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $usuarios = $query->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Buscar estatísticas de notificações para cada usuário
            $usuariosEnriquecidos = $usuarios->map(function ($usuario) {
                $stats = DB::table('notificacao_enviadas')
                    ->select([
                        DB::raw('COUNT(*) as total_enviadas'),
                        DB::raw('SUM(CASE WHEN status = "enviado" THEN 1 ELSE 0 END) as enviados'),
                        DB::raw('SUM(CASE WHEN status = "entregue" THEN 1 ELSE 0 END) as entregues'),
                        DB::raw('SUM(CASE WHEN status = "lido" THEN 1 ELSE 0 END) as lidos'),
                        DB::raw('SUM(CASE WHEN status = "falha" THEN 1 ELSE 0 END) as falhas')
                    ])
                    ->where('usuario_id', $usuario->id)
                    ->first();

                // Preferências simuladas baseadas no histórico de notificações
                $preferencias = [
                    'email' => true,
                    'sms' => !empty($usuario->telefone),
                    'push' => $stats->total_enviadas > 0,
                    'in_app' => true
                ];

                // Determinar tipo baseado no cargo ou empresa
                $tipo = 'cliente';
                if (!empty($usuario->cargo) && str_contains(strtolower($usuario->cargo), 'admin')) {
                    $tipo = 'admin';
                } elseif ($usuario->empresa_id) {
                    $tipo = 'empresa';
                }

                // Formatar último acesso
                $ultimo_acesso = 'Nunca';
                if ($usuario->last_login) {
                    $ultimo_acesso = Carbon::parse($usuario->last_login)->diffForHumans();
                }

                return [
                    'id' => $usuario->id,
                    'nome' => $usuario->nome,
                    'email' => $usuario->email,
                    'telefone' => $usuario->telefone ?: '',
                    'tipo' => $tipo,
                    'status' => $usuario->status,
                    'ultimo_acesso' => $ultimo_acesso,
                    'cargo' => $usuario->cargo ?: '',
                    'created_at' => $usuario->created_at,
                    'preferencias' => $preferencias,
                    'notificacoes' => [
                        'enviadas' => (int)$stats->total_enviadas,
                        'lidas' => (int)$stats->lidos,
                        'pendentes' => (int)($stats->enviados - $stats->lidos)
                    ]
                ];
            });

            return response()->json([
                'data' => $usuariosEnriquecidos,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao carregar usuários: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiEstatisticas()
    {
        try {
            // Estatísticas gerais
            $totalUsuarios = DB::table('empresa_usuarios')
                ->whereNull('deleted_at')
                ->count();

            $usuariosAtivos = DB::table('empresa_usuarios')
                ->whereNull('deleted_at')
                ->where('status', 'ativo')
                ->count();

            $usuariosComNotificacoes = DB::table('empresa_usuarios')
                ->whereNull('deleted_at')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('notificacao_enviadas')
                        ->whereRaw('notificacao_enviadas.usuario_id = empresa_usuarios.id');
                })
                ->count();

            // Usuário mais ativo (com mais notificações)
            $usuarioMaisAtivo = DB::table('empresa_usuarios')
                ->select([
                    'empresa_usuarios.nome',
                    DB::raw('COUNT(notificacao_enviadas.id) as total_notificacoes')
                ])
                ->leftJoin('notificacao_enviadas', 'empresa_usuarios.id', '=', 'notificacao_enviadas.usuario_id')
                ->whereNull('empresa_usuarios.deleted_at')
                ->groupBy('empresa_usuarios.id', 'empresa_usuarios.nome')
                ->orderBy('total_notificacoes', 'desc')
                ->first();

            return response()->json([
                'total_usuarios' => $totalUsuarios,
                'usuarios_ativos' => $usuariosAtivos,
                'usuarios_com_notificacoes' => $usuariosComNotificacoes,
                'usuario_mais_ativo' => [
                    'nome' => $usuarioMaisAtivo->nome ?? 'Nenhum',
                    'total_notificacoes' => $usuarioMaisAtivo->total_notificacoes ?? 0
                ],
                'taxa_ativacao' => $totalUsuarios > 0 ? round(($usuariosAtivos / $totalUsuarios) * 100, 1) : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiDetalhes($id)
    {
        try {
            $usuario = DB::table('empresa_usuarios')
                ->select([
                    'id',
                    'nome',
                    'email',
                    'telefone',
                    'status',
                    'last_login',
                    'cargo',
                    'created_at',
                    'empresa_id'
                ])
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$usuario) {
                return response()->json([
                    'error' => true,
                    'message' => 'Usuário não encontrado'
                ], 404);
            }

            // Histórico de notificações
            $notificacoes = DB::table('notificacao_enviadas')
                ->select([
                    'id',
                    'titulo',
                    'canal',
                    'status',
                    'created_at',
                    'enviado_em',
                    'entregue_em',
                    'lido_em'
                ])
                ->where('usuario_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Estatísticas detalhadas
            $stats = DB::table('notificacao_enviadas')
                ->select([
                    'canal',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "lido" THEN 1 ELSE 0 END) as lidos'),
                    DB::raw('SUM(CASE WHEN status = "falha" THEN 1 ELSE 0 END) as falhas')
                ])
                ->where('usuario_id', $id)
                ->groupBy('canal')
                ->get()
                ->keyBy('canal');

            return response()->json([
                'usuario' => $usuario,
                'notificacoes' => $notificacoes,
                'estatisticas_por_canal' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Erro ao carregar detalhes: ' . $e->getMessage()
            ], 500);
        }
    }
}
