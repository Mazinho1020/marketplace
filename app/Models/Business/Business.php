<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model para a tabela empresas seguindo PADRAO_BANCO_DADOS.md
 * 
 * @property int $id
 * @property string $razao_social
 * @property string $nome_fantasia
 * @property string $cnpj
 * @property string $email
 * @property string $status
 * @property bool $ativo
 */
class Business extends Model
{
    use HasFactory, SoftDeletes;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'empresas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'razao_social',
        'nome_fantasia',
        'trade_name',
        'document',
        'document_type',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'data_abertura',
        'telefone',
        'celular',
        'email',
        'site',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'pais',
        'regime_tributario',
        'optante_simples',
        'incentivo_fiscal',
        'cnae_principal',
        'banco_nome',
        'banco_agencia',
        'banco_conta',
        'banco_tipo_conta',
        'banco_pix',
        'moeda_padrao',
        'fuso_horario',
        'idioma_padrao',
        'logo_url',
        'status',
        'subscription_plan',
        'trial_ends_at',
        'subscription_ends_at',
        'cor_principal',
        'ativo',
        'data_cadastro',
        'data_atualizacao',
        'sync_data',
        'sync_hash',
        'sync_status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sync_data' => 'datetime',
        'data_abertura' => 'date',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'data_cadastro' => 'datetime',
        'data_atualizacao' => 'datetime',
        'optante_simples' => 'boolean',
        'incentivo_fiscal' => 'boolean',
        'ativo' => 'boolean'
    ];

    // 2. CONSTANTES
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';
    public const STATUS_SUSPENSO = 'suspenso';
    public const STATUS_BLOQUEADO = 'bloqueado';

    public const STATUS_OPTIONS = [
        self::STATUS_ATIVO => 'Ativo',
        self::STATUS_INATIVO => 'Inativo',
        self::STATUS_SUSPENSO => 'Suspenso',
        self::STATUS_BLOQUEADO => 'Bloqueado',
    ];

    public const PLANO_BASICO = 'basico';
    public const PLANO_PRO = 'pro';
    public const PLANO_PREMIUM = 'premium';
    public const PLANO_ENTERPRISE = 'enterprise';

    public const PLANO_OPTIONS = [
        self::PLANO_BASICO => 'Básico',
        self::PLANO_PRO => 'Pro',
        self::PLANO_PREMIUM => 'Premium',
        self::PLANO_ENTERPRISE => 'Enterprise',
    ];

    // Constantes para sincronização
    public const SYNC_PENDING = 'pending';
    public const SYNC_SYNCED = 'synced';
    public const SYNC_ERROR = 'error';
    public const SYNC_IGNORED = 'ignored';

    public const SYNC_STATUS_OPTIONS = [
        self::SYNC_PENDING => 'Pendente',
        self::SYNC_SYNCED => 'Sincronizado',
        self::SYNC_ERROR => 'Erro',
        self::SYNC_IGNORED => 'Ignorado',
    ];

    // 3. RELACIONAMENTOS
    /**
     * Relacionamento com usuários da empresa
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(\App\Models\User\EmpresaUsuario::class, 'empresa_id');
    }

    /**
     * Relacionamento com carteiras de fidelidade
     */
    public function carteirasFidelidade(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCarteira::class, 'empresa_id');
    }

    /**
     * Relacionamento com transações de cashback
     */
    public function transacoesCashback(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCashbackTransacao::class, 'empresa_id');
    }

    /**
     * Relacionamento com cupons
     */
    public function cupons(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCupom::class, 'empresa_id');
    }

    /**
     * Relacionamento com regras de cashback
     */
    public function regrasCashback(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCashbackRegra::class, 'empresa_id');
    }

    /**
     * Relacionamento com conquistas
     */
    public function conquistas(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeConquista::class, 'empresa_id');
    }

    /**
     * Relacionamento com créditos
     */
    public function creditos(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCredito::class, 'empresa_id');
    }

    // 4. SCOPES
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ATIVO)->where('ativo', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INATIVO)->orWhere('ativo', false);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUSPENSO);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BLOQUEADO);
    }

    public function scopeByPlan(Builder $query, string $plan): Builder
    {
        return $query->where('subscription_plan', $plan);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<', now());
    }

    public function scopeSyncPending(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    // Alias para compatibilidade
    public function scopeAtivas(Builder $query): Builder
    {
        return $this->scopeActive($query);
    }

    // 5. ACCESSORS E MUTATORS (Laravel 9+ Attributes)
    protected function statusFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => self::STATUS_OPTIONS[$this->status] ?? $this->status
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_ATIVO => '<span class="badge bg-success">Ativo</span>',
                self::STATUS_INATIVO => '<span class="badge bg-secondary">Inativo</span>',
                self::STATUS_SUSPENSO => '<span class="badge bg-warning">Suspenso</span>',
                self::STATUS_BLOQUEADO => '<span class="badge bg-danger">Bloqueado</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            }
        );
    }

    protected function planoFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => self::PLANO_OPTIONS[$this->subscription_plan] ?? $this->subscription_plan
        );
    }

    protected function planoBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->subscription_plan) {
                self::PLANO_BASICO => '<span class="badge bg-info">Básico</span>',
                self::PLANO_PRO => '<span class="badge bg-primary">Pro</span>',
                self::PLANO_PREMIUM => '<span class="badge bg-warning">Premium</span>',
                self::PLANO_ENTERPRISE => '<span class="badge bg-success">Enterprise</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            }
        );
    }

    protected function enderecoCompleto(): Attribute
    {
        return Attribute::make(
            get: fn() => collect([
                $this->logradouro,
                $this->numero,
                $this->complemento,
                $this->bairro,
                $this->cidade,
                $this->uf
            ])->filter()->implode(', ') ?: 'Endereço não informado'
        );
    }

    protected function syncStatusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->sync_status) {
                self::SYNC_SYNCED => '<span class="badge bg-success">Sincronizado</span>',
                self::SYNC_PENDING => '<span class="badge bg-warning">Pendente</span>',
                self::SYNC_ERROR => '<span class="badge bg-danger">Erro</span>',
                self::SYNC_IGNORED => '<span class="badge bg-secondary">Ignorado</span>',
                default => '<span class="badge bg-secondary">N/A</span>'
            }
        );
    }

    // Accessors para compatibilidade com código existente
    protected function plano(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->subscription_plan
        );
    }

    protected function estado(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->uf
        );
    }

    protected function endereco(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->logradouro . ($this->numero ? ', ' . $this->numero : '')
        );
    }

    // 6. MÉTODOS CUSTOMIZADOS
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ATIVO && $this->ativo === true;
    }

    public function isVencido(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    public function diasParaVencimento(): ?int
    {
        if (!$this->subscription_ends_at) return null;
        return now()->diffInDays($this->subscription_ends_at, false);
    }

    public function getStatusBadgeClass(): string
    {
        if (!$this->ativo) return 'secondary';

        return match ($this->status) {
            self::STATUS_ATIVO => 'success',
            self::STATUS_INATIVO => 'secondary',
            self::STATUS_SUSPENSO => 'warning',
            self::STATUS_BLOQUEADO => 'danger',
            default => 'secondary'
        };
    }

    public function getPlanoBadgeClass(): string
    {
        return match ($this->subscription_plan) {
            self::PLANO_BASICO => 'info',
            self::PLANO_PRO => 'primary',
            self::PLANO_PREMIUM => 'warning',
            self::PLANO_ENTERPRISE => 'success',
            default => 'secondary'
        };
    }

    public function generateSyncHash(): string
    {
        $data = $this->only([
            'razao_social',
            'nome_fantasia',
            'cnpj',
            'email',
            'status',
            'ativo'
        ]);

        return md5(json_encode($data));
    }

    public function needsSync(): bool
    {
        return $this->sync_status === self::SYNC_PENDING;
    }

    public function markAsSynced(): bool
    {
        $this->sync_status = self::SYNC_SYNCED;
        $this->sync_data = now();
        $this->sync_hash = $this->generateSyncHash();

        return $this->save();
    }

    public function markSyncError(string $error = null): bool
    {
        $this->sync_status = self::SYNC_ERROR;
        $this->sync_data = now();

        return $this->save();
    }

    // Estatísticas (se necessário)
    public function getQuantidadeUsuariosAttribute(): int
    {
        return $this->usuarios()->count();
    }

    // 7. BOOT METHODS (Eventos)
    protected static function booted(): void
    {
        // Atualizar hash de sincronização
        static::saving(function ($empresa) {
            if ($empresa->isDirty() && !$empresa->isDirty(['sync_status', 'sync_data', 'sync_hash'])) {
                $empresa->sync_status = self::SYNC_PENDING;
                $empresa->sync_hash = $empresa->generateSyncHash();
            }
        });
    }
}
