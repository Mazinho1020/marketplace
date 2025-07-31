<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FidelidadeConquista extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fidelidade_conquistas';

    protected $fillable = [
        'empresa_id',
        'nome',
        'descricao',
        'icone',
        'xp_recompensa',
        'credito_recompensa',
        'tipo_requisito',
        'valor_requisito',
        'ativo'
    ];

    protected $casts = [
        'xp_recompensa' => 'integer',
        'credito_recompensa' => 'decimal:2',
        'valor_requisito' => 'integer',
        'ativo' => 'boolean'
    ];

    // Constantes para tipos de requisito
    public const TIPO_PRIMEIRA_COMPRA = 'primeira_compra';
    public const TIPO_VALOR_TOTAL = 'valor_total';
    public const TIPO_QUANTIDADE_COMPRAS = 'quantidade_compras';
    public const TIPO_PRODUTOS_CATEGORIA = 'produtos_categoria';
    public const TIPO_AVALIACOES = 'avaliacoes';

    public const TIPOS_REQUISITO = [
        self::TIPO_PRIMEIRA_COMPRA => 'Primeira Compra',
        self::TIPO_VALOR_TOTAL => 'Valor Total de Compras',
        self::TIPO_QUANTIDADE_COMPRAS => 'Quantidade de Compras',
        self::TIPO_PRODUTOS_CATEGORIA => 'Produtos de Categoria',
        self::TIPO_AVALIACOES => 'Avaliações Positivas',
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    /**
     * Relacionamento com Clientes que conquistaram
     */
    public function clientesConquistas(): HasMany
    {
        return $this->hasMany(FidelidadeClienteConquista::class, 'conquista_id');
    }

    /**
     * Relacionamento com clientes (através da tabela pivot)
     */
    public function clientes()
    {
        return $this->belongsToMany(\App\Models\Cliente::class, 'fidelidade_cliente_conquistas', 'conquista_id', 'cliente_id')
            ->withPivot(['data_desbloqueio', 'recompensa_resgatada', 'data_resgate'])
            ->withTimestamps();
    }

    /**
     * Accessors
     */
    protected function creditoRecompensaFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => 'R$ ' . number_format($this->credito_recompensa, 2, ',', '.')
        );
    }

    protected function tipoRequisitoFormatado(): Attribute
    {
        return Attribute::make(
            get: fn() => self::TIPOS_REQUISITO[$this->tipo_requisito] ?? $this->tipo_requisito
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ativo
                ? '<span class="badge bg-success">Ativa</span>'
                : '<span class="badge bg-danger">Inativa</span>'
        );
    }

    /**
     * Query Scopes
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    public function scopeInativas(Builder $query): Builder
    {
        return $query->where('ativo', false);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_requisito', $tipo);
    }

    public function scopeComRecompensaCredito(Builder $query): Builder
    {
        return $query->where('credito_recompensa', '>', 0);
    }

    public function scopeComRecompensaXp(Builder $query): Builder
    {
        return $query->where('xp_recompensa', '>', 0);
    }

    public function scopeOrdenadaPorDificuldade(Builder $query): Builder
    {
        return $query->orderBy('valor_requisito', 'asc');
    }

    public function scopeEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Verificar se cliente já possui esta conquista
     */
    public function clienteJaPossui(int $clienteId): bool
    {
        return $this->clientesConquistas()
            ->where('cliente_id', $clienteId)
            ->exists();
    }

    /**
     * Verificar se cliente cumpre os requisitos
     */
    public function clienteCumpreRequisitos(int $clienteId): bool
    {
        if ($this->clienteJaPossui($clienteId)) {
            return false;
        }

        return match ($this->tipo_requisito) {
            self::TIPO_PRIMEIRA_COMPRA => $this->verificarPrimeiraCompra($clienteId),
            self::TIPO_VALOR_TOTAL => $this->verificarValorTotal($clienteId),
            self::TIPO_QUANTIDADE_COMPRAS => $this->verificarQuantidadeCompras($clienteId),
            self::TIPO_PRODUTOS_CATEGORIA => $this->verificarProdutosCategoria($clienteId),
            self::TIPO_AVALIACOES => $this->verificarAvaliacoes($clienteId),
            default => false
        };
    }

    /**
     * Desbloquear conquista para cliente
     */
    public function desbloquearPara(int $clienteId): ?FidelidadeClienteConquista
    {
        if ($this->clienteJaPossui($clienteId)) {
            return null;
        }

        if (!$this->clienteCumpreRequisitos($clienteId)) {
            return null;
        }

        return FidelidadeClienteConquista::create([
            'cliente_id' => $clienteId,
            'conquista_id' => $this->id,
            'data_desbloqueio' => now(),
            'recompensa_resgatada' => false
        ]);
    }

    /**
     * Resgatar recompensa da conquista
     */
    public function resgatarRecompensa(int $clienteId): bool
    {
        $clienteConquista = $this->clientesConquistas()
            ->where('cliente_id', $clienteId)
            ->where('recompensa_resgatada', false)
            ->first();

        if (!$clienteConquista) {
            return false;
        }

        $clienteConquista->update([
            'recompensa_resgatada' => true,
            'data_resgate' => now()
        ]);

        // Aplicar recompensas (credito/xp)
        $this->aplicarRecompensas($clienteId);

        return true;
    }

    /**
     * Obter estatísticas da conquista
     */
    public function estatisticas(): array
    {
        $totalDesbloqueios = $this->clientesConquistas()->count();
        $totalResgates = $this->clientesConquistas()->where('recompensa_resgatada', true)->count();
        $taxaResgate = $totalDesbloqueios > 0 ? ($totalResgates / $totalDesbloqueios) * 100 : 0;

        return [
            'total_desbloqueios' => $totalDesbloqueios,
            'total_resgates' => $totalResgates,
            'taxa_resgate' => round($taxaResgate, 2),
            'credito_distribuido' => $totalResgates * $this->credito_recompensa,
            'xp_distribuido' => $totalResgates * $this->xp_recompensa,
        ];
    }

    /**
     * Verificações específicas de requisitos
     */
    private function verificarPrimeiraCompra(int $clienteId): bool
    {
        // Implementar lógica para verificar primeira compra
        return true; // Placeholder
    }

    private function verificarValorTotal(int $clienteId): bool
    {
        // Implementar verificação de valor total
        return true; // Placeholder
    }

    private function verificarQuantidadeCompras(int $clienteId): bool
    {
        // Implementar verificação de quantidade
        return true; // Placeholder
    }

    private function verificarProdutosCategoria(int $clienteId): bool
    {
        // Implementar verificação de categoria
        return true; // Placeholder
    }

    private function verificarAvaliacoes(int $clienteId): bool
    {
        // Implementar verificação de avaliações
        return true; // Placeholder
    }

    /**
     * Aplicar recompensas ao cliente
     */
    private function aplicarRecompensas(int $clienteId): void
    {
        if ($this->credito_recompensa > 0) {
            // Aplicar crédito na carteira do cliente
            // Implementar lógica específica
        }

        if ($this->xp_recompensa > 0) {
            // Aplicar XP ao cliente
            // Implementar lógica específica
        }
    }

    /**
     * Boot method para eventos do modelo
     */
    protected static function booted(): void
    {
        static::creating(function ($conquista) {
            // Garantir que valores não sejam negativos
            $conquista->xp_recompensa = max(0, $conquista->xp_recompensa ?? 0);
            $conquista->credito_recompensa = max(0, $conquista->credito_recompensa ?? 0);
            $conquista->valor_requisito = max(1, $conquista->valor_requisito ?? 1);
        });
    }
}
