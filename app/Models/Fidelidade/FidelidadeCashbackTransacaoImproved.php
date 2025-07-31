<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class FidelidadeCashbackTransacaoImproved extends Model
{
    use HasFactory;

    protected $table = 'fidelidade_cashback_transacoes';

    protected $fillable = [
        'cliente_id',
        'empresa_id',
        'tipo',
        'valor',
        'descricao',
        'pedido_id',
        'status',
        'data_processamento',
        'observacoes'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_processamento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constantes organizadas
    public const TIPO_CREDITO = 'credito';
    public const TIPO_DEBITO = 'debito';

    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_PROCESSADO = 'processado';
    public const STATUS_CANCELADO = 'cancelado';
    public const STATUS_ESTORNADO = 'estornado';

    public const TIPOS = [
        self::TIPO_CREDITO => 'Crédito',
        self::TIPO_DEBITO => 'Débito',
    ];

    public const STATUS_OPCOES = [
        self::STATUS_PENDENTE => 'Pendente',
        self::STATUS_PROCESSADO => 'Processado',
        self::STATUS_CANCELADO => 'Cancelado',
        self::STATUS_ESTORNADO => 'Estornado',
    ];

    /**
     * Relacionamentos
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Accessors usando novos Attributes (Laravel 9+)
     */
    protected function valorFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => 'R$ ' . number_format($this->valor, 2, ',', '.')
        );
    }

    protected function statusFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUS_OPCOES[$this->status] ?? $this->status
        );
    }

    protected function statusCor(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_PENDENTE => 'warning',
                self::STATUS_PROCESSADO => 'success',
                self::STATUS_CANCELADO => 'danger',
                self::STATUS_ESTORNADO => 'secondary',
                default => 'primary'
            }
        );
    }

    /**
     * Query Scopes otimizados
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('cliente', fn($q) => $q->where('nome', 'like', "%{$search}%"))
                        ->orWhere('descricao', 'like', "%{$search}%")
                        ->orWhere('pedido_id', 'like', "%{$search}%");
                });
            })
            ->when(
                $filters['status'] ?? null,
                fn($query, $status) =>
                $query->where('status', $status)
            )
            ->when(
                $filters['tipo'] ?? null,
                fn($query, $tipo) =>
                $query->where('tipo', $tipo)
            )
            ->when(
                $filters['cliente_id'] ?? null,
                fn($query, $clienteId) =>
                $query->where('cliente_id', $clienteId)
            )
            ->when(
                $filters['empresa_id'] ?? null,
                fn($query, $empresaId) =>
                $query->where('empresa_id', $empresaId)
            )
            ->when(
                $filters['data_inicio'] ?? null,
                fn($query, $dataInicio) =>
                $query->whereDate('created_at', '>=', $dataInicio)
            )
            ->when(
                $filters['data_fim'] ?? null,
                fn($query, $dataFim) =>
                $query->whereDate('created_at', '<=', $dataFim)
            );
    }

    public function scopeCreditos(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_CREDITO);
    }

    public function scopeDebitos(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_DEBITO);
    }

    public function scopeProcessadas(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PROCESSADO);
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    public function scopeHoje(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeMesAtual(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeValorEntre(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('valor', [$min, $max]);
    }

    /**
     * Métodos de conveniência
     */
    public function isCredito(): bool
    {
        return $this->tipo === self::TIPO_CREDITO;
    }

    public function isDebito(): bool
    {
        return $this->tipo === self::TIPO_DEBITO;
    }

    public function isProcessada(): bool
    {
        return $this->status === self::STATUS_PROCESSADO;
    }

    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    public function podeCancelar(): bool
    {
        return in_array($this->status, [self::STATUS_PENDENTE, self::STATUS_PROCESSADO]);
    }

    public function podeEstornar(): bool
    {
        return $this->status === self::STATUS_PROCESSADO && $this->isCredito();
    }

    /**
     * Boot method para eventos do modelo
     */
    protected static function booted(): void
    {
        static::creating(function ($transacao) {
            // Auto-definir data de processamento se não informada
            if (!$transacao->data_processamento && $transacao->status === self::STATUS_PROCESSADO) {
                $transacao->data_processamento = now();
            }
        });

        static::updating(function ($transacao) {
            // Auto-definir data de processamento quando status muda para processado
            if ($transacao->isDirty('status') && $transacao->status === self::STATUS_PROCESSADO) {
                $transacao->data_processamento = now();
            }
        });
    }
}
