<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadeCashbackRegra extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_cashback_regras';

    protected $fillable = [
        'empresa_id',
        'tipo_regra',
        'referencia_id',
        'dia_semana',
        'horario_inicio',
        'horario_fim',
        'percentual_cashback',
        'valor_maximo_cashback',
        'ativo'
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'horario_inicio' => 'datetime:H:i',
        'horario_fim' => 'datetime:H:i',
        'percentual_cashback' => 'decimal:2',
        'valor_maximo_cashback' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    const TIPOS_REGRA = [
        'categoria' => 'Por Categoria',
        'produto' => 'Por Produto',
        'dia_semana' => 'Por Dia da Semana',
        'horario' => 'Por Horário',
        'primeira_compra' => 'Primeira Compra'
    ];

    const DIAS_SEMANA = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado'
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Scope para regras ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope por tipo de regra
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_regra', $tipo);
    }

    /**
     * Verificar se a regra se aplica ao contexto atual
     */
    public function seAplica($contexto = []): bool
    {
        if (!$this->ativo) {
            return false;
        }

        $agora = now();

        switch ($this->tipo_regra) {
            case 'dia_semana':
                return $agora->dayOfWeek == $this->dia_semana;

            case 'horario':
                $horaAtual = $agora->format('H:i');
                return $horaAtual >= $this->horario_inicio->format('H:i')
                    && $horaAtual <= $this->horario_fim->format('H:i');

            case 'categoria':
                return isset($contexto['categoria_id'])
                    && $contexto['categoria_id'] == $this->referencia_id;

            case 'produto':
                return isset($contexto['produto_id'])
                    && $contexto['produto_id'] == $this->referencia_id;

            case 'primeira_compra':
                return isset($contexto['is_primeira_compra'])
                    && $contexto['is_primeira_compra'] === true;

            default:
                return true;
        }
    }

    /**
     * Calcular cashback baseado no valor
     */
    public function calcularCashback($valor): float
    {
        $cashback = $valor * ($this->percentual_cashback / 100);

        if ($this->valor_maximo_cashback) {
            $cashback = min($cashback, $this->valor_maximo_cashback);
        }

        return round($cashback, 2);
    }

    /**
     * Obter descrição do tipo de regra
     */
    public function getTipoRegraDescricaoAttribute()
    {
        return self::TIPOS_REGRA[$this->tipo_regra] ?? $this->tipo_regra;
    }

    /**
     * Obter descrição do dia da semana
     */
    public function getDiaSemanaDescricaoAttribute()
    {
        return $this->dia_semana !== null ? self::DIAS_SEMANA[$this->dia_semana] : null;
    }

    /**
     * Obter descrição completa da regra
     */
    public function getDescricaoCompletaAttribute(): string
    {
        $descricao = "{$this->percentual_cashback}% de cashback";

        switch ($this->tipo_regra) {
            case 'dia_semana':
                $descricao .= " às {$this->dia_semana_descricao}";
                break;

            case 'horario':
                $descricao .= " das {$this->horario_inicio->format('H:i')} às {$this->horario_fim->format('H:i')}";
                break;

            case 'categoria':
                $descricao .= " em produtos da categoria ID {$this->referencia_id}";
                break;

            case 'produto':
                $descricao .= " no produto ID {$this->referencia_id}";
                break;

            case 'primeira_compra':
                $descricao .= " na primeira compra";
                break;
        }

        if ($this->valor_maximo_cashback) {
            $descricao .= " (máximo R$ {$this->valor_maximo_cashback})";
        }

        return $descricao;
    }
}
