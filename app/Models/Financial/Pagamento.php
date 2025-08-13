<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\Enums\FrequenciaRecorrenciaEnum;
use Carbon\Carbon;

class Pagamento extends Model
{
    protected $table = 'pagamentos';

    protected $fillable = [
        'lancamento_id',
        'valor',
        'data_pagamento',
        'forma_pagamento_id',
        'bandeira_id',
        'conta_bancaria_id',
        'taxa',
        'valor_taxa',
        'observacoes',
        'numero_comprovante',
        'dados_confirmacao',
        'usuario_id',
        'status',
        'sync_data',
        'sync_hash',
        'sync_status',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'taxa' => 'decimal:4',
        'valor_taxa' => 'decimal:2',
        'data_pagamento' => 'datetime',
        'dados_confirmacao' => 'array',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELACIONAMENTOS =====

    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(LancamentoFinanceiro::class, 'lancamento_id');
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }

    public function bandeira(): BelongsTo
    {
        return $this->belongsTo(Bandeira::class, 'bandeira_id');
    }

    public function contaBancaria(): BelongsTo
    {
        return $this->belongsTo(ContaBancaria::class, 'conta_bancaria_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    // ===== SCOPES =====

    public function scopeConfirmados(Builder $query): Builder
    {
        return $query->where('status', 'confirmado');
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', 'pendente');
    }

    public function scopeCancelados(Builder $query): Builder
    {
        return $query->where('status', 'cancelado');
    }

    public function scopePorPeriodo(Builder $query, Carbon $inicio, Carbon $fim): Builder
    {
        return $query->whereBetween('data_pagamento', [$inicio, $fim]);
    }

    public function scopePorFormaPagamento(Builder $query, int $formaPagamentoId): Builder
    {
        return $query->where('forma_pagamento_id', $formaPagamentoId);
    }

    // ===== MÉTODOS DE VERIFICAÇÃO =====

    public function isConfirmado(): bool
    {
        return $this->status === 'confirmado';
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    public function temTaxa(): bool
    {
        return $this->valor_taxa > 0;
    }

    // ===== MÉTODOS DE NEGÓCIO =====

    public function confirmar(): bool
    {
        $this->status = 'confirmado';
        $saved = $this->save();
        
        if ($saved) {
            // Atualizar situação do lançamento
            $this->lancamento->atualizarSituacao();
        }
        
        return $saved;
    }

    public function cancelar(string $motivo = null): bool
    {
        $this->status = 'cancelado';
        
        if ($motivo) {
            $observacoes = $this->observacoes ? $this->observacoes . "\n" : '';
            $this->observacoes = $observacoes . "Cancelado: {$motivo}";
        }
        
        $saved = $this->save();
        
        if ($saved) {
            // Atualizar situação do lançamento
            $this->lancamento->atualizarSituacao();
        }
        
        return $saved;
    }

    // ===== ATRIBUTOS COMPUTED =====

    public function getValorLiquidoAttribute(): float
    {
        return $this->valor - $this->valor_taxa;
    }

    public function getPercentualTaxaAttribute(): float
    {
        return $this->valor > 0 ? ($this->valor_taxa / $this->valor) * 100 : 0;
    }

    // ===== EVENTOS =====

    protected static function booted()
    {
        // Quando um pagamento é criado, atualizar situação do lançamento
        static::created(function (Pagamento $pagamento) {
            $pagamento->lancamento->atualizarSituacao();
        });

        // Quando um pagamento é atualizado, atualizar situação do lançamento
        static::updated(function (Pagamento $pagamento) {
            $pagamento->lancamento->atualizarSituacao();
        });

        // Quando um pagamento é deletado, atualizar situação do lançamento
        static::deleted(function (Pagamento $pagamento) {
            $pagamento->lancamento->atualizarSituacao();
        });
    }
}