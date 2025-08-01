<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FidelidadeController extends Controller
{
    public function dashboard()
    {
        try {
            // Estatísticas gerais baseadas nas tabelas reais
            $stats = [
                'total_regras' => DB::table('fidelidade_cashback_regras')->count(),
                'regras_ativas' => DB::table('fidelidade_cashback_regras')->where('status', 'ativo')->count(),
                'total_carteiras' => DB::table('fidelidade_carteiras')->count(),
                'total_creditos' => DB::table('fidelidade_creditos')->count(),
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'total_valor' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'total_cashback' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0
            ];

            // Atividade recente de transações
            $atividade_recente = DB::table('fidelidade_cashback_transacoes as fct')
                ->leftJoin('empresas as e', 'fct.empresa_id', '=', 'e.id')
                ->select(
                    'fct.*',
                    'e.nome_fantasia as empresa_nome',
                    'fct.observacoes as descricao'
                )
                ->orderBy('fct.created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.fidelidade.dashboard', compact('stats', 'atividade_recente'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.dashboard', [
                'stats' => [
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'total_carteiras' => 0,
                    'total_creditos' => 0,
                    'total_transacoes' => 0,
                    'total_valor' => 0,
                    'total_cashback' => 0
                ],
                'atividade_recente' => collect([])
            ]);
        }
    }

    public function programas()
    {
        try {
            // Usar as regras de cashback como "programas"
            $programas = DB::table('fidelidade_cashback_regras as fcr')
                ->leftJoin('empresas as e', 'fcr.empresa_id', '=', 'e.id')
                ->leftJoin(
                    DB::raw('(SELECT empresa_id, COUNT(*) as total_transacoes FROM fidelidade_cashback_transacoes GROUP BY empresa_id) as trans_count'),
                    'fcr.empresa_id',
                    '=',
                    'trans_count.empresa_id'
                )
                ->select(
                    'fcr.*',
                    'e.nome_fantasia as empresa_nome',
                    DB::raw('COALESCE(trans_count.total_transacoes, 0) as total_transacoes')
                )
                ->orderBy('fcr.criado_em', 'desc')
                ->get();

            $stats = [
                'total_regras' => $programas->count(),
                'regras_ativas' => $programas->where('status', 'ativo')->count(),
                'regras_inativas' => $programas->where('status', 'inativo')->count(),
                'total_transacoes' => $programas->sum('total_transacoes')
            ];

            return view('admin.fidelidade.programas', compact('programas', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.programas', [
                'programas' => collect([]),
                'stats' => [
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'regras_inativas' => 0,
                    'total_transacoes' => 0
                ]
            ]);
        }
    }

    public function clientes()
    {
        try {
            // Usar carteiras como "clientes"
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

    public function cartoes()
    {
        try {
            // Usar carteiras como cartões
            $cartoes = DB::table('fidelidade_carteiras as fc')
                ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
                ->select(
                    'fc.*',
                    'e.nome_fantasia as empresa_nome'
                )
                ->orderBy('fc.criado_em', 'desc')
                ->get();

            $stats = [
                'total_carteiras' => $cartoes->count(),
                'carteiras_ativas' => $cartoes->where('status', 'ativa')->count(),
                'carteiras_inativas' => $cartoes->where('status', 'inativa')->count(),
                'total_transacoes' => 0 // Placeholder até implementar transações por carteira
            ];

            return view('admin.fidelidade.cartoes', compact('cartoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cartoes', [
                'cartoes' => collect([]),
                'stats' => [
                    'total_carteiras' => 0,
                    'carteiras_ativas' => 0,
                    'carteiras_inativas' => 0,
                    'total_transacoes' => 0
                ]
            ]);
        }
    }

    public function transacoes()
    {
        try {
            $transacoes = DB::table('fidelidade_cashback_transacoes as fct')
                ->leftJoin('empresas as e', 'fct.empresa_id', '=', 'e.id')
                ->leftJoin('fidelidade_cashback_regras as fcr', 'fct.empresa_id', '=', 'fcr.empresa_id')
                ->select(
                    'fct.*',
                    'e.nome_fantasia as empresa_nome',
                    'fcr.nome as regra_nome'
                )
                ->orderBy('fct.created_at', 'desc')
                ->limit(1000) // Limitar para performance
                ->get();

            $stats = [
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'valor_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'valor_pedidos' => DB::table('fidelidade_cashback_transacoes')->sum('valor_pedido_original') ?? 0,
                'total_cashback' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0
            ];

            return view('admin.fidelidade.transacoes', compact('transacoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.transacoes', [
                'transacoes' => collect([]),
                'stats' => [
                    'total_transacoes' => 0,
                    'valor_total' => 0,
                    'valor_pedidos' => 0,
                    'total_cashback' => 0
                ]
            ]);
        }
    }

    public function cashback()
    {
        try {
            // Regras de cashback
            $regras = DB::table('fidelidade_cashback_regras as fcr')
                ->leftJoin('empresas as e', 'fcr.empresa_id', '=', 'e.id')
                ->select('fcr.*', 'e.nome_fantasia as empresa_nome')
                ->orderBy('fcr.criado_em', 'desc')
                ->get()
                ->map(function ($regra) {
                    // Formatar faixa de valores
                    $faixa = '';
                    if ($regra->valor_minimo && $regra->valor_maximo) {
                        $faixa = 'R$ ' . number_format($regra->valor_minimo, 0, ',', '.') . ' - R$ ' . number_format($regra->valor_maximo, 0, ',', '.');
                    } elseif ($regra->valor_minimo) {
                        $faixa = 'Acima de R$ ' . number_format($regra->valor_minimo, 0, ',', '.');
                    } elseif ($regra->valor_maximo) {
                        $faixa = 'Até R$ ' . number_format($regra->valor_maximo, 0, ',', '.');
                    } else {
                        $faixa = 'R$ 0 - ∞';
                    }

                    // Formatar valor
                    $valor = '';
                    if ($regra->tipo_cashback == 'percentual') {
                        $valor = number_format($regra->valor_cashback, 1) . '%';
                    } elseif ($regra->tipo_cashback == 'fixo') {
                        $valor = 'R$ ' . number_format($regra->valor_cashback, 2, ',', '.');
                    } else {
                        $valor = number_format($regra->valor_cashback, 1) . '%';
                    }

                    // Formatar vigência
                    $vigencia = '';
                    if ($regra->data_inicio && $regra->data_fim) {
                        $vigencia = date('d/m', strtotime($regra->data_inicio)) . ' - ' . date('d/m/Y', strtotime($regra->data_fim));
                    } elseif ($regra->data_inicio) {
                        $vigencia = 'A partir de ' . date('d/m/Y', strtotime($regra->data_inicio));
                    } elseif ($regra->data_fim) {
                        $vigencia = 'Até ' . date('d/m/Y', strtotime($regra->data_fim));
                    } else {
                        $vigencia = 'Permanente';
                    }

                    return [
                        'id' => $regra->id,
                        'nome' => $regra->nome,
                        'empresa' => $regra->empresa_nome ?: 'Sem empresa',
                        'tipo' => $regra->tipo_cashback,
                        'valor' => $valor,
                        'faixa' => $faixa,
                        'status' => $regra->status,
                        'vigencia' => $vigencia
                    ];
                });

            // Estatísticas de cashback
            $stats = [
                'total_regras' => $regras->count(),
                'regras_ativas' => $regras->where('status', 'ativo')->count(),
                'cashback_distribuido' => DB::table('fidelidade_cashback_transacoes')->sum('valor') ?? 0,
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count()
            ];

            return view('admin.fidelidade.cashback', compact('regras', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cashback', [
                'regras' => collect([]),
                'stats' => [
                    'total_regras' => 0,
                    'regras_ativas' => 0,
                    'cashback_distribuido' => 0,
                    'pontos_distribuidos' => 0
                ]
            ]);
        }
    }

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
                ->get();

            $stats = [
                'total_cupons' => $cupons->count(),
                'cupons_ativos' => $cupons->where('status', 'ativo')->count(),
                'cupons_inativos' => $cupons->where('status', 'inativo')->count(),
                'cupons_usados' => DB::table('fidelidade_cupons_uso')->count()
            ];

            return view('admin.fidelidade.cupons', compact('cupons', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cupons', [
                'cupons' => collect([]),
                'stats' => [
                    'total_cupons' => 0,
                    'cupons_ativos' => 0,
                    'cupons_inativos' => 0,
                    'cupons_usados' => 0
                ]
            ]);
        }
    }
}
