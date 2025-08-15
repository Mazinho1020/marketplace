<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\FormaPagamento;
use App\Models\Financial\ContaBancaria;
use App\Models\Empresa;

class Pagamento extends Model
{
    protected $table = 'pagamentos';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'lancamento_id',
        'empresa_id',
        'tipo_id',
        'forma_pagamento_id',
        'bandeira_id',
        'conta_bancaria_id',
        'numero_parcela_pagamento',
        'valor',
        'valor_principal',
        'valor_juros',
        'valor_multa',
        'valor_desconto',
        'data_pagamento',
        'data_compensacao',
        'observacao',
        'comprovante_pagamento',
        'taxa',
        'valor_taxa',
        'referencia_externa',
        'usuario_id',
        'status_pagamento',
        'caixa_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_principal' => 'decimal:2',
        'valor_juros' => 'decimal:2',
        'valor_multa' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'taxa' => 'decimal:2',
        'valor_taxa' => 'decimal:2',
        'data_pagamento' => 'date',
        'data_compensacao' => 'date',
    ];

    // =================== RELACIONAMENTOS ===================

    /**
     * Relacionamento com lançamento financeiro
     */
    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(LancamentoFinanceiro::class, 'lancamento_id', 'id');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com forma de pagamento
     */
    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    /**
     * Relacionamento com bandeira
     */
    public function bandeira(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Financial\FormaPagBandeira::class, 'bandeira_id');
    }

    /**
     * Relacionamento com conta bancária
     */
    public function contaBancaria(): BelongsTo
    {
        return $this->belongsTo(ContaBancaria::class, 'conta_bancaria_id');
    }

    // =================== SCOPES ===================

    /**
     * Scope para pagamentos confirmados
     */
    public function scopeConfirmados($query)
    {
        return $query->where('status_pagamento', 'confirmado');
    }

    /**
     * Scope para pagamentos processando
     */
    public function scopeProcessando($query)
    {
        return $query->where('status_pagamento', 'processando');
    }

    /**
     * Scope para pagamentos estornados
     */
    public function scopeEstornados($query)
    {
        return $query->where('status_pagamento', 'estornado');
    }

    /**
     * Scope para empresa específica
     */
    public function scopeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // =================== ATRIBUTOS CALCULADOS ===================

    /**
     * Valor líquido do pagamento (valor - desconto + juros + multa)
     */
    public function getValorLiquidoAttribute(): float
    {
        return $this->valor_principal - $this->valor_desconto + $this->valor_juros + $this->valor_multa;
    }

    /**
     * Verifica se o pagamento está confirmado
     */
    public function getIsConfirmadoAttribute(): bool
    {
        return $this->status_pagamento === 'confirmado';
    }

    /**
     * Verifica se o pagamento foi estornado
     */
    public function getIsEstornadoAttribute(): bool
    {
        return $this->status_pagamento === 'estornado';
    }

    // =================== MÉTODOS DE NEGÓCIO ===================

    /**
     * Estornar este pagamento
     */
    public function estornar(string $motivo = ''): bool
    {
        $this->update([
            'status_pagamento' => 'estornado',
            'observacao' => ($this->observacao ?? '') . "\n[ESTORNO] " . $motivo
        ]);

        // Atualizar situação do lançamento
        $this->lancamento->atualizarSituacao();

        return true;
    }

    /**
     * Confirmar pagamento que estava processando
     */
    public function confirmar(): bool
    {
        if ($this->status_pagamento !== 'processando') {
            return false;
        }

        $this->update(['status_pagamento' => 'confirmado']);

        // Atualizar situação do lançamento
        $this->lancamento->atualizarSituacao();

        return true;
    }

    // =================== EVENTOS DO MODEL ===================

    protected static function booted()
    {
        // Quando um pagamento é criado, atualizar o lançamento
        static::created(function ($pagamento) {
            $pagamento->lancamento->atualizarSituacao();
        });

        // Quando um pagamento é atualizado, atualizar o lançamento
        static::updated(function ($pagamento) {
            if ($pagamento->wasChanged(['valor', 'status_pagamento'])) {
                $pagamento->lancamento->atualizarSituacao();
            }
        });

        // Quando um pagamento é deletado, atualizar o lançamento
        static::deleted(function ($pagamento) {
            $pagamento->lancamento->atualizarSituacao();
        });
    }
}
