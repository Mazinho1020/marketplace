<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fidelidade\StoreTransacaoRequest;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Cliente;
use App\Models\Business\Business;
use App\Services\Fidelidade\TransacaoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransacoesController extends Controller
{
    public function __construct(
        private TransacaoService $transacaoService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $tipo = $request->get('tipo');
        $clienteId = $request->get('cliente_id');
        $empresaId = $request->get('empresa_id');
        $dataInicio = $request->get('data_inicio');
        $dataFim = $request->get('data_fim');

        $transacoes = FidelidadeCashbackTransacao::with(['cliente', 'empresa'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('cliente', function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                })->orWhere('descricao', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($tipo, function ($query, $tipo) {
                return $query->where('tipo', $tipo);
            })
            ->when($clienteId, function ($query, $clienteId) {
                return $query->where('cliente_id', $clienteId);
            })
            ->when($empresaId, function ($query, $empresaId) {
                return $query->where('empresa_id', $empresaId);
            })
            ->when($dataInicio, function ($query, $dataInicio) {
                return $query->whereDate('created_at', '>=', $dataInicio);
            })
            ->when($dataFim, function ($query, $dataFim) {
                return $query->whereDate('created_at', '<=', $dataFim);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcular estatísticas
        $totalCreditos = FidelidadeCashbackTransacao::where('tipo', 'credito')->sum('valor');
        $totalDebitos = FidelidadeCashbackTransacao::where('tipo', 'debito')->sum('valor');

        // Buscar dados para os filtros
        $clientes = Cliente::orderBy('nome')->get();
        $empresas = Business::orderBy('nome_fantasia')->get();

        $estatisticas = [
            'total_transacoes' => FidelidadeCashbackTransacao::count(),
            'transacoes_hoje' => FidelidadeCashbackTransacao::whereDate('created_at', today())->count(),
            'valor_total_credito' => $totalCreditos,
            'valor_total_debito' => $totalDebitos,
            'transacoes_disponivel' => FidelidadeCashbackTransacao::where('status', 'disponivel')->count()
        ];

        return view('fidelidade.transacoes.index', compact(
            'transacoes',
            'estatisticas',
            'totalCreditos',
            'totalDebitos',
            'clientes',
            'empresas'
        ));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $empresas = Business::orderBy('nome_fantasia')->get();

        return view('fidelidade.transacoes.create', compact('clientes', 'empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:funforcli,id',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo' => 'required|in:credito,debito',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string|max:255',
            'pedido_id' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($request) {
            // Buscar ou criar carteira do cliente
            $carteira = FidelidadeCarteira::firstOrCreate([
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $request->empresa_id,
            ], [
                'saldo_cashback' => 0,
                'saldo_creditos' => 0,
                'saldo_total_disponivel' => 0,
                'nivel' => 'bronze',
                'status' => 'ativo',
            ]);

            // Criar a transação
            FidelidadeCashbackTransacao::create([
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $request->empresa_id,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'descricao' => $request->descricao,
                'pedido_id' => $request->pedido_id,
                'status' => 'processado',
            ]);

            // Atualizar saldo da carteira
            if ($request->tipo === 'credito') {
                $carteira->saldo_cashback += $request->valor;
            } else {
                $carteira->saldo_cashback -= $request->valor;
            }

            $carteira->saldo_total_disponivel = $carteira->saldo_cashback + $carteira->saldo_creditos;
            $carteira->save();
        });

        return redirect()->route('fidelidade.transacoes.index')
            ->with('success', 'Transação criada com sucesso!');
    }

    public function show(FidelidadeCashbackTransacao $transacao)
    {
        $transacao->load(['cliente', 'empresa']);

        return view('fidelidade.transacoes.show', compact('transacao'));
    }

    public function edit(FidelidadeCashbackTransacao $transacao)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $empresas = Business::orderBy('nome_fantasia')->get();

        return view('fidelidade.transacoes.edit', compact('transacao', 'clientes', 'empresas'));
    }

    public function update(Request $request, FidelidadeCashbackTransacao $transacao)
    {
        $request->validate([
            'cliente_id' => 'required|exists:funforcli,id',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo' => 'required|in:credito,debito',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string|max:255',
            'pedido_id' => 'nullable|string|max:50',
        ]);

        $valorAnterior = $transacao->valor;
        $tipoAnterior = $transacao->tipo;

        DB::transaction(function () use ($request, $transacao, $valorAnterior, $tipoAnterior) {
            // Reverter a transação anterior
            $carteira = FidelidadeCarteira::where('cliente_id', $transacao->cliente_id)
                ->where('empresa_id', $transacao->empresa_id)
                ->first();

            if ($carteira) {
                if ($tipoAnterior === 'credito') {
                    $carteira->saldo_cashback -= $valorAnterior;
                } else {
                    $carteira->saldo_cashback += $valorAnterior;
                }

                // Aplicar a nova transação
                if ($request->tipo === 'credito') {
                    $carteira->saldo_cashback += $request->valor;
                } else {
                    $carteira->saldo_cashback -= $request->valor;
                }

                $carteira->saldo_total_disponivel = $carteira->saldo_cashback + $carteira->saldo_creditos;
                $carteira->save();
            }

            // Atualizar a transação
            $transacao->update([
                'cliente_id' => $request->cliente_id,
                'empresa_id' => $request->empresa_id,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'descricao' => $request->descricao,
                'pedido_id' => $request->pedido_id,
            ]);
        });

        return redirect()->route('fidelidade.transacoes.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(FidelidadeCashbackTransacao $transacao)
    {
        DB::transaction(function () use ($transacao) {
            // Reverter o impacto na carteira
            $carteira = FidelidadeCarteira::where('cliente_id', $transacao->cliente_id)
                ->where('empresa_id', $transacao->empresa_id)
                ->first();

            if ($carteira) {
                if ($transacao->tipo === 'credito') {
                    $carteira->saldo_cashback -= $transacao->valor;
                } else {
                    $carteira->saldo_cashback += $transacao->valor;
                }

                $carteira->saldo_total_disponivel = $carteira->saldo_cashback + $carteira->saldo_creditos;
                $carteira->save();
            }

            $transacao->delete();
        });

        return redirect()->route('fidelidade.transacoes.index')
            ->with('success', 'Transação excluída com sucesso!');
    }

    public function dashboard()
    {
        $hoje = Carbon::today();
        $ontem = Carbon::yesterday();
        $mesAtual = Carbon::now()->startOfMonth();
        $mesPassado = Carbon::now()->subMonth()->startOfMonth();

        $estatisticas = [
            'total_hoje' => FidelidadeCashbackTransacao::whereDate('created_at', $hoje)->count(),
            'total_ontem' => FidelidadeCashbackTransacao::whereDate('created_at', $ontem)->count(),
            'valor_hoje' => FidelidadeCashbackTransacao::whereDate('created_at', $hoje)->sum('valor'),
            'valor_ontem' => FidelidadeCashbackTransacao::whereDate('created_at', $ontem)->sum('valor'),
            'mes_atual' => FidelidadeCashbackTransacao::where('created_at', '>=', $mesAtual)->count(),
            'mes_passado' => FidelidadeCashbackTransacao::where('created_at', '>=', $mesPassado)
                ->where('created_at', '<', $mesAtual)->count(),
        ];

        return view('fidelidade.transacoes.dashboard', compact('estatisticas'));
    }

    public function exportar(Request $request)
    {
        $transacoes = FidelidadeCashbackTransacao::with(['cliente', 'empresa'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'transacoes_cashback_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($transacoes) {
            $file = fopen('php://output', 'w');

            // Cabeçalho CSV
            fputcsv($file, [
                'ID',
                'Cliente',
                'Empresa',
                'Tipo',
                'Valor',
                'Descrição',
                'Pedido ID',
                'Status',
                'Data Criação'
            ]);

            // Dados
            foreach ($transacoes as $transacao) {
                fputcsv($file, [
                    $transacao->id,
                    $transacao->cliente->nome ?? 'N/A',
                    $transacao->empresa->nome_fantasia ?? 'N/A',
                    $transacao->tipo,
                    number_format($transacao->valor, 2, ',', '.'),
                    $transacao->descricao,
                    $transacao->pedido_id ?? '',
                    $transacao->status ?? 'processado',
                    $transacao->created_at->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
