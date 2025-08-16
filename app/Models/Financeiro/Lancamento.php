<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
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
        'valor_pago',
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
        'motivo_exclusao'
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
        'valor_pago' => 'decimal:4',
        'situacao_financeira' => \App\Enums\SituacaoFinanceiraEnum::class,
        'natureza_financeira' => \App\Enums\NaturezaFinanceiraEnum::class,
        'e_recorrente' => 'boolean',
        'recorrencia_ativa' => 'boolean',
        'cobranca_automatica' => 'boolean',
        'boleto_gerado' => 'boolean',
                'juros_multa_config' => 'json',
        'desconto_antecipacao' => 'json',
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
            
            // Definir usuário de criação
            if (empty($model->usuario_criacao)) {
                $model->usuario_criacao = $model->usuario_id;
            }
        });

        // Recalcular situação ao atualizar
        static::updating(function ($model) {
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
        // Comentado temporariamente - modelo LancamentoMovimentacao não existe ainda
        // return $this->hasMany(LancamentoMovimentacao::class, 'lancamento_id');
        return $this->hasMany(self::class, 'id')->whereRaw('1 = 0'); // Retorna collection vazia
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(\App\Models\Financeiro\Pagamento::class, 'lancamento_id');
    }

    /**
     * Relacionamento com recebimentos (pagamentos confirmados)
     */
    public function recebimentos(): HasMany
    {
        return $this->hasMany(\App\Models\Financeiro\Pagamento::class, 'lancamento_id')
            ->where('status_pagamento', 'confirmado')
            ->orderBy('data_pagamento', 'desc');
    }

    /**
     * Relacionamento com pessoa (cliente/fornecedor)
     */
    public function pessoa()
    {
        return $this->belongsTo(\App\Modules\Comerciante\Models\Pessoas\Pessoa::class, 'pessoa_id');
    }

    /**
     * Relacionamento com conta gerencial
     */
    public function contaGerencial()
    {
        return $this->belongsTo(\App\Models\Financial\ContaGerencial::class, 'conta_gerencial_id');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Relacionamento com usuário criador
     */
    public function usuarioCriacao()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_criacao');
    }

    /**
     * Relacionamento com usuário que fez última alteração
     */
    public function usuarioUltimaAlteracao()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_ultima_alteracao');
    }

    public function pagamentosConfirmados(): HasMany
    {
        return $this->hasMany(\App\Models\Financeiro\Pagamento::class, 'lancamento_id')
                    ->where('status_pagamento', 'confirmado');
    }

    public function pagamentosEstornados(): HasMany
    {
        return $this->hasMany(\App\Models\Financeiro\Pagamento::class, 'lancamento_id')
                    ->where('status_pagamento', 'estornado');
    }

    /**
     * Relacionamento com parcelas relacionadas
     * Retorna outras parcelas do mesmo grupo de parcelamento
     */
    public function parcelasRelacionadas()
    {
        // Se não tem grupo_parcelas, retorna uma coleção vazia
        if (empty($this->grupo_parcelas)) {
            return $this->hasMany(self::class, 'id', 'id')->whereRaw('1 = 0');
        }

        return $this->hasMany(self::class, 'grupo_parcelas', 'grupo_parcelas')
            ->where('id', '!=', $this->id)
            ->orderBy('parcela_atual');
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

    public function scopeParcelados(Builder $query): Builder
    {
        return $query->where('total_parcelas', '>', 1);
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
    public function getValorLiquidoAttribute()
    {
        return $this->valor_bruto 
               - $this->valor_desconto 
               + $this->valor_acrescimo 
               + $this->valor_juros 
               + $this->valor_multa;
    }

    public function getValorSaldoAttribute()
    {
        return $this->getValorLiquidoAttribute() - $this->valor_pago;
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
        } elseif ($this->valor_pago >= $this->getValorLiquidoAttribute()) {
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
        return $this->total_parcelas > 1;
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
     * Métodos para ações - INTEGRADO COM TABELA PAGAMENTOS EXISTENTE
     */
    public function adicionarPagamento(float $valor, array $dados = []): Pagamento
    {
        $pagamento = $this->pagamentos()->create(array_merge($dados, [
            'valor' => $valor,
            'data_pagamento' => now()->format('Y-m-d'),
            'status_pagamento' => 'confirmado',
            'usuario_id' => $this->usuario_id,
            'empresa_id' => $this->empresa_id,
        ]));

        // Os triggers do BD vão atualizar automaticamente valor_pago, valor_saldo e situacao_financeira
        $this->refresh();

        return $pagamento;
    }

    public function estornarPagamento(Pagamento $pagamento, string $motivo = null): bool
    {
        $sucesso = $pagamento->estornar($motivo);
        
        if ($sucesso) {
            // Os triggers do BD vão recalcular automaticamente
            $this->refresh();
        }

        return $sucesso;
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
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getValorLiquidoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->getValorLiquidoAttribute(), 2, ',', '.');
    }

    public function getValorPagoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_pago, 2, ',', '.');
    }

    public function getValorSaldoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->getValorSaldoAttribute(), 2, ',', '.');
    }

    /**
     * Accessor para valor pago calculado dinamicamente
     */
    public function getValorPagoCalculadoAttribute(): float
    {
        return $this->pagamentos()
            ->where('status_pagamento', 'confirmado')
            ->sum('valor') ?? 0.0;
    }

    /**
     * Accessor para saldo devedor calculado
     */
    public function getSaldoDevedorAttribute(): float
    {
        return $this->getValorLiquidoAttribute() - $this->getValorPagoCalculadoAttribute();
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
