<?php

namespace App\Services\Fidelidade;

use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCredito;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeConquista;
use Illuminate\Support\Facades\DB;

class FidelidadeService
{
    /**
     * Obter ou criar carteira do cliente
     */
    public function obterCarteira($clienteId)
    {
        return FidelidadeCarteira::firstOrCreate(
            [
                'cliente_id' => $clienteId
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
    public function obterEstatisticas()
    {
        $query = FidelidadeCarteira::query();

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
    public function obterTransacoesRecentes($limit = 10)
    {
        return FidelidadeCashbackTransacao::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obter carteiras por nível
     */
    public function obterCarteirasPorNivel()
    {
        return FidelidadeCarteira::select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel')
            ->get();
    }

    /**
     * Calcular cashback para uma compra
     */
    public function calcularCashback($valor, $clienteId)
    {
        // Implementar lógica de cálculo de cashback
        // Por exemplo: 2% sobre o valor da compra
        return $valor * 0.02;
    }

    /**
     * Processar cashback de uma transação
     */
    public function processarCashback($clienteId, $valor, $transacaoId = null)
    {
        $carteira = $this->obterCarteira($clienteId);
        $valorCashback = $this->calcularCashback($valor, $clienteId);

        // Criar transação de cashback
        $transacao = FidelidadeCashbackTransacao::create([
            'cliente_id' => $clienteId,
            'valor_cashback' => $valorCashback,
            'valor_pedido_original' => $valor,
            'tipo' => 'credito',
            'status' => 'disponivel',
            'data_transacao' => now(),
            'transacao_id' => $transacaoId
        ]);

        // Atualizar saldo da carteira
        $carteira->increment('total_cashback', $valorCashback);
        $carteira->increment('saldo_creditos', $valorCashback);

        return $transacao;
    }

    /**
     * Resgatar cashback
     */
    public function resgatarCashback($clienteId, $valor)
    {
        $carteira = $this->obterCarteira($clienteId);

        if ($carteira->saldo_creditos < $valor) {
            throw new \Exception('Saldo insuficiente para resgate');
        }

        // Criar transação de débito
        $transacao = FidelidadeCashbackTransacao::create([
            'cliente_id' => $clienteId,
            'valor_cashback' => -$valor,
            'tipo' => 'debito',
            'status' => 'usado',
            'data_transacao' => now(),
            'observacoes' => 'Resgate de cashback'
        ]);

        // Atualizar saldo da carteira
        $carteira->decrement('saldo_creditos', $valor);

        return $transacao;
    }
}
