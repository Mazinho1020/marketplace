<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificacaoController extends Controller
{
    protected $notificacaoService;
    protected $configService;

    public function __construct()
    {
        // Middleware já aplicado nas rotas
    }

    /**
     * Lista de notificações do comerciante
     */
    public function index(Request $request)
    {
        $empresaId = $this->getEmpresaId();
        $aplicacaoId = $this->getAplicacaoEmpresa();

        $query = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->whereIn('canal', ['in_app', 'push', 'email'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('canal')) {
            $query->where('canal', $request->canal);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $notificacoes = $query->paginate(15);

        // Converter datas para objetos Carbon
        $notificacoes->getCollection()->transform(function ($notificacao) {
            $notificacao->created_at = \Carbon\Carbon::parse($notificacao->created_at);
            $notificacao->updated_at = \Carbon\Carbon::parse($notificacao->updated_at);
            if ($notificacao->lido_em) {
                $notificacao->lido_em = \Carbon\Carbon::parse($notificacao->lido_em);
            }
            return $notificacao;
        });

        // Estatísticas
        $stats = [
            'total' => DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)->count(),
            'nao_lidas' => DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
                ->whereNull('lido_em')->count(),
            'hoje' => DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
                ->whereDate('created_at', today())->count(),
            'taxa_leitura' => $this->calcularTaxaLeitura($empresaId)
        ];

        return view('comerciantes.notificacoes.index', compact('notificacoes', 'stats'));
    }

    /**
     * API para notificações do header
     */
    public function headerNotifications(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Garantir que temos um ID de empresa válido
            $empresaId = session('empresa_atual_id');
            if (!$empresaId && $user && method_exists($user, 'empresas')) {
                $primeiraEmpresa = $user->empresas()->first();
                $empresaId = $primeiraEmpresa ? $primeiraEmpresa->id : 1;
            }
            if (!$empresaId) {
                $empresaId = 1;
            }

            // Verificar se a tabela existe
            if (!Schema::hasTable('notificacao_enviadas')) {
                return response()->json([
                    'success' => true,
                    'notificacoes' => [],
                    'total_nao_lidas' => 0,
                    'message' => 'Tabela de notificações não existe'
                ]);
            }

            $aplicacaoId = $this->getAplicacaoEmpresa();

            $notificacoes = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
                ->where('aplicacao_id', $aplicacaoId)
                ->whereNull('lido_em')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['id', 'titulo', 'mensagem', 'created_at', 'prioridade', 'canal', 'lido_em']);

            // Converter datas para objetos Carbon
            $notificacoes = $notificacoes->map(function ($notificacao) {
                $notificacao->created_at = \Carbon\Carbon::parse($notificacao->created_at);
                return $notificacao;
            });

            $naoLidas = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
                ->where('aplicacao_id', $aplicacaoId)
                ->whereNull('lido_em')
                ->count();

            $notificacoesFormatadas = $notificacoes->map(function ($notificacao) {
                return [
                    'id' => $notificacao->id,
                    'titulo' => $notificacao->titulo,
                    'mensagem' => substr($notificacao->mensagem, 0, 60) . (strlen($notificacao->mensagem) > 60 ? '...' : ''),
                    'tempo' => $this->calcularTempoDecorrido($notificacao->created_at),
                    'icone' => $this->getIconeNotificacao($notificacao),
                    'cor' => $this->getCorNotificacao($notificacao),
                    'lida' => !is_null($notificacao->lido_em),
                    'url' => $this->getUrlNotificacao($notificacao)
                ];
            });

            return response()->json([
                'success' => true,
                'notificacoes' => $notificacoesFormatadas,
                'total_nao_lidas' => $naoLidas
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao carregar notificações do header', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::guard('comerciante')->id(),
                'empresa_id' => session('empresa_atual_id')
            ]);

            return response()->json([
                'success' => true,
                'notificacoes' => [],
                'total_nao_lidas' => 0,
                'message' => 'Erro ao carregar notificações, mas sistema funciona normalmente'
            ]);
        }
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarComoLida($id)
    {
        $user = Auth::guard('comerciante')->user();
        $empresaId = session('empresa_atual_id') ?: $user->empresas->first()?->id;

        $notificacao = DB::table('notificacao_enviadas')->where('id', $id)
            ->where('empresa_relacionada_id', $empresaId)
            ->first();

        if (!$notificacao) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ], 404);
        }

        DB::table('notificacao_enviadas')->where('id', $id)->update([
            'lido_em' => now(),
            'user_agent' => request()->userAgent()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas como lidas
     */
    public function marcarTodasComoLidas()
    {
        $empresaId = $this->getEmpresaId();

        $updated = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $this->getAplicacaoEmpresa())
            ->whereNull('lido_em')
            ->update([
                'lido_em' => now(),
                'user_agent' => request()->userAgent()
            ]);

        return response()->json([
            'success' => true,
            'message' => "Marcadas {$updated} notificações como lidas"
        ]);
    }

    /**
     * Dashboard das notificações com estatísticas
     */
    public function dashboard()
    {
        $empresaId = $this->getEmpresaId();
        $aplicacaoId = $this->getAplicacaoEmpresa();

        // Estatísticas básicas
        $totalNotificacoes = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->count();

        $naoLidas = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->whereNull('lido_em')
            ->count();

        $ultimasSemana = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        // Estatísticas por canal nos últimos 30 dias
        $porCanal = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->where('created_at', '>=', now()->subMonth())
            ->selectRaw('canal, count(*) as total')
            ->groupBy('canal')
            ->pluck('total', 'canal');

        // Estatísticas de hoje
        $hoje = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->count();

        // Taxa de leitura
        $lidas = $totalNotificacoes - $naoLidas;
        $taxaLeitura = $totalNotificacoes > 0 ? round(($lidas / $totalNotificacoes) * 100, 1) : 0;

        // Estatísticas dos últimos 7 dias para gráfico
        $ultimosSete = [];
        $notificacoesPorDiaArray = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i)->format('Y-m-d');
            $count = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
                ->where('aplicacao_id', $aplicacaoId)
                ->whereDate('created_at', $data)
                ->count();
            $ultimosSete[] = [
                'data' => $data,
                'count' => $count
            ];
            // Criar array para o gráfico (data => count)
            $notificacoesPorDiaArray[$data] = $count;
        }

        // Converter para Collection para compatibilidade com toArray() na view
        $notificacoesPorDia = collect($notificacoesPorDiaArray);

        // Criar array de estatísticas para a view
        $stats = [
            'total' => $totalNotificacoes,
            'nao_lidas' => $naoLidas,
            'hoje' => $hoje,
            'taxa_leitura' => $taxaLeitura
        ];

        // Buscar notificações recentes para exibir no dashboard
        $notificacoesRecentes = DB::table('notificacao_enviadas')
            ->where('empresa_relacionada_id', $empresaId)
            ->where('aplicacao_id', $aplicacaoId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->select('id', 'titulo', 'mensagem', 'canal', 'status', 'prioridade', 'created_at', 'lido_em', 'entregue_em')
            ->get();

        // Converter datas para objetos Carbon
        $notificacoesRecentes = $notificacoesRecentes->map(function ($notificacao) {
            $notificacao->created_at = \Carbon\Carbon::parse($notificacao->created_at);
            if ($notificacao->lido_em) {
                $notificacao->lido_em = \Carbon\Carbon::parse($notificacao->lido_em);
            }
            return $notificacao;
        });

        return view('comerciantes.notificacoes.dashboard', compact(
            'stats',
            'totalNotificacoes',
            'naoLidas',
            'ultimasSemana',
            'ultimosSete',
            'notificacoesRecentes'
        ), [
            'notificacoesPorCanal' => $porCanal,
            'notificacoesPorDia' => $notificacoesPorDia
        ]);
    }

    /**
     * Detalhes de uma notificação
     */
    public function show($id)
    {
        $empresaId = $this->getEmpresaId();

        $notificacao = DB::table('notificacao_enviadas')->where('id', $id)
            ->where('empresa_relacionada_id', $empresaId)
            ->first();

        if (!$notificacao) {
            abort(404, 'Notificação não encontrada');
        }

        // Converter datas para objetos Carbon
        $notificacao->created_at = \Carbon\Carbon::parse($notificacao->created_at);
        $notificacao->updated_at = \Carbon\Carbon::parse($notificacao->updated_at);
        if ($notificacao->lido_em) {
            $notificacao->lido_em = \Carbon\Carbon::parse($notificacao->lido_em);
        }
        if ($notificacao->entregue_em) {
            $notificacao->entregue_em = \Carbon\Carbon::parse($notificacao->entregue_em);
        }
        if ($notificacao->enviado_em) {
            $notificacao->enviado_em = \Carbon\Carbon::parse($notificacao->enviado_em);
        }

        // Marcar como lida se ainda não foi
        if (!$notificacao->lido_em) {
            DB::table('notificacao_enviadas')->where('id', $id)->update([
                'lido_em' => now(),
                'user_agent' => request()->userAgent()
            ]);
            $notificacao->lido_em = now();
        }

        return view('comerciantes.notificacoes.show', compact('notificacao'));
    }

    // Métodos auxiliares
    protected function getEmpresaId()
    {
        $user = Auth::guard('comerciante')->user();

        // Tentar obter da sessão primeiro
        $empresaId = session('empresa_atual_id');

        // Se não tem na sessão, tentar obter do usuário
        if (!$empresaId && $user && method_exists($user, 'empresas')) {
            $primeiraEmpresa = $user->empresas()->first();
            $empresaId = $primeiraEmpresa ? $primeiraEmpresa->id : null;
        }

        // Fallback para empresa ID 1 se nada foi encontrado
        return $empresaId ?: 1;
    }

    protected function getAplicacaoEmpresa()
    {
        $aplicacao = DB::table('notificacao_aplicacoes')->where('codigo', 'empresa')->first();
        return $aplicacao ? $aplicacao->id : 2; // Fallback para ID 2 se não encontrar
    }

    protected function calcularTaxaLeitura($empresaId)
    {
        $total = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->whereIn('canal', ['in_app', 'push', 'email'])
            ->count();

        if ($total == 0) return 0;

        $lidas = DB::table('notificacao_enviadas')->where('empresa_relacionada_id', $empresaId)
            ->whereIn('canal', ['in_app', 'push', 'email'])
            ->whereNotNull('lido_em')
            ->count();

        return round(($lidas / $total) * 100, 1);
    }

    protected function calcularTempoDecorrido($timestamp)
    {
        return Carbon::parse($timestamp)->diffForHumans();
    }

    protected function getIconeNotificacao($notificacao)
    {
        $titulo = strtolower($notificacao->titulo);

        if (str_contains($titulo, 'pedido')) return 'fas fa-shopping-cart';
        if (str_contains($titulo, 'pagamento')) return 'fas fa-credit-card';
        if (str_contains($titulo, 'produto')) return 'fas fa-box';
        if (str_contains($titulo, 'cliente')) return 'fas fa-user';
        if (str_contains($titulo, 'entrega')) return 'fas fa-truck';

        return 'fas fa-bell';
    }

    protected function getCorNotificacao($notificacao)
    {
        $titulo = strtolower($notificacao->titulo);

        if (str_contains($titulo, 'erro') || str_contains($titulo, 'falha')) return 'text-danger';
        if (str_contains($titulo, 'sucesso') || str_contains($titulo, 'aprovado')) return 'text-success';
        if (str_contains($titulo, 'pendente') || str_contains($titulo, 'aguardando')) return 'text-warning';

        return 'text-primary';
    }

    protected function getUrlNotificacao($notificacao)
    {
        $titulo = strtolower($notificacao->titulo);

        if (str_contains($titulo, 'pedido')) {
            return '#'; // route('comerciantes.pedidos.index');
        }
        if (str_contains($titulo, 'produto')) {
            return '#'; // route('comerciantes.produtos.index');
        }

        return route('comerciantes.notificacoes.show', $notificacao->id);
    }
}
