<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller para páginas administrativas de VISUALIZAÇÃO do sistema de fidelidade
 * Responsabilidade: Apenas exibir dados e relatórios (READ-ONLY)
 * Não inclui operações de CRUD
 */
class AdminFidelidadeController extends Controller
{
    /**
     * Dashboard administrativo de fidelidade
     * Exibe estatísticas gerais e resumos
     */
    public function dashboard()
    {
        try {
            // Estatísticas gerais baseadas nas tabelas reais
            $stats = [
                'total_clientes' => DB::table('fidelidade_carteiras')->count(),
                'clientes_ativos' => DB::table('fidelidade_carteiras')->where('status', 'ativa')->count(),
                'total_cupons' => DB::table('fidelidade_cupons')->count(),
                'cupons_ativos' => DB::table('fidelidade_cupons')->where('status', 'ativo')->count(),
                'total_regras' => DB::table('fidelidade_cashback_regras')->count(),
                'regras_ativas' => DB::table('fidelidade_cashback_regras')->where('status', 'ativo')->count(),
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'valor_total_transacoes' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'total_cashback_distribuido' => DB::table('fidelidade_cashback_transacoes')->sum('cashback_valor') ?? 0,
                'saldo_total_clientes' => DB::table('fidelidade_carteiras')->sum('saldo_total_disponivel') ?? 0
            ];

            // Atividade recente (últimas 10 transações)
            $atividade_recente = DB::table('fidelidade_cashback_transacoes as fct')
                ->leftJoin('empresas as e', 'fct.empresa_id', '=', 'e.id')
                ->leftJoin('fidelidade_carteiras as fc', 'fct.carteira_id', '=', 'fc.id')
                ->select(
                    'fct.*',
                    'e.nome_fantasia as empresa_nome',
                    'fc.cliente_id'
                )
                ->orderBy('fct.created_at', 'desc')
                ->limit(10)
                ->get();

            // Top clientes por saldo
            $top_clientes = DB::table('fidelidade_carteiras as fc')
                ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
                ->select(
                    'fc.cliente_id',
                    'fc.saldo_total_disponivel',
                    'fc.nivel_atual',
                    'fc.xp_total',
                    'e.nome_fantasia as empresa_nome'
                )
                ->orderBy('fc.saldo_total_disponivel', 'desc')
                ->limit(10)
                ->get();

            return view('admin.fidelidade.dashboard', compact('stats', 'atividade_recente', 'top_clientes'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.dashboard', [
                'stats' => [
                    'total_clientes' => 0,
                    'clientes_ativos' => 0,
                    'total_cupons' => 0,
                    'cupons_ativos' => 0,
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'total_transacoes' => 0,
                    'valor_total_transacoes' => 0,
                    'total_cashback_distribuido' => 0,
                    'saldo_total_clientes' => 0
                ],
                'atividade_recente' => collect([]),
                'top_clientes' => collect([])
            ]);
        }
    }

    /**
     * Visualização de clientes (READ-ONLY)
     * Lista todos os clientes com suas informações básicas
     */
    public function clientes()
    {
        try {
            $clientes = DB::table('fidelidade_carteiras as fc')
                ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
                ->select(
                    'fc.*',
                    'e.nome_fantasia as empresa_nome'
                )
                ->orderBy('fc.criado_em', 'desc')
                ->paginate(15);

            $stats = [
                'total_clientes' => DB::table('fidelidade_carteiras')->count(),
                'clientes_ativos' => DB::table('fidelidade_carteiras')->where('status', 'ativa')->count(),
                'clientes_inativos' => DB::table('fidelidade_carteiras')->where('status', 'inativa')->count(),
                'saldo_total' => DB::table('fidelidade_carteiras')->sum('saldo_total_disponivel')
            ];

            return view('admin.fidelidade.clientes', compact('clientes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.clientes', [
                'clientes' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_clientes' => 0,
                    'clientes_ativos' => 0,
                    'clientes_inativos' => 0,
                    'saldo_total' => 0
                ]
            ]);
        }
    }

    /**
     * Visualização de transações (READ-ONLY)
     * Lista todas as transações do sistema
     */
    public function transacoes()
    {
        try {
            $transacoes = DB::table('fidelidade_cashback_transacoes as fct')
                ->leftJoin('empresas as e', 'fct.empresa_id', '=', 'e.id')
                ->leftJoin('fidelidade_carteiras as fc', 'fct.carteira_id', '=', 'fc.id')
                ->select(
                    'fct.*',
                    'e.nome_fantasia as empresa_nome',
                    'fc.cliente_id'
                )
                ->orderBy('fct.created_at', 'desc')
                ->paginate(15);

            $stats = [
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'transacoes_pendentes' => DB::table('fidelidade_cashback_transacoes')->where('status', 'pendente')->count(),
                'transacoes_processadas' => DB::table('fidelidade_cashback_transacoes')->where('status', 'processado')->count(),
                'valor_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'valor_pedidos' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'total_cashback' => DB::table('fidelidade_cashback_transacoes')->sum('cashback_valor') ?? 0
            ];

            return view('admin.fidelidade.transacoes', compact('transacoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.transacoes', [
                'transacoes' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_transacoes' => 0,
                    'transacoes_pendentes' => 0,
                    'transacoes_processadas' => 0,
                    'valor_total' => 0,
                    'valor_pedidos' => 0,
                    'total_cashback' => 0
                ]
            ]);
        }
    }

    /**
     * Visualização de cupons (READ-ONLY)
     * Lista todos os cupons do sistema
     */
    public function cupons()
    {
        try {
            $cupons = DB::table('fidelidade_cupons as fc')
                ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
                ->select(
                    'fc.*',
                    'e.nome_fantasia as empresa_nome'
                )
                ->orderBy('fc.criado_em', 'desc')
                ->paginate(15);

            $stats = [
                'total_cupons' => DB::table('fidelidade_cupons')->count(),
                'cupons_ativos' => DB::table('fidelidade_cupons')->where('status', 'ativo')->count(),
                'cupons_inativos' => DB::table('fidelidade_cupons')->where('status', 'inativo')->count(),
                'cupons_utilizados' => DB::table('fidelidade_cupons_uso')->count(),
                'cupons_usados' => DB::table('fidelidade_cupons_uso')->count()
            ];

            return view('admin.fidelidade.cupons', compact('cupons', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cupons', [
                'cupons' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_cupons' => 0,
                    'cupons_ativos' => 0,
                    'cupons_inativos' => 0,
                    'cupons_utilizados' => 0,
                    'cupons_usados' => 0
                ]
            ]);
        }
    }

    /**
     * Visualização de regras de cashback (READ-ONLY)
     * Lista todas as regras de cashback configuradas
     */
    public function cashback()
    {
        try {
            $regras = DB::table('fidelidade_cashback_regras as fcr')
                ->leftJoin('empresas as e', 'fcr.empresa_id', '=', 'e.id')
                ->select(
                    'fcr.*',
                    'e.nome_fantasia as empresa_nome'
                )
                ->orderBy('fcr.criado_em', 'desc')
                ->paginate(15);

            $stats = [
                'total_regras' => DB::table('fidelidade_cashback_regras')->count(),
                'regras_ativas' => DB::table('fidelidade_cashback_regras')->where('status', 'ativo')->count(),
                'regras_inativas' => DB::table('fidelidade_cashback_regras')->where('status', 'inativo')->count(),
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'cashback_distribuido' => DB::table('fidelidade_cashback_transacoes')->sum('cashback_valor') ?? 0
            ];

            return view('admin.fidelidade.cashback', compact('regras', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cashback', [
                'regras' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'regras_inativas' => 0,
                    'total_transacoes' => 0,
                    'cashback_distribuido' => 0
                ]
            ]);
        }
    }

    /**
     * Relatórios administrativos
     * Exibe relatórios detalhados para tomada de decisão
     */
    public function relatorios()
    {
        try {
            // Relatório por período (últimos 30 dias)
            $dataInicio = now()->subDays(30);

            $relatorio = [
                'periodo' => [
                    'inicio' => $dataInicio->format('d/m/Y'),
                    'fim' => now()->format('d/m/Y')
                ],
                'transacoes_periodo' => DB::table('fidelidade_cashback_transacoes')
                    ->where('created_at', '>=', $dataInicio)
                    ->count(),
                'valor_periodo' => DB::table('fidelidade_cashback_transacoes')
                    ->where('created_at', '>=', $dataInicio)
                    ->sum('valor'),
                'cashback_periodo' => DB::table('fidelidade_cashback_transacoes')
                    ->where('created_at', '>=', $dataInicio)
                    ->sum('cashback_valor'),
                'novos_clientes' => DB::table('fidelidade_carteiras')
                    ->where('criado_em', '>=', $dataInicio)
                    ->count(),
                'cupons_utilizados' => DB::table('fidelidade_cupons_uso')
                    ->where('data_uso', '>=', $dataInicio)
                    ->count()
            ];

            return view('admin.fidelidade.relatorios', compact('relatorio'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.relatorios', [
                'relatorio' => [
                    'periodo' => [
                        'inicio' => now()->subDays(30)->format('d/m/Y'),
                        'fim' => now()->format('d/m/Y')
                    ],
                    'transacoes_periodo' => 0,
                    'valor_periodo' => 0,
                    'cashback_periodo' => 0,
                    'novos_clientes' => 0,
                    'cupons_utilizados' => 0
                ]
            ]);
        }
    }
}
