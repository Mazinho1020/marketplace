<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use App\Enums\FrequenciaRecorrenciaEnum;
use App\Models\Financial\Pagamento;
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

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'lancamento_id');
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'lancamento_id')
            ->where('tipo_id', 2)
            ->orderBy('data_pagamento', 'desc');
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

    /**
     * Adicionar pagamento ao lançamento
     */
    public function adicionarPagamento(array $dadosPagamento): Pagamento
    {
        $pagamento = $this->pagamentos()->create([
            'empresa_id' => $this->empresa_id, // Incluir empresa_id do lançamento
            'tipo_id' => $dadosPagamento['tipo_id'] ?? 1,
            'forma_pagamento_id' => $dadosPagamento['forma_pagamento_id'],
            'bandeira_id' => $dadosPagamento['bandeira_id'] ?? null,
            'conta_bancaria_id' => $dadosPagamento['conta_bancaria_id'] ?? null,
            'valor' => $dadosPagamento['valor'],
            'valor_principal' => $dadosPagamento['valor_principal'] ?? $dadosPagamento['valor'],
            'valor_juros' => $dadosPagamento['valor_juros'] ?? 0,
            'valor_multa' => $dadosPagamento['valor_multa'] ?? 0,
            'valor_desconto' => $dadosPagamento['valor_desconto'] ?? 0,
            'data_pagamento' => $dadosPagamento['data_pagamento'],
            'data_compensacao' => $dadosPagamento['data_compensacao'] ?? null,
            'observacao' => $dadosPagamento['observacao'] ?? null,
            'comprovante_pagamento' => $dadosPagamento['comprovante_pagamento'] ?? null,
            'usuario_id' => $dadosPagamento['usuario_id'] ?? (\Illuminate\Support\Facades\Auth::id() ?? 1),
            'taxa' => $dadosPagamento['taxa'] ?? 0,
            'valor_taxa' => $dadosPagamento['valor_taxa'] ?? 0,
            'referencia_externa' => $dadosPagamento['referencia_externa'] ?? null,
            'status_pagamento' => 'confirmado',
        ]);

        // Atualizar situação do lançamento
        $this->atualizarSituacao();

        return $pagamento;
    }

    /**
     * Atualizar situação financeira baseada nos recebimentos/pagamentos
     */
    public function atualizarSituacao(): void
    {
        if ($this->isContaReceber()) {
            $valorRecebido = $this->recebimentos()
                ->where('status_pagamento', 'confirmado')
                ->sum('valor');

            $valorTotal = $this->valor_final;

            if ($valorRecebido >= $valorTotal) {
                $this->situacao_financeira = SituacaoFinanceiraEnum::PAGO;
            } elseif ($valorRecebido > 0) {
                // Para pagamento parcial, manter como pendente mas com observação
                $this->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
            } else {
                $this->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
            }
        } elseif ($this->isContaPagar()) {
            $valorPago = $this->pagamentos()
                ->where('status_pagamento', 'confirmado')
                ->sum('valor');

            $valorTotal = $this->valor_final;

            if ($valorPago >= $valorTotal) {
                $this->situacao_financeira = SituacaoFinanceiraEnum::PAGO;
            } elseif ($valorPago > 0) {
                // Para pagamento parcial, manter como pendente
                $this->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
            } else {
                $this->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
            }
        }

        $this->save();
    }
}
