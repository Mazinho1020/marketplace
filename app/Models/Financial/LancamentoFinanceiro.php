<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\Enums\FrequenciaRecorrenciaEnum;
use Carbon\Carbon;

class LancamentoFinanceiro extends Model
{
    protected $table = 'lancamentos';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'funcionario_id',
        'usuario_id',
        'conta_gerencial_id',
        'natureza_financeira',
        'pessoa_id',
        'pessoa_tipo',
        'numero_documento',
        'descricao',
        'observacoes',
        'data',
        'data_emissao',
        'data_competencia',
        'data_vencimento',
        'data_pagamento',
        'valor',
        'valor_original',
        'valor_desconto',
        'valor_acrescimo',
        'valor_juros',
        'valor_multa',
        'valor_final',
        'parcela_atual',
        'total_parcelas',
        'grupo_parcelas',
        'parcela_referencia',
        'intervalo_parcelas',
        'situacao_financeira',
        'forma_pagamento',
        'conta_bancaria_id',
        'e_recorrente',
        'frequencia_recorrencia',
        'proxima_recorrencia',
        'juros_multa_config',
        'desconto_antecipacao',
        'config_alertas',
        'anexos',
        'status_aprovacao',
        'aprovado_por',
        'data_aprovacao',
    ];

    protected $casts = [
        'data' => 'datetime',
        'data_emissao' => 'date',
        'data_competencia' => 'date',
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'proxima_recorrencia' => 'date',
        'data_aprovacao' => 'datetime',
        'valor' => 'decimal:2',
        'valor_original' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_acrescimo' => 'decimal:2',
        'valor_juros' => 'decimal:2',
        'valor_multa' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'e_recorrente' => 'boolean',
        'juros_multa_config' => 'array',
        'desconto_antecipacao' => 'array',
        'config_alertas' => 'array',
        'anexos' => 'array',
        'natureza_financeira' => NaturezaFinanceiraEnum::class,
        'situacao_financeira' => SituacaoFinanceiraEnum::class,
        'frequencia_recorrencia' => FrequenciaRecorrenciaEnum::class,
    ];

    // ===== RELACIONAMENTOS =====

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    public function contaGerencial(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Financial\ContaGerencial::class, 'conta_gerencial_id');
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'pessoa_id');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'funcionario_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'aprovado_por');
    }

    public function parcelasRelacionadas()
    {
        // Se não tem parcela_referencia, retorna uma coleção vazia
        if (empty($this->parcela_referencia)) {
            return $this->hasMany(self::class, 'id', 'id')->whereRaw('1 = 0');
        }

        return $this->hasMany(self::class, 'parcela_referencia', 'parcela_referencia')
            ->where('id', '!=', $this->id)
            ->orderBy('parcela_atual');
    }

    // ===== SCOPES =====

    public function scopeContasPagar(Builder $query): Builder
    {
        return $query->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR);
    }

    public function scopeContasReceber(Builder $query): Builder
    {
        return $query->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE);
    }

    public function scopePagas(Builder $query): Builder
    {
        return $query->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
            ->orWhere(function ($q) {
                $q->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                    ->where('data_vencimento', '<', now());
            });
    }

    public function scopeVencendoEm(Builder $query, int $dias): Builder
    {
        $dataLimite = now()->addDays($dias);
        return $query->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
            ->whereBetween('data_vencimento', [now(), $dataLimite]);
    }

    public function scopeRecorrentes(Builder $query): Builder
    {
        return $query->where('e_recorrente', true);
    }

    public function scopePorPessoa(Builder $query, int $pessoaId, string $pessoaTipo): Builder
    {
        return $query->where('pessoa_id', $pessoaId)
            ->where('pessoa_tipo', $pessoaTipo);
    }

    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    // ===== MÉTODOS DE NEGÓCIO =====

    public function calcularValorFinal(): float
    {
        $valorBase = $this->valor_original ?? $this->valor ?? 0;
        $desconto = $this->valor_desconto ?? 0;
        $acrescimo = $this->valor_acrescimo ?? 0;
        $juros = $this->valor_juros ?? 0;
        $multa = $this->valor_multa ?? 0;

        return $valorBase - $desconto + $acrescimo + $juros + $multa;
    }

    public function calcularJurosMulta(): array
    {
        if (!$this->isVencida() || !$this->juros_multa_config) {
            return ['juros' => 0, 'multa' => 0];
        }

        $config = $this->juros_multa_config;
        $diasAtraso = now()->diffInDays($this->data_vencimento);
        $carencia = $config['carencia_dias'] ?? 0;

        if ($diasAtraso <= $carencia) {
            return ['juros' => 0, 'multa' => 0];
        }

        $diasCalculoJuros = $diasAtraso - $carencia;
        $valorBase = $this->valor_original ?? $this->valor ?? 0;

        // Calcula juros
        $taxaJurosDia = ($config['taxa_juros_mes'] ?? 0) / 30;
        $juros = $valorBase * ($taxaJurosDia / 100) * $diasCalculoJuros;

        // Calcula multa (uma vez só)
        $multa = $valorBase * (($config['multa_atraso'] ?? 0) / 100);

        return [
            'juros' => round($juros, 2),
            'multa' => round($multa, 2),
            'dias_atraso' => $diasAtraso
        ];
    }

    public function calcularDescontoAntecipacao(): float
    {
        if (!$this->desconto_antecipacao || $this->isPaga()) {
            return 0;
        }

        $config = $this->desconto_antecipacao;
        if (!($config['ativo'] ?? false)) {
            return 0;
        }

        $diasAntecedencia = now()->diffInDays($this->data_vencimento, false);
        $diasMinimos = $config['dias_antecedencia'] ?? 0;

        if ($diasAntecedencia < $diasMinimos) {
            return 0;
        }

        $valorBase = $this->valor_original ?? $this->valor ?? 0;
        $percentual = $config['percentual'] ?? 0;

        return round($valorBase * ($percentual / 100), 2);
    }

    /**
     * Calcular dias para vencimento
     */
    public function diasParaVencimento(): int
    {
        return Carbon::today()->diffInDays($this->data_vencimento, false);
    }

    /**
     * Verificar se está vencido
     */
    public function isVencido(): bool
    {
        return $this->data_vencimento < Carbon::today() &&
            $this->situacao_financeira === SituacaoFinanceiraEnum::PENDENTE;
    }

    public function marcarComoPaga(\Carbon\Carbon $dataPagamento = null): void
    {
        $this->update([
            'situacao_financeira' => SituacaoFinanceiraEnum::PAGO,
            'data_pagamento' => $dataPagamento ?? now(),
        ]);

        // Se for recorrente, gerar próximo lançamento
        if ($this->e_recorrente) {
            $this->gerarProximaRecorrencia();
        }
    }

    public function gerarProximaRecorrencia(): ?self
    {
        if (!$this->e_recorrente || !$this->frequencia_recorrencia) {
            return null;
        }

        $proximaData = $this->frequencia_recorrencia->calcularProximaData(
            Carbon::parse($this->data_vencimento)
        );

        return self::create([
            'empresa_id' => $this->empresa_id,
            'natureza_financeira' => $this->natureza_financeira,
            'pessoa_id' => $this->pessoa_id,
            'pessoa_tipo' => $this->pessoa_tipo,
            'conta_gerencial_id' => $this->conta_gerencial_id,
            'descricao' => $this->descricao . ' (Recorrente)',
            'valor_original' => $this->valor_original,
            'data_vencimento' => $proximaData,
            'data_emissao' => now(),
            'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
            'e_recorrente' => true,
            'frequencia_recorrencia' => $this->frequencia_recorrencia,
            'juros_multa_config' => $this->juros_multa_config,
            'desconto_antecipacao' => $this->desconto_antecipacao,
            'config_alertas' => $this->config_alertas,
        ]);
    }

    // ===== MÉTODOS DE VERIFICAÇÃO =====

    public function isPaga(): bool
    {
        return $this->situacao_financeira === SituacaoFinanceiraEnum::PAGO;
    }

    public function isVencida(): bool
    {
        return $this->situacao_financeira === SituacaoFinanceiraEnum::VENCIDO ||
            ($this->situacao_financeira === SituacaoFinanceiraEnum::PENDENTE &&
                $this->data_vencimento < now());
    }

    public function isPendente(): bool
    {
        return $this->situacao_financeira === SituacaoFinanceiraEnum::PENDENTE;
    }

    public function isContaPagar(): bool
    {
        return $this->natureza_financeira === NaturezaFinanceiraEnum::PAGAR;
    }

    public function isContaReceber(): bool
    {
        return $this->natureza_financeira === NaturezaFinanceiraEnum::RECEBER;
    }

    public function temParcelamento(): bool
    {
        return $this->total_parcelas > 1;
    }

    // ===== ATRIBUTOS COMPUTED =====

    public function getValorFinalAttribute(): float
    {
        return $this->calcularValorFinal();
    }

    public function getDiasVencimentoAttribute(): int
    {
        if (!$this->data_vencimento) return 0;

        return now()->diffInDays($this->data_vencimento, false);
    }

    public function getStatusVencimentoAttribute(): string
    {
        if ($this->isPaga()) return 'pago';

        $dias = $this->dias_vencimento;
        if ($dias < 0) return 'vencido';
        if ($dias <= 7) return 'vencendo';

        return 'normal';
    }
}
