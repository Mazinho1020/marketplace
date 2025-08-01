<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FidelidadeController extends Controller
{
    public function dashboard()
    {
        try {
            // Estatísticas gerais
            $stats = [
                'total_programas' => DB::table('programas_fidelidade')->count(),
                'programas_ativos' => DB::table('programas_fidelidade')->where('status', 'ativo')->count(),
                'total_clientes' => DB::table('clientes')->count(),
                'total_cartoes' => DB::table('cartoes_fidelidade')->count(),
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'total_pontos' => DB::table('fidelidade_cashback_transacoes')->sum('pontos_ganhos') ?? 0,
                'total_cashback' => DB::table('fidelidade_cashback_transacoes')->sum('valor_cashback') ?? 0
            ];

            // Atividade recente
            $atividade_recente = DB::table('fidelidade_cashback_transacoes as fct')
                ->join('clientes as c', 'fct.cliente_id', '=', 'c.id')
                ->join('programas_fidelidade as pf', 'fct.programa_id', '=', 'pf.id')
                ->select('fct.*', 'c.nome as cliente_nome', 'pf.nome as programa_nome')
                ->orderBy('fct.data_transacao', 'desc')
                ->limit(10)
                ->get();

            return view('admin.fidelidade.dashboard', compact('stats', 'atividade_recente'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.dashboard', [
                'stats' => [
                    'total_programas' => 0,
                    'programas_ativos' => 0,
                    'total_clientes' => 0,
                    'total_cartoes' => 0,
                    'total_transacoes' => 0,
                    'total_pontos' => 0,
                    'total_cashback' => 0
                ],
                'atividade_recente' => collect([])
            ]);
        }
    }

    public function programas()
    {
        try {
            $programas = DB::table('programas_fidelidade as pf')
                ->leftJoin('empresas as e', 'pf.empresa_id', '=', 'e.id')
                ->leftJoin(
                    DB::raw('(SELECT programa_id, COUNT(*) as total_clientes FROM clientes GROUP BY programa_id) as cliente_count'),
                    'pf.id',
                    '=',
                    'cliente_count.programa_id'
                )
                ->select(
                    'pf.*',
                    'e.nome as empresa_nome',
                    DB::raw('COALESCE(cliente_count.total_clientes, 0) as total_clientes')
                )
                ->orderBy('pf.created_at', 'desc')
                ->get();

            $stats = [
                'total_programas' => $programas->count(),
                'programas_ativos' => $programas->where('status', 'ativo')->count(),
                'programas_inativos' => $programas->where('status', 'inativo')->count(),
                'total_clientes' => $programas->sum('total_clientes')
            ];

            return view('admin.fidelidade.programas', compact('programas', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.programas', [
                'programas' => collect([]),
                'stats' => [
                    'total_programas' => 0,
                    'programas_ativos' => 0,
                    'programas_inativos' => 0,
                    'total_clientes' => 0
                ]
            ]);
        }
    }

    public function clientes()
    {
        try {
            $clientes = DB::table('clientes as c')
                ->leftJoin('programas_fidelidade as pf', 'c.programa_id', '=', 'pf.id')
                ->leftJoin('cartoes_fidelidade as cf', 'c.id', '=', 'cf.cliente_id')
                ->leftJoin(
                    DB::raw('(SELECT cliente_id, SUM(pontos_ganhos) as total_pontos FROM fidelidade_cashback_transacoes GROUP BY cliente_id) as pontos'),
                    'c.id',
                    '=',
                    'pontos.cliente_id'
                )
                ->select(
                    'c.*',
                    'pf.nome as programa_nome',
                    'cf.numero_cartao',
                    'cf.status as cartao_status',
                    DB::raw('COALESCE(pontos.total_pontos, 0) as total_pontos')
                )
                ->orderBy('c.created_at', 'desc')
                ->get();

            $stats = [
                'total_clientes' => $clientes->count(),
                'clientes_ativos' => $clientes->where('status', 'ativo')->count(),
                'clientes_inativos' => $clientes->where('status', 'inativo')->count(),
                'total_pontos' => $clientes->sum('total_pontos')
            ];

            return view('admin.fidelidade.clientes', compact('clientes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.clientes', [
                'clientes' => collect([]),
                'stats' => [
                    'total_clientes' => 0,
                    'clientes_ativos' => 0,
                    'clientes_inativos' => 0,
                    'total_pontos' => 0
                ]
            ]);
        }
    }

    public function cartoes()
    {
        try {
            $cartoes = DB::table('cartoes_fidelidade as cf')
                ->join('clientes as c', 'cf.cliente_id', '=', 'c.id')
                ->leftJoin('programas_fidelidade as pf', 'c.programa_id', '=', 'pf.id')
                ->select(
                    'cf.*',
                    'c.nome as cliente_nome',
                    'c.email as cliente_email',
                    'pf.nome as programa_nome'
                )
                ->orderBy('cf.created_at', 'desc')
                ->get();

            $stats = [
                'total_cartoes' => $cartoes->count(),
                'cartoes_ativos' => $cartoes->where('status', 'ativo')->count(),
                'cartoes_bloqueados' => $cartoes->where('status', 'bloqueado')->count(),
                'cartoes_expirados' => $cartoes->where('status', 'expirado')->count()
            ];

            return view('admin.fidelidade.cartoes', compact('cartoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cartoes', [
                'cartoes' => collect([]),
                'stats' => [
                    'total_cartoes' => 0,
                    'cartoes_ativos' => 0,
                    'cartoes_bloqueados' => 0,
                    'cartoes_expirados' => 0
                ]
            ]);
        }
    }

    public function transacoes()
    {
        try {
            $transacoes = DB::table('fidelidade_cashback_transacoes as fct')
                ->join('clientes as c', 'fct.cliente_id', '=', 'c.id')
                ->leftJoin('programas_fidelidade as pf', 'fct.programa_id', '=', 'pf.id')
                ->select(
                    'fct.*',
                    'c.nome as cliente_nome',
                    'pf.nome as programa_nome'
                )
                ->orderBy('fct.data_transacao', 'desc')
                ->limit(1000) // Limitar para performance
                ->get();

            $stats = [
                'total_transacoes' => DB::table('fidelidade_cashback_transacoes')->count(),
                'valor_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor_compra') ?? 0,
                'pontos_total' => DB::table('fidelidade_cashback_transacoes')->sum('pontos_ganhos') ?? 0,
                'cashback_total' => DB::table('fidelidade_cashback_transacoes')->sum('valor_cashback') ?? 0
            ];

            return view('admin.fidelidade.transacoes', compact('transacoes', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.transacoes', [
                'transacoes' => collect([]),
                'stats' => [
                    'total_transacoes' => 0,
                    'valor_total' => 0,
                    'pontos_total' => 0,
                    'cashback_total' => 0
                ]
            ]);
        }
    }

    public function cashback()
    {
        try {
            // Regras de cashback
            $regras = DB::table('fidelidade_cashback_regras as fcr')
                ->leftJoin('programas_fidelidade as pf', 'fcr.programa_id', '=', 'pf.id')
                ->select('fcr.*', 'pf.nome as programa_nome')
                ->orderBy('fcr.created_at', 'desc')
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
                        'programa' => $regra->programa_nome ?: 'Sem programa',
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
                'cashback_distribuido' => DB::table('fidelidade_cashback_transacoes')->sum('valor_cashback') ?? 0,
                'pontos_distribuidos' => DB::table('fidelidade_cashback_transacoes')->sum('pontos_ganhos') ?? 0
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
                ->leftJoin('programas_fidelidade as pf', 'fc.programa_id', '=', 'pf.id')
                ->leftJoin('clientes as c', 'fc.cliente_id', '=', 'c.id')
                ->select(
                    'fc.*',
                    'pf.nome as programa_nome',
                    'c.nome as cliente_nome'
                )
                ->orderBy('fc.created_at', 'desc')
                ->get();

            $stats = [
                'total_cupons' => $cupons->count(),
                'cupons_ativos' => $cupons->where('status', 'ativo')->count(),
                'cupons_usados' => $cupons->where('status', 'usado')->count(),
                'cupons_expirados' => $cupons->where('status', 'expirado')->count()
            ];

            return view('admin.fidelidade.cupons', compact('cupons', 'stats'));
        } catch (\Exception $e) {
            return view('admin.fidelidade.cupons', [
                'cupons' => collect([]),
                'stats' => [
                    'total_cupons' => 0,
                    'cupons_ativos' => 0,
                    'cupons_usados' => 0,
                    'cupons_expirados' => 0
                ]
            ]);
        }
    }

    // CRUD Operations
    public function store(Request $request)
    {
        // Implementar criação baseada no tipo
        $tipo = $request->input('tipo');

        switch ($tipo) {
            case 'programa':
                return $this->storePrograma($request);
            case 'cliente':
                return $this->storeCliente($request);
            case 'cartao':
                return $this->storeCartao($request);
            default:
                return response()->json(['error' => 'Tipo não especificado'], 400);
        }
    }

    private function storePrograma(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'empresa_id' => 'required|integer',
            'pontos_real' => 'required|numeric|min:0',
            'status' => 'required|in:ativo,inativo'
        ]);

        try {
            $id = DB::table('programas_fidelidade')->insertGetId([
                'nome' => $request->nome,
                'empresa_id' => $request->empresa_id,
                'pontos_real' => $request->pontos_real,
                'status' => $request->status,
                'descricao' => $request->descricao,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar programa'], 500);
        }
    }

    private function storeCliente(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'programa_id' => 'required|integer',
            'status' => 'required|in:ativo,inativo'
        ]);

        try {
            $id = DB::table('clientes')->insertGetId([
                'nome' => $request->nome,
                'email' => $request->email,
                'programa_id' => $request->programa_id,
                'status' => $request->status,
                'telefone' => $request->telefone,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar cliente'], 500);
        }
    }

    private function storeCartao(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|integer',
            'numero_cartao' => 'required|string|max:50',
            'status' => 'required|in:ativo,bloqueado,expirado'
        ]);

        try {
            $id = DB::table('cartoes_fidelidade')->insertGetId([
                'cliente_id' => $request->cliente_id,
                'numero_cartao' => $request->numero_cartao,
                'status' => $request->status,
                'data_expiracao' => $request->data_expiracao,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar cartão'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Implementar atualização
        $tipo = $request->input('tipo');

        switch ($tipo) {
            case 'programa':
                return $this->updatePrograma($request, $id);
            case 'cliente':
                return $this->updateCliente($request, $id);
            case 'cartao':
                return $this->updateCartao($request, $id);
            default:
                return response()->json(['error' => 'Tipo não especificado'], 400);
        }
    }

    private function updatePrograma(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'empresa_id' => 'required|integer',
            'pontos_real' => 'required|numeric|min:0',
            'status' => 'required|in:ativo,inativo'
        ]);

        try {
            DB::table('programas_fidelidade')->where('id', $id)->update([
                'nome' => $request->nome,
                'empresa_id' => $request->empresa_id,
                'pontos_real' => $request->pontos_real,
                'status' => $request->status,
                'descricao' => $request->descricao,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar programa'], 500);
        }
    }

    private function updateCliente(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'programa_id' => 'required|integer',
            'status' => 'required|in:ativo,inativo'
        ]);

        try {
            DB::table('clientes')->where('id', $id)->update([
                'nome' => $request->nome,
                'email' => $request->email,
                'programa_id' => $request->programa_id,
                'status' => $request->status,
                'telefone' => $request->telefone,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar cliente'], 500);
        }
    }

    private function updateCartao(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|integer',
            'numero_cartao' => 'required|string|max:50',
            'status' => 'required|in:ativo,bloqueado,expirado'
        ]);

        try {
            DB::table('cartoes_fidelidade')->where('id', $id)->update([
                'cliente_id' => $request->cliente_id,
                'numero_cartao' => $request->numero_cartao,
                'status' => $request->status,
                'data_expiracao' => $request->data_expiracao,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar cartão'], 500);
        }
    }

    public function destroy($tipo, $id)
    {
        try {
            switch ($tipo) {
                case 'programa':
                    DB::table('programas_fidelidade')->where('id', $id)->delete();
                    break;
                case 'cliente':
                    DB::table('clientes')->where('id', $id)->delete();
                    break;
                case 'cartao':
                    DB::table('cartoes_fidelidade')->where('id', $id)->delete();
                    break;
                default:
                    return response()->json(['error' => 'Tipo inválido'], 400);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir registro'], 500);
        }
    }

    public function toggleStatus($tipo, $id)
    {
        try {
            $table = '';
            switch ($tipo) {
                case 'programa':
                    $table = 'programas_fidelidade';
                    break;
                case 'cliente':
                    $table = 'clientes';
                    break;
                case 'cartao':
                    $table = 'cartoes_fidelidade';
                    break;
                default:
                    return response()->json(['error' => 'Tipo inválido'], 400);
            }

            $current = DB::table($table)->where('id', $id)->value('status');
            $newStatus = $current === 'ativo' ? 'inativo' : 'ativo';

            DB::table($table)->where('id', $id)->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'status' => $newStatus]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao alterar status'], 500);
        }
    }
}
