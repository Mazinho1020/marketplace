<?php

namespace App\Http\Resources\Financeiro;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para Movimentações de Lançamentos
 */
class LancamentoMovimentacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lancamento_id' => $this->lancamento_id,
            
            // Tipo e valor
            'tipo' => $this->tipo,
            'tipo_formatado' => $this->tipo_formatado,
            'valor' => $this->valor,
            'valor_formatado' => $this->valor_formatado,
            
            // Data
            'data_movimentacao' => $this->data_movimentacao?->format('Y-m-d H:i:s'),
            'data_movimentacao_formatada' => $this->data_movimentacao_formatada,
            
            // Forma de pagamento
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'conta_bancaria_id' => $this->conta_bancaria_id,
            'numero_documento' => $this->numero_documento,
            
            // Informações complementares
            'observacoes' => $this->observacoes,
            'metadados' => $this->metadados,
            
            // Identificação
            'usuario_id' => $this->usuario_id,
            'empresa_id' => $this->empresa_id,
            
            // Helpers
            'is_pagamento' => $this->is_pagamento(),
            'is_recebimento' => $this->is_recebimento(),
            'is_estorno' => $this->is_estorno(),
            'pode_ser_estornado' => $this->pode_ser_estornado(),
            
            // Timestamp
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
