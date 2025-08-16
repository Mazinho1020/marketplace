<?php

namespace App\Http\Resources\Financeiro;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para Lançamentos Financeiros
 * 
 * Formata os dados dos lançamentos para retorno da API
 */
class LancamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            
            // Relacionamentos
            'empresa_id' => $this->empresa_id,
            'usuario_id' => $this->usuario_id,
            'mesa_id' => $this->mesa_id,
            'caixa_id' => $this->caixa_id,
            'pessoa_id' => $this->pessoa_id,
            'pessoa_tipo' => $this->pessoa_tipo,
            'funcionario_id' => $this->funcionario_id,
            
            // Classificação
            'tipo_lancamento_id' => $this->tipo_lancamento_id,
            'conta_gerencial_id' => $this->conta_gerencial_id,
            'natureza_financeira' => $this->natureza_financeira,
            'natureza_formatada' => $this->natureza_formatada,
            'categoria' => $this->categoria,
            'origem' => $this->origem,
            
            // Valores financeiros
            'valor' => $this->valor,
            'valor_desconto' => $this->valor_desconto,
            'valor_acrescimo' => $this->valor_acrescimo,
            'valor_juros' => $this->valor_juros,
            'valor_multa' => $this->valor_multa,
            'valor_liquido' => $this->valor_liquido,
            'valor_pago' => $this->valor_pago,
            'valor_saldo' => $this->valor_saldo,
            
            // Valores formatados
            'valor_formatado' => $this->valor_formatado,
            'valor_liquido_formatado' => $this->valor_liquido_formatado,
            'valor_pago_formatado' => $this->valor_pago_formatado,
            'valor_saldo_formatado' => $this->valor_saldo_formatado,
            
            // Situação
            'situacao_financeira' => $this->situacao_financeira,
            'situacao_formatada' => $this->situacao_formatada,
            
            // Datas
            'data_lancamento' => $this->data_lancamento?->format('Y-m-d H:i:s'),
            'data_emissao' => $this->data_emissao?->format('Y-m-d'),
            'data_competencia' => $this->data_competencia?->format('Y-m-d'),
            'data_vencimento' => $this->data_vencimento?->format('Y-m-d'),
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d H:i:s'),
            'data_ultimo_pagamento' => $this->data_ultimo_pagamento?->format('Y-m-d H:i:s'),
            
            // Informações descritivas
            'descricao' => $this->descricao,
            'numero_documento' => $this->numero_documento,
            'observacoes' => $this->observacoes,
            'observacoes_pagamento' => $this->observacoes_pagamento,
            
            // Parcelamento
            'e_parcelado' => $this->e_parcelado,
            'parcela_atual' => $this->parcela_atual,
            'total_parcelas' => $this->total_parcelas,
            'grupo_parcelas' => $this->grupo_parcelas,
            'intervalo_parcelas' => $this->intervalo_parcelas,
            'parcelas_info' => $this->when($this->e_parcelado, function() {
                return [
                    'atual' => $this->parcela_atual,
                    'total' => $this->total_parcelas,
                    'descricao' => $this->parcela_atual . '/' . $this->total_parcelas,
                ];
            }),
            
            // Recorrência
            'e_recorrente' => $this->e_recorrente,
            'frequencia_recorrencia' => $this->frequencia_recorrencia,
            'proxima_recorrencia' => $this->proxima_recorrencia?->format('Y-m-d'),
            'recorrencia_ativa' => $this->recorrencia_ativa,
            
            // Forma de pagamento
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'bandeira_id' => $this->bandeira_id,
            'conta_bancaria_id' => $this->conta_bancaria_id,
            
            // Cobrança automática
            'cobranca_automatica' => $this->cobranca_automatica,
            'data_proxima_cobranca' => $this->data_proxima_cobranca?->format('Y-m-d'),
            'tentativas_cobranca' => $this->tentativas_cobranca,
            'max_tentativas_cobranca' => $this->max_tentativas_cobranca,
            
            // Boleto
            'boleto_gerado' => $this->boleto_gerado,
            'boleto_nosso_numero' => $this->boleto_nosso_numero,
            'boleto_data_geracao' => $this->boleto_data_geracao?->format('Y-m-d H:i:s'),
            'boleto_url' => $this->boleto_url,
            'boleto_linha_digitavel' => $this->boleto_linha_digitavel,
            'boleto_info' => $this->when($this->boleto_gerado, function() {
                return [
                    'nosso_numero' => $this->boleto_nosso_numero,
                    'data_geracao' => $this->boleto_data_geracao?->format('d/m/Y H:i'),
                    'url' => $this->boleto_url,
                    'linha_digitavel' => $this->boleto_linha_digitavel,
                ];
            }),
            
            // Aprovação
            'status_aprovacao' => $this->status_aprovacao,
            'aprovado_por' => $this->aprovado_por,
            'data_aprovacao' => $this->data_aprovacao?->format('Y-m-d H:i:s'),
            'motivo_rejeicao' => $this->motivo_rejeicao,
            'requer_aprovacao' => $this->requer_aprovacao(),
            'is_aprovado' => $this->is_aprovado(),
            
            // Configurações
            'juros_multa_config' => $this->juros_multa_config,
            'config_desconto' => $this->config_desconto,
            'config_alertas' => $this->config_alertas,
            'anexos' => $this->anexos,
            'metadados' => $this->metadados,
            
            // Sincronização
            'sync_status' => $this->sync_status,
            'sync_tentativas' => $this->sync_tentativas,
            'sync_ultimo_erro' => $this->sync_ultimo_erro,
            'sync_hash' => $this->sync_hash,
            
            // Auditoria
            'usuario_criacao' => $this->usuario_criacao,
            'usuario_ultima_alteracao' => $this->usuario_ultima_alteracao,
            'data_exclusao' => $this->data_exclusao?->format('Y-m-d H:i:s'),
            'usuario_exclusao' => $this->usuario_exclusao,
            'motivo_exclusao' => $this->motivo_exclusao,
            
            // Timestamps
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Status helpers
            'is_pago' => $this->is_pago(),
            'is_pendente' => $this->is_pendente(),
            'is_vencido' => $this->is_vencido(),
            'is_parcialmente_pago' => $this->is_parcialmente_pago(),
            'is_conta_receber' => $this->is_conta_receber(),
            'is_conta_pagar' => $this->is_conta_pagar(),
            'is_parcelado' => $this->is_parcelado(),
            'is_recorrente' => $this->is_recorrente(),
            'tem_boleto_gerado' => $this->tem_boleto_gerado(),
            
            // Relacionamentos
            'itens' => LancamentoItemResource::collection($this->whenLoaded('itens')),
            'movimentacoes' => LancamentoMovimentacaoResource::collection($this->whenLoaded('movimentacoes')),
            'pagamentos' => LancamentoMovimentacaoResource::collection($this->whenLoaded('pagamentos')),
            'recebimentos' => LancamentoMovimentacaoResource::collection($this->whenLoaded('recebimentos')),
            'estornos' => LancamentoMovimentacaoResource::collection($this->whenLoaded('estornos')),
            
            // Estatísticas dos relacionamentos
            'total_itens' => $this->when($this->relationLoaded('itens'), function() {
                return $this->itens->count();
            }),
            'total_movimentacoes' => $this->when($this->relationLoaded('movimentacoes'), function() {
                return $this->movimentacoes->count();
            }),
            'valor_total_pagamentos' => $this->when($this->relationLoaded('movimentacoes'), function() {
                return $this->movimentacoes
                           ->whereIn('tipo', ['pagamento', 'recebimento'])
                           ->sum('valor');
            }),
            'valor_total_estornos' => $this->when($this->relationLoaded('movimentacoes'), function() {
                return $this->movimentacoes
                           ->where('tipo', 'estorno')
                           ->sum('valor');
            }),
            
            // Informações de vencimento
            'dias_vencimento' => $this->when($this->data_vencimento, function() {
                $hoje = now();
                $vencimento = $this->data_vencimento;
                
                if ($vencimento->isPast()) {
                    return [
                        'status' => 'vencido',
                        'dias' => $hoje->diffInDays($vencimento),
                        'texto' => 'Vencido há ' . $hoje->diffInDays($vencimento) . ' dias'
                    ];
                } elseif ($vencimento->isToday()) {
                    return [
                        'status' => 'hoje',
                        'dias' => 0,
                        'texto' => 'Vence hoje'
                    ];
                } else {
                    return [
                        'status' => 'futuro',
                        'dias' => $hoje->diffInDays($vencimento),
                        'texto' => 'Vence em ' . $hoje->diffInDays($vencimento) . ' dias'
                    ];
                }
            }),
            
            // Percentual pago
            'percentual_pago' => $this->when($this->valor_liquido > 0, function() {
                return round(($this->valor_pago / $this->valor_liquido) * 100, 2);
            }),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'enums' => [
                    'natureza_financeira' => [
                        'entrada' => 'Conta a Receber',
                        'saida' => 'Conta a Pagar',
                    ],
                    'categoria' => [
                        'venda' => 'Venda',
                        'compra' => 'Compra',
                        'servico' => 'Serviço',
                        'taxa' => 'Taxa',
                        'imposto' => 'Imposto',
                        'transferencia' => 'Transferência',
                        'ajuste' => 'Ajuste',
                        'outros' => 'Outros',
                    ],
                    'origem' => [
                        'pdv' => 'PDV',
                        'manual' => 'Manual',
                        'delivery' => 'Delivery',
                        'api' => 'API',
                        'importacao' => 'Importação',
                        'recorrencia' => 'Recorrência',
                    ],
                    'situacao_financeira' => [
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'parcialmente_pago' => 'Parcialmente Pago',
                        'vencido' => 'Vencido',
                        'cancelado' => 'Cancelado',
                        'em_negociacao' => 'Em Negociação',
                        'estornado' => 'Estornado',
                    ],
                    'status_aprovacao' => [
                        'pendente_aprovacao' => 'Pendente de Aprovação',
                        'aprovado' => 'Aprovado',
                        'rejeitado' => 'Rejeitado',
                        'nao_requer' => 'Não Requer Aprovação',
                    ],
                    'frequencia_recorrencia' => [
                        'diaria' => 'Diária',
                        'semanal' => 'Semanal',
                        'quinzenal' => 'Quinzenal',
                        'mensal' => 'Mensal',
                        'bimestral' => 'Bimestral',
                        'trimestral' => 'Trimestral',
                        'semestral' => 'Semestral',
                        'anual' => 'Anual',
                    ],
                ]
            ]
        ];
    }
}
