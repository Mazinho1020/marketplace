<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCupomUso;
use App\Models\Fidelidade\FidelidadeCredito;
use App\Models\Fidelidade\FidelidadeConquista;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FidelidadeControllerNew extends Controller
{
    public function index()
    {
        // Estatísticas reais do dashboard
        $estatisticas = [
            'carteiras_ativas' => FidelidadeCarteira::where('status', 'ativa')->count(),
            'total_carteiras' => FidelidadeCarteira::count(),
            'total_cashback' => FidelidadeCarteira::sum('saldo_cashback'),
            'total_creditos' => FidelidadeCarteira::sum('saldo_creditos'),
            'transacoes_hoje' => FidelidadeCashbackTransacao::whereDate('created_at', Carbon::today())->count(),
            'transacoes_mes' => FidelidadeCashbackTransacao::whereMonth('created_at', Carbon::now()->month)->count(),
            'cupons_ativos' => FidelidadeCupom::where('status', 'ativo')->count(),
            'cupons_utilizados_mes' => FidelidadeCupomUso::whereMonth('created_at', Carbon::now()->month)->count(),
            'clientes_nivel_bronze' => FidelidadeCarteira::where('nivel_atual', 'bronze')->count(),
            'clientes_nivel_prata' => FidelidadeCarteira::where('nivel_atual', 'prata')->count(),
            'clientes_nivel_ouro' => FidelidadeCarteira::where('nivel_atual', 'ouro')->count(),
            'valor_medio_transacao' => FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')->avg('valor') ?: 0,
            'cashback_distribuido_mes' => FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('valor'),
            'cashback_resgatado_mes' => FidelidadeCashbackTransacao::where('tipo_transacao', 'resgate')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('valor')
        ];

        // Transações recentes
        $transacoesRecentes = FidelidadeCashbackTransacao::with(['carteira.cliente'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Gráfico de crescimento (últimos 7 dias)
        $crescimentoSemanal = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = Carbon::now()->subDays($i);
            $crescimentoSemanal[] = [
                'data' => $data->format('d/m'),
                'transacoes' => FidelidadeCashbackTransacao::whereDate('created_at', $data)->count(),
                'valor' => FidelidadeCashbackTransacao::whereDate('created_at', $data)->sum('valor')
            ];
        }

        // Top 5 clientes mais ativos
        $topClientes = FidelidadeCarteira::with('cliente')
            ->orderBy('saldo_total_disponivel', 'desc')
            ->limit(5)
            ->get();

        return view('fidelidade.dashboard', compact(
            'estatisticas',
            'transacoesRecentes',
            'crescimentoSemanal',
            'topClientes'
        ));
    }

    public function configuracoes()
    {
        return view('fidelidade.configuracoes');
    }

    public function salvarConfiguracoes(Request $request)
    {
        $request->validate([
            'cashback_padrao' => 'required|numeric|min:0|max:100',
            'pontos_por_real' => 'required|numeric|min:0',
            'valor_minimo_resgate' => 'required|numeric|min:0',
            'prazo_validade_creditos' => 'required|integer|min:1',
            'nivel_bronze_min' => 'required|integer|min:0',
            'nivel_prata_min' => 'required|integer|min:0',
            'nivel_ouro_min' => 'required|integer|min:0'
        ]);

        // Aqui você salvaria as configurações no banco ou arquivo de config
        // Por enquanto, só retornamos sucesso

        return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
    }

    /**
     * Métodos específicos para soft deletes
     */

    /**
     * Listar registros deletados de todos os tipos
     */
    public function registrosDeletados(Request $request)
    {
        $tipo = $request->get('tipo', 'all');

        $dados = [];

        if ($tipo === 'all' || $tipo === 'carteiras') {
            $dados['carteiras'] = FidelidadeCarteira::onlyTrashed()->get();
        }

        if ($tipo === 'all' || $tipo === 'cupons') {
            $dados['cupons'] = FidelidadeCupom::onlyTrashed()->get();
        }

        if ($tipo === 'all' || $tipo === 'creditos') {
            $dados['creditos'] = FidelidadeCredito::onlyTrashed()->get();
        }

        if ($tipo === 'all' || $tipo === 'conquistas') {
            $dados['conquistas'] = FidelidadeConquista::onlyTrashed()->get();
        }

        if ($tipo === 'all' || $tipo === 'transacoes') {
            $dados['transacoes'] = FidelidadeCashbackTransacao::onlyTrashed()->get();
        }

        return response()->json($dados);
    }

    /**
     * Restaurar registro por tipo e ID
     */
    public function restaurarRegistro(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');

        $model = null;
        $nome = '';

        switch ($tipo) {
            case 'carteira':
                $model = FidelidadeCarteira::withTrashed()->find($id);
                $nome = 'Carteira';
                break;
            case 'cupom':
                $model = FidelidadeCupom::withTrashed()->find($id);
                $nome = 'Cupom';
                break;
            case 'credito':
                $model = FidelidadeCredito::withTrashed()->find($id);
                $nome = 'Crédito';
                break;
            case 'conquista':
                $model = FidelidadeConquista::withTrashed()->find($id);
                $nome = 'Conquista';
                break;
            case 'transacao':
                $model = FidelidadeCashbackTransacao::withTrashed()->find($id);
                $nome = 'Transação';
                break;
        }

        if (!$model) {
            return response()->json(['error' => "{$nome} não encontrada"], 404);
        }

        $model->restore();

        return response()->json(['success' => "{$nome} restaurada com sucesso"]);
    }

    /**
     * Deletar permanentemente por tipo e ID
     */
    public function deletarPermanente(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');

        $model = null;
        $nome = '';

        switch ($tipo) {
            case 'carteira':
                $model = FidelidadeCarteira::withTrashed()->find($id);
                $nome = 'Carteira';
                break;
            case 'cupom':
                $model = FidelidadeCupom::withTrashed()->find($id);
                $nome = 'Cupom';
                break;
            case 'credito':
                $model = FidelidadeCredito::withTrashed()->find($id);
                $nome = 'Crédito';
                break;
            case 'conquista':
                $model = FidelidadeConquista::withTrashed()->find($id);
                $nome = 'Conquista';
                break;
            case 'transacao':
                $model = FidelidadeCashbackTransacao::withTrashed()->find($id);
                $nome = 'Transação';
                break;
        }

        if (!$model) {
            return response()->json(['error' => "{$nome} não encontrada"], 404);
        }

        $model->forceDelete();

        return response()->json(['success' => "{$nome} deletada permanentemente"]);
    }
}
