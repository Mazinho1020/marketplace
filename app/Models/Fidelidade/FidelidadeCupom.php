<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FidelidadeCupom extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_cupons';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'tipo',
        'valor_desconto',
        'percentual_desconto',
        'valor_minimo_pedido',
        'quantidade_maxima_uso',
        'quantidade_usada',
        'uso_por_cliente',
        'data_inicio',
        'data_fim',
        'status'
    ];

    protected $casts = [
        'valor_desconto' => 'decimal:2',
        'percentual_desconto' => 'decimal:2',
        'valor_minimo_pedido' => 'decimal:2',
        'quantidade_maxima_uso' => 'integer',
        'quantidade_usada' => 'integer',
        'uso_por_cliente' => 'integer',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime'
    ];

    const TIPOS = [
        'desconto_sacola' => 'Desconto na Sacola',
        'desconto_entrega' => 'Desconto na Entrega',
        'desconto_item' => 'Desconto em Item',
        'beneficio_extra' => 'Benefício Extra'
    ];

    const STATUS = [
        'ativo' => 'Ativo',
        'pausado' => 'Pausado',
        'expirado' => 'Expirado',
        'esgotado' => 'Esgotado'
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Relacionamento com usos do cupom
     */
    public function usos(): HasMany
    {
        return $this->hasMany(FidelidadeCupomUso::class, 'cupom_id');
    }

    /**
     * Scope para cupons ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para cupons válidos (dentro do período e com usos disponíveis)
     */
    public function scopeValidos($query)
    {
        $agora = now();

        return $query->where('status', 'ativo')
            ->where(function ($q) use ($agora) {
                $q->whereNull('data_inicio')
                    ->orWhere('data_inicio', '<=', $agora);
            })
            ->where(function ($q) use ($agora) {
                $q->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', $agora);
            })
            ->whereRaw('(quantidade_maxima_uso IS NULL OR quantidade_usada < quantidade_maxima_uso)');
    }

    /**
     * Verificar se o cupom é válido
     */
    public function isValido(): bool
    {
        // Verificar status
        if ($this->status !== 'ativo') {
            return false;
        }

        // Verificar período
        $agora = now();
        if ($this->data_inicio && $this->data_inicio > $agora) {
            return false;
        }
        if ($this->data_fim && $this->data_fim < $agora) {
            return false;
        }

        // Verificar limite de uso
        if ($this->quantidade_maxima_uso && $this->quantidade_usada >= $this->quantidade_maxima_uso) {
            return false;
        }

        return true;
    }

    /**
     * Verificar se cliente pode usar o cupom
     */
    public function clientePodeUsar($clienteId): bool
    {
        if (!$this->isValido()) {
            return false;
        }

        $usosCliente = $this->usos()->where('cliente_id', $clienteId)->count();

        return $usosCliente < $this->uso_por_cliente;
    }

    /**
     * Calcular desconto para um valor
     */
    public function calcularDesconto($valorPedido): float
    {
        if ($valorPedido < $this->valor_minimo_pedido) {
            return 0;
        }

        if ($this->valor_desconto) {
            return min($this->valor_desconto, $valorPedido);
        }

        if ($this->percentual_desconto) {
            return $valorPedido * ($this->percentual_desconto / 100);
        }

        return 0;
    }

    /**
     * Usar o cupom
     */
    public function usar($clienteId, $pedidoId = null, $valorDesconto = null)
    {
        if (!$this->clientePodeUsar($clienteId)) {
            throw new \Exception('Cliente não pode usar este cupom.');
        }

        // Registrar uso
        $uso = $this->usos()->create([
            'cliente_id' => $clienteId,
            'pedido_id' => $pedidoId,
            'valor_desconto_aplicado' => $valorDesconto,
            'data_uso' => now()
        ]);

        // Incrementar contador
        $this->increment('quantidade_usada');

        // Verificar se esgotou
        if ($this->quantidade_maxima_uso && $this->quantidade_usada >= $this->quantidade_maxima_uso) {
            $this->update(['status' => 'esgotado']);
        }

        return $uso;
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

    /**
     * Verificar se está próximo do vencimento
     */
    public function isProximoVencimento($dias = 7): bool
    {
        if (!$this->data_fim) {
            return false;
        }

        return $this->data_fim <= now()->addDays($dias);
    }
}
