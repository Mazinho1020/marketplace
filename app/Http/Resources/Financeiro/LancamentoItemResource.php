<?php

namespace App\Http\Resources\Financeiro;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para Itens de Lançamentos
 */
class LancamentoItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lancamento_id' => $this->lancamento_id,
            
            // Produto
            'produto_id' => $this->produto_id,
            'produto_variacao_id' => $this->produto_variacao_id,
            'codigo_produto' => $this->codigo_produto,
            'nome_produto' => $this->nome_produto,
            
            // Quantidades e valores
            'quantidade' => $this->quantidade,
            'valor_unitario' => $this->valor_unitario,
            'valor_desconto_item' => $this->valor_desconto_item,
            'valor_total' => $this->valor_total,
            
            // Formatados
            'quantidade_formatada' => $this->quantidade_formatada,
            'valor_unitario_formatado' => $this->valor_unitario_formatado,
            'valor_total_formatado' => $this->valor_total_formatado,
            
            // Informações complementares
            'observacoes' => $this->observacoes,
            'metadados' => $this->metadados,
            'empresa_id' => $this->empresa_id,
            
            // Helpers
            'tem_desconto' => $this->tem_desconto(),
            'percentual_desconto' => $this->when($this->tem_desconto(), function() {
                return $this->getPercentualDesconto();
            }),
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
