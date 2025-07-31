<?php

namespace App\Models\Fidelidade;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadeCredito extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_creditos';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'tipo',
        'valor_original',
        'valor_atual',
        'codigo_ativacao',
        'data_expiracao',
        'pedido_origem_id',
        'observacoes',
        'status'
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'valor_atual' => 'decimal:2',
        'data_expiracao' => 'date'
    ];

    const TIPOS = [
        'comprado' => 'Comprado',
        'cortesia' => 'Cortesia',
        'devolucao' => 'Devolução',
        'premio' => 'Prêmio',
        'indicacao' => 'Indicação'
    ];

    const STATUS = [
        'ativo' => 'Ativo',
        'usado' => 'Usado',
        'expirado' => 'Expirado',
        'cancelado' => 'Cancelado'
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
     * Relacionamento com Pedido de Origem
     */
    public function pedidoOrigem()
    {
        return $this->belongsTo(\App\Models\PDV\Sale::class, 'pedido_origem_id');
    }

    /**
     * Scope para créditos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para créditos válidos (ativos e não expirados)
     */
    public function scopeValidos($query)
    {
        return $query->where('status', 'ativo')
            ->where(function ($q) {
                $q->whereNull('data_expiracao')
                    ->orWhere('data_expiracao', '>=', now()->toDateString());
            });
    }

    /**
     * Scope para créditos próximos ao vencimento
     */
    public function scopeProximosVencimento($query, $dias = 30)
    {
        return $query->where('data_expiracao', '<=', now()->addDays($dias))
            ->where('status', 'ativo');
    }

    /**
     * Verificar se o crédito está válido
     */
    public function isValido(): bool
    {
        if ($this->status !== 'ativo') {
            return false;
        }

        if ($this->valor_atual <= 0) {
            return false;
        }

        if ($this->data_expiracao && $this->data_expiracao < now()->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Usar parte do crédito
     */
    public function usar($valor)
    {
        if (!$this->isValido()) {
            throw new \Exception('Crédito não está válido para uso.');
        }

        if ($valor > $this->valor_atual) {
            throw new \Exception('Valor solicitado é maior que o saldo disponível.');
        }

        $this->valor_atual -= $valor;

        if ($this->valor_atual <= 0) {
            $this->status = 'usado';
        }

        $this->save();

        return $this;
    }

    /**
     * Marcar como expirado
     */
    public function marcarExpirado()
    {
        $this->update(['status' => 'expirado']);
    }

    /**
     * Cancelar crédito
     */
    public function cancelar($motivo = null)
    {
        $this->update([
            'status' => 'cancelado',
            'observacoes' => $motivo ? "Cancelado: {$motivo}" : 'Cancelado'
        ]);
    }

    /**
     * Gerar código de ativação único
     */
    public static function gerarCodigoAtivacao(): string
    {
        do {
            $codigo = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (self::where('codigo_ativacao', $codigo)->exists());

        return $codigo;
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
    public function isProximoVencimento($dias = 30): bool
    {
        if (!$this->data_expiracao) {
            return false;
        }

        return $this->data_expiracao <= now()->addDays($dias)->toDateString();
    }
}
