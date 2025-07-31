<?php

namespace App\Services\Fidelidade;

use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCarteira;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransacaoService
{
    /**
     * Processar transação de cashback
     */
    public function processarTransacao(array $dados): FidelidadeCashbackTransacao
    {
        return DB::transaction(function () use ($dados) {
            // Buscar ou criar carteira
            $carteira = $this->obterOuCriarCarteira($dados['cliente_id'], $dados['empresa_id']);

            // Criar transação
            $transacao = FidelidadeCashbackTransacao::create([
                'cliente_id' => $dados['cliente_id'],
                'empresa_id' => $dados['empresa_id'],
                'tipo' => $dados['tipo'],
                'valor' => $dados['valor'],
                'descricao' => $dados['descricao'],
                'pedido_id' => $dados['pedido_id'] ?? null,
                'status' => 'processado',
            ]);

            // Atualizar saldo da carteira
            $this->atualizarSaldoCarteira($carteira, $dados['tipo'], $dados['valor']);

            Log::info('Transação processada', [
                'transacao_id' => $transacao->id,
                'cliente_id' => $dados['cliente_id'],
                'valor' => $dados['valor'],
                'tipo' => $dados['tipo']
            ]);

            return $transacao;
        });
    }

    /**
     * Cancelar transação
     */
    public function cancelarTransacao(FidelidadeCashbackTransacao $transacao): bool
    {
        if ($transacao->status === 'cancelado') {
            return false;
        }

        return DB::transaction(function () use ($transacao) {
            $carteira = FidelidadeCarteira::where('cliente_id', $transacao->cliente_id)
                ->where('empresa_id', $transacao->empresa_id)
                ->first();

            if ($carteira) {
                // Reverter o valor na carteira
                $tipoReversao = $transacao->tipo === 'credito' ? 'debito' : 'credito';
                $this->atualizarSaldoCarteira($carteira, $tipoReversao, $transacao->valor);
            }

            $transacao->update(['status' => 'cancelado']);

            Log::info('Transação cancelada', ['transacao_id' => $transacao->id]);

            return true;
        });
    }

    /**
     * Obter ou criar carteira do cliente
     */
    private function obterOuCriarCarteira(int $clienteId, int $empresaId): FidelidadeCarteira
    {
        return FidelidadeCarteira::firstOrCreate([
            'cliente_id' => $clienteId,
            'empresa_id' => $empresaId,
        ], [
            'saldo_cashback' => 0,
            'saldo_creditos' => 0,
            'saldo_total_disponivel' => 0,
            'nivel_atual' => 'bronze',
            'status' => 'ativa',
            'xp_total' => 0,
        ]);
    }

    /**
     * Atualizar saldo da carteira
     */
    private function atualizarSaldoCarteira(FidelidadeCarteira $carteira, string $tipo, float $valor): void
    {
        if ($tipo === 'credito') {
            $carteira->increment('saldo_cashback', $valor);
        } else {
            $carteira->decrement('saldo_cashback', $valor);
        }

        $carteira->update([
            'saldo_total_disponivel' => $carteira->saldo_cashback + $carteira->saldo_creditos
        ]);
    }
}
