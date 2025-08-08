<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AfiPlanAssinaturas extends Model
{
    use HasFactory;

    protected $table = 'afi_plan_assinaturas';

    protected $fillable = [
        'empresa_id',
        'funforcli_id',
        'plano_id',
        'ciclo_cobranca',
        'valor',
        'status',
        'trial_expira_em',
        'iniciado_em',
        'expira_em',
        'proxima_cobranca_em',
        'ultima_cobranca_em',
        'cancelado_em',
        'renovacao_automatica'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'trial_expira_em' => 'datetime',
        'iniciado_em' => 'datetime',
        'expira_em' => 'datetime',
        'proxima_cobranca_em' => 'datetime',
        'ultima_cobranca_em' => 'datetime',
        'cancelado_em' => 'datetime',
        'renovacao_automatica' => 'boolean'
    ];

    /**
     * Status possíveis da assinatura
     */
    const STATUS_TRIAL = 'trial';
    const STATUS_ATIVO = 'ativo';
    const STATUS_SUSPENSO = 'suspenso';
    const STATUS_EXPIRADO = 'expirado';
    const STATUS_CANCELADO = 'cancelado';

    /**
     * Ciclos de cobrança possíveis
     */
    const CICLO_MENSAL = 'mensal';
    const CICLO_ANUAL = 'anual';
    const CICLO_VITALICIO = 'vitalicio';

    /**
     * Relacionamento com plano
     */
    public function plano()
    {
        return $this->belongsTo(AfiPlanPlanos::class, 'plano_id');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }

    /**
     * Relacionamento com usuário (funforcli)
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\Funforcli::class, 'funforcli_id');
    }

    /**
     * Relacionamento com transações
     */
    public function transacoes()
    {
        return $this->hasMany(AfiPlanTransacoes::class, 'id_origem')
            ->where('tipo_origem', 'nova_assinatura');
    }

    /**
     * Scope para assinaturas ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', self::STATUS_ATIVO)
            ->where('expira_em', '>', now());
    }

    /**
     * Scope para assinaturas expiradas
     */
    public function scopeExpirado($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_EXPIRADO)
                ->orWhere('expira_em', '<=', now());
        });
    }

    /**
     * Verificar se está ativo
     */
    public function isAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO &&
            $this->expira_em > now();
    }

    /**
     * Verificar se está em trial
     */
    public function isTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL &&
            $this->trial_expira_em > now();
    }

    /**
     * Verificar se está expirado
     */
    public function isExpirado(): bool
    {
        return $this->status === self::STATUS_EXPIRADO ||
            $this->expira_em <= now();
    }

    /**
     * Dias restantes da assinatura
     */
    public function getDiasRestantesAttribute(): int
    {
        if ($this->isExpirado()) {
            return 0;
        }

        return now()->diffInDays($this->expira_em, false);
    }

    /**
     * Verificar se tem um recurso específico
     */
    public function hasFeature(string $feature): bool
    {
        return $this->plano ? $this->plano->hasFeature($feature) : false;
    }

    /**
     * Obter limite de um recurso
     */
    public function getLimit(string $resource): int
    {
        return $this->plano ? $this->plano->getLimit($resource) : 0;
    }

    /**
     * Verificar se está próximo do vencimento (7 dias)
     */
    public function isProximoVencimento(): bool
    {
        return $this->isAtivo() && $this->dias_restantes <= 7;
    }

    /**
     * Calcular próxima data de cobrança
     */
    public function calcularProximaCobranca(): Carbon
    {
        $base = $this->proxima_cobranca_em ?? $this->expira_em ?? now();

        return match ($this->ciclo_cobranca) {
            self::CICLO_MENSAL => $base->addMonth(),
            self::CICLO_ANUAL => $base->addYear(),
            default => $base->addMonth()
        };
    }

    /**
     * Renovar assinatura
     */
    public function renovar(): bool
    {
        if (!$this->renovacao_automatica) {
            return false;
        }

        $proximaExpiracao = $this->calcularProximaCobranca();

        $this->update([
            'expira_em' => $proximaExpiracao,
            'proxima_cobranca_em' => $proximaExpiracao,
            'ultima_cobranca_em' => now(),
            'status' => self::STATUS_ATIVO
        ]);

        return true;
    }
}
