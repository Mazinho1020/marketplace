<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEvent extends Model
{
    protected $fillable = [
        'transaction_id',
        'event_type',
        'event_data',
        'previous_status',
        'new_status',
        'triggered_by',
        'user_id'
    ];

    protected $casts = [
        'event_data' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }

    // Scope para filtrar por tipo de evento
    public function scopeByType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    // Scope para filtrar por usuário
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope para filtrar por origem
    public function scopeByTriggeredBy($query, string $triggeredBy)
    {
        return $query->where('triggered_by', $triggeredBy);
    }

    // Getter para data formatada
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    // Getter para descrição do evento
    public function getDescriptionAttribute(): string
    {
        return match ($this->event_type) {
            'created' => 'Transação criada',
            'sent_to_gateway' => 'Enviado para o gateway',
            'gateway_response' => 'Resposta do gateway recebida',
            'webhook_received' => 'Webhook recebido',
            'status_changed' => 'Status alterado',
            'payment_approved' => 'Pagamento aprovado',
            'payment_declined' => 'Pagamento negado',
            'payment_cancelled' => 'Pagamento cancelado',
            'payment_expired' => 'Pagamento expirado',
            'payment_refunded' => 'Pagamento estornado',
            'refund_requested' => 'Estorno solicitado',
            'manual_update' => 'Atualização manual',
            default => 'Evento desconhecido',
        };
    }
}
