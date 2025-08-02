<?php

namespace App\Affiliates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliatePayment extends Model
{
    use HasFactory;

    protected $table = 'affiliate_payments';

    protected $fillable = [
        'affiliate_id',
        'amount',
        'commission_count',
        'payment_method',
        'status',
        'reference_id',
        'bank_details',
        'processed_at',
        'failed_at',
        'failure_reason',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_count' => 'integer',
        'bank_details' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'bank_details'
    ];

    /**
     * Status possíveis do pagamento
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relacionamentos
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'payment_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByAffiliate($query, $affiliateId)
    {
        return $query->where('affiliate_id', $affiliateId);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);
    }

    /**
     * Métodos de negócio
     */

    /**
     * Marcar como processando
     */
    public function markAsProcessing(string $referenceId = null): bool
    {
        $this->status = self::STATUS_PROCESSING;

        if ($referenceId) {
            $this->reference_id = $referenceId;
        }

        return $this->save();
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(string $referenceId = null): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->processed_at = now();

        if ($referenceId) {
            $this->reference_id = $referenceId;
        }

        // Atualizar status das comissões relacionadas
        $this->commissions()->update(['status' => 'paid', 'paid_at' => now()]);

        return $this->save();
    }

    /**
     * Marcar como falhado
     */
    public function markAsFailed(string $reason): bool
    {
        $this->status = self::STATUS_FAILED;
        $this->failed_at = now();
        $this->failure_reason = $reason;

        return $this->save();
    }

    /**
     * Cancelar pagamento
     */
    public function cancel(string $reason = null): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING])) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;

        if ($reason) {
            $this->notes = $reason;
        }

        return $this->save();
    }

    /**
     * Tentar novamente pagamento falhado
     */
    public function retry(): bool
    {
        if ($this->status !== self::STATUS_FAILED) {
            return false;
        }

        $this->status = self::STATUS_PENDING;
        $this->failed_at = null;
        $this->failure_reason = null;

        return $this->save();
    }

    /**
     * Verificar se pode ser processado
     */
    public function canBeProcessed(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Verificar se foi completado
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Verificar se falhou
     */
    public function hasFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Obter detalhes de pagamento formatados
     */
    public function getPaymentDetailsAttribute(): array
    {
        $details = [
            'method' => $this->payment_method,
            'amount' => $this->amount,
            'commissions' => $this->commission_count,
            'reference' => $this->reference_id
        ];

        if ($this->payment_method === 'pix' && isset($this->bank_details['pix_key'])) {
            $details['pix_key'] = substr($this->bank_details['pix_key'], 0, 6) . '***';
        }

        if ($this->payment_method === 'bank_transfer' && isset($this->bank_details['account'])) {
            $details['account'] = '***' . substr($this->bank_details['account'], -4);
        }

        return $details;
    }

    /**
     * Obter status formatado
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PROCESSING => 'Processando',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_FAILED => 'Falhado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    /**
     * Obter cor do status para UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'dark'
        };
    }

    /**
     * Calcular tempo de processamento
     */
    public function getProcessingTimeAttribute(): ?int
    {
        if (!$this->processed_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->processed_at);
    }

    /**
     * Formatar para API
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        $array['status_label'] = $this->status_label;
        $array['status_color'] = $this->status_color;
        $array['payment_details'] = $this->payment_details;
        $array['processing_time'] = $this->processing_time;
        $array['can_be_processed'] = $this->canBeProcessed();
        $array['is_completed'] = $this->isCompleted();
        $array['has_failed'] = $this->hasFailed();

        // Remover dados sensíveis
        unset($array['bank_details']);

        return $array;
    }
}
