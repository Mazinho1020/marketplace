<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCredito;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Cliente;
use App\Models\Business\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarteirasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $nivel = $request->get('nivel');
            $saldoMin = $request->get('saldo_min');

            $carteiras = FidelidadeCarteira::query()
                ->when($search, function ($query, $search) {
                    return $query->where('cliente_id', 'like', "%{$search}%");
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($nivel, function ($query, $nivel) {
                    return $query->where('nivel_atual', $nivel);
                })
                ->when($saldoMin, function ($query, $saldoMin) {
                    return $query->where('saldo_total_disponivel', '>=', $saldoMin);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $estatisticas = [
                'total_carteiras' => FidelidadeCarteira::count(),
                'carteiras_ativas' => FidelidadeCarteira::where('status', 'ativa')->count(),
                'saldo_total' => FidelidadeCarteira::sum('saldo_total_disponivel'),
                'cashback_total' => FidelidadeCarteira::sum('saldo_cashback')
            ];

            return view('fidelidade.carteiras.index', compact('carteiras', 'estatisticas'));
        } catch (\Exception $e) {
            return view('fidelidade.carteiras.index', [
                'carteiras' => FidelidadeCarteira::paginate(20),
                'estatisticas' => [
                    'total_carteiras' => 0,
                    'carteiras_ativas' => 0,
                    'saldo_total' => 0,
                    'cashback_total' => 0
                ]
            ]);
        }
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();

        // Como não temos uma tabela de empresas específica, vamos criar uma lista baseada nas carteiras existentes
        $empresas = collect([
            (object)['id' => 1, 'business_name' => 'Empresa Principal'],
            (object)['id' => 2, 'business_name' => 'Filial 1'],
            (object)['id' => 3, 'business_name' => 'Filial 2']
        ]);

        return view('fidelidade.carteiras.create', compact('clientes', 'empresas'));
    }
    public function show($id)
    {
        $carteira = FidelidadeCarteira::with(['cliente', 'empresa', 'transacoesCashback', 'creditos'])
            ->findOrFail($id);

        $ultimasTransacoes = FidelidadeCashbackTransacao::where('cliente_id', $carteira->cliente_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $creditosDisponiveis = FidelidadeCredito::where('cliente_id', $carteira->cliente_id)
            ->where('status', 'disponivel')
            ->where('data_expiracao', '>', Carbon::now())
            ->get();

        $estatisticasTransacoes = [
            'total_ganho' => FidelidadeCashbackTransacao::where('cliente_id', $carteira->cliente_id)
                ->where('tipo', 'credito')
                ->sum('valor'),
            'total_resgatado' => FidelidadeCashbackTransacao::where('cliente_id', $carteira->cliente_id)
                ->where('tipo', 'debito')
                ->sum('valor'),
            'transacoes_mes' => FidelidadeCashbackTransacao::where('cliente_id', $carteira->cliente_id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count()
        ];

        return view('fidelidade.carteiras.show', compact(
            'carteira',
            'ultimasTransacoes',
            'creditosDisponiveis',
            'estatisticasTransacoes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:funforcli,id',
            'empresa_id' => 'required|integer',
            'nivel_atual' => 'required|string'
        ]);

        $carteira = FidelidadeCarteira::create([
            'cliente_id' => $request->cliente_id,
            'empresa_id' => $request->empresa_id,
            'saldo_cashback' => 0.00,
            'saldo_creditos' => 0.00,
            'saldo_bloqueado' => 0.00,
            'saldo_total_disponivel' => 0.00,
            'nivel_atual' => $request->nivel_atual,
            'xp_total' => 0,
            'status' => 'ativa',
            'sync_status' => 'sincronizado'
        ]);

        return redirect()->route('fidelidade.carteiras.show', $carteira->id)
            ->with('success', 'Carteira criada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $carteira = FidelidadeCarteira::findOrFail($id);

        $request->validate([
            'status' => 'required|in:ativa,bloqueada,suspensa,cancelada',
            'nivel_atual' => 'required|string'
        ]);

        $carteira->update($request->only(['status', 'nivel_atual']));

        return redirect()->back()->with('success', 'Carteira atualizada com sucesso!');
    }

    public function edit($id)
    {
        $carteira = FidelidadeCarteira::findOrFail($id);

        $clientes = Cliente::orderBy('nome')->get();

        // Como não temos uma tabela de empresas específica, vamos criar uma lista baseada nas carteiras existentes
        $empresas = collect([
            (object)['id' => 1, 'business_name' => 'Empresa Principal'],
            (object)['id' => 2, 'business_name' => 'Filial 1'],
            (object)['id' => 3, 'business_name' => 'Filial 2']
        ]);

        return view('fidelidade.carteiras.edit', compact('carteira', 'clientes', 'empresas'));
    }
    public function destroy($id)
    {
        try {
            $carteira = FidelidadeCarteira::findOrFail($id);

            // Verificar se tem transações antes de deletar
            $temTransacoes = FidelidadeCashbackTransacao::where('cliente_id', $carteira->cliente_id)->exists();

            if ($temTransacoes) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir carteira que possui transações. Considere inativá-la.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Não é possível excluir carteira que possui transações. Considere inativá-la.');
            }

            $carteira->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Carteira excluída com sucesso!']);
            }

            return redirect()->route('fidelidade.carteiras.index')
                ->with('success', 'Carteira excluída com sucesso!');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Erro ao excluir carteira: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao excluir carteira: ' . $e->getMessage());
        }
    }

    public function ajustarSaldo(Request $request, $id)
    {
        $carteira = FidelidadeCarteira::findOrFail($id);

        $request->validate([
            'tipo_ajuste' => 'required|in:cashback,credito',
            'valor' => 'required|numeric|min:0',
            'operacao' => 'required|in:adicionar,remover',
            'motivo' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($carteira, $request) {
            if ($request->tipo_ajuste === 'cashback') {
                if ($request->operacao === 'adicionar') {
                    $carteira->saldo_cashback += $request->valor;
                } else {
                    $carteira->saldo_cashback = max(0, $carteira->saldo_cashback - $request->valor);
                }
            } else {
                if ($request->operacao === 'adicionar') {
                    $carteira->saldo_creditos += $request->valor;
                } else {
                    $carteira->saldo_creditos = max(0, $carteira->saldo_creditos - $request->valor);
                }
            }

            $carteira->saldo_total_disponivel = $carteira->calcularSaldoTotal();
            $carteira->save();

            // Registrar transação
            FidelidadeCashbackTransacao::create([
                'cliente_id' => $carteira->cliente_id,
                'empresa_id' => $carteira->empresa_id,
                'tipo' => $request->operacao === 'adicionar' ? 'credito' : 'debito',
                'valor' => $request->valor,
                'saldo_anterior' => $carteira->saldo_total_disponivel - $request->valor,
                'saldo_posterior' => $carteira->saldo_total_disponivel,
                'observacoes' => $request->motivo,
                'status' => 'disponivel'
            ]);
        });

        return redirect()->back()->with('success', 'Saldo ajustado com sucesso!');
    }

    public function bloquear($id)
    {
        try {
            $carteira = FidelidadeCarteira::findOrFail($id);
            $carteira->update(['status' => 'bloqueada']);

            return response()->json(['success' => true, 'message' => 'Carteira bloqueada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao bloquear carteira: ' . $e->getMessage()], 500);
        }
    }

    public function desbloquear($id)
    {
        try {
            $carteira = FidelidadeCarteira::findOrFail($id);
            $carteira->update(['status' => 'ativa']);

            return response()->json(['success' => true, 'message' => 'Carteira desbloqueada com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao desbloquear carteira: ' . $e->getMessage()], 500);
        }
    }

    public function exportar(Request $request)
    {
        $carteiras = FidelidadeCarteira::with(['cliente', 'empresa'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="carteiras_fidelidade.csv"',
        ];

        $callback = function () use ($carteiras) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Cliente', 'Email', 'Empresa', 'Saldo Cashback', 'Saldo Créditos', 'Nível', 'Status', 'Data Criação']);

            foreach ($carteiras as $carteira) {
                fputcsv($file, [
                    $carteira->id,
                    $carteira->cliente->name,
                    $carteira->cliente->email,
                    $carteira->empresa->name ?? 'N/A',
                    $carteira->saldo_cashback,
                    $carteira->saldo_creditos,
                    $carteira->nivel_atual,
                    $carteira->status,
                    $carteira->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
