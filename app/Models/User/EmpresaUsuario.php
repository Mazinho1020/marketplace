<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\Models\Business\Business;

class EmpresaUsuario extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'empresa_usuarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'empresa_id',
        'nome',
        'email',
        'email_verified_at',
        'senha',
        'telefone',
        'data_nascimento',
        'sexo',
        'cpf',
        'avatar',
        'status',
        'perfil_id',
        'tipo_id',
        // COLUNAS COMENTADAS TEMPORARIAMENTE - não existem na tabela atual
        // 'ultimo_login',
        // 'ultimo_ip',
        // 'tentativas_login',
        // 'bloqueado_ate',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'data_nascimento' => 'date',
        'ultimo_login' => 'datetime',
        'bloqueado_ate' => 'datetime',
        'sync_data' => 'datetime',
        'tentativas_login' => 'integer',
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

    public const SEXO_MASCULINO = 'M';
    public const SEXO_FEMININO = 'F';
    public const SEXO_OUTRO = 'O';

    public const SEXO_OPTIONS = [
        self::SEXO_MASCULINO => 'Masculino',
        self::SEXO_FEMININO => 'Feminino',
        self::SEXO_OUTRO => 'Outro',
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

    // MÉTODO PARA AUTENTICAÇÃO - Informa ao Laravel qual coluna usar para senha
    public function getAuthPassword()
    {
        return $this->senha;
    }

    // 3. RELACIONAMENTOS
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function tipoPrincipal(): BelongsTo
    {
        return $this->belongsTo(EmpresaUsuarioTipo::class, 'tipo_id');
    }

    public function tipos(): HasMany
    {
        return $this->hasMany(EmpresaUsuarioTipoRel::class, 'usuario_id');
    }

    public function tiposAtivos()
    {
        return $this->tipos()->with('tipo')->whereNull('deleted_at');
    }

    // 4. SCOPES
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INATIVO);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUSPENSO);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BLOQUEADO);
    }

    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeByCpf(Builder $query, string $cpf): Builder
    {
        return $query->where('cpf', $cpf);
    }

    public function scopeWithType(Builder $query, string $tipoCode): Builder
    {
        return $query->whereHas('tipos.tipo', function ($q) use ($tipoCode) {
            $q->where('codigo', $tipoCode);
        });
    }

    public function scopePendingSync(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    public function scopeSynced(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_SYNCED);
    }

    public function scopeSyncError(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_ERROR);
    }

    // 5. ACCESSORS/MUTATORS
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

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->avatar
                ? asset('storage/avatars/' . $this->avatar)
                : asset('assets/images/avatars/default.jpg')
        );
    }

    protected function nomeCompleto(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->nome
        );
    }

    protected function primeiroNome(): Attribute
    {
        return Attribute::make(
            get: fn() => explode(' ', $this->nome)[0]
        );
    }

    // 6. MÉTODOS CUSTOMIZADOS
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOQUEADO ||
            ($this->bloqueado_ate && $this->bloqueado_ate->isFuture());
    }

    public function canLogin(): bool
    {
        return $this->isActive() && !$this->isBlocked();
    }

    public function needsSync(): bool
    {
        return $this->sync_status === self::SYNC_PENDING;
    }

    public function isSynced(): bool
    {
        return $this->sync_status === self::SYNC_SYNCED;
    }

    public function generateSyncHash(): string
    {
        $data = collect($this->getAttributes())
            ->except(['id', 'sync_hash', 'sync_status', 'sync_data', 'created_at', 'updated_at', 'senha', 'remember_token'])
            ->toJson();

        return md5($data);
    }

    public function markForSync(): void
    {
        $this->update([
            'sync_status' => self::SYNC_PENDING,
            'sync_hash' => $this->generateSyncHash()
        ]);
    }

    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => self::SYNC_SYNCED,
            'sync_data' => now()
        ]);
    }

    public function markSyncError(): void
    {
        $this->update(['sync_status' => self::SYNC_ERROR]);
    }

    /**
     * Métodos de tipos de usuário
     */
    public function getTipos()
    {
        return $this->tipos()->with('tipo')->get()->map(function ($rel) {
            return [
                'id' => $rel->tipo->id,
                'codigo' => $rel->tipo->codigo,
                'nome' => $rel->tipo->nome,
                'nivel_acesso' => $rel->tipo->nivel_acesso,
                'is_primary' => $rel->is_primary,
            ];
        });
    }

    public function hasType(string $tipoCode): bool
    {
        return $this->tipos()->whereHas('tipo', function ($q) use ($tipoCode) {
            $q->where('codigo', $tipoCode);
        })->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasType(EmpresaUsuarioTipo::CODIGO_ADMIN);
    }

    public function isComerciante(): bool
    {
        return $this->hasType(EmpresaUsuarioTipo::CODIGO_COMERCIANTE);
    }

    public function isCliente(): bool
    {
        return $this->hasType(EmpresaUsuarioTipo::CODIGO_CLIENTE);
    }

    public function isEntregador(): bool
    {
        return $this->hasType(EmpresaUsuarioTipo::CODIGO_ENTREGADOR);
    }

    public function addTipo(int $tipoId, bool $isPrimary = false): bool
    {
        $rel = EmpresaUsuarioTipoRel::updateOrCreate(
            ['usuario_id' => $this->id, 'tipo_id' => $tipoId],
            ['is_primary' => $isPrimary]
        );

        if ($isPrimary) {
            $this->update(['tipo_id' => $tipoId]);
        }

        return $rel->exists;
    }

    public function removeTipo(int $tipoId): bool
    {
        return EmpresaUsuarioTipoRel::where('usuario_id', $this->id)
            ->where('tipo_id', $tipoId)
            ->delete();
    }

    public function setPrimaryType(int $tipoId): bool
    {
        // Verificar se o usuário tem esse tipo
        if (!$this->tipos()->where('tipo_id', $tipoId)->exists()) {
            return false;
        }

        // Remover primary de todos os tipos
        $this->tipos()->update(['is_primary' => false]);

        // Definir como primary
        $this->tipos()->where('tipo_id', $tipoId)->update(['is_primary' => true]);

        // Atualizar o campo tipo_id principal
        $this->update(['tipo_id' => $tipoId]);

        return true;
    }

    public function getTipoPrincipal()
    {
        if ($this->tipo_id) {
            return EmpresaUsuarioTipo::find($this->tipo_id);
        }

        $primaryRel = $this->tipos()->where('is_primary', true)->first();

        if ($primaryRel) {
            return EmpresaUsuarioTipo::find($primaryRel->tipo_id);
        }

        return null;
    }

    public function hasAccess(int $requiredLevel): bool
    {
        $primaryType = $this->getPrimaryType();
        return $primaryType && $primaryType->canAccess($requiredLevel);
    }

    /**
     * Métodos de autenticação e segurança
     */
    public function recordLoginAttempt(bool $success, string $ip = null): void
    {
        if ($success) {
            $this->update([
                'tentativas_login' => 0,
                'ultimo_login' => now(),
                'ultimo_ip' => $ip ?? request()->ip(),
                'bloqueado_ate' => null,
            ]);
        } else {
            $attempts = $this->tentativas_login + 1;
            $updateData = ['tentativas_login' => $attempts];

            // Bloquear após 5 tentativas por 15 minutos
            if ($attempts >= 5) {
                $updateData['bloqueado_ate'] = now()->addMinutes(15);
            }

            $this->update($updateData);
        }
    }

    public function clearLoginAttempts(): void
    {
        $this->update([
            'tentativas_login' => 0,
            'bloqueado_ate' => null,
        ]);
    }

    /**
     * Métodos estáticos de conveniência
     */
    public static function findByEmail(string $email): ?self
    {
        return static::where('email', $email)->first();
    }

    public static function findByCpf(string $cpf): ?self
    {
        return static::where('cpf', $cpf)->first();
    }

    public static function createWithType(array $userData, string $tipoCode): self
    {
        $tipo = EmpresaUsuarioTipo::getByCode($tipoCode);
        if (!$tipo) {
            throw new \Exception("Tipo de usuário '{$tipoCode}' não encontrado");
        }

        $userData['tipo_id'] = $tipo->id;
        $userData['senha'] = bcrypt($userData['senha']);

        $user = static::create($userData);
        $user->addTipo($tipo->id, true);

        return $user;
    }

    // 7. BOOT METHOD
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // Auto-preenchimento de empresa_id se logado
            if (!$model->empresa_id && Auth::check() && Auth::user()->empresa_id) {
                $model->empresa_id = Auth::user()->empresa_id;
            }

            // Marcar para sincronização
            $model->sync_status = self::SYNC_PENDING;
            $model->sync_hash = $model->generateSyncHash();
        });

        static::updating(function ($model) {
            // Verificar se houve mudanças que requerem sincronização
            if ($model->isDirty() && !$model->isDirty(['sync_status', 'sync_data', 'sync_hash', 'ultimo_login', 'ultimo_ip', 'tentativas_login'])) {
                $model->sync_status = self::SYNC_PENDING;
                $model->sync_hash = $model->generateSyncHash();
            }
        });
    }

    // 8. MÉTODOS DE AUTENTICAÇÃO E SEGURANÇA

    /**
     * Verifica se a conta está bloqueada por tentativas excessivas
     */
    /**
     * Verifica se a conta está bloqueada
     * TEMPORARIAMENTE DESABILITADO - coluna bloqueado_ate não existe
     */
    public function isContaBloqueada(): bool
    {
        // TEMPORARIAMENTE RETORNA FALSE - coluna não existe
        return false;

        /*
        if (!$this->bloqueado_ate) {
            return false;
        }

        // Se o tempo de bloqueio já passou, libera a conta
        if ($this->bloqueado_ate->isPast()) {
            $this->resetarTentativasLogin();
            return false;
        }

        return true;
        */
    }

    /**
     * Incrementa tentativas de login e bloqueia se necessário
     * TEMPORARIAMENTE DESABILITADO - coluna tentativas_login não existe
     */
    public function incrementarTentativasLogin(): void
    {
        // COMENTADO TEMPORARIAMENTE - colunas não existem
        /*
        $this->tentativas_login = ($this->tentativas_login ?? 0) + 1;

        // Bloqueia por 15 minutos após 5 tentativas
        if ($this->tentativas_login >= 5) {
            $this->bloqueado_ate = now()->addMinutes(15);
        }

        $this->save();
        */
    }

    /**
     * Reseta tentativas de login e remove bloqueio
     * TEMPORARIAMENTE DESABILITADO - colunas não existem
     */
    public function resetarTentativasLogin(): void
    {
        // COMENTADO TEMPORARIAMENTE - colunas não existem
        /*
        $this->tentativas_login = 0;
        $this->bloqueado_ate = null;
        $this->save();
        */
    }

    /**
     * Atualiza dados do último login
     * TEMPORARIAMENTE DESABILITADO - colunas não existem
     */
    public function atualizarUltimoLogin(): void
    {
        // COMENTADO TEMPORARIAMENTE - colunas não existem
        /*
        $this->ultimo_login = now();
        $this->ultimo_ip = request()->ip();
        $this->save();
        */
    }

    /**
     * Verifica se o usuário está ativo
     */
    public function getAtivoAttribute(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }
}
