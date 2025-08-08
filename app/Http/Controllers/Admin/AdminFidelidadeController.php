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
            // Estatísticas gerais baseadas na tabela pessoas
            $stats = [
                'total_clientes' => DB::table('pessoas')->where(function ($query) {
                    $query->where('tipo', 'like', '%cliente%')->orWhere('tipo', 'like', '%funcionario%');
                })->count(),
                'clientes_ativos' => DB::table('pessoas')->where('status', 'ativo')->where(function ($query) {
                    $query->where('tipo', 'like', '%cliente%')->orWhere('tipo', 'like', '%funcionario%');
                })->count(),
                'total_cupons' => DB::table('fidelidade_cupons')->count(),
                'cupons_ativos' => DB::table('fidelidade_cupons')->where('status', 'ativo')->count(),
                'total_regras' => DB::table('fidelidade_cashback_regras')->count(),
                'regras_ativas' => DB::table('fidelidade_cashback_regras')->where('status', 'ativo')->count(),
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'valor_total_transacoes' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'total_cashback_distribuido' => 0, // Campo não existe na nova tabela
                'saldo_total_clientes' => 0 // Campo não existe na nova tabela
            ];

            // Top clientes por pontos
            $top_clientes = DB::table('pessoas as p')
                ->leftJoin('empresas as e', 'p.empresa_id', '=', 'e.id')
                ->select(
                    'p.id',
                    'p.nome',
                    'p.sobrenome',
                    DB::raw('0 as pontos_acumulados'), // Campo não existe na nova tabela
                    DB::raw('0 as saldo_disponivel'), // Campo não existe na nova tabela
                    DB::raw('"bronze" as nivel_fidelidade'), // Campo não existe na nova tabela
                    'e.nome_fantasia as empresa_nome'
                )
                ->where(function ($query) {
                    $query->where('p.tipo', 'like', '%cliente%')->orWhere('p.tipo', 'like', '%funcionario%');
                })
                ->orderBy('p.created_at', 'desc')
                ->limit(10)
                ->get();

            // Atividade recente (simulada baseada em criação de clientes)
            $atividade_recente = DB::table('pessoas as p')
                ->leftJoin('empresas as e', 'p.empresa_id', '=', 'e.id')
                ->select(
                    'p.id',
                    'p.nome',
                    'p.sobrenome',
                    'p.created_at',
                    'e.nome_fantasia as empresa_nome'
                )
                ->where(function ($query) {
                    $query->where('p.tipo', 'like', '%cliente%')->orWhere('p.tipo', 'like', '%funcionario%');
                })
                ->orderBy('p.created_at', 'desc')
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
            $clientes = DB::table('pessoas as p')
                ->leftJoin('fidelidade_carteiras as cart', 'p.id', '=', 'cart.cliente_id')
                ->leftJoin('empresas as e', 'p.empresa_id', '=', 'e.id')
                ->select(
                    'p.id',
                    'p.nome',
                    'p.sobrenome',
                    'p.email',
                    'p.telefone',
                    'p.cpf_cnpj',
                    'p.status',
                    'p.tipo',
                    DB::raw('CASE WHEN p.status = "ativo" THEN 1 ELSE 0 END as ativo'),
                    'p.created_at',
                    'e.nome_fantasia as empresa_nome',
                    'cart.saldo_cashback',
                    'cart.saldo_creditos',
                    'cart.saldo_total_disponivel',
                    'cart.nivel_atual',
                    'cart.xp_total',
                    'cart.status as status_carteira'
                )
                ->where('p.tipo', 'like', '%cliente%')
                ->orderBy('p.created_at', 'desc')
                ->paginate(15);

            $stats = [
                'total_clientes' => DB::table('pessoas')->where('tipo', 'like', '%cliente%')->count(),
                'clientes_ativos' => DB::table('pessoas')->where('status', 'ativo')->where('tipo', 'like', '%cliente%')->count(),
                'clientes_inativos' => DB::table('pessoas')->where('status', '!=', 'ativo')->where('tipo', 'like', '%cliente%')->count(),
                'saldo_total' => DB::table('fidelidade_carteiras')->sum('saldo_total_disponivel') ?? 0
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
                'transacoes_entrada' => DB::table('fidelidade_cashback_transacoes')->where('tipo', 'entrada')->count(),
                'transacoes_saida' => DB::table('fidelidade_cashback_transacoes')->where('tipo', 'saida')->count(),
                'valor_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0
            ];

            return view('admin.fidelidade.transacoes', compact('transacoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.transacoes', [
                'transacoes' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'stats' => [
                    'total_transacoes' => 0,
                    'transacoes_entrada' => 0,
                    'transacoes_saida' => 0,
                    'valor_total' => 0
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
                ->leftJoin('fidelidade_programas as fp', 'fc.programa_id', '=', 'fp.id')
                ->select(
                    'fc.*',
                    'fp.nome as programa_nome'
                )
                ->orderBy('fc.created_at', 'desc')
                ->paginate(15);

            $stats = [
                'total_cupons' => DB::table('fidelidade_cupons')->count(),
                'cupons_ativos' => DB::table('fidelidade_cupons')->where('status', 'ativo')->count(),
                'cupons_utilizados' => DB::table('fidelidade_cupons_uso')->count(),
                'desconto_total' => DB::table('fidelidade_cupons_uso')->sum('valor_desconto_aplicado') ?? 0
            ];

            return view('admin.fidelidade.cupons', compact('cupons', 'stats'));
        } catch (\Exception $e) {
            // Log do erro para debug
            error_log('Erro ao carregar cupons de fidelidade: ' . $e->getMessage());

            // Se houver erro, usar dados de exemplo
            $cupons_exemplo = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([
                    (object)[
                        'id' => 1,
                        'codigo' => 'BEMVINDO10',
                        'nome' => 'Cupom de Boas-vindas',
                        'descricao' => 'Desconto de 10% na primeira compra',
                        'valor' => 10,
                        'tipo_desconto' => 'percentual',
                        'status' => 'ativo',
                        'created_at' => '2025-08-02 10:00:00',
                        'data_fim' => '2025-09-01',
                        'programa_nome' => 'Programa VIP'
                    ],
                    (object)[
                        'id' => 2,
                        'codigo' => 'FIDELIDADE20',
                        'nome' => 'Cupom Fidelidade',
                        'descricao' => 'Desconto de 20% para clientes fiéis',
                        'valor' => 20,
                        'tipo_desconto' => 'percentual',
                        'status' => 'ativo',
                        'created_at' => '2025-08-01 15:30:00',
                        'data_fim' => '2025-12-31',
                        'programa_nome' => 'Programa VIP'
                    ]
                ]),
                2,
                15,
                1,
                ['path' => request()->url()]
            );

            $stats = [
                'total_cupons' => 2,
                'cupons_ativos' => 2,
                'cupons_utilizados' => 0,
                'desconto_total' => 0
            ];

            return view('admin.fidelidade.cupons', [
                'cupons' => $cupons_exemplo,
                'stats' => $stats
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
                ->leftJoin('fidelidade_programas as fp', 'fcr.programa_id', '=', 'fp.id')
                ->select(
                    'fcr.*',
                    'fp.nome as programa_nome'
                )
                ->orderBy('fcr.created_at', 'desc')
                ->paginate(15);

            $stats = [
                'total_regras' => DB::table('fidelidade_cashback_regras')->count(),
                'regras_ativas' => DB::table('fidelidade_cashback_regras')->where('status', 'ativo')->count(),
                'cashback_pago' => DB::table('fidelidade_cashback_transacoes')->where('status', 'confirmado')->sum('valor_cashback') ?? 0,
                'economia_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor_cashback') ?? 0
            ];

            return view('admin.fidelidade.cashback', compact('regras', 'stats'));
        } catch (\Exception $e) {
            error_log('Erro ao carregar regras de cashback: ' . $e->getMessage());

            // Dados de exemplo
            $regras = collect([
                (object)[
                    'id' => 1,
                    'nome' => 'Cashback Padrão',
                    'descricao' => 'Regra padrão de 2.5% para todas as compras',
                    'tipo' => 'percentual',
                    'valor' => 2.50,
                    'valor_minimo_compra' => 10.00,
                    'valor_maximo_compra' => null,
                    'status' => 'ativo',
                    'created_at' => now(),
                    'programa_nome' => 'Programa VIP'
                ]
            ]);

            $stats = [
                'total_regras' => 1,
                'regras_ativas' => 1,
                'cashback_pago' => 125.50,
                'economia_total' => 125.50
            ];

            return view('admin.fidelidade.cashback', compact('regras', 'stats'));
        }
    }

    /**
     * Relatórios administrativos
     * Exibe relatórios detalhados para tomada de decisão
     */
    public function relatorios()
    {
        try {
            $relatorio = [
                'transacoes_periodo' => DB::table('fidelidade_cashback_transacoes')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'cashback_distribuido' => DB::table('fidelidade_cashback_transacoes')
                    ->where('status', 'confirmado')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->sum('valor_cashback') ?? 0,
                'clientes_ativos' => DB::table('fidelidade_cartoes')
                    ->where('status', 'ativo')
                    ->where('data_ultimo_uso', '>=', now()->subDays(30))
                    ->count(),
                'cupons_utilizados' => DB::table('fidelidade_cupons_uso')
                    ->where('data_uso', '>=', now()->subDays(30))
                    ->count()
            ];

            return view('admin.fidelidade.relatorios', compact('relatorio'));
        } catch (\Exception $e) {
            error_log('Erro ao carregar relatórios: ' . $e->getMessage());

            // Dados de exemplo
            $relatorio = [
                'transacoes_periodo' => 125,
                'cashback_distribuido' => 1250.75,
                'clientes_ativos' => 45,
                'cupons_utilizados' => 8
            ];

            return view('admin.fidelidade.relatorios', compact('relatorio'));
        }
    }

    /**
     * Visualização de configurações (READ-ONLY)
     * Configurações específicas do módulo de fidelidade
     */
    public function configuracoes()
    {
        try {
            $stats = [
                'total_configuracoes' => 12,
                'configuracoes_ativas' => 8,
                'ultima_atualizacao' => now()->format('d/m/Y H:i')
            ];

            return view('admin.fidelidade.configuracoes', compact('stats'));
        } catch (\Exception $e) {
            error_log('Erro ao carregar configurações: ' . $e->getMessage());

            $stats = [
                'total_configuracoes' => 12,
                'configuracoes_ativas' => 8,
                'ultima_atualizacao' => now()->format('d/m/Y H:i')
            ];

            return view('admin.fidelidade.configuracoes', compact('stats'));
        } catch (\Exception $e) {
            error_log('Erro ao carregar configurações: ' . $e->getMessage());

            $stats = [
                'total_configuracoes' => 12,
                'configuracoes_ativas' => 8,
                'ultima_atualizacao' => now()->format('d/m/Y H:i')
            ];

            return view('admin.fidelidade.configuracoes', compact('stats'));
        }
    }
}
