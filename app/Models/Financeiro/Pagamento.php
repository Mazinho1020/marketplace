<?php

namespace App\Models\Financeiro;

use App\Models\Financial\BaseFinancialModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends BaseFinancialModel
{
    protected $table = 'pagamentos';

    protected $fillable = [
        'lancamento_id',
        'numero_parcela_pagamento', 
        'tipo_id',
        'forma_pagamento_id',
        'bandeira_id',
        'valor',
        'valor_principal',
        'valor_juros',
        'valor_multa',
        'valor_desconto',
        'data_pagamento',
        'data_compensacao',
        'observacao',
        'comprovante_pagamento',
        'status_pagamento',
        'referencia_externa',
        'conta_bancaria_id',
        'taxa',
        'empresa_id',
        'caixa_id',
        'usuario_id',
        'valor_taxa'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_principal' => 'decimal:2',
        'valor_juros' => 'decimal:2',
        'valor_multa' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_taxa' => 'decimal:2',
        'taxa' => 'decimal:2',
        'data_pagamento' => 'date',
        'data_compensacao' => 'date',
        'sync_data' => 'datetime',
    ];

    // Status possíveis
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_CONFIRMADO = 'confirmado';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_ESTORNADO = 'estornado';

    /**
     * Relacionamentos
     */
    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(Lancamento::class);
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo('App\Models\FormaPagamento', 'forma_pagamento_id');
    }

    public function contaBancaria(): BelongsTo
    {
        return $this->belongsTo('App\Models\ContaBancaria', 'conta_bancaria_id');
    }

    /**
     * Scopes
     */
    public function scopeConfirmados($query)
    {
        return $query->where('status_pagamento', self::STATUS_CONFIRMADO);
    }

    public function scopeEstornados($query)
    {
        return $query->where('status_pagamento', self::STATUS_ESTORNADO);
    }

    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Métodos utilitários
     */
    public function isConfirmado(): bool
    {
        return $this->status_pagamento === self::STATUS_CONFIRMADO;
    }

    public function isEstornado(): bool
    {
        return $this->status_pagamento === self::STATUS_ESTORNADO;
    }

    public function isPendente(): bool
    {
        return $this->status_pagamento === self::STATUS_PROCESSANDO;
    }

    /**
     * Confirmar pagamento
     */
    public function confirmar(): bool
    {
        $this->status_pagamento = self::STATUS_CONFIRMADO;
        return $this->save();
    }

    /**
     * Estornar pagamento
     */
    public function estornar(string $motivo = null): bool
    {
        $this->status_pagamento = self::STATUS_ESTORNADO;
        if ($motivo) {
            $this->observacao = ($this->observacao ? $this->observacao . "\n" : '') . "ESTORNO: " . $motivo;
        }
        return $this->save();
    }

    /**
     * Formatters
     */
    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->valor, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute(): string
    {
        $status = [
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_CONFIRMADO => 'Confirmado', 
            self::STATUS_CANCELADO => 'Cancelado',
            self::STATUS_ESTORNADO => 'Estornado',
        ];

        return $status[$this->status_pagamento] ?? $this->status_pagamento;
    }
}
