<?php

namespace App\Models\Exemplo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model de exemplo seguindo PADRAO_BANCO_DADOS.md
 * 
 * Demonstra a implementação completa dos padrões definidos
 * para modelos do marketplace
 */
class ExemploProduto extends Model
{
    use HasFactory, SoftDeletes;

    // 1. CONFIGURAÇÕES DA TABELA
    protected $table = 'exemplo_produtos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'empresa_id',
        'nome',
        'sku',
        'descricao',
        'preco',
        'preco_promocional',
        'preco_custo',
        'estoque',
        'controla_estoque',
        'status',
        'is_active',
        'categoria_id',
        'marca_id',
        'slug',
        'meta_title',
        'meta_description',
        'imagens',
        'atributos',
        'configuracoes',
        'data_lancamento',
        'data_descontinuacao',
        'sync_hash',
        'sync_status',
        'sync_data',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'sync_data' => 'datetime',
        'preco' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'preco_custo' => 'decimal:2',
        'estoque' => 'integer',
        'controla_estoque' => 'boolean',
        'is_active' => 'boolean',
        'data_lancamento' => 'date',
        'data_descontinuacao' => 'date',
        'imagens' => 'array',
        'atributos' => 'array',
        'configuracoes' => 'array',
    ];

    // 2. CONSTANTES
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';
    public const STATUS_DESCONTINUADO = 'descontinuado';

    public const STATUS_OPTIONS = [
        self::STATUS_ATIVO => 'Ativo',
        self::STATUS_INATIVO => 'Inativo',
        self::STATUS_DESCONTINUADO => 'Descontinuado',
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
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function categoria(): BelongsTo
    {
        // return $this->belongsTo(\App\Models\Categoria::class, 'categoria_id');
        return $this->belongsTo(Model::class, 'categoria_id'); // Placeholder - ajustar conforme modelo real
    }

    public function marca(): BelongsTo
    {
        // return $this->belongsTo(\App\Models\Marca::class, 'marca_id');
        return $this->belongsTo(Model::class, 'marca_id'); // Placeholder - ajustar conforme modelo real
    }

    public function vendas(): HasMany
    {
        // return $this->hasMany(\App\Models\PDV\VendaItem::class, 'produto_id');
        return $this->hasMany(Model::class, 'produto_id'); // Placeholder - ajustar conforme modelo real
    }

    // 4. SCOPES
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ATIVO)->where('is_active', true);
    }

    public function scopeComEstoque(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('controla_estoque', false)
                ->orWhere('estoque', '>', 0);
        });
    }

    public function scopePromocional(Builder $query): Builder
    {
        return $query->whereNotNull('preco_promocional')
            ->where('preco_promocional', '>', 0);
    }

    public function scopeSyncPending(Builder $query): Builder
    {
        return $query->where('sync_status', self::SYNC_PENDING);
    }

    public function scopeBusca(Builder $query, string $termo): Builder
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'LIKE', "%{$termo}%")
                ->orWhere('sku', 'LIKE', "%{$termo}%")
                ->orWhere('descricao', 'LIKE', "%{$termo}%");
        });
    }

    // 5. ACCESSORS E MUTATORS (Laravel 9+ Attributes)
    protected function precoFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => 'R$ ' . number_format($this->preco, 2, ',', '.')
        );
    }

    protected function precoPromocionalFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->preco_promocional
                ? 'R$ ' . number_format($this->preco_promocional, 2, ',', '.')
                : null
        );
    }

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
                self::STATUS_INATIVO => '<span class="badge bg-warning">Inativo</span>',
                self::STATUS_DESCONTINUADO => '<span class="badge bg-danger">Descontinuado</span>',
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

    protected function imagemPrincipal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->imagens[0] ?? null
        );
    }

    protected function temEstoque(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->controla_estoque || $this->estoque > 0
        );
    }

    protected function precoFinal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->preco_promocional && $this->preco_promocional > 0
                ? $this->preco_promocional
                : $this->preco
        );
    }

    // Mutators
    protected function sku(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtoupper(trim($value))
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::slug($value)
        );
    }

    // 6. MÉTODOS CUSTOMIZADOS
    public function isActive(): bool
    {
        return $this->is_active === true && $this->status === self::STATUS_ATIVO;
    }

    public function isPromocional(): bool
    {
        return $this->preco_promocional && $this->preco_promocional > 0;
    }

    public function temEstoqueDisponivel(): bool
    {
        return !$this->controla_estoque || $this->estoque > 0;
    }

    public function podeSerVendido(): bool
    {
        return $this->isActive() && $this->temEstoqueDisponivel();
    }

    public function calcularDesconto(): float
    {
        if (!$this->isPromocional()) {
            return 0;
        }

        return (($this->preco - $this->preco_promocional) / $this->preco) * 100;
    }

    public function baixarEstoque(int $quantidade): bool
    {
        if (!$this->controla_estoque) {
            return true;
        }

        if ($this->estoque >= $quantidade) {
            $this->estoque -= $quantidade;
            return $this->save();
        }

        return false;
    }

    public function adicionarEstoque(int $quantidade): bool
    {
        if (!$this->controla_estoque) {
            return true;
        }

        $this->estoque += $quantidade;
        return $this->save();
    }

    public function generateSyncHash(): string
    {
        $data = $this->only([
            'nome',
            'sku',
            'preco',
            'estoque',
            'status',
            'is_active'
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

        if ($error) {
            $configs = $this->configuracoes ?? [];
            $configs['sync_error'] = $error;
            $this->configuracoes = $configs;
        }

        return $this->save();
    }

    // 7. BOOT METHODS (Eventos)
    protected static function booted(): void
    {
        // Gerar slug automaticamente
        static::creating(function ($produto) {
            if (!$produto->slug) {
                $produto->slug = Str::slug($produto->nome);
            }
        });

        // Atualizar hash de sincronização
        static::saving(function ($produto) {
            if ($produto->isDirty() && !$produto->isDirty(['sync_status', 'sync_data', 'sync_hash'])) {
                $produto->sync_status = self::SYNC_PENDING;
                $produto->sync_hash = $produto->generateSyncHash();
            }
        });

        // Global scope para multitenancy (se necessário)
        // static::addGlobalScope('empresa', function (Builder $builder) {
        //     if (auth()->check() && auth()->user()->empresa_id) {
        //         $builder->where('empresa_id', auth()->user()->empresa_id);
        //     }
        // });
    }
}
