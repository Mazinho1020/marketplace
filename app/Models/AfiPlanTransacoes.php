<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AfiPlanTransacoes extends Model
{
    use HasFactory;

    protected $table = 'afi_plan_transacoes';

    protected $fillable = [
        'uuid',
        'empresa_id',
        'codigo_transacao',
        'cliente_id',
        'gateway_id',
        'gateway_transacao_id',
        'tipo_origem',
        'id_origem',
        'valor_original',
        'valor_desconto',
        'valor_taxas',
        'valor_final',
        'moeda',
        'forma_pagamento',
        'status',
        'gateway_status',
        'cliente_nome',
        'cliente_email',
        'descricao',
        'metadados',
        'expira_em',
        'processado_em',
        'aprovado_em',
        'cancelado_em'
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_taxas' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'metadados' => 'array',
        'expira_em' => 'datetime',
        'processado_em' => 'datetime',
        'aprovado_em' => 'datetime',
        'cancelado_em' => 'datetime'
    ];

    /**
     * Status possíveis da transação
     */
    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_PENDENTE = 'pendente';
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_APROVADO = 'aprovado';
    const STATUS_RECUSADO = 'recusado';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_ESTORNADO = 'estornado';

    /**
     * Tipos de origem
     */
    const TIPO_NOVA_ASSINATURA = 'nova_assinatura';
    const TIPO_RENOVACAO = 'renovacao_assinatura';
    const TIPO_COMISSAO = 'comissao_afiliado';
    const TIPO_VENDA_AVULSA = 'venda_avulsa';

    /**
     * Relacionamento com gateway
     */
    public function gateway()
    {
        return $this->belongsTo(AfiPlanGateways::class, 'gateway_id');
    }

    /**
     * Relacionamento com assinatura (quando aplicável)
     */
    public function assinatura()
    {
        return $this->belongsTo(AfiPlanAssinaturas::class, 'id_origem')
            ->where('tipo_origem', self::TIPO_NOVA_ASSINATURA);
    }

    /**
     * Scope para transações aprovadas
     */
    public function scopeAprovado($query)
    {
        return $query->where('status', self::STATUS_APROVADO);
    }

    /**
     * Scope para transações pendentes
     */
    public function scopePendente($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    /**
     * Verificar se está aprovada
     */
    public function isAprovado(): bool
    {
        return $this->status === self::STATUS_APROVADO;
    }

    /**
     * Verificar se está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    /**
     * Verificar se está expirada
     */
    public function isExpirado(): bool
    {
        return $this->expira_em && $this->expira_em <= now();
    }

    /**
     * Obter cor do status
     */
    public function getStatusCorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APROVADO => 'success',
            self::STATUS_PENDENTE => 'warning',
            self::STATUS_PROCESSANDO => 'info',
            self::STATUS_RECUSADO => 'danger',
            self::STATUS_CANCELADO => 'secondary',
            self::STATUS_ESTORNADO => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Obter ícone do status
     */
    public function getStatusIconeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APROVADO => 'fas fa-check-circle',
            self::STATUS_PENDENTE => 'fas fa-clock',
            self::STATUS_PROCESSANDO => 'fas fa-spinner',
            self::STATUS_RECUSADO => 'fas fa-times-circle',
            self::STATUS_CANCELADO => 'fas fa-ban',
            self::STATUS_ESTORNADO => 'fas fa-undo',
            default => 'fas fa-question-circle'
        };
    }

    /**
     * Aprovar transação
     */
    public function aprovar(): bool
    {
        $this->update([
            'status' => self::STATUS_APROVADO,
            'aprovado_em' => now()
        ]);

        return true;
    }

    /**
     * Cancelar transação
     */
    public function cancelar(): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELADO,
            'cancelado_em' => now()
        ]);

        return true;
    }
}
