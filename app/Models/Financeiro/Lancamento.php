<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Model de Lançamentos Financeiros
 * 
 * Modelo unificado para gerenciar todas as movimentações financeiras:
 * - Contas a pagar e receber
 * - Vendas e compras
 * - Parcelamentos e recorrências
 * - Workflow de aprovação
 * - Auditoria completa
 * 
 * @property int $id
 * @property string $uuid
 * @property int $empresa_id
 * @property int $usuario_id
 * @property string $natureza_financeira
 * @property string $categoria
 * @property string $origem
 * @property float $valor_bruto
 * @property float $valor_liquido
 * @property float $valor_pago
 * @property float $valor_saldo
 * @property string $situacao_financeira
 * @property string $descricao
 * @property Carbon $data_vencimento
 * @property Carbon $data_emissao
 * @property Carbon $data_competencia
 */
class Lancamento extends Model
{
    use HasFactory;

    protected $table = 'lancamentos';

    protected $fillable = [
        'uuid',
        'empresa_id',
        'usuario_id',
        'mesa_id',
        'caixa_id',
        'pessoa_id',
        'pessoa_tipo',
        'funcionario_id',
        'tipo_lancamento_id',
        'conta_gerencial_id',
        'natureza_financeira',
        'categoria',
        'origem',
        'valor_bruto',
        'valor_desconto',
        'valor_acrescimo',
        'valor_juros',
        'valor_multa',
        'valor_liquido',
        'valor_pago',
        'valor_saldo',
        'situacao_financeira',
        'data_lancamento',
        'data_emissao',
        'data_competencia',
        'data_vencimento',
        'data_pagamento',
        'data_ultimo_pagamento',
        'descricao',
        'numero_documento',
        'observacoes',
        'observacoes_pagamento',
        'e_parcelado',
        'parcela_atual',
        'total_parcelas',
        'grupo_parcelas',
        'intervalo_dias',
        'e_recorrente',
        'frequencia_recorrencia',
        'proxima_recorrencia',
        'recorrencia_ativa',
        'forma_pagamento_id',
        'bandeira_id',
        'conta_bancaria_id',
        'cobranca_automatica',
        'data_proxima_cobranca',
        'tentativas_cobranca',
        'max_tentativas_cobranca',
        'boleto_gerado',
        'boleto_nosso_numero',
        'boleto_data_geracao',
        'boleto_url',
        'boleto_linha_digitavel',
        'status_aprovacao',
        'aprovado_por',
        'data_aprovacao',
        'motivo_rejeicao',
        'config_juros_multa',
        'config_desconto',
        'config_alertas',
        'anexos',
        'metadados',
        'sync_status',
        'sync_tentativas',
        'sync_ultimo_erro',
        'sync_hash',
        'usuario_criacao',
        'usuario_ultima_alteracao',
        'data_exclusao',
        'usuario_exclusao',
        'motivo_exclusao',
    ];

    protected $casts = [
        'data_lancamento' => 'datetime',
        'data_emissao' => 'date',
        'data_competencia' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'data_ultimo_pagamento' => 'datetime',
        'proxima_recorrencia' => 'date',
        'data_proxima_cobranca' => 'date',
        'boleto_data_geracao' => 'datetime',
        'data_aprovacao' => 'datetime',
        'data_exclusao' => 'datetime',
        'valor_bruto' => 'decimal:4',
        'valor_desconto' => 'decimal:4',
        'valor_acrescimo' => 'decimal:4',
        'valor_juros' => 'decimal:4',
        'valor_multa' => 'decimal:4',
        'valor_liquido' => 'decimal:4',
        'valor_pago' => 'decimal:4',
        'valor_saldo' => 'decimal:4',
        'e_parcelado' => 'boolean',
        'e_recorrente' => 'boolean',
        'recorrencia_ativa' => 'boolean',
        'cobranca_automatica' => 'boolean',
        'boleto_gerado' => 'boolean',
        'config_juros_multa' => 'json',
        'config_desconto' => 'json',
        'config_alertas' => 'json',
        'anexos' => 'json',
        'metadados' => 'json',
    ];

    /**
     * Enums para validação
     */
    const NATUREZA_ENTRADA = 'entrada';
    const NATUREZA_SAIDA = 'saida';

    const CATEGORIA_VENDA = 'venda';
    const CATEGORIA_COMPRA = 'compra';
    const CATEGORIA_SERVICO = 'servico';
    const CATEGORIA_TAXA = 'taxa';
    const CATEGORIA_IMPOSTO = 'imposto';
    const CATEGORIA_TRANSFERENCIA = 'transferencia';
    const CATEGORIA_AJUSTE = 'ajuste';
    const CATEGORIA_OUTROS = 'outros';

    const ORIGEM_PDV = 'pdv';
    const ORIGEM_MANUAL = 'manual';
    const ORIGEM_DELIVERY = 'delivery';
    const ORIGEM_API = 'api';
    const ORIGEM_IMPORTACAO = 'importacao';
    const ORIGEM_RECORRENCIA = 'recorrencia';

    const SITUACAO_PENDENTE = 'pendente';
    const SITUACAO_PAGO = 'pago';
    const SITUACAO_PARCIALMENTE_PAGO = 'parcialmente_pago';
    const SITUACAO_VENCIDO = 'vencido';
    const SITUACAO_CANCELADO = 'cancelado';
    const SITUACAO_EM_NEGOCIACAO = 'em_negociacao';
    const SITUACAO_ESTORNADO = 'estornado';

    const STATUS_APROVACAO_PENDENTE = 'pendente_aprovacao';
    const STATUS_APROVACAO_APROVADO = 'aprovado';
    const STATUS_APROVACAO_REJEITADO = 'rejeitado';
    const STATUS_APROVACAO_NAO_REQUER = 'nao_requer';

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Gerar UUID automaticamente
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            
            // Calcular valor líquido
            $model->calcularValorLiquido();
            
            // Definir usuário de criação
            if (empty($model->usuario_criacao)) {
                $model->usuario_criacao = $model->usuario_id;
            }
        });

        // Recalcular valores ao atualizar
        static::updating(function ($model) {
            $model->calcularValorLiquido();
            $model->calcularSituacaoFinanceira();
        });
    }

    /**
     * Relacionamentos
     */
    public function itens(): HasMany
    {
        return $this->hasMany(LancamentoItem::class, 'lancamento_id');
    }

    public function movimentacoes(): HasMany
    {
        return $this->hasMany(LancamentoMovimentacao::class, 'lancamento_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(LancamentoMovimentacao::class, 'lancamento_id')
                    ->where('tipo', 'pagamento');
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(LancamentoMovimentacao::class, 'lancamento_id')
                    ->where('tipo', 'recebimento');
    }

    public function estornos(): HasMany
    {
        return $this->hasMany(LancamentoMovimentacao::class, 'lancamento_id')
                    ->where('tipo', 'estorno');
    }

    /**
     * Scopes para facilitar consultas
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorNatureza($query, $natureza)
    {
        return $query->where('natureza_financeira', $natureza);
    }

    public function scopeContasReceber($query)
    {
        return $query->where('natureza_financeira', self::NATUREZA_ENTRADA);
    }

    public function scopeContasPagar($query)
    {
        return $query->where('natureza_financeira', self::NATUREZA_SAIDA);
    }

    public function scopePendentes($query)
    {
        return $query->where('situacao_financeira', self::SITUACAO_PENDENTE);
    }

    public function scopePagos($query)
    {
        return $query->where('situacao_financeira', self::SITUACAO_PAGO);
    }

    public function scopeVencidos($query)
    {
        return $query->where('situacao_financeira', self::SITUACAO_VENCIDO);
    }

    public function scopeVencimentoEntre($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_vencimento', [$dataInicio, $dataFim]);
    }

    public function scopeCompetenciaEntre($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_competencia', [$dataInicio, $dataFim]);
    }

    public function scopeParcelados($query)
    {
        return $query->where('e_parcelado', true);
    }

    public function scopeRecorrentes($query)
    {
        return $query->where('e_recorrente', true)
                    ->where('recorrencia_ativa', true);
    }

    public function scopeAtivos($query)
    {
        return $query->whereNull('data_exclusao');
    }

    /**
     * Métodos de cálculo
     */
    public function calcularValorLiquido()
    {
        $this->valor_liquido = $this->valor_bruto 
                             - $this->valor_desconto 
                             + $this->valor_acrescimo 
                             + $this->valor_juros 
                             + $this->valor_multa;
        
        $this->calcularSaldo();
    }

    public function calcularSaldo()
    {
        $this->valor_saldo = $this->valor_liquido - $this->valor_pago;
    }

    public function calcularSituacaoFinanceira()
    {
        // Não alterar se foi cancelado ou estornado manualmente
        if (in_array($this->situacao_financeira, [self::SITUACAO_CANCELADO, self::SITUACAO_ESTORNADO])) {
            return;
        }

        if ($this->valor_pago == 0) {
            if ($this->data_vencimento < now()->format('Y-m-d') && $this->situacao_financeira == self::SITUACAO_PENDENTE) {
                $this->situacao_financeira = self::SITUACAO_VENCIDO;
            } else {
                $this->situacao_financeira = self::SITUACAO_PENDENTE;
            }
        } elseif ($this->valor_pago >= $this->valor_liquido) {
            $this->situacao_financeira = self::SITUACAO_PAGO;
            $this->data_pagamento = $this->data_ultimo_pagamento ?? now();
        } else {
            $this->situacao_financeira = self::SITUACAO_PARCIALMENTE_PAGO;
        }
    }

    /**
     * Métodos de utilidade
     */
    public function isPago(): bool
    {
        return $this->situacao_financeira === self::SITUACAO_PAGO;
    }

    public function isPendente(): bool
    {
        return $this->situacao_financeira === self::SITUACAO_PENDENTE;
    }

    public function isVencido(): bool
    {
        return $this->situacao_financeira === self::SITUACAO_VENCIDO;
    }

    public function isParcialmentePago(): bool
    {
        return $this->situacao_financeira === self::SITUACAO_PARCIALMENTE_PAGO;
    }

    public function isContaReceber(): bool
    {
        return $this->natureza_financeira === self::NATUREZA_ENTRADA;
    }

    public function isContaPagar(): bool
    {
        return $this->natureza_financeira === self::NATUREZA_SAIDA;
    }

    public function isParcelado(): bool
    {
        return $this->e_parcelado === true;
    }

    public function isRecorrente(): bool
    {
        return $this->e_recorrente === true && $this->recorrencia_ativa === true;
    }

    public function temBoletoGerado(): bool
    {
        return $this->boleto_gerado === true;
    }

    public function requerAprovacao(): bool
    {
        return $this->status_aprovacao !== self::STATUS_APROVACAO_NAO_REQUER;
    }

    public function isAprovado(): bool
    {
        return $this->status_aprovacao === self::STATUS_APROVACAO_APROVADO;
    }

    /**
     * Métodos para ações
     */
    public function adicionarPagamento(float $valor, array $dados = []): LancamentoMovimentacao
    {
        $tipo = $this->isContaReceber() ? 'recebimento' : 'pagamento';
        
        $movimentacao = $this->movimentacoes()->create(array_merge($dados, [
            'tipo' => $tipo,
            'valor' => $valor,
            'data_movimentacao' => now(),
            'usuario_id' => auth()->id() ?? $this->usuario_id,
            'empresa_id' => $this->empresa_id,
        ]));

        // Atualizar valor pago
        $this->valor_pago = $this->movimentacoes()
                                ->whereIn('tipo', ['pagamento', 'recebimento'])
                                ->sum('valor');
        
        $this->data_ultimo_pagamento = now();
        $this->calcularSituacaoFinanceira();
        $this->save();

        return $movimentacao;
    }

    public function estornarPagamento(LancamentoMovimentacao $movimentacao, string $motivo = null): LancamentoMovimentacao
    {
        $estorno = $this->movimentacoes()->create([
            'tipo' => 'estorno',
            'valor' => $movimentacao->valor,
            'data_movimentacao' => now(),
            'observacoes' => "Estorno do pagamento #{$movimentacao->id}. Motivo: " . ($motivo ?? 'Não informado'),
            'usuario_id' => auth()->id() ?? $this->usuario_id,
            'empresa_id' => $this->empresa_id,
        ]);

        // Recalcular valor pago
        $this->valor_pago = $this->movimentacoes()
                                ->whereIn('tipo', ['pagamento', 'recebimento'])
                                ->sum('valor')
                          - $this->estornos()->sum('valor');
        
        $this->calcularSituacaoFinanceira();
        $this->save();

        return $estorno;
    }

    public function aprovar(int $usuarioId, string $observacoes = null): bool
    {
        if ($this->status_aprovacao !== self::STATUS_APROVACAO_PENDENTE) {
            return false;
        }

        $this->status_aprovacao = self::STATUS_APROVACAO_APROVADO;
        $this->aprovado_por = $usuarioId;
        $this->data_aprovacao = now();
        
        if ($observacoes) {
            $this->observacoes_pagamento = $observacoes;
        }

        return $this->save();
    }

    public function rejeitar(int $usuarioId, string $motivo): bool
    {
        if ($this->status_aprovacao !== self::STATUS_APROVACAO_PENDENTE) {
            return false;
        }

        $this->status_aprovacao = self::STATUS_APROVACAO_REJEITADO;
        $this->aprovado_por = $usuarioId;
        $this->data_aprovacao = now();
        $this->motivo_rejeicao = $motivo;

        return $this->save();
    }

    public function cancelar(int $usuarioId, string $motivo): bool
    {
        $this->situacao_financeira = self::SITUACAO_CANCELADO;
        $this->usuario_ultima_alteracao = $usuarioId;
        $this->observacoes_pagamento = "Cancelado: " . $motivo;

        return $this->save();
    }

    /**
     * Formatters para exibição
     */
    public function getValorBrutoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_bruto, 2, ',', '.');
    }

    public function getValorLiquidoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_liquido, 2, ',', '.');
    }

    public function getValorPagoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_pago, 2, ',', '.');
    }

    public function getValorSaldoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_saldo, 2, ',', '.');
    }

    public function getSituacaoFormatadaAttribute(): string
    {
        $situacoes = [
            self::SITUACAO_PENDENTE => 'Pendente',
            self::SITUACAO_PAGO => 'Pago',
            self::SITUACAO_PARCIALMENTE_PAGO => 'Parcialmente Pago',
            self::SITUACAO_VENCIDO => 'Vencido',
            self::SITUACAO_CANCELADO => 'Cancelado',
            self::SITUACAO_EM_NEGOCIACAO => 'Em Negociação',
            self::SITUACAO_ESTORNADO => 'Estornado',
        ];

        return $situacoes[$this->situacao_financeira] ?? $this->situacao_financeira;
    }

    public function getNaturezaFormatadaAttribute(): string
    {
        return $this->natureza_financeira === self::NATUREZA_ENTRADA ? 'Conta a Receber' : 'Conta a Pagar';
    }
}
