<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadeCashbackTransacao extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_cashback_transacoes';

    protected $fillable = [
        'cliente_id',
        'empresa_id',
        'pedido_id',
        'tipo',
        'valor',
        'valor_pedido_original',
        'percentual_aplicado',
        'saldo_anterior',
        'saldo_posterior',
        'data_expiracao',
        'status',
        'motivo_bloqueio',
        'observacoes',
        'sync_status',
        'sync_data'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_pedido_original' => 'decimal:2',
        'percentual_aplicado' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_posterior' => 'decimal:2',
        'data_expiracao' => 'date',
        'sync_data' => 'datetime'
    ];

    const TIPOS = [
        'credito' => 'Crédito',
        'debito' => 'Débito',
        'expiracao' => 'Expiração',
        'bloqueio' => 'Bloqueio'
    ];

    const STATUS = [
        'disponivel' => 'Disponível',
        'usado' => 'Usado',
        'expirado' => 'Expirado',
        'bloqueado' => 'Bloqueado'
    ];

    /**
     * Relacionamento com Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Relacionamento com Pedido (se aplicável)
     */
    public function pedido()
    {
        return $this->belongsTo(\App\Models\PDV\Sale::class, 'pedido_id');
    }

    /**
     * Scope para transações disponíveis
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    /**
     * Scope para transações de crédito
     */
    public function scopeCredito($query)
    {
        return $query->where('tipo', 'credito');
    }

    /**
     * Scope para transações de débito
     */
    public function scopeDebito($query)
    {
        return $query->where('tipo', 'debito');
    }

    /**
     * Scope para transações por período
     */
    public function scopePeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('created_at', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para transações próximas ao vencimento
     */
    public function scopeProximasVencimento($query, $dias = 30)
    {
        return $query->where('data_expiracao', '<=', now()->addDays($dias))
            ->where('status', 'disponivel');
    }

    /**
     * Verificar se está expirada
     */
    public function isExpirada(): bool
    {
        return $this->data_expiracao && $this->data_expiracao < now()->toDateString();
    }

    /**
     * Marcar como usada
     */
    public function marcarUsada($observacoes = null)
    {
        $this->update([
            'status' => 'usado',
            'observacoes' => $observacoes
        ]);
    }

    /**
     * Marcar como expirada
     */
    public function marcarExpirada()
    {
        $this->update(['status' => 'expirado']);
    }

    /**
     * Obter descrição do tipo
     */
    public function getTipoDescricaoAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    /**
     * Obter descrição do status
     */
    public function getStatusDescricaoAttribute()
    {
        return self::STATUS[$this->status] ?? $this->status;
    }
}
