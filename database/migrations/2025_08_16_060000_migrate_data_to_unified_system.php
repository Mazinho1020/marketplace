<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financeiro\Lancamento;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar lançamentos existentes
        LancamentoFinanceiro::chunk(100, function ($lancamentos) {
            foreach ($lancamentos as $antigo) {
                Lancamento::create([
                    'empresa_id' => $antigo->empresa_id,
                    'usuario_id' => $antigo->usuario_id,
                    'pessoa_id' => $antigo->pessoa_id,
                    'pessoa_tipo' => $antigo->pessoa_tipo,
                    'conta_gerencial_id' => $antigo->conta_gerencial_id,
                    'natureza_financeira' => $antigo->natureza_financeira === 'pagar' ? 'saida' : 'entrada',
                    'categoria_operacao' => $this->mapearCategoria($antigo),
                    'origem' => 'migração',
                    'valor_bruto' => $antigo->valor_original ?? $antigo->valor_final,
                    'valor_liquido' => $antigo->valor_final,
                    'valor_pago' => $antigo->valor_pago ?? 0,
                    'situacao_financeira' => $this->mapearSituacao($antigo->situacao_financeira),
                    'data_emissao' => $antigo->data_emissao,
                    'data_competencia' => $antigo->data_competencia,
                    'data_vencimento' => $antigo->data_vencimento,
                    'data_pagamento' => $antigo->data_pagamento,
                    'descricao' => $antigo->descricao,
                    'numero_documento' => $antigo->numero_documento,
                    'observacoes' => $antigo->observacoes,
                    'created_at' => $antigo->created_at,
                    'updated_at' => $antigo->updated_at,
                ]);
            }
        });
    }

    private function mapearCategoria($lancamento): string
    {
        // Mapear categorias do sistema antigo
        return match($lancamento->natureza_financeira) {
            'pagar' => 'compra',
            'receber' => 'venda',
            default => 'outros'
        };
    }

    private function mapearSituacao($situacao): string
    {
        // Mapear situações do sistema antigo
        return match($situacao) {
            'pendente' => 'pendente',
            'pago' => 'pago',
            'vencido' => 'vencido',
            'cancelado' => 'cancelado',
            default => 'pendente'
        };
    }
};