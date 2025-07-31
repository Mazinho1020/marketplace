<?php

namespace App\Services\Fidelidade;

use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCredito;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeConquista;
use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Facades\DB;

class FidelidadeService
{
    /**
     * Obter ou criar carteira do cliente
     */
    public function obterCarteira($clienteId, $businessId)
    {
        return FidelidadeCarteira::firstOrCreate(
            [
                'cliente_id' => $clienteId,
                'business_id' => $businessId
            ],
            [
                'saldo_creditos' => 0,
                'total_cashback' => 0,
                'nivel' => 'Bronze',
                'status' => 'ativo'
            ]
        );
    }

    /**
     * Obter estatísticas gerais do programa de fidelidade
     */
    public function obterEstatisticas($businessId = null)
    {
        $query = FidelidadeCarteira::query();

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $totalCarteiras = $query->count();
        $totalCreditos = $query->sum('saldo_creditos');
        $totalCashback = $query->sum('total_cashback');

        // Contar cupons ativos
        $cuponsAtivos = FidelidadeCupom::where('status', 'ativo')
            ->where('data_fim', '>=', now())
            ->count();

        return [
            'total_carteiras' => $totalCarteiras,
            'total_creditos' => $totalCreditos,
            'total_cashback' => $totalCashback,
            'cupons_ativos' => $cuponsAtivos
        ];
    }

    /**
     * Obter transações recentes de cashback
     */
    public function obterTransacoesRecentes($businessId = null, $limit = 10)
    {
        $query = FidelidadeCashbackTransacao::with(['cliente', 'business'])
            ->orderBy('created_at', 'desc');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Obter carteiras por nível
     */
    public function obterCarteirasPorNivel($businessId = null)
    {
        $query = FidelidadeCarteira::select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->get()->pluck('total', 'nivel')->toArray();
    }

    /**
     * Calcular cashback para uma transação
     */
    public function calcularCashback($valor, $businessId, $clienteId)
    {
        // Lógica básica - pode ser expandida
        $percentual = 0.02; // 2% padrão

        return $valor * $percentual;
    }

    /**
     * Processar cashback
     */
    public function processarCashback($clienteId, $businessId, $valor, $transacaoId = null)
    {
        $carteira = $this->obterCarteira($clienteId, $businessId);
        $valorCashback = $this->calcularCashback($valor, $businessId, $clienteId);

        // Criar transação de cashback
        $transacao = FidelidadeCashbackTransacao::create([
            'cliente_id' => $clienteId,
            'business_id' => $businessId,
            'carteira_id' => $carteira->id,
            'transacao_id' => $transacaoId,
            'valor_compra' => $valor,
            'valor_cashback' => $valorCashback,
            'percentual' => ($valorCashback / $valor) * 100,
            'status' => 'processado'
        ]);

        // Atualizar carteira
        $carteira->increment('total_cashback', $valorCashback);
        $carteira->increment('saldo_creditos', $valorCashback);

        return $transacao;
    }
}
