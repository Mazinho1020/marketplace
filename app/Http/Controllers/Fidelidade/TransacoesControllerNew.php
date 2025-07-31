<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fidelidade\StoreTransacaoRequest;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Cliente;
use App\Models\Business\Business;
use App\Services\Fidelidade\TransacaoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TransacoesController extends Controller
{
    public function __construct(
        private TransacaoService $transacaoService
    ) {}

    public function index(Request $request): View
    {
        $transacoes = FidelidadeCashbackTransacao::with(['cliente', 'empresa'])
            ->filter($request->only(['search', 'status', 'tipo', 'cliente_id', 'empresa_id', 'data_inicio', 'data_fim']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $estatisticas = $this->obterEstatisticas();
        $filtros = $this->obterDadosFiltros();

        return view('fidelidade.transacoes.index', compact('transacoes', 'estatisticas', 'filtros'));
    }

    public function create(): View
    {
        $filtros = $this->obterDadosFiltros();

        return view('fidelidade.transacoes.create', $filtros);
    }

    public function store(StoreTransacaoRequest $request): RedirectResponse
    {
        try {
            $transacao = $this->transacaoService->processarTransacao($request->validated());

            return redirect()
                ->route('fidelidade.transacoes.show', $transacao)
                ->with('success', 'Transação criada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar transação: ' . $e->getMessage());
        }
    }

    public function show(FidelidadeCashbackTransacao $transacao): View
    {
        $transacao->load(['cliente', 'empresa']);

        return view('fidelidade.transacoes.show', compact('transacao'));
    }

    public function edit(FidelidadeCashbackTransacao $transacao): View
    {
        $filtros = $this->obterDadosFiltros();

        return view('fidelidade.transacoes.edit', compact('transacao') + $filtros);
    }

    public function update(StoreTransacaoRequest $request, FidelidadeCashbackTransacao $transacao): RedirectResponse
    {
        try {
            $transacao->update($request->validated());

            return redirect()
                ->route('fidelidade.transacoes.show', $transacao)
                ->with('success', 'Transação atualizada com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar transação: ' . $e->getMessage());
        }
    }

    public function destroy(FidelidadeCashbackTransacao $transacao): RedirectResponse
    {
        try {
            $this->transacaoService->cancelarTransacao($transacao);

            return redirect()
                ->route('fidelidade.transacoes.index')
                ->with('success', 'Transação cancelada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cancelar transação: ' . $e->getMessage());
        }
    }

    public function cancelar(FidelidadeCashbackTransacao $transacao): RedirectResponse
    {
        return $this->destroy($transacao);
    }

    public function processar(FidelidadeCashbackTransacao $transacao): RedirectResponse
    {
        try {
            $transacao->update(['status' => 'processado']);

            return back()->with('success', 'Transação processada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao processar transação: ' . $e->getMessage());
        }
    }

    public function estornar(FidelidadeCashbackTransacao $transacao): RedirectResponse
    {
        try {
            $this->transacaoService->cancelarTransacao($transacao);
            $transacao->update(['status' => 'estornado']);

            return back()->with('success', 'Transação estornada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao estornar transação: ' . $e->getMessage());
        }
    }

    public function exportar(Request $request)
    {
        // Implementar exportação usando Laravel Excel ou similar
        return response()->download(storage_path('exports/transacoes.csv'));
    }

    public function dashboard(): View
    {
        $estatisticas = $this->obterEstatisticasCompletas();

        return view('fidelidade.transacoes.dashboard', compact('estatisticas'));
    }

    public function criarManual(Request $request): RedirectResponse
    {
        $request->validate([
            'cliente_id' => 'required|exists:funforcli,id',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string',
        ]);

        try {
            $dados = $request->only(['cliente_id', 'valor', 'descricao']) + [
                'empresa_id' => auth()->user()->empresa_id ?? 1, // Ajustar conforme sua lógica
                'tipo' => 'credito',
            ];

            $transacao = $this->transacaoService->processarTransacao($dados);

            return back()->with('success', 'Transação manual criada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar transação: ' . $e->getMessage());
        }
    }

    /**
     * Obter dados para filtros (Cache por 5 minutos)
     */
    private function obterDadosFiltros(): array
    {
        return Cache::remember('transacoes_filtros', 300, function () {
            return [
                'clientes' => Cliente::orderBy('nome')->get(['id', 'nome']),
                'empresas' => Business::orderBy('nome_fantasia')->get(['id', 'nome_fantasia']),
            ];
        });
    }

    /**
     * Obter estatísticas básicas
     */
    private function obterEstatisticas(): array
    {
        return Cache::remember('transacoes_estatisticas', 300, function () {
            return [
                'total_transacoes' => FidelidadeCashbackTransacao::count(),
                'transacoes_hoje' => FidelidadeCashbackTransacao::whereDate('created_at', today())->count(),
                'valor_total_credito' => FidelidadeCashbackTransacao::where('tipo', 'credito')->sum('valor'),
                'valor_total_debito' => FidelidadeCashbackTransacao::where('tipo', 'debito')->sum('valor'),
            ];
        });
    }

    /**
     * Obter estatísticas completas para dashboard
     */
    private function obterEstatisticasCompletas(): array
    {
        return Cache::remember('transacoes_estatisticas_completas', 600, function () {
            $hoje = today();
            $inicioMes = $hoje->copy()->startOfMonth();

            return [
                'hoje' => [
                    'transacoes' => FidelidadeCashbackTransacao::whereDate('created_at', $hoje)->count(),
                    'valor_credito' => FidelidadeCashbackTransacao::whereDate('created_at', $hoje)->where('tipo', 'credito')->sum('valor'),
                    'valor_debito' => FidelidadeCashbackTransacao::whereDate('created_at', $hoje)->where('tipo', 'debito')->sum('valor'),
                ],
                'mes_atual' => [
                    'transacoes' => FidelidadeCashbackTransacao::whereBetween('created_at', [$inicioMes, $hoje->copy()->endOfDay()])->count(),
                    'valor_credito' => FidelidadeCashbackTransacao::whereBetween('created_at', [$inicioMes, $hoje->copy()->endOfDay()])->where('tipo', 'credito')->sum('valor'),
                    'valor_debito' => FidelidadeCashbackTransacao::whereBetween('created_at', [$inicioMes, $hoje->copy()->endOfDay()])->where('tipo', 'debito')->sum('valor'),
                ],
                'total' => [
                    'transacoes' => FidelidadeCashbackTransacao::count(),
                    'valor_credito' => FidelidadeCashbackTransacao::where('tipo', 'credito')->sum('valor'),
                    'valor_debito' => FidelidadeCashbackTransacao::where('tipo', 'debito')->sum('valor'),
                ],
            ];
        });
    }
}
